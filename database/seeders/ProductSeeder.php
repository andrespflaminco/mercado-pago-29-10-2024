<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Product::create([
        	'name' => 'LARAVEL Y LIVEWIRE',
        	'cost' => 200,
        	'price' => 350,
        	'barcode' => '75010065987',
        	'stock' => 1000,
        	'alerts' => 10,
        	'category_id' => 1,
        	'image' => 'curso.png'
        ]);        
    }
}
