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

    public function expiredDataGET()
    {
        $expiredDateRequest = $_GET['columns'][2]['search']['value'] == "" ? '1980-01-01||2999-01-01' : $_GET['columns'][2]['search']['value'];
        $expiredDateStart = explode("||", $expiredDateRequest)[0];
        $expiredDateEnd = explode("||", $expiredDateRequest)[1];

        $dataCount = AdvanceReceive::with('customers', 'branches', 'products.categories')
            ->whereRelation('customers', 'customer_id', 'ILIKE', '%'.$_GET['columns'][3]['search']['value'].'%')
            ->whereRelation('customers', 'name', 'ILIKE', '%'.$_GET['columns'][4]['search']['value'].'%')
            ->whereRelation('branches', function (Builder $query) {
                if ($_GET['columns'][0]['search']['value'] != ""){
                    $query->where('id', $_GET['columns'][0]['search']['value']);
                }
            })
            ->whereBetween('expired_date', [$expiredDateStart, $expiredDateEnd])
            ->where('status', $_GET['columns'][10]['search']['value'] == "" ?  "EXPIRED" : $_GET['columns'][10]['search']['value'])
            ->get();

        $data = AdvanceReceive::with('customers', 'branches', 'products.categories')
            ->whereRelation('customers', 'customer_id', 'ILIKE', '%'.$_GET['columns'][3]['search']['value'].'%')
            ->whereRelation('customers', 'name', 'ILIKE', '%'.$_GET['columns'][4]['search']['value'].'%')
            ->whereRelation('branches', function (Builder $query) {
                if ($_GET['columns'][0]['search']['value'] != ""){
                    $query->where('id', $_GET['columns'][0]['search']['value']);
                }
            })
            ->whereBetween('expired_date', [$expiredDateStart, $expiredDateEnd])
            ->where('status', $_GET['columns'][10]['search']['value'] == "" ?  "EXPIRED" : $_GET['columns'][10]['search']['value'])
            ->offset($_GET['start'])
            ->limit($_GET['length'])
            ->get();

        $report = [
            'qty_expired' => $data->sum('qty_expired'),
            'idr_expired' => $data->sum('idr_expired'),
            'qty_remains' => $data->sum('qty_remains'),
            'idr_remains' => $data->sum('idr_remains')
        ];

        return response()->json([
            'draw' => intval($_GET['draw']),
            'recordsTotal' => intval($dataCount->count()),
            'recordsFiltered' => intval($dataCount->count()),
            'data' => $data,
            'report' => $report
        ]);
    }

    public function expiredDataGetAvailable()
    {
        $expiredDateRequest = $_GET['columns'][2]['search']['value'] == "" ? '1980-01-01||2999-01-01' : $_GET['columns'][2]['search']['value'];
        $expiredDateStart = explode("||", $expiredDateRequest)[0];
        $expiredDateEnd = explode("||", $expiredDateRequest)[1];

        $dataCount = AdvanceReceive::with('customers', 'branches', 'products.categories')
            ->whereRelation('customers', 'customer_id', 'ILIKE', '%'.$_GET['columns'][3]['search']['value'].'%')
            ->whereRelation('customers', 'name', 'ILIKE', '%'.$_GET['columns'][4]['search']['value'].'%')
            ->whereRelation('branches', function (Builder $query) {
                if ($_GET['columns'][0]['search']['value'] != ""){
                    $query->where('id', $_GET['columns'][0]['search']['value']);
                }
            })
            ->whereBetween('expired_date', [$expiredDateStart, $expiredDateEnd])
            ->where('status', 'AVAILABLE')
            ->get();

        $data = AdvanceReceive::with('customers', 'branches', 'products.categories')
            ->whereRelation('customers', 'customer_id', 'ILIKE', '%'.$_GET['columns'][3]['search']['value'].'%')
            ->whereRelation('customers', 'name', 'ILIKE', '%'.$_GET['columns'][4]['search']['value'].'%')
            ->whereRelation('branches', function (Builder $query) {
                if ($_GET['columns'][0]['search']['value'] != ""){
                    $query->where('id', $_GET['columns'][0]['search']['value']);
                }
            })
            ->whereBetween('expired_date', [$expiredDateStart, $expiredDateEnd])
            ->where('status', 'AVAILABLE')
            ->offset($_GET['start'])
            ->limit($_GET['length'])
            ->get();

        $report = [
            'qty_expired' => $dataCount->sum('qty_expired'),
            'idr_expired' => $dataCount->sum('idr_expired'),
            'qty_remains' => $dataCount->sum('qty_remains'),
            'idr_remains' => $dataCount->sum('idr_remains')
        ];

        return response()->json([
            'draw' => intval($_GET['draw']),
            'recordsTotal' => intval(count($dataCount)),
            'recordsFiltered' => intval(count($dataCount)),
            'data' => $data,
            'report' => $report
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
            $batch = Bus::batch([
                new ExpiredExportJob($request->all())
            ])->dispatch();

            // flush all failed job if exist
            Artisan::call("queue:flush");
            Artisan::call("queue:work --stop-when-empty ");

            return response()->json([
                'status' => 'success',
                'batchID' => $batch->id
            ]);

        }catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'batchID' => ''
            ]);
        }
    }

    public function exportCheckStatus($id)
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
            'exportURL' => \url('refund/refund-export/download')
        ]);
    }

    public function exportDownload()
    {
        return Storage::download('public/expired_report.xlsx');
    }
}
