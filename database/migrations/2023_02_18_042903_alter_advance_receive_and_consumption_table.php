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
        Schema::table('advance_receives', function (Blueprint $table) {
            $table
                ->foreign('consumption_id')
                ->references('id')
                ->on('consumptions')
                ->onUpdate('set null')
                ->onDelete('set null');
        });

        Schema::table('consumptions', function (Blueprint $table) {
            $table
                ->foreign('parent_id')
                ->references('id')
                ->on('consumptions')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
