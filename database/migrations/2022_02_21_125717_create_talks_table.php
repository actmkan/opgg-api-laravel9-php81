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
        Schema::create('talks', function (Blueprint $table) {
            $table->id();
            $table->string('name', 40)->index()->comment('톡명');
            $table->string('display_name', 40)->comment('톡 표시명');
            $table->bigInteger('banner_image_id')->comment('배너 이미지 id');
            $table->bigInteger('logo_image_id')->comment('로고 이미지 id');
            $table->bigInteger('background_image_id')->comment('배경 이미지 id');
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
        Schema::dropIfExists('talks');
    }
};
