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
            $table->foreignId('product_id')
                ->references('id')
                ->on('products')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('customer_id')
                ->references('id')
                ->on('customers')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('branch_id')
                ->references('id')
                ->on('branches')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->string('type')->nullable();
            $table->decimal('buy_price', 16, 2)->nullable();
            $table->decimal('net_sale', 16, 2)->nullable();
            $table->decimal('tax', 16, 2)->nullable();
            $table->decimal('unit_price', 16, 2)->nullable();
            $table->string('payment')->nullable();
            $table->integer('qty')->nullable();
            $table->date('buy_date')->nullable();
            $table->date('expired_date')->nullable();
            $table->integer('qty_total')->nullable();
            $table->decimal('idr_total', 16, 2)->nullable();
            $table->integer('qty_expired')->nullable();
            $table->decimal('idr_expired', 16, 2)->nullable();
            $table->integer('qty_refund')->nullable();
            $table->decimal('idr_refund', 16, 2)->nullable();
            $table->integer('qty_sum_all')->nullable();
            $table->decimal('idr_sum_all', 16, 2)->nullable();
            $table->integer('qty_remains')->nullable();
            $table->decimal('idr_remains', 16, 2)->nullable();
            $table->string('status')->nullable();
            $table->string('notes')->nullable();
            $table->string('memo')->nullable();
            $table->foreignId('refund_branch_id')
                ->nullable()
                ->references('id')
                ->on('branches')
                ->onUpdate('cascade')
                ->onDelete('restrict');
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
        Schema::dropIfExists('advance_receives');
    }
};
