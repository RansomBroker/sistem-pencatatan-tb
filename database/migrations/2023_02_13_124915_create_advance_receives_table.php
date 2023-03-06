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
        Schema::create('advance_receives', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('product_id')->unsigned()->index();
            $table->bigInteger('customer_id')->unsigned()->index();
            $table->bigInteger('branch_id')->unsigned()->index();
            $table->string('type');
            $table->decimal('buy_price', 16, 2);
            $table->decimal('net_sale', 16, 2);
            $table->decimal('tax', 16, 2);
            $table->decimal('unit_price', 16, 2);
            $table->string('payment');
            $table->integer('qty');
            $table->date('buy_date');
            $table->date('expired_date');
            $table->integer('qty_total')->nullable()->comment("Total Konsumsi");
            $table->decimal('idr_total', 16, 2)->nullable()->comment("Total Rupiah");
            $table->integer('qty_expired')->nullable()->comment("Total Konsumsi");
            $table->decimal('idr_expired', 16, 2)->nullable()->comment("Total Rupiah");
            $table->integer('qty_refund')->nullable()->comment("Total Konsumsi");
            $table->decimal('idr_refund', 16, 2)->nullable()->comment("Total Rupiah");
            $table->integer('qty_sum_all')->nullable()->comment("Konsumsi + Expired + refund");
            $table->decimal('idr_sum_all', 16, 2)->nullable()->comment("Konsumsi + Expired + refund");
            $table->integer('qty_remains')->nullable()->comment("Sisa/belum tergunakan");
            $table->decimal('idr_remains', 16, 2)->nullable()->comment("Sisa Belum digunakan");
            $table->string('status')->nullable();
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
        Schema::dropIfExists('advance_receives');
    }
};
