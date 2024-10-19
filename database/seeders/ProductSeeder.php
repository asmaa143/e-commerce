<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('products')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('products')->insert([
            [

                'name' => json_encode(['en' => 'test', 'ar' => 'تيست']),
                'price' => 10,
                'stock_quantity' => 10,
                'description' => json_encode(['en' => 'test', 'ar' => 'تيست']),
            ],
            [

                'name' => json_encode(['en' => 'test2', 'ar' => 'تيست']),
                'price' => 15,
                'stock_quantity' => 10,
                'description' => json_encode(['en' => 'test', 'ar' => 'تيست']),
            ],
            [

                'name' => json_encode(['en' => 'test3', 'ar' => 'تيست']),
                'price' => 20,
                'stock_quantity' => 10,
                'description' => json_encode(['en' => 'test', 'ar' => 'تيست']),
            ],
        ]);



    }
}
