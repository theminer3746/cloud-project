<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGatewaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gateways', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('activation_key', 50)->unique();
            $table->string('arn', 120)->unique();
            $table->string('name', 120);
            $table->string('real_name', 120)->unique();
            $table->string('region', 30)->nullable();
            $table->string('timezone', 40)->nullable();
            $table->string('type', 20);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gateways');
    }
}
