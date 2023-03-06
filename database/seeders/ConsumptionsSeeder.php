<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConsumptionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*
         * NOTES: Make sure status are available and expired status active, and every insert it means equal +1 for qty_remains, and qty_remains > 0
         * 1. get advance receive ID
         * 2. insert into consumptions table
         * 3. update advance receive (qty_remains, idr_remains, qty_total, idr_total, qty_sum_all, idr_sum_all)
         * */

        $advanceReceiveID = 7;
        $branchID = 502;
        $consumptionDate = '2023-02-23';

        /* update advance receive*/
        $advanceReceive = DB::table('advance_receives')->find($advanceReceiveID);
        $consumption = DB::table('consumptions')->where('advance_receive_id', $advanceReceiveID)->first();

        if (($advanceReceive->qty_remains > 0 || $advanceReceive->qty_remains == null) &&  $advanceReceive->status == "AVAILABLE" && $advanceReceive->expired_status == "ACTIVE") {
            $qtyTotal = $advanceReceive->qty_total ?? 0;
            $qtyTotal += 1;

            $idrTotal = $qtyTotal * $advanceReceive->unit_price;

            // need get value
            $qtyExpired = $advanceReceive->qty_expired ?? 0;
            $idrExpired = $advanceReceive->idr_expired ?? 0;
            $qtyRefund = $advanceReceive->qty_refund ?? 0;
            $idrRefund = $advanceReceive->idr_refund ?? 0;

            // calculate
            $qtySumAll = $qtyTotal + $qtyExpired + $qtyRefund;
            $idrSumAll = $idrTotal + $idrExpired + $idrRefund;

            $qtyRemains = $advanceReceive->qty - $qtySumAll;

            $idrRemains = $advanceReceive->net_sale - $idrSumAll  ;

            /* 2. insert into consumptions table */
            DB::table('consumptions')->insert([
                'parent_id' => $consumption->id,
                'branch_id' => $branchID,
                'consumption_date' => $consumptionDate,
                'used_count' => $qtyTotal
            ]);

            /* 3. update advance receives */
            DB::table('advance_receives')
                ->where('id', $advanceReceiveID)
                ->update([
                    'qty_total' => $qtyTotal,
                    'idr_total' => $idrTotal,
                    'qty_sum_all' => $qtySumAll,
                    'idr_sum_all' => $idrSumAll,
                    'qty_remains' => $qtyRemains,
                    'idr_remains' => $idrRemains
                ]);
        }
    }
}
