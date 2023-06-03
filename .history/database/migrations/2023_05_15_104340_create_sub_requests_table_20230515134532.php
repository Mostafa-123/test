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
        Schema::create('sub_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supplier_id');
            $table->string('supplier_name');
            $table->unsignedBigInteger('user_id');
            $table->string('user_name');
            $table->string('role');
            $table->unsignedBigInteger('sub_id');
            $table->string('sub_name');
            $table->decimal('price', 8, 2); // Assuming a decimal field for price
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->string('status');
            $table->timestamps();

            // Define foreign key constraints if needed
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sub_requests');
    }
};
