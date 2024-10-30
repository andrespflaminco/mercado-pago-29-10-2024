<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Estados;

class EstadosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      Estados::create([
        'nombre' => 'Pendiente'
      ]);
      Estados::create([
        'nombre' => 'En fabricación'
      ]);
      Estados::create([
        'nombre' => 'Terminado'
      ]);
      Estados::create([
        'nombre' => 'Entregado'
      ]);
    }
}
