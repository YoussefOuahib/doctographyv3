<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('patient_id');
            $table->string('act')->nullable();
            $table->string('medical_treatment')->nullable();
            $table->date('next_examination_date')->nullable();
            $table->enum('type', ['payment', 'session']);
            // $table->bigInteger('total_sessions')->nullable();
            $table->bigInteger('total_amount')->nullable();
            $table->bigInteger('remaining_amount')->nullable();
            // $table->bigInteger('remaining_sessions')->nullable();
            $table->string('note')->nullable();
            $table->bigInteger('rate')->default(0);
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
        Schema::dropIfExists('appointments');
    }
};
