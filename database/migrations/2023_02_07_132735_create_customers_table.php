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
            $table->string('customer_id', 10)->unique();
            $table->string('name', 40);
            $table->string('nickname', 20)->nullable()->default('');
            $table->string('phone', 40)->nullable()->default('');
            $table->string('identity_number', 40)->nullable()->default('');
            $table->date('birth_date')->nullable();
            $table->string('address', 40)->nullable()->default('');
            $table->string('email')->nullable()->default('');
            $table->string('payment_number', 25)->nullable()->default('');
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
