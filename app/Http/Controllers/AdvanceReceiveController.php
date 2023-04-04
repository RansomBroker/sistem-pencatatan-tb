<?php

namespace App\Http\Controllers;

use App\Exports\AdvanceReceiveExport;
use App\Jobs\AdvanceReceiveExportJob;
use App\Models\AdvanceReceive;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Consumption;
use App\Models\Customer;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Sabberworm\CSS\Value\URL;

class AdvanceReceiveController extends Controller
{
    public function advanceReceive()
    {
        $branches = Branch::all();
        $data['branches'] = $branches;
        return view('advance_receives/advance_receive', $data);
    }

    public function advanceReceiveDataGET(Request $request)
    {
        $buyDateRequest = isset($request['columns'][2]['search']['value']) ? ($request['columns'][2]['search']['value'] == "" ? '1980-01-01||2999-01-01' : $request['columns'][2]['search']['value'])  : '1980-01-01||2999-01-01';
        $buyDateStart = explode("||", $buyDateRequest)[0];
        $buyDateEnd = explode("||", $buyDateRequest)[1];

        $idFilter = $request['columns'][4]['search']['value'] ?? '';
        $nameFilter = $request['columns'][5]['search']['value'] ?? '';
        $branchFilter = $request['columns'][1]['search']['value'] ?? '';

        // generate data
        $columnsRecord = AdvanceReceive::orderBy('buy_date', 'asc')
            ->with('customers', 'branches', 'products.categories', 'consumptions.history.branches', 'refund_branches')
            ->whereRelation('customers', 'customer_id', 'ILIKE', '%'.$idFilter.'%')
            ->whereRelation('customers', 'name', 'ILIKE', '%'.$nameFilter.'%')
            ->whereRelation('branches', 'name', 'ILIKE', '%'.$branchFilter.'%')
            ->whereBetween('buy_date', [$buyDateStart, $buyDateEnd])
            ->get();

        $data = AdvanceReceive::orderBy('buy_date', 'asc')
            ->with('customers', 'branches', 'products.categories', 'consumptions.history.branches', 'refund_branches')
            ->whereRelation('customers', 'customer_id', 'ILIKE', '%'.$idFilter.'%')
            ->whereRelation('customers', 'name', 'ILIKE', '%'.$nameFilter.'%')
            ->whereRelation('branches', 'name', 'ILIKE', '%'.$branchFilter.'%')
            ->whereBetween('buy_date', [$buyDateStart, $buyDateEnd])
            ->offset($request['start'] ?? 0)
            ->limit($request['length'] ?? 10)
            ->get();

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

        // generate report
        $report = collect($columnsRecord);
        $reportData[] = [
            'sales' => $this->formatNumberPrice($report->sum('buy_price')),
            'advanceReceive' => $this->formatNumberPrice($report->sum('net_sale')),
            'consumption' => $this->formatNumberPrice($report->sum('idr_total')),
            'expired' => $this->formatNumberPrice($report->sum('idr_expired')),
            'refund' => $this->formatNumberPrice($report->sum('idr_refund')),
            'outstanding' => $this->formatNumberPrice($report->sum('idr_remains'))
        ];

        return response()->json([
            'draw' => intval($request['draw'] ?? 0),
            'recordsTotal' => intval($columnsRecord->count()),
            'recordsFiltered' => intval($columnsRecord->count()),
            'data' => $userDataConsumption,
            'report' => $reportData
        ]);
    }

    public function advanceReceiveAddView()
    {
        $branches = Branch::all();
        $products = Product::all();
        $data['branches'] = $branches;
        $data['products'] = $products;
        return view('advance_receives/advance_receive_add', $data);
    }

    public function advanceReceiveAdd(Request $request)
    {
        $validator = $request->validate([
            'branch' => 'required',
            'buy-date' => 'required',
            'expired-date' => 'required',
            'customer-id' => 'required',
            'type' => 'required',
            'buy-price' => 'required',
            'tax-include' => 'required',
            'net-sales' => 'required',
            'tax' => 'required',
            'payment' => 'required',
            'qty' => 'numeric|required|min:1|max:12',
            'unit-price' => 'required',
            'product' => 'required',
            'category' => 'required',
        ]);

        $regex = "/[,]/";
        $buyPrice = preg_replace($regex, '', $validator['buy-price']);
        $netSales = preg_replace($regex, '', $validator['net-sales']);
        $tax = preg_replace($regex, '', $validator['tax']);
        $unitPrice = preg_replace($regex, '', $validator['unit-price']);

        /* get customer id */
        $customer =  Customer::where('customer_id', 'ILIKE' , '%'.$validator['customer-id'].'%')->first();
        /* write to db */
        $advanceReceive = new AdvanceReceive();

        $expiredDate = Carbon::create($validator['expired-date'])->toDateString();
        $currDate = Carbon::now()->toDateString();
        if ($expiredDate >= $currDate) {
            $status = "AVAILABLE";
        } else {
            $status = "EXPIRED";
            $advanceReceive->qty_expired = $validator['qty'];
            $advanceReceive->idr_expired = $netSales;
            $advanceReceive->qty_remains = 0;
            $advanceReceive->idr_remains = 0;
        }

        $advanceReceive->product_id = $validator['product'];
        $advanceReceive->customer_id = $customer->id;
        $advanceReceive->branch_id = $validator['branch'];
        $advanceReceive->type = $validator['type'];
        $advanceReceive->buy_price = $buyPrice;
        $advanceReceive->net_sale = $netSales;
        $advanceReceive->tax = $tax;
        $advanceReceive->unit_price = $unitPrice;
        $advanceReceive->payment = $validator['payment'];
        $advanceReceive->qty = $validator['qty'];
        $advanceReceive->buy_date = $validator['buy-date'];
        $advanceReceive->expired_date = $validator['expired-date'];
        $advanceReceive->status = $status;
        $advanceReceive->notes = $request['notes'] ?? '';
        $advanceReceive->memo = $request['memo'] ?? '';

        if ($advanceReceive->save()) {
            $consumption = new Consumption();
            $consumption->advance_receive_id = $advanceReceive->id;
            if ($consumption->save()) {
                $updateAdvanceReceive = AdvanceReceive::find($advanceReceive->id);
                $updateAdvanceReceive->consumption_id = $consumption->id;
                if ($updateAdvanceReceive->save()){
                    $request->session()->flash('status', 'success');
                    $request->session()->flash('message', 'Berhasil menambahkan  data');
                }
            }
        }else {
            $request->session()->flash('status', 'danger');
            $request->session()->flash('message', 'Gagal menambahkan data');
        }
        return redirect('advance-receive');
    }

    public function advanceReceiveEditView($id)
    {
        $advanceReceive = AdvanceReceive::with('products.categories', 'branches', 'customers')->find($id);
        $branches = Branch::all();
        $products = Product::all();
        $data['branches'] = $branches;
        $data['products'] = $products;
        $data['advanceReceive'] = $advanceReceive;
        return view('advance_receives/advance_receive_edit', $data);
    }

    public function advanceReceiveEdit(Request $request)
    {
        $validator = $request->validate([
            'branch' => 'required',
            'buy-date' => 'required',
            'expired-date' => 'required',
            'customer-id' => 'required',
            'type' => 'required',
            'buy-price' => 'required',
            'tax-include' => 'required',
            'net-sales' => 'required',
            'tax' => 'required',
            'payment' => 'required',
            'qty' => 'numeric|required|min:1|max:12',
            'unit-price' => 'required',
            'product' => 'required',
            'category' => 'required',
        ]);

        $regex = "/[,]/";
        $buyPrice = preg_replace($regex, '', $validator['buy-price']);
        $netSales = (int) preg_replace($regex, '', $validator['net-sales']);
        $tax = preg_replace($regex, '', $validator['tax']);
        $unitPrice = (int) preg_replace($regex, '', $validator['unit-price']);

        /* get customer id */
        $customer =  Customer::where('customer_id', 'ILIKE' , '%'.$validator['customer-id'].'%')->first();

        /* write to db */
        $advanceReceive = AdvanceReceive::find($request['id']);

        // Jika qty < qty total
        if ($validator['qty'] < ( $advanceReceive->qty_total ?? 0 ) ) {
            $request->session()->flash('status', 'danger');
            $request->session()->flash('message', 'Gagal mengedit data Quantity tidak boleh kecil dari QTY Total Consumption    ');
            return redirect('advance-receive');
        }

        /* update data if exist */
        /* check expired date */
        // calculate total
        $qtyTotal = $advanceReceive->qty_total ?? 0;
        $idrTotal = $qtyTotal * $unitPrice;

        // need get value

        // calculate expired
        $expiredDate = Carbon::create($validator['expired-date'])->toDateString();
        $currDate = Carbon::now()->toDateString();
        if (($expiredDate >= $currDate) &&  ($advanceReceive->qty_remains > 0 || $advanceReceive->qty_remains == null)) {
            /*
             *  Karna mengaktifkan kembali maka qty expired = 0
             * */
            $status = "AVAILABLE";
            $qtyExpired = 0;
            $idrExpired = $qtyExpired * $unitPrice;
        } else {
            /*
             * qty expired
             * ex = 5 - 3 (yang sudah di consum)
             * */
            $status = "EXPIRED";
            $qtyExpired = $validator['qty'] - $qtyTotal;
            $idrExpired = $qtyExpired * $unitPrice;
        }

        $qtyRefund = $advanceReceive->qty_refund ?? 0;
        $idrRefund = $qtyRefund * $unitPrice;

        // calculate
        $qtySumAll = $qtyTotal + $qtyExpired + $qtyRefund;
        $idrSumAll = $idrTotal + $idrExpired + $idrRefund;

        $qtyRemains = $validator['qty'] - $qtySumAll;
        $idrRemains = $netSales - $idrSumAll;

        // check selisih karna angka desimal dari outstanding
        if ($qtyRemains == 0) {
            $idrSumAll += $idrRemains;
            $idrExpired += $idrRemains;
            // paling akhir
            $idrRemains -= $idrRemains;
        }

        // Update Data
        $advanceReceive->product_id = $validator['product'];
        $advanceReceive->customer_id = $customer->id;
        $advanceReceive->branch_id = $validator['branch'];
        $advanceReceive->type = $validator['type'];
        $advanceReceive->buy_price = $buyPrice;
        $advanceReceive->net_sale = $netSales;
        $advanceReceive->tax = $tax;
        $advanceReceive->unit_price = $unitPrice;
        $advanceReceive->payment = $validator['payment'];
        $advanceReceive->qty = $validator['qty'];
        $advanceReceive->buy_date = $validator['buy-date'];
        $advanceReceive->expired_date = $validator['expired-date'];
        $advanceReceive->status = $status;
        $advanceReceive->notes = $request['notes'] ?? '';
        $advanceReceive->memo = $request['memo']??'';
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

        if (($qtyRemains == 0) && ($qtyExpired == 0 || $qtyExpired == null)) {
            $advanceReceive->status = "OUT";
        }
        if ($advanceReceive->save()) {
            $request->session()->flash('status', 'success');
            $request->session()->flash('message', 'Berhasil mengedit  data');
        }else {
            $request->session()->flash('status', 'danger');
            $request->session()->flash('message', 'Gagal mengedit data');
        }
        return redirect('advance-receive');
    }

    public function getCustomerByID($id)
    {
        $customer = Customer::where('customer_id', 'ILIKE' ,'%'.$id.'%')->first();
        return response()->json([
            'data' => $customer
        ]);
    }

    public function getCategoryByID($id)
    {
        $category = Product::with('categories')->where('id', $id)->first();
        return response()->json([
            'data' => $category
        ]);
    }

    public function advanceReceiveDelete($id)
    {
        $advanceReceive = AdvanceReceive::find($id);
        try {
            $advanceReceive->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Berhasil menghapus Advance Receive'
            ]);
        }catch (QueryException $error) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Gagal menghapus Advance Receive dikarenakan Advance Receive sedang digunakan'
            ]);
        }
    }

    public function advanceReceiveExportExcel(Request $request)
    {
        try {
            $name = Carbon::now()->timestamp;
            $batch = Bus::batch([
                new AdvanceReceiveExportJob($request->all(), $name)
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
            'exportURL' => \url('advance-receive/advance-receive-export/download/'.$name)
        ]);
    }

    public function exportDownload($name)
    {
        return Storage::download('public/advance_receive_report_'.$name.'.xlsx');
    }

    private function formatNumberPrice($n)
    {
        $explodePrice = explode('.', $n)[0];
        return explode('.', preg_replace("/\B(?=(\d{3})+(?!\d))/", ",", $n))[0];
    }

}
