<?php

namespace Database\Seeders;

use App\Models\ApiKey;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ApiKeySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ApiKey::create([
            "key" => "0AE5744CD44AD1CF094F71081A9755D18DF121CA63947BC7AF7837F6629D95F5",
            "name" => "bate-forte",
        ]);
    }
}
