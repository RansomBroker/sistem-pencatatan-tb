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
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable()->default('');
            $table->string('branch')->nullable()->default('');
            $table->string('address')->nullable()->default('');
            $table->string('telephone')->nullable()->default('');
            $table->string('phone')->nullable()->default('');
            $table->string('npwp')->nullable()->default('');
            $table->string('company')->nullable()->default('');
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
        Schema::dropIfExists('branches');
    }
};
