<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConnoteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('connote', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('connote_number');
            $table->string('connote_service');
            $table->string('connote_service_price');
            $table->string('connote_amount');
			$table->string('connote_code');
            $table->string('connote_booking_code')->nullable();
			$table->string('connote_order');
            $table->unsignedBigInteger('connote_state_id')->nullable();
            $table->foreign('connote_state_id')->references('id')->on('connote_state')->onDelete('cascade');
            $table->string('zone_code_from');
			$table->string('zone_code_to');
			$table->string('surcharge_amount')->nullable();
			$table->string('transaction_id')->nullable();
			$table->string('actual_weight');
			$table->string('volume_weight');
			$table->string('chargeable_weight');
            $table->timestamps();
			$table->string('organization_id');
            $table->unsignedBigInteger('location_id')->nullable();
            $table->foreign('location_id')->references('id')->on('location')->onDelete('cascade');
			$table->string('connote_total_package');
			$table->string('connote_surcharge_amount');
			$table->string('connote_sla_day');
			$table->string('location_current');
			$table->string('source_tariff_db');
			$table->string('id_source_tariff');
			$table->string('pod')->nullable();
			$table->string('history')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('koli', function (Blueprint $table) {
            $table->dropForeign(['connote_state_id','location_id']);
        });
        Schema::dropIfExists('connote');
    }
}
