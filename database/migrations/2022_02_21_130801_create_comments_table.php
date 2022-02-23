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
        Schema::create('comments', function (Blueprint $table) {
            $table->charset = 'utf8mb4'; // 이모지 사용 가능
            $table->id();
            $table->boolean('is_notice')->comment('공지사항 여부')->index();
            $table->bigInteger('article_id')->comment('작성글 id')->index();
            $table->bigInteger('parent_id')->comment('부모댓글 id')->index()->nullable();
            $table->bigInteger('user_id')->comment('회원 id')->index();
            $table->bigInteger('image_id')->comment('이미지 id')->index();
            $table->text('content')->comment('내용');
            $table->bigInteger('like_count')->comment('추천 갯수')->index();
            $table->bigInteger('unlike_count')->comment('비추천 갯수')->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comments');
    }
};
