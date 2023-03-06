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
        Schema::table('consumptions', function (Blueprint $table) {
            $table
                ->foreign('advance_receive_id')
                ->references('id')
                ->on('advance_receives')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table
                ->foreign('branch_id')
                ->references('id')
                ->on('branches')
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
        Schema::table('consumptions', function(Blueprint $table) {
            $table->dropForeign('consumptions_advance_receive_id_foreign');
            $table->dropForeign('consumptions_branch_id_foreign');
        });

        Schema::disableForeignKeyConstraints();
    }
};
