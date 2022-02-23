<?php

use App\Enums\ChannelPermissionEnum;
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
        Schema::create('channel_permissions', function (Blueprint $table) {
            $table->id();
            $table->enum('type', [
                ...collect(ChannelPermissionEnum::cases())->map(fn ($permission) => $permission->name)
            ])->comment('액션타입')->index();
            $table->integer('channel_id')->comment('채널 id')->index();
            $table->integer('grade_id')->comment('등급 id')->index()->nullable();
            $table->boolean('is_writer')->comment('작성자만 허용')->index()->default(0);
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
        Schema::dropIfExists('channel_permissions');
    }
};
