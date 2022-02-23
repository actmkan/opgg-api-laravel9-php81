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
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->char('hash', 32)->index()->comment('md5 hash');
            $table->integer('created_user_id')->comment('최초 업로드 유저')->nullable()->index();
            $table->string('origin_path', 100)->comment('원본 파일 s3 경로');
            $table->string('optimize_path', 100)->comment('최적화 파일 s3 경로');
            $table->char('extension', 5)->comment('파일 확장자');
            $table->integer('size')->comment('원본 이미지 파일 사이즈 byte');
            $table->integer('width')->comment('이미지 가로 사이즈');
            $table->integer('height')->comment('이미지 세로 사이즈');
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
        Schema::dropIfExists('images');
    }
};
