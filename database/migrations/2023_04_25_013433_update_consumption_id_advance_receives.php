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
           $table->foreignId('consumption_id')
               ->nullable()
               ->references('id')
               ->on('consumptions')
               ->onUpdate('set null')
               ->onDelete('set null');
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
        Schema::table('advance_receives', function (Blueprint $table) {
            $table->dropColumn('consumption_id');
        });
    }
};
