<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new  class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('advance_receives', function (Blueprint $table) {
            $table->decimal('buy_price', 20, 9)->nullable()->change();
            $table->decimal('net_sale', 20, 9)->nullable()->change();
            $table->decimal('tax', 20, 9)->nullable()->change();
            $table->decimal('unit_price', 20, 9)->nullable()->change();
            $table->decimal('idr_total', 20, 9)->nullable()->change();
            $table->decimal('idr_expired', 20, 9)->nullable()->change();
            $table->decimal('idr_refund', 20, 9)->nullable()->change();
            $table->decimal('idr_sum_all', 20, 9)->nullable()->change();
            $table->decimal('idr_remains', 20, 9)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('advance_receives', function (Blueprint $table) {
            $table->decimal('buy_price', 16, 4)->nullable()->change();
            $table->decimal('net_sale', 16, 4)->nullable()->change();
            $table->decimal('tax', 16, 4)->nullable()->change();
            $table->decimal('unit_price', 16, 4)->nullable()->change();
            $table->decimal('idr_total', 16, 4)->nullable()->change();
            $table->decimal('idr_expired', 16, 4)->nullable()->change();
            $table->decimal('idr_refund', 16, 4)->nullable()->change();
            $table->decimal('idr_sum_all', 16, 4)->nullable()->change();
            $table->decimal('idr_remains', 16, 4)->nullable()->change();
        });
    }
};
