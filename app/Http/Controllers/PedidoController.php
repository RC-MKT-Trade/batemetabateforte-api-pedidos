<?php

namespace App\Http\Controllers;

use App\Models\HistoricItens;
use App\Models\Pedidos;
use App\Models\Itens;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PedidoController extends Controller
{

    public function salvarPedidos(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'statusCompra' => 'required|string',
            'codCliente' => 'required|string',
            'cnpjCliente' => 'required|string',
            'cnpjFilialBf' => 'required|string',
            'codRca' => 'required|string',
            'numPedido' => 'required|string',
            'dataPedido' => 'required|date',
            'valorTotalPedido' => 'required|numeric',
            'canalVenda' => 'required|string',
            'segmento' => 'required|string',
            'chaveSefaz' => 'required|array',
            'chaveSefaz.*.id' => 'required|string',
            'chaveSefaz.*.itens' => 'required|array',
            'chaveSefaz.*.itens.*.descCompleta' => 'required|string',
            'chaveSefaz.*.itens.*.ean' => 'required|string',
            'chaveSefaz.*.itens.*.quantProdutoUnidade' => 'required|numeric',
            'chaveSefaz.*.itens.*.quantProdutoCaixa' => 'required|numeric',
            'chaveSefaz.*.itens.*.valorProdutoUnidade' => 'required|numeric',
            'chaveSefaz.*.itens.*.valorTotalProduto' => 'required|numeric',
        ]);

        // Se a validação falhar
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erro de validação',
                'errors' => $validator->errors()
            ], 422);
        }

        // Pega os dados validados
        $data = $validator->validated();

        // Verifica se o pedido já existe com base no número do pedido
        $pedido = Pedidos::firstOrNew(['numPedido' => $data['numPedido']]);

        // Se o pedido não existir, cria um novo
        if (!$pedido->exists) {
            if ($data['statusCompra'] == 'devolucao_parcial') {       
                $statusFaturado = Status::where('status', 'faturado')->where('idPedido', $pedido->id)->orderBy('data', 'desc')->first();

                if (!$statusFaturado) {
                    return response()->json([
                        'message' => 'Pedido não possui status faturado para devolução parcial',
                    ], 422);
                }
            }

            $pedido->fill([
                'statusCompra' => $data['statusCompra'],
                'codCliente' => $data['codCliente'],
                'cnpjCliente' => $data['cnpjCliente'],
                'cnpjFilialBf' => $data['cnpjFilialBf'],
                'codRca' => $data['codRca'],
                'numPedido' => $data['numPedido'],
                'dataPedido' => $data['dataPedido'],
                'valorTotalPedido' => $data['valorTotalPedido'],
                'canalVenda' => $data['canalVenda'],
                'segmento' => $data['segmento'],
                'criadoEm' => now(),
            ]);
            $pedido->save();

            Status::create([
                'idPedido' => $pedido->id,
                'status' => $data['statusCompra'],
                'valorTotal' => $data['valorTotalPedido'] ?? 0,
                'data' => $data['dataPedido'] ?? now(),
            ]);
            
        } else {

            if ($data['statusCompra'] !== 'devolucao_parcial') {
                $statusExiste = Status::where('status', $data['statusCompra'])->where('idPedido', $pedido->id)->exists();  

                if ($statusExiste) {
                    return response()->json([
                        'message' => 'Status duplicado para o pedido informado',
                    ], 422);
                }
            }

            
            // Verifica e atualiza campos que têm dados novos
            $pedido->fill([
                'statusCompra' => $data['statusCompra'],
                'codCliente' => $data['codCliente'],
                'cnpjCliente' => $data['cnpjCliente'],
                'cnpjFilialBf' => $data['cnpjFilialBf'],
                'codRca' => $data['codRca'],
                'dataPedido' => $data['dataPedido'],
                'valorTotalPedido' => $data['valorTotalPedido'],
                'canalVenda' => $data['canalVenda'],
                'segmento' => $data['segmento'],
                'atualizadoEm' => now(),
            ]);
            $pedido->save(); // Salva as atualizações se houver alterações

            // Salva o novo status na tabela status_pedido se o status foi atualizado
            if ($pedido->wasChanged('statusCompra')) {
                Status::create([
                    'idPedido' => $pedido->id,
                    'status' => $data['statusCompra'],
                    'valorTotal' => $data['valorTotalPedido'] ?? 0,
                    'data' => $data['dataPedido'] ?? now(),
                ]);
            }
        }
        // Recupera todos os itens atuais associados ao pedido
        $itensExistentes = Itens::where('idPedido', $pedido->id)->get()->keyBy('ean');

        if ($data['statusCompra'] == 'devolucao_total') {
            foreach ($itensExistentes as $item) {
                $item->delete();
            }
        }

        if ($data['statusCompra'] == 'devolucao_parcial') {
            $statusId = Status::where('status', 'devolucao_parcial')->where('idPedido', $pedido->id)->orderBy('data', 'desc')->first()->id;

            foreach ($itensExistentes as $item) {
                HistoricItens::create([
                    'idStatus' => $statusId,
                    'idItem' => $item->id,
                ]);

                $item->delete();
            }
            
            $statusFaturado = Status::where('status', 'faturado')->where('idPedido', $pedido->id)->orderBy('data', 'desc')->first();

            if (!$statusFaturado) {
                return response()->json([
                    'message' => 'Pedido não possui status faturado para devolução parcial',
                ], 422);
            }

            $valorTotalSaldo = floatval($statusFaturado->valorTotal) - floatval($data['valorTotalPedido']);

            Status::create([
                'idPedido' => $pedido->id,
                'status' =>  "saldo_entregue",
                'valorTotal' => $valorTotalSaldo,
                'data' => $data['dataPedido'] ?? now(),
            ]);

        } else {
            // Atualiza ou cria os itens associados ao pedido
            foreach ($data['chaveSefaz'] as $chave) {
                foreach ($chave['itens'] as $itemData) {
                    // Verifica se o item já existe pelo EAN e idPedido
                    if (isset($itensExistentes[$itemData['ean']])) {
                        // Preenche o modelo existente com os novos dados
                        $itemExistente = $itensExistentes[$itemData['ean']];
                        $itemExistente->fill([
                            'chaveSefaz' => $chave['id'],
                            'descCompleta' => $itemData['descCompleta'],
                            'ean' => $itemData['ean'],
                            'quantProdutoUnidade' => $itemData['quantProdutoUnidade'],
                            'quantProdutoCaixa' => $itemData['quantProdutoCaixa'],
                            'valorProdutoUnidade' => $itemData['valorProdutoUnidade'],
                            'valorTotalProduto' => $itemData['valorTotalProduto'],
                        ]);

                        // Verifica se houve mudanças e salva
                        if ($itemExistente->isDirty()) {
                            $itemExistente->save(); // Atualiza o item existente
                        }
                    } else {
                        // Se o item não existir, cria um novo
                        Itens::create([
                            'idPedido' => $pedido->id,
                            'chaveSefaz' => $chave['id'],
                            'descCompleta' => $itemData['descCompleta'],
                            'ean' => $itemData['ean'],
                            'quantProdutoUnidade' => $itemData['quantProdutoUnidade'],
                            'quantProdutoCaixa' => $itemData['quantProdutoCaixa'],
                            'valorProdutoUnidade' => $itemData['valorProdutoUnidade'],
                            'valorTotalProduto' => $itemData['valorTotalProduto'],
                        ]);
                    }
                }
            }
        }

        // Retorna uma resposta de sucesso
        return response()->json([
            'message' => 'Pedido cadastrado/atualizado com sucesso',
        ], 200);
    }
}
