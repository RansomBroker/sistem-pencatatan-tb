<?php

namespace App\Http\Middleware;

use App\Models\AdvanceReceive;
use App\Models\ExpiredLog;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;

class ExpiredCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */

    public function handle(Request $request, Closure $next)
    {
        // check apakah tgl hari ini ada di db
        $checkLog = ExpiredLog::where('expired_date_check', Carbon::now()->toDateString())->first();
        if ($checkLog == null) {
            $expiredAdvanceReceive = AdvanceReceive::where('expired_date', '<' , Carbon::now()->toDateString())
                ->where('status', 'AVAILABLE')
                ->get();

            foreach ($expiredAdvanceReceive as $expiredData) {
                $advanceReceive = AdvanceReceive::find($expiredData->id);
                $advanceReceive->status = 'EXPIRED';

                $qtyTotal = $advanceReceive->qty_total ?? 0;
                $idrTotal = $advanceReceive->idr_total ?? 0;

                $qtyExpired = $advanceReceive->qty - $qtyTotal;
                $idrExpired = $qtyExpired * $advanceReceive->unit_price;

                $qtyRefund = $advanceReceive->qty_refund ?? 0;
                $idrRefund = $qtyRefund * $advanceReceive->unit_price;

                // calculate
                $qtySumAll = $qtyTotal + $qtyExpired + $qtyRefund;
                $idrSumAll = $idrTotal + $idrExpired + $idrRefund;

                $qtyRemains = $advanceReceive->qty - $qtySumAll;
                $idrRemains = $advanceReceive->net_sale - $idrSumAll;

                // check selisih karna angka desimal dari outstanding
                if ($qtyRemains == 0) {
                    $idrSumAll += $idrRemains;
                    $idrExpired += $idrRemains;
                    // paling akhir
                    $idrRemains -= $idrRemains;
                }

                // update
                $advanceReceive->idr_total = $idrTotal;
                $advanceReceive->idr_expired = $idrExpired;
                $advanceReceive->idr_refund = $idrRefund;
                $advanceReceive->idr_sum_all = $idrSumAll;
                $advanceReceive->idr_remains = $idrRemains;
                $advanceReceive->qty_total = $qtyTotal;
                $advanceReceive->qty_expired = $qtyExpired;
                $advanceReceive->qty_refund = $qtyRefund;
                $advanceReceive->qty_sum_all = $qtySumAll;
                $advanceReceive->qty_remains = $qtyRemains;
                $advanceReceive->save();
            }

            // write new log
            $log = new ExpiredLog();
            $log->expired_date_check = Carbon::now()->toDateString();
            $log->save();
        }

        return $next($request);
    }
}
