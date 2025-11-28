<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('expert_system_histories', function (Blueprint $table) {
            $table->id();
            $table->integer('budget');
            $table->string('kebutuhan');
            $table->integer('durasi_gaming')->nullable();
            $table->string('layar')->nullable();
            $table->string('chipset')->nullable();
            $table->json('result');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('expert_system_histories');
    }
};