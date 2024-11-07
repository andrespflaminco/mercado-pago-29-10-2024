<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsMpToSuscripcionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('suscripcions', function (Blueprint $table) {

            $table->string('collector_id_mp')->nullable();
            $table->string('application_id_mp')->nullable();
            $table->string('reason_mp')->nullable();
            $table->dateTime('date_created_mp')->nullable();
            $table->dateTime('last_modified_mp')->nullable();
            $table->integer('frequency_mp')->nullable();
            $table->string('frequency_type_mp')->nullable();
            $table->double('transaction_amount_mp')->nullable();
            $table->string('currency_id_mp')->nullable();
            $table->dateTime('start_date_mp')->nullable();
            $table->dateTime('end_date_mp')->nullable();
            $table->string('free_trial_mp')->nullable();



            $table->integer('quotas_mp')->nullable();
            $table->integer('charged_quantity_mp')->nullable();
            $table->integer('pending_charge_quantity_mp')->nullable();
            $table->double('charged_amount_mp')->nullable();
            $table->double('pending_charge_amount_mp')->nullable();


            $table->string('semaphore_mp')->nullable();
            $table->dateTime('last_charged_date_mp')->nullable();
            $table->double('last_charged_amount_mp')->nullable();


            $table->dateTime('next_payment_date_mp')->nullable();
            $table->string('payment_method_id_mp')->nullable();
            $table->string('payment_method_id_secondary_mp')->nullable();
            $table->string('first_invoice_offset_mp')->nullable();
        });

        Schema::table('suscripcion_controls', function (Blueprint $table) {

            $table->string('collector_id_mp')->nullable();
            $table->string('application_id_mp')->nullable();
            $table->string('reason_mp')->nullable();
            $table->dateTime('date_created_mp')->nullable();
            $table->dateTime('last_modified_mp')->nullable();
            $table->integer('frequency_mp')->nullable();
            $table->string('frequency_type_mp')->nullable();
            $table->double('transaction_amount_mp')->nullable();
            $table->string('currency_id_mp')->nullable();
            $table->dateTime('start_date_mp')->nullable();
            $table->dateTime('end_date_mp')->nullable();
            $table->string('free_trial_mp')->nullable();



            $table->integer('quotas_mp')->nullable();
            $table->integer('charged_quantity_mp')->nullable();
            $table->integer('pending_charge_quantity_mp')->nullable();
            $table->double('charged_amount_mp')->nullable();
            $table->double('pending_charge_amount_mp')->nullable();


            $table->string('semaphore_mp')->nullable();
            $table->dateTime('last_charged_date_mp')->nullable();
            $table->double('last_charged_amount_mp')->nullable();


            $table->dateTime('next_payment_date_mp')->nullable();
            $table->string('payment_method_id_mp')->nullable();
            $table->string('payment_method_id_secondary_mp')->nullable();
            $table->string('first_invoice_offset_mp')->nullable();
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


            $table->dropColumn('collector_id_mp');
            $table->dropColumn('application_id_mp');
            $table->dropColumn('reason_mp');
            $table->dropColumn('date_created_mp');
            $table->dropColumn('last_modified_mp');
            $table->dropColumn('frequency_mp');
            $table->dropColumn('frequency_type_mp');
            $table->dropColumn('transaction_amount_mp');
            $table->dropColumn('currency_id_mp');
            $table->dropColumn('start_date_mp');
            $table->dropColumn('end_date_mp');
            $table->dropColumn('free_trial_mp');



            $table->dropColumn('quotas_mp');
            $table->dropColumn('charged_quantity_mp');
            $table->dropColumn('pending_charge_quantity_mp');
            $table->dropColumn('charged_amount_mp');
            $table->dropColumn('pending_charge_amount_mp');


            $table->dropColumn('semaphore_mp');
            $table->dropColumn('last_charged_date_mp');
            $table->dropColumn('last_charged_amount_mp');


            $table->dropColumn('next_payment_date_mp');
            $table->dropColumn('payment_method_id_mp');
            $table->dropColumn('payment_method_id_secondary_mp');
            $table->dropColumn('first_invoice_offset_mp');
        });

        Schema::table('suscripcions_control', function (Blueprint $table) {

            $table->dropColumn('collector_id_mp');
            $table->dropColumn('application_id_mp');
            $table->dropColumn('reason_mp');
            $table->dropColumn('date_created_mp');
            $table->dropColumn('last_modified_mp');
            $table->dropColumn('frequency_mp');
            $table->dropColumn('frequency_type_mp');
            $table->dropColumn('transaction_amount_mp');
            $table->dropColumn('currency_id_mp');
            $table->dropColumn('start_date_mp');
            $table->dropColumn('end_date_mp');
            $table->dropColumn('free_trial_mp');



            $table->dropColumn('quotas_mp');
            $table->dropColumn('charged_quantity_mp');
            $table->dropColumn('pending_charge_quantity_mp');
            $table->dropColumn('charged_amount_mp');
            $table->dropColumn('pending_charge_amount_mp');


            $table->dropColumn('semaphore_mp');
            $table->dropColumn('last_charged_date_mp');
            $table->dropColumn('last_charged_amount_mp');


            $table->dropColumn('next_payment_date_mp');
            $table->dropColumn('payment_method_id_mp');
            $table->dropColumn('payment_method_id_secondary_mp');
            $table->dropColumn('first_invoice_offset_mp');
        });
    }
}
