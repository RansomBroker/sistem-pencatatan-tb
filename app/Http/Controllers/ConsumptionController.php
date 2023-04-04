<?php

namespace App\Http\Controllers;

use App\Jobs\AdvanceReceiveExportJob;
use App\Jobs\ConsumptionExportJob;
use App\Models\AdvanceReceive;
use App\Models\Branch;
use App\Models\Consumption;
use App\Models\Customer;
use Dotenv\Repository\AdapterRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\Builder;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\StyleMerger;

use function GuzzleHttp\Promise\all;

class ConsumptionController extends Controller
{
    public function consumption()
    {
        $branches = Branch::all();
        $data['branches'] = $branches;
        return view('consumptions/consumption', $data);
    }

    public function consumptionDataGET(Request $request)
    {
        $consumptionDateRequest = isset($request['columns'][2]['search']['value']) && $request['columns'][2]['search']['value'] != ""  ? $request['columns'][2]['search']['value']: '1980-01-01||2999-12-31';

        $idFilter = $request['columns'][4]['search']['value'] ?? '';
        $nameFilter = $request['columns'][5]['search']['value'] ?? '';
        $branchFilter = $request['columns'][1]['search']['value'] ?? '';

        // generate data
        $columnsRecord = AdvanceReceive::orderBy('buy_date', 'asc')
            ->with('customers', 'branches', 'products.categories', 'consumptions.history.branches', 'refund_branches')
            ->whereRelation('customers', 'customer_id', 'ILIKE', '%'.$idFilter.'%')
            ->whereRelation('customers', 'name', 'ILIKE', '%'.$nameFilter.'%')
            ->whereRelation('consumptions', function (Builder $query)  use($branchFilter, $consumptionDateRequest) {
                if ($branchFilter != "" || $consumptionDateRequest != "1980-01-01||2999-12-31") {
                    $query->whereRelation('history', function (Builder $q) use($branchFilter, $consumptionDateRequest){
                        if ($branchFilter !=  "") {
                            $q
                                ->where( 'branch_id', $branchFilter)
                                ->whereBetween('consumption_date', [explode('||', $consumptionDateRequest)[0], explode('||', $consumptionDateRequest)[1]]);
                        } else {
                            $q->whereBetween('consumption_date', [explode('||', $consumptionDateRequest)[0], explode('||', $consumptionDateRequest)[1]]);
                        }
                    });
                }
            })
            ->get();


        $data = AdvanceReceive::orderBy('buy_date', 'asc')
            ->with('customers', 'branches', 'products.categories', 'consumptions.history.branches', 'refund_branches')
            ->whereRelation('customers', 'customer_id', 'ILIKE', '%'.$idFilter.'%')
            ->whereRelation('customers', 'name', 'ILIKE', '%'.$nameFilter.'%')
            ->whereRelation('consumptions', function (Builder $query)  use($branchFilter, $consumptionDateRequest) {
                if ($branchFilter != "" || $consumptionDateRequest != "1980-01-01||2999-12-31") {
                    $query->whereRelation('history', function (Builder $q) use($branchFilter, $consumptionDateRequest){
                        if ($branchFilter !=  "") {
                            $q
                                ->where( 'branch_id', $branchFilter)
                                ->whereBetween('consumption_date', [explode('||', $consumptionDateRequest)[0], explode('||', $consumptionDateRequest)[1]]);
                        } else {
                            $q->whereBetween('consumption_date', [explode('||', $consumptionDateRequest)[0], explode('||', $consumptionDateRequest)[1]]);
                        }
                    });
                }
            })
            ->offset($request['start'] ?? 0)
            ->limit($request['length'] ?? 10)
            ->get();

        $getBranchFilter = AdvanceReceive::orderBy('buy_date', 'asc')
                ->with(['customers', 'branches', 'products.categories', 'consumptions.history' => function ($query) use ($branchFilter, $consumptionDateRequest){
                    if ($branchFilter != "") {
                        $query
                            ->where( 'branch_id', $branchFilter)
                            ->whereBetween('consumption_date', [explode('||', $consumptionDateRequest)[0], explode('||', $consumptionDateRequest)[1]]);
                    } else {
                        $query->whereBetween('consumption_date', [explode('||', $consumptionDateRequest)[0], explode('||', $consumptionDateRequest)[1]]);
                    }
                }])
                ->whereRelation('consumptions.history', function (Builder $query) use($branchFilter, $consumptionDateRequest){
                    if ($branchFilter != "") {
                        $query
                            ->where( 'branch_id', $branchFilter)
                            ->whereBetween('consumption_date', [explode('||', $consumptionDateRequest)[0], explode('||', $consumptionDateRequest)[1]]);
                    } else {
                        $query->whereBetween('consumption_date', [explode('||', $consumptionDateRequest)[0], explode('||', $consumptionDateRequest)[1]]);
                    }
                })
                ->whereRelation('customers', 'customer_id', 'ILIKE', '%'.$idFilter.'%')
                ->whereRelation('customers', 'name', 'ILIKE', '%'.$nameFilter.'%')
                ->get();

        $recordsTotal = count($columnsRecord);

        $userDataConsumption = [];
        foreach ($data as $key => $advanceReceive) {
            $userDataConsumption[] = [
                'memo'=> $advanceReceive->memo,
                'action' => $advanceReceive->id,
                'id' => $advanceReceive->id,
                'branch' => $advanceReceive->branches[0]->name,
                'buy_date' => $advanceReceive->buy_date,
                'expired_date' => $advanceReceive->expired_date,
                'customer_id' => $advanceReceive->customers[0]->customer_id,
                'customer_name'=> $advanceReceive->customers[0]->name,
                'type' => ucfirst(strtolower($advanceReceive->type)),
                'buy_price' => $this->formatNumberPrice($advanceReceive->buy_price),
                'net_sales' => $this->formatNumberPrice($advanceReceive->net_sale),
                'tax' => $this->formatNumberPrice($advanceReceive->tax),
                'payment' => $advanceReceive->payment,
                'qty' => $advanceReceive->qty,
                'unit_price'=> $this->formatNumberPrice($advanceReceive->unit_price),
                'product' => $advanceReceive->products[0]->name,
                'category' => $advanceReceive->products[0]->categories[0]->name,
                'notes' => $advanceReceive->notes == "" ? '-' : $advanceReceive->notes,
                'qty_total' => $advanceReceive->qty_total ?? '-',
                'idr_total' => $this->formatNumberPrice($advanceReceive->idr_total) ?? '-',
                'qty_expired' => $advanceReceive->qty_expired ?? "-",
                'idr_expired' => $this->formatNumberPrice($advanceReceive->idr_expired) ?? "-",
                'qty_refund' => $advanceReceive->qty_refund ?? "-",
                'idr_refund' => $this->formatNumberPrice($advanceReceive->idr_refund) ?? "-",
                'qty_sum_all' => $advanceReceive->qty_sum_all ?? "-",
                'idr_sum_all' => $this->formatNumberPrice($advanceReceive->idr_sum_all) ?? "-",
                'qty_remains' => $advanceReceive->qty_remains ?? "-",
                'idr_remains' => $this->formatNumberPrice($advanceReceive->idr_remains) ?? "-",
                'status' => $advanceReceive->status,
                'refund_branch' => count($advanceReceive->refund_branches) > 0 ? $advanceReceive->refund_branches[0]->name : '',
            ];

            for ($i = 0; $i < 12 ; $i++) {
                $usedCount = $advanceReceive->consumptions[0]->history->count();
                if ($i < $usedCount) {
                    $history = $advanceReceive->consumptions[0]->history[$i];
                    $userDataConsumption[$key]['consumption-date-'.$history->used_count] = $history->consumption_date;
                    $userDataConsumption[$key]['consumption-branch-'.$history->used_count] = $history->branches[0]->name;
                }else {
                    if (($userDataConsumption[$key]['status'] == "EXPIRED") &&  ($userDataConsumption[$key]['qty'] > $i )) {
                        $consumptionDate = 'EXPIRED';
                        $consumptionBranch = $userDataConsumption[$key]['branch'];
                    }else if (($userDataConsumption[$key]['status'] == "REFUND") &&  ($userDataConsumption[$key]['qty'] > $i )) {
                        $consumptionDate = 'REFUND';
                        $consumptionBranch = $userDataConsumption[$key]['refund_branch'];
                    } else {
                        $consumptionDate = '-';
                        $consumptionBranch = '-';
                    }
                    $userDataConsumption[$key]['consumption-date-'.$i +1] = $consumptionDate;
                    $userDataConsumption[$key]['consumption-branch-'.$i +1] = $consumptionBranch;
                }
            }
        }

        // idr
        $totalBranchFilterConsumption = 0;
        $idrBranchFilterConsumption = 0;
        foreach ($getBranchFilter as $branch) {
            $totalBranchFilterConsumption += count($branch->consumptions[0]->history);
            $idrBranchFilterConsumption += count($branch->consumptions[0]->history) * $branch->unit_price;
        }

        // generate report
        $report = collect($columnsRecord);
        $reportData[] = [
            'qtyTotal' => $this->formatNumberPrice($totalBranchFilterConsumption),
            'idrTotal' => $this->formatNumberPrice($idrBranchFilterConsumption),
            'qtyRemains' => $this->formatNumberPrice($report->sum('qty_remains')),
            'idrRemains' => $this->formatNumberPrice($report->sum('idr_remains')),
        ];

        return response()->json([
            'draw' => intval($request['draw'] ?? 0),
            'recordsTotal' => intval($recordsTotal),
            'recordsFiltered' => intval($recordsTotal),
            'data' => $userDataConsumption,
            'report' => $reportData
        ]);
    }

    public function consumptionGetAvailableData(Request $request)
    {
        $consumptionDateRequest = isset($request['columns'][2]['search']['value']) && $request['columns'][2]['search']['value'] != ""  ? $request['columns'][2]['search']['value']: '1980-01-01||2999-12-31';

        $idFilter = $request['columns'][4]['search']['value'] ?? '';
        $nameFilter = $request['columns'][5]['search']['value'] ?? '';
        $branchFilter = $request['columns'][1]['search']['value'] ?? '';

        // generate data
        $columnsRecord = AdvanceReceive::orderBy('buy_date', 'asc')
            ->with('customers', 'branches', 'products.categories', 'consumptions.history.branches', 'refund_branches')
            ->whereRelation('customers', 'customer_id', 'ILIKE', '%'.$idFilter.'%')
            ->whereRelation('customers', 'name', 'ILIKE', '%'.$nameFilter.'%')
            ->whereRelation('consumptions', function (Builder $query)  use($branchFilter, $consumptionDateRequest) {
                if ($branchFilter != "" || $consumptionDateRequest != "1980-01-01||2999-12-31") {
                    $query->whereRelation('history', function (Builder $q) use($branchFilter, $consumptionDateRequest){
                        if ($branchFilter !=  "") {
                            $q
                                ->where( 'branch_id', $branchFilter)
                                ->whereBetween('consumption_date', [explode('||', $consumptionDateRequest)[0], explode('||', $consumptionDateRequest)[1]]);
                        } else {
                            $q->whereBetween('consumption_date', [explode('||', $consumptionDateRequest)[0], explode('||', $consumptionDateRequest)[1]]);
                        }
                    });
                }
            })
            ->where('status', 'AVAILABLE')
            ->get();


        $data = AdvanceReceive::orderBy('buy_date', 'asc')
            ->with('customers', 'branches', 'products.categories', 'consumptions.history.branches', 'refund_branches')
            ->whereRelation('customers', 'customer_id', 'ILIKE', '%'.$idFilter.'%')
            ->whereRelation('customers', 'name', 'ILIKE', '%'.$nameFilter.'%')
            ->whereRelation('consumptions', function (Builder $query)  use($branchFilter, $consumptionDateRequest) {
                if ($branchFilter != "" || $consumptionDateRequest != "1980-01-01||2999-12-31") {
                    $query->whereRelation('history', function (Builder $q) use($branchFilter, $consumptionDateRequest){
                        if ($branchFilter !=  "") {
                            $q
                                ->where( 'branch_id', $branchFilter)
                                ->whereBetween('consumption_date', [explode('||', $consumptionDateRequest)[0], explode('||', $consumptionDateRequest)[1]]);
                        } else {
                            $q->whereBetween('consumption_date', [explode('||', $consumptionDateRequest)[0], explode('||', $consumptionDateRequest)[1]]);
                        }
                    });
                }
            })
            ->offset($request['start'] ?? 0)
            ->limit($request['length'] ?? 10)
            ->where('status', 'AVAILABLE')
            ->get();

        $getBranchFilter = AdvanceReceive::orderBy('buy_date', 'asc')
            ->with(['customers', 'branches', 'products.categories', 'consumptions.history' => function ($query) use ($branchFilter, $consumptionDateRequest){
                if ($branchFilter != "") {
                    $query
                        ->where( 'branch_id', $branchFilter)
                        ->whereBetween('consumption_date', [explode('||', $consumptionDateRequest)[0], explode('||', $consumptionDateRequest)[1]]);
                } else {
                    $query->whereBetween('consumption_date', [explode('||', $consumptionDateRequest)[0], explode('||', $consumptionDateRequest)[1]]);
                }
            }])
            ->whereRelation('consumptions.history', function (Builder $query) use($branchFilter, $consumptionDateRequest){
                if ($branchFilter != "") {
                    $query
                        ->where( 'branch_id', $branchFilter)
                        ->whereBetween('consumption_date', [explode('||', $consumptionDateRequest)[0], explode('||', $consumptionDateRequest)[1]]);
                } else {
                    $query->whereBetween('consumption_date', [explode('||', $consumptionDateRequest)[0], explode('||', $consumptionDateRequest)[1]]);
                }
            })
            ->whereRelation('customers', 'customer_id', 'ILIKE', '%'.$idFilter.'%')
            ->whereRelation('customers', 'name', 'ILIKE', '%'.$nameFilter.'%')
            ->where('status', 'AVAILABLE')
            ->get();

        $recordsTotal = count($columnsRecord);

        $userDataConsumption = [];
        foreach ($data as $key => $advanceReceive) {
            $userDataConsumption[] = [
                'memo'=> $advanceReceive->memo,
                'action' => $advanceReceive->id,
                'id' => $advanceReceive->id,
                'branch' => $advanceReceive->branches[0]->name,
                'buy_date' => $advanceReceive->buy_date,
                'expired_date' => $advanceReceive->expired_date,
                'customer_id' => $advanceReceive->customers[0]->customer_id,
                'customer_name'=> $advanceReceive->customers[0]->name,
                'type' => ucfirst(strtolower($advanceReceive->type)),
                'buy_price' => $this->formatNumberPrice($advanceReceive->buy_price),
                'net_sales' => $this->formatNumberPrice($advanceReceive->net_sale),
                'tax' => $this->formatNumberPrice($advanceReceive->tax),
                'payment' => $advanceReceive->payment,
                'qty' => $advanceReceive->qty,
                'unit_price'=> $this->formatNumberPrice($advanceReceive->unit_price),
                'product' => $advanceReceive->products[0]->name,
                'category' => $advanceReceive->products[0]->categories[0]->name,
                'notes' => $advanceReceive->notes == "" ? '-' : $advanceReceive->notes,
                'qty_total' => $advanceReceive->qty_total ?? '-',
                'idr_total' => $this->formatNumberPrice($advanceReceive->idr_total) ?? '-',
                'qty_expired' => $advanceReceive->qty_expired ?? "-",
                'idr_expired' => $this->formatNumberPrice($advanceReceive->idr_expired) ?? "-",
                'qty_refund' => $advanceReceive->qty_refund ?? "-",
                'idr_refund' => $this->formatNumberPrice($advanceReceive->idr_refund) ?? "-",
                'qty_sum_all' => $advanceReceive->qty_sum_all ?? "-",
                'idr_sum_all' => $this->formatNumberPrice($advanceReceive->idr_sum_all) ?? "-",
                'qty_remains' => $advanceReceive->qty_remains ?? "-",
                'idr_remains' => $this->formatNumberPrice($advanceReceive->idr_remains) ?? "-",
                'status' => $advanceReceive->status,
                'refund_branch' => count($advanceReceive->refund_branches) > 0 ? $advanceReceive->refund_branches[0]->name : '',
            ];

            for ($i = 0; $i < 12 ; $i++) {
                $usedCount = $advanceReceive->consumptions[0]->history->count();
                if ($i < $usedCount) {
                    $history = $advanceReceive->consumptions[0]->history[$i];
                    $userDataConsumption[$key]['consumption-date-'.$history->used_count] = $history->consumption_date;
                    $userDataConsumption[$key]['consumption-branch-'.$history->used_count] = $history->branches[0]->name;
                }else {
                    if (($userDataConsumption[$key]['status'] == "EXPIRED") &&  ($userDataConsumption[$key]['qty'] > $i )) {
                        $consumptionDate = 'EXPIRED';
                        $consumptionBranch = $userDataConsumption[$key]['branch'];
                    }else if (($userDataConsumption[$key]['status'] == "REFUND") &&  ($userDataConsumption[$key]['qty'] > $i )) {
                        $consumptionDate = 'REFUND';
                        $consumptionBranch = $userDataConsumption[$key]['refund_branch'];
                    } else {
                        $consumptionDate = '-';
                        $consumptionBranch = '-';
                    }
                    $userDataConsumption[$key]['consumption-date-'.$i +1] = $consumptionDate;
                    $userDataConsumption[$key]['consumption-branch-'.$i +1] = $consumptionBranch;
                }
            }
        }

        return response()->json([
            'draw' => intval($request['draw'] ?? 0),
            'recordsTotal' => intval($recordsTotal),
            'recordsFiltered' => intval($recordsTotal),
            'data' => $userDataConsumption,
        ]);
    }

    public function consumptionAddView()
    {
        $branches = Branch::all();
        $data['branches'] = $branches;
        return view('consumptions/consumption_add', $data);
    }

    public function consumptionAdd(Request $request)
    {
        $dataCount = count($request['advance-receive-id-list']);
        $consumptionDate = $request['consumption-date'];
        $branchID = $request['branch-id'];
        $dataIntegrity = 0;

        foreach ($request['advance-receive-id-list'] as $key => $id) {
            /* update advance receive*/
            $advanceReceive = AdvanceReceive::find($id);
            $consumption = Consumption::where('advance_receive_id', $id)->first();

            if (($advanceReceive->qty_remains > 0 || $advanceReceive->qty_remains == null) &&  $advanceReceive->status == "AVAILABLE") {
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

                $idrRemains = $advanceReceive->net_sale - $idrSumAll;

                if ($qtyRemains == 0) {
                    $idrTotal += $idrRemains;
                    $idrSumAll += $idrRemains;
                    $idrRemains -= $idrRemains;
                }

                /* 2. insert into consumptions table */
                $consumptionAdd = new Consumption();
                $consumptionAdd->parent_id = $consumption->id;
                $consumptionAdd->branch_id = $branchID;
                $consumptionAdd->consumption_date = $consumptionDate;
                $consumptionAdd->used_count = $qtyTotal;
                if ($consumptionAdd->save()) {
                    /* 3. update advance receives */
                    $updateAdvanceReceive = AdvanceReceive::where('id', $id)->first();
                    $updateAdvanceReceive->qty_total = $qtyTotal;
                    $updateAdvanceReceive->idr_total = $idrTotal;
                    $updateAdvanceReceive->qty_sum_all = $qtySumAll;
                    $updateAdvanceReceive->idr_sum_all = $idrSumAll;
                    $updateAdvanceReceive->qty_remains = $qtyRemains;
                    $updateAdvanceReceive->idr_remains = $idrRemains;

                    if ($qtyRemains == 0 ) {
                        $updateAdvanceReceive->status = "OUT";
                    }

                    $updateAdvanceReceive->save();

                    $dataIntegrity += $key +1;

                }
            }
        }

        if ($dataIntegrity == $dataCount) {
            return response()->json([
                'status' => 'success',
                'dataIntegrity' => 'success',
                'message' => 'berhasil menambahkan seluruh data yang dipilih',
                'url' => URL::to('consumption')
            ]);
        }
    }

    public function consumptionEditView($id)
    {
        $advanceReceive = AdvanceReceive::with('customers', 'branches', 'products.categories', 'consumptions.history.branches')->find($id);
        $branches = Branch::all();
        $data['branches'] = $branches;
        $data['advanceReceive'] = $advanceReceive;
        return view('consumptions/consumption_edit', $data);
    }

    public function consumptionEdit(Request $request)
    {
        $totalData = count($request['consumption-id']);
        $pos = 0;
        /* update */
        for ($i =0; $i < $totalData ; $i++) {
            $consumption = Consumption::find($request['consumption-id'][$i]);
            $consumption->consumption_date = $request['consumption-date'][$i];
            $consumption->branch_id= $request['consumption-branch'][$i];
            $consumption->save();
            $pos+= $i;
        }

        if ($pos == $totalData) {
            $request->session()->flash('status', 'success');
            $request->session()->flash('message', 'Berhasil mengedit  data');
        }else {
            $request->session()->flash('status', 'danger');
            $request->session()->flash('message', 'Gagal mengedit data');
        }
        return redirect('consumption');
    }

    public function consumptionDelete(Request $request)
    {
        // find advance receive
        $advanceReceive = AdvanceReceive::with('consumptions.history')->find($request['advance-receive-id']);
        $deleteTarget = $request['consumption-used-count'];
        // check if used count are in last place
        $pos = 0;
        $lastUsed = count($advanceReceive->consumptions[0]->history);
        foreach ($advanceReceive->consumptions[0]->history as $consumption) {
            if ($consumption->used_count == $deleteTarget) {
                $pos = $deleteTarget;
            }
        }

        if ($pos != $lastUsed) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Consumption tidak dapat di delete, silahkan delete consumption terbaru terlebih dahulu',
            ]);
        }

        // delete
        $qtyTotal = $advanceReceive->qty_total ?? 0;
        $qtyTotal -= 1;

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

        $idrRemains = $advanceReceive->net_sale - $idrSumAll;

        // check if qty Remains is bigger than 0
        $status = "";
        if ($qtyRemains > 0) {
            $status = "AVAILABLE";
        } else {
            $status = "OUT";
        }

        /* update advance receive */
        $updateAdvanceReceive = AdvanceReceive::find($request['advance-receive-id']);
        $updateAdvanceReceive->qty_total = $qtyTotal;
        $updateAdvanceReceive->idr_total = $idrTotal;
        $updateAdvanceReceive->qty_sum_all = $qtySumAll;
        $updateAdvanceReceive->idr_sum_all = $idrSumAll;
        $updateAdvanceReceive->qty_remains = $qtyRemains;
        $updateAdvanceReceive->idr_remains = $idrRemains;
        $updateAdvanceReceive->status = $status;

        if ($updateAdvanceReceive->save()) {
            $consumption = Consumption::find($request['consumption-id']);
            if ($consumption->delete()){
                return response()->json([
                    'status' => 'success',
                    'message' => 'Berhasil menghapus consumption ke-'.$deleteTarget,
                ]);
            }
        }
    }

    public function consumptionGetSelectedCostumer(Request $request)
    {
        if ($request['selectedRows'] == null) {
            return response()->json([
                'data' => []
            ]);
        }

        $costumers = AdvanceReceive::with('customers')->whereIn('id', $request['selectedRows'])->get();

        return response()->json([
           'data' => $costumers
        ]);
    }

    public function consumptionExportExcel(Request $request)
    {
        try {
            $batch = Bus::batch([
                new ConsumptionExportJob($request->all())
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
            'exportStatus' => $exportBatchStatus,
            'exportURL' => \url('consumption/consumption-export/download')
        ]);
    }

    public function exportDownload()
    {
        return Storage::download('public/consumption_report.xlsx');
    }

    private function formatNumberPrice($n) {
        $explodePrice = explode('.', $n)[0];
        return explode('.', preg_replace("/\B(?=(\d{3})+(?!\d))/", ",", $n))[0];
    }


}
