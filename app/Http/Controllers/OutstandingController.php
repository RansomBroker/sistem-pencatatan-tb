<?php

namespace App\Http\Controllers;

use App\Jobs\OutstandingExportJob;
use App\Jobs\RefundExportJob;
use App\Models\AdvanceReceive;
use App\Models\Branch;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;

class OutstandingController extends Controller
{
    public function outstanding()
    {
        $branches = Branch::all();
        $data['branches'] = $branches;
        return view('outstanding/outstanding', $data);
    }

    public function outstandingDataGET(Request $request)
    {
        $outstandingDateRequest = $request['columns'][2]['search']['value'] == "" ? '1980-01-01||2999-01-01' : $request['columns'][2]['search']['value'];
        $outstandingDateStart = explode("||", $outstandingDateRequest)[0];
        $outstandingDateEnd = explode("||", $outstandingDateRequest)[1];

        $outstandingData = AdvanceReceive::query()->with('customers', 'branches', 'products.categories' );

        if ($request['columns'][3]['search']['value'] != '') {
            $outstandingData->whereRelation('customers', 'customer_id', 'ILIKE', '%'.$request['columns'][3]['search']['value'].'%');
        }

        if ($request['columns'][4]['search']['value'] != '') {
            $outstandingData->whereRelation('customers', 'name', 'ILIKE', '%'.$request['columns'][4]['search']['value'].'%');
        }

        if ($request['columns'][0]['search']['value'] != '') {
            $outstandingData->whereRelation('branches', 'id', $request['columns'][0]['search']['value']);
        }

        $outstandingData->whereBetween('buy_date', [$outstandingDateStart, $outstandingDateEnd]);

        $dataCount = $outstandingData->get();

        $data = $outstandingData->offset($request['start'])
            ->limit($request['length'])
            ->get();

        $report = [
            'qty_remains' => $dataCount->sum('qty_remains'),
            'idr_remains' => $dataCount->sum('idr_remains')
        ];

        return response()->json([
            'draw' => intval($request['draw']),
            'recordsTotal' => intval(count($dataCount)),
            'recordsFiltered' => intval(count($dataCount)),
            'data' => $data,
            'report' => $report
        ]);
    }

    public function outstandingExportExcel(Request $request)
    {
        try {
            $name = Carbon::now()->timestamp;
            $batch = Bus::batch([
                new  OutstandingExportJob($request->all(), $name)
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
            'exportURL' => \url('outstanding/outstanding-export/download/'.$name)
        ]);
    }

    public function exportDownload($name)
    {
        return Storage::download('public/outstanding_report_'.$name.'.xlsx');
    }

}
