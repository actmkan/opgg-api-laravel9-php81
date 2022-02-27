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
        Schema::create('articles', function (Blueprint $table) {
            $table->charset = 'utf8mb4'; // 이모지 사용 가능
            $table->id();
            $table->boolean('is_notice')->comment('채널 공지사항 여부')->index();
            $table->boolean('is_all_notice')->comment('전체 공지사항 여부')->index();
            $table->integer('talk_id')->comment('톡 id')->index();
            $table->integer('channel_id')->comment('채널 id')->index();
            $table->bigInteger('user_id')->comment('회원 id')->index();
            $table->string('title', 100)->comment('제목')->index();
            $table->mediumText('content')->comment('내용');
            $table->bigInteger('like_count')->comment('추천 갯수')->index();
            $table->bigInteger('unlike_count')->comment('비추천 갯수')->index();
            $table->bigInteger('view_count')->comment('조회수')->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('articles');
    }
};
