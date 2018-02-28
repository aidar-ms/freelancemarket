<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->text('description');
            $table->integer('freelancer_id')->nullable();
            $table->string('freelancer')->nullable();
            $table->string('freelancer_email')->nullable();
            $table->integer('hirer_id');
            $table->string('hirer');
            $table->string('hirer_email');
            $table->double('price');
            $table->string('status');
            $table->timestamp('deadline_at');
            $table->timestamp('assigned_at')->nullable();
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
        Schema::dropIfExists('contracts');
    }
}
