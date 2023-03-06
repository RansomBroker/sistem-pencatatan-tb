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
            $table->bigInteger('consumption_id')->unsigned()->index()->nullable();
        });

        Schema::table('consumptions', function (Blueprint $table) {
            $table->bigInteger('parent_id')->unsigned()->index()->nullable();
            $table->date('consumption_date')->nullable()->change();
            $table->bigInteger('branch_id')->nullable()->change();
            $table->bigInteger('advance_receive_id')->nullable()->change();
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
        Schema::table('advance_receives', function (Blueprint $table) {
            $table->dropColumn('consumption_id');
        });
        Schema::table('consumptions', function (Blueprint $table) {
            $table->dropColumn('parent_id');
        });
    }
};
