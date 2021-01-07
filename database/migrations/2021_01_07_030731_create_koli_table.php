<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKoliTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('koli', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->string('koli_id');
			$table->string('koli_code');
            $table->string('koli_length');
            $table->string('awb_url');
            $table->integer('koli_chargeable_weight');
            $table->integer('koli_width');
			$table->integer('koli_surcharge');
			$table->integer('koli_height');
			$table->string('koli_description');
			$table->string('koli_formula_id')->nullable();
			$table->unsignedBigInteger('connote_id')->nullable();
			$table->integer('koli_volume');
            $table->timestamps();
            $table->foreign('connote_id')->references('id')->on('connote')->onDelete('cascade');
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
            $table->dropForeign(['connote_id']);
        });
        Schema::dropIfExists('koli');
    }
}
