<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetailSolicitationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_solicitations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('solicitation_id')->unsigned();
            $table->foreign('solicitation_id')->references('id')->on('solicitations');
            $table->string('name');
            $table->string('last_name');
            $table->string('department');
            $table->string('registration');
            $table->string('photo');
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
        Schema::dropIfExists('detail_solicitations');
    }
}
