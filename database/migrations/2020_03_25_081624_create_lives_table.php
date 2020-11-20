<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lives', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->comment('会员ID');
            $table->unsignedBigInteger('category_id')->comment('分类');
            $table->string('title',100)->comment('直播间名称');
            $table->boolean('screen')->default(0)->comment('屏幕方向');
            $table->string('cover',100)->comment('直播间封面');
            $table->string('hls')->nullable()->comment('播放地址');
            $table->json('backs')->nullable()->comment('往期直播');
            $table->unsignedBigInteger('times')->comment('直播总时长');
            $table->unsignedBigInteger('product')->nullable()->comment('直播商品ID');
            $table->boolean('status')->default(0)->comment('直播间状态');
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
        Schema::dropIfExists('lives');
    }
}
