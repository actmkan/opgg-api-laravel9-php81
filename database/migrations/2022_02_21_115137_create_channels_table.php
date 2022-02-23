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
        Schema::create('channels', function (Blueprint $table) {
            $table->id();
            $table->integer('talk_id')->index()->comment('톡 id');
            $table->string('group', 40)->index()->comment('채널 그룹명');
            $table->string('display_group', 40)->comment('채널 그룹 표시명');
            $table->string('name', 40)->index()->comment('채널명');
            $table->string('display_name', 40)->comment('채널 표시명');
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
        Schema::dropIfExists('channels');
    }
};
