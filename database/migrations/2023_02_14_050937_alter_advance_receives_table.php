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
                ->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table
                ->foreign('customer_id')
                ->references('id')
                ->on('customers')
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
        // revert all to nothing
        Schema::table('advance_receives', function(Blueprint $table) {
            $table->dropForeign('advance_receives_customer_id_foreign');
            $table->dropForeign('advance_receives_product_id_foreign');
            $table->dropForeign('advance_receives_branch_id_foreign');
        });

        Schema::disableForeignKeyConstraints();
    }
};
