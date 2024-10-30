<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientesMostradorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clientes_mostradors', function (Blueprint $table) {
            $table->id();
            $table->string('nombre',255);
            $table->string('telefono',11)->nullable();
            $table->string('email',100)->unique();
            $table->string('direccion',100);
            $table->string('barrio',100)->nullable();
            $table->string('localidad',100);
            $table->string('dni',100);
            $table->enum('status',['ACTIVO','BLOQUEADO'])->default('ACTIVO');
            $table->string('image',50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clientes_mostradors');
    }
}
