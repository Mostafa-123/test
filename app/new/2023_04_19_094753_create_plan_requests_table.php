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
        Schema::create('plan_requests', function (Blueprint $table) {
            $table->id();
            $table->integer('planner_id');
            $table->string('planner_name');
            $table->integer('user_id');
            $table->string('user_name');
            $table->integer('plan_id');
            $table->string('plan_name');
            $table->integer('price');
            $table->enum('status', ['unconfirmed', 'confirmed', 'cancelled'])->default('unconfirmed');
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
        Schema::dropIfExists('plan_requests');
    }
};
