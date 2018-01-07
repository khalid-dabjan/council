<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateThreadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('threads', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug')->unique()->nullable();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('channel_id');
            $table->string('title');
            $table->unsignedInteger('replies_count')->default(0);
            $table->unsignedInteger('best_reply_id')->nullable();
            $table->text('body');
            $table->timestamps();
            $table->boolean('locked')->default(false);

            $table->foreign('best_reply_id')
                ->references('id')->on('replies')
                ->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('threads');
    }
}
