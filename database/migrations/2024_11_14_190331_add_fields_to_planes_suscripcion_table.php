<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToPlanesSuscripcionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('planes_suscripcions', function (Blueprint $table) {
            $table->integer('frequency')->nullable()->default(1);
            $table->string('frequency_type')->nullable()->default('months');
            $table->integer('trial_frequency')->nullable()->default(14);
            $table->string('trial_frequency_type')->nullable()->default('days');
            $table->integer('billing_day')->nullable();
            $table->boolean('billing_day_proportional')->nullable()->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('planes_suscripcions', function (Blueprint $table) {
            $table->dropColumn([
                'frequency',
                'frequency_type',
                'trial_frequency',
                'trial_frequency_type',
                'billing_day',
                'billing_day_proportional',
            ]);
        });
    }
}
