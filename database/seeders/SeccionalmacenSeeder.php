<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\seccionalmacen;

class SeccionalmacenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      Estados::create([
        'nombre' => 'Sin almacen'
      ]);
    }
}
