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
        Schema::create('consumptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('advance_receive_id')
                ->nullable()
                ->references('id')
                ->on('advance_receives')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignId('branch_id')
                ->nullable()
                ->references('id')
                ->on('branches')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('parent_id')
                ->nullable()
                ->references('id')
                ->on('consumptions')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->date('consumption_date')->nullable();
            $table->string('status')->nullable();
            $table->integer('used_count')->nullable();
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
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('consumptions');
    }
};
