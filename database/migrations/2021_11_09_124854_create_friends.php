<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFriends extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('friends', function (Blueprint $table) {
            $table->id("fid");
            $table->bigInteger("user_id1")->unsigned();
            $table->bigInteger("user_id2")->unsigned();
            $table->timestamps();
            $table->foreign('user_id1')->references('uid')->on('users');
            $table->foreign('user_id2')->references('uid')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('friends');
    }
}
