<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsMpToSuscriptionsTable2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('suscripcions', function (Blueprint $table) {
            $table->string('payer_first_name_mp')->nullable()->after('reason_mp');
            $table->string('payer_last_name_mp')->nullable()->after('reason_mp');
            $table->string('observaciones_mp')->nullable()->after('has_billing_day_mp');
            $table->string('proceso_asociado')->nullable()->after('proximo_cobro');         
             
        });

        Schema::table('suscripcion_controls', function (Blueprint $table) {
            $table->string('payer_first_name_mp')->nullable()->after('reason_mp');
            $table->string('payer_last_name_mp')->nullable()->after('reason_mp');
            $table->string('observaciones_mp')->nullable()->after('has_billing_day_mp');
            $table->string('proceso_asociado')->nullable()->after('modulos_amount');         
             
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('suscripcions', function (Blueprint $table) {
            $table->dropColumn('payer_first_name_mp');
            $table->dropColumn('payer_last_name_mp');
            $table->dropColumn('observaciones_mp');
            $table->dropColumn('proceso_asociado');
        });

        Schema::table('suscripcion_controls', function (Blueprint $table) {
            $table->dropColumn('payer_first_name_mp');
            $table->dropColumn('payer_last_name_mp');
            $table->dropColumn('observaciones_mp');
            $table->dropColumn('proceso_asociado');
        });
    }
}
