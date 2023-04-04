<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdvanceReceiveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 0; $i < 500000; $i++) {
            $advanceReceiveID = DB::table('advance_receives')->insertGetId([
                'product_id' => 3,
                'customer_id' => 2,
                'branch_id' => 2,
                'type' => "REPEATER",
                'buy_price' => 500000,
                'net_sale' => 4504505,
                'tax' => 495495,
                'unit_price' => 450450,
                'payment' => 'QRIS',
                'qty' => 10,
                'buy_date' => '2023-03-29',
                'expired_date'=> '2025-03-29',
                'status' => 'AVAILABLE'
            ]);

            DB::table('consumptions')->insert([
                'advance_receive_id' => $advanceReceiveID,
            ]);
        }
        /*DB::table('advance_receives')->insert([
            'product_id' => 21,
            'customer_id' => 1,
            'branch_id' => 501,
            'type' => "REPEATER",
            'buy_price' => 500000,
            'net_sale' => 450450,
            'tax' => 49550,
            'unit_price' => 90090,
            'payment' => 'Cash',
            'qty' => 5,
            'buy_date' => '2023-02-15',
            'expired_date'=> '2025-02-15',
            'status' => "AVAILABLE"
        ]);*/
    }
}
