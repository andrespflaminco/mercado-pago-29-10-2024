<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_details', function (Blueprint $table) {
            $table->id();
            $table->decimal('price',10,2);
            $table->decimal('quantity',10,2);
            $table->integer('metodo_pago');
            $table->integer('comercio_id');
            $table->integer('seccionalmacen_id')->default(1);
            $table->foreignId('product_id')->constrained(); // product
            $table->foreignId('sale_id')->constrained();
            $table->integer('estado');  




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
        Schema::dropIfExists('sale_details');
    }
}
