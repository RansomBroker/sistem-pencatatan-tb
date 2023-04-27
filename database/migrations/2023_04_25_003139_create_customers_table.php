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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_id');
            $table->string('name');
            $table->string('nickname')->nullable()->default('');
            $table->string('phone')->nullable()->default('');
            $table->string('identity_number')->nullable()->default('');
            $table->date('birth_date')->nullable();
            $table->string('address')->nullable()->default('');
            $table->string('email')->nullable()->default('');
            $table->string('payment_number')->nullable()->default('');
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
        Schema::dropIfExists('customers');
    }
};
