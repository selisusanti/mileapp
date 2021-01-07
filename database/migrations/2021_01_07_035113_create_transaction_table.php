<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('transaction_code');
            $table->string('transaction_order');
            $table->string('transaction_payment_type_name');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->foreign('customer_id')->references('id')->on('customer')->onDelete('cascade');
            $table->string('transaction_additional_field')->nullable();
            $table->unsignedBigInteger('transaction_payment_type')->nullable();
            $table->foreign('transaction_payment_type')->references('id')->on('connote_state')->onDelete('cascade');
            $table->string('location_id')->nullable();
            $table->unsignedBigInteger('connote_id')->nullable();
            $table->foreign('connote_id')->references('id')->on('connote')->onDelete('cascade');
            $table->unsignedBigInteger('origin_data')->nullable();
            $table->foreign('origin_data')->references('id')->on('customer')->onDelete('cascade');
            $table->unsignedBigInteger('destination_data')->nullable();
            $table->foreign('destination_data')->references('id')->on('customer')->onDelete('cascade');
            $table->string('custom_field')->nullable();
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
        Schema::table('koli', function (Blueprint $table) {
            $table->dropForeign(['destination_data','origin_data','connote_id','customer_id','transaction_payment_type']);
        });
        Schema::dropIfExists('transaction');
    }
}
