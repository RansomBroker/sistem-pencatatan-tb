<?php

namespace App\Http\Controllers;

use App\Jobs\ConsumptionExportJob;
use App\Jobs\ExpiredExportJob;
use App\Models\AdvanceReceive;
use App\Models\Branch;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;

class ExpiredController extends Controller
{
    public function expired()
    {
        $branches = Branch::all();
        $data['branches'] = $branches;
        return view('expired/expired', $data);
    }

    public function expiredDataGET(Request $request)
    {
        $expiredDateRequest = $request['columns'][2]['search']['value'] == "" ? '1980-01-01||2999-01-01' : $request['columns'][2]['search']['value'];
        $expiredDateStart = explode("||", $expiredDateRequest)[0];
        $expiredDateEnd = explode("||", $expiredDateRequest)[1];

        $expiredData = AdvanceReceive::query()->with('customers', 'branches', 'products.categories');

        if ($request['columns'][3]['search']['value'] != '') {
            $expiredData->whereRelation('customers', 'customer_id', 'ILIKE', '%'.$request['columns'][3]['search']['value'].'%');
        }

        if ($request['columns'][4]['search']['value'] != '') {
            $expiredData->whereRelation('customers', 'name', 'ILIKE', '%'.$request['columns'][4]['search']['value'].'%');
        }

        if ($request['columns'][0]['search']['value'] != '') {
            $expiredData->whereRelation('branches', 'id', $request['columns'][0]['search']['value']);
        }

        $expiredData->whereBetween('expired_date', [$expiredDateStart, $expiredDateEnd])
            ->where('status', $request['columns'][10]['search']['value'] == "" ?  "EXPIRED" : $request['columns'][10]['search']['value']);

        $dataCount = $expiredData->get();

        $data = $expiredData->offset($request['start'])
            ->limit($request['length'])
            ->get();

        $reportCollection = collect($dataCount);

        $report = [
            'qty_expired' => $reportCollection->sum('qty_expired'),
            'idr_expired' => $reportCollection->sum('idr_expired'),
            'qty_remains' => $reportCollection->sum('qty_remains'),
            'idr_remains' => $reportCollection->sum('idr_remains')
        ];

        return response()->json([
            'draw' => intval($request['draw']),
            'recordsTotal' => intval($dataCount->count()),
            'recordsFiltered' => intval($dataCount->count()),
            'data' => $data,
            'report' => $report
        ]);
    }

    public function expiredDataGetAvailable(Request $request)
    {
        $expiredDateRequest = $request['columns'][2]['search']['value'] == "" ? '1980-01-01||2999-01-01' : $request['columns'][2]['search']['value'];
        $expiredDateStart = explode("||", $expiredDateRequest)[0];
        $expiredDateEnd = explode("||", $expiredDateRequest)[1];


        $expiredData = AdvanceReceive::query()->with('customers', 'branches', 'products.categories');

        if ($request['columns'][3]['search']['value'] != '') {
            $expiredData->whereRelation('customers', 'customer_id', 'ILIKE', '%'.$request['columns'][3]['search']['value'].'%');
        }

        if ($request['columns'][4]['search']['value'] != '') {
            $expiredData->whereRelation('customers', 'name', 'ILIKE', '%'.$request['columns'][4]['search']['value'].'%');
        }

        if ($request['columns'][0]['search']['value'] != '') {
            $expiredData->whereRelation('branches', 'id', $request['columns'][0]['search']['value']);
        }

        $expiredData->whereBetween('expired_date', [$expiredDateStart, $expiredDateEnd])
            ->where('status', 'AVAILABLE');

        $dataCount = $expiredData->get();

        $data = $expiredData->where('status', 'AVAILABLE')
            ->offset($request['start'])
            ->limit($request['length'])
            ->get();

        return response()->json([
            'draw' => intval($request['draw']),
            'recordsTotal' => intval(count($dataCount)),
            'recordsFiltered' => intval(count($dataCount)),
            'data' => $data,
        ]);
    }

    public function expiredAddView()
    {
        $branches = Branch::all();
        $data['branches'] = $branches;
        return view('expired/expired_add', $data);
    }

    public function expiredAdd($id)
    {
        /* write to db */
        $advanceReceive = AdvanceReceive::find($id);
        $advanceReceive->status = 'EXPIRED';
        $advanceReceive->expired_date = Carbon::now()->subDays(1)->toDateString();

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

        return response()->json([
           'status' => 'success',
           'message' => 'Berhasil mengexpiredkan data'
        ]);
    }

    public function expiredExportExcel(Request $request)
    {
        try {
            $name = Carbon::now()->timestamp;
            $batch = Bus::batch([
                new ExpiredExportJob($request->all(), $name)
            ])->dispatch();

            // flush all failed job if exist
            Artisan::call("queue:flush");
            Artisan::call("queue:work --stop-when-empty ");

            return response()->json([
                'status' => 'success',
                'name' => $name,
                'batchID' => $batch->id
            ]);

        }catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'name' => $name,
                'batchID' => ''
            ]);
        }
    }

    public function exportCheckStatus($id, $name)
    {
        $exportBatchStatusCanceled = Bus::findBatch($id)->canceled();
        $exportBatchStatusFinished = Bus::findBatch($id)->finished();

        if($exportBatchStatusFinished == 1 && $exportBatchStatusCanceled ==  1) {
            return response()->json([
                'status' => 'failed',
                'exportStatus' => $exportBatchStatusFinished,
                'exportURL' => null
            ]);
        }

        return response()->json([
            'status' => 'success',
            'exportStatus' => $exportBatchStatusFinished,
            'exportURL' => \url('expired/expired-export/download/'.$name)
        ]);
    }

    public function exportDownload($name)
    {
        return Storage::download('public/expired_report_'. $name .'.xlsx');
    }
}
