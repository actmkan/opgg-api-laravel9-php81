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
        Schema::create('user_points', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->index()->comment('회원 id');
            $table->unsignedInteger('point')->index()->comment('포인트 증감');
            $table->string('type', 40)->index()->comment('포인트 증감 유형');
            $table->string('memo', 200)->comment('포인트 증감 메모')->nullable();
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
        Schema::dropIfExists('user_points');
    }
};
