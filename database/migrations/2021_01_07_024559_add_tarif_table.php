<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTarifTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tarif', function (Blueprint $table) {
            $table->foreign('connote_id')->references('id')->on('connote')->onDelete('cascade');
			$table->foreign('connote_state_id')->references('id')->on('connote_state')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tarif', function (Blueprint $table) {
            //
            $table->dropForeign(['connote_id','connote_state_id']);
        });
    }
}
