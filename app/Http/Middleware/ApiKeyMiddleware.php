<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ApiKey;

class ApiKeyMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $apiKey = $request->header('x-api-key');

        if (!$this->isValidApiKey($apiKey)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }

    private function isValidApiKey($apiKey)
    {
        // Verifica se a chave existe no banco de dados
        return ApiKey::where('key', $apiKey)->exists();
    }
}
