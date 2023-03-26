<?php

namespace App\Http\Controllers;

use App\Models\AdvanceReceive;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class RefundController extends Controller
{
    public function refund()
    {
        $branches = Branch::all();
        $data['branches'] = $branches;
        return view('refund/refund', $data);
    }

    public function addRefundView ()
    {
        $branches = Branch::all();
        $data['branches'] = $branches;
        return view('refund/refund_add', $data);
    }

    public function addRefund(Request $request)
    {
        $id = $request['id'];
        $branchId = $request['branchId'];

        $advanceReceive = AdvanceReceive::find($id);
        $advanceReceive->status = 'REFUND';
        $advanceReceive->refund_branch_id = $branchId;
        $qtyTotal = $advanceReceive->qty_total ?? 0;
        $idrTotal = $advanceReceive->idr_total ?? 0;

        $qtyrefund = $advanceReceive->qty_refund ?? 0;
        $idrrefund = $qtyrefund * $advanceReceive->unit_price;

        $qtyRefund = $advanceReceive->qty - $qtyTotal;
        $idrRefund = $qtyRefund * $advanceReceive->unit_price;

        // calculate
        $qtySumAll = $qtyTotal + $qtyrefund + $qtyRefund;
        $idrSumAll = $idrTotal + $idrrefund + $idrRefund;

        $qtyRemains = $advanceReceive->qty - $qtySumAll;
        $idrRemains = $advanceReceive->net_sale - $idrSumAll;

        // check selisih karna angka desimal dari outstanding
        if ($qtyRemains == 0) {
            $idrSumAll += $idrRemains;
            $idrRefund += $idrRemains;
            // paling akhir
            $idrRemains -= $idrRemains;
        }

        // update
        $advanceReceive->idr_total = $idrTotal;
        $advanceReceive->idr_refund = $idrrefund;
        $advanceReceive->idr_refund = $idrRefund;
        $advanceReceive->idr_sum_all = $idrSumAll;
        $advanceReceive->idr_remains = $idrRemains;
        $advanceReceive->qty_total = $qtyTotal;
        $advanceReceive->qty_refund = $qtyrefund;
        $advanceReceive->qty_refund = $qtyRefund;
        $advanceReceive->qty_sum_all = $qtySumAll;
        $advanceReceive->qty_remains = $qtyRemains;
        $advanceReceive->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Refund berhasil ditambahkan'
        ]);
    }

    public function refundDataGET()
    {
        $buyDateRequest = $_GET['columns'][2]['search']['value'] == "" ? '1980-01-01||2999-01-01' : $_GET['columns'][2]['search']['value'];
        $buyDateStart = explode("||", $buyDateRequest)[0];
        $buyDateEnd = explode("||", $buyDateRequest)[1];

        $dataCount = AdvanceReceive::with('customers', 'branches', 'products.categories', 'refund_branches' )
            ->whereRelation('customers', 'customer_id', 'ILIKE', '%'.$_GET['columns'][3]['search']['value'].'%')
            ->whereRelation('customers', 'name', 'ILIKE', '%'.$_GET['columns'][4]['search']['value'].'%')
            ->whereRelation('branches', function (Builder $query) {
                if ($_GET['columns'][0]['search']['value'] != ""){
                    $query->where('id', $_GET['columns'][0]['search']['value']);
                }
            })
            ->whereBetween('buy_date', [$buyDateStart, $buyDateEnd])
            ->where('status', 'REFUND')
            ->get();

        $data = AdvanceReceive::with('customers', 'branches', 'products.categories', 'refund_branches')
            ->whereRelation('customers', 'customer_id', 'ILIKE', '%'.$_GET['columns'][3]['search']['value'].'%')
            ->whereRelation('customers', 'name', 'ILIKE', '%'.$_GET['columns'][4]['search']['value'].'%')
            ->whereRelation('branches', function (Builder $query) {
                if ($_GET['columns'][0]['search']['value'] != ""){
                    $query->where('id', $_GET['columns'][0]['search']['value']);
                }
            })
            ->whereBetween('buy_date', [$buyDateStart, $buyDateEnd])
            ->where('status', 'REFUND')
            ->offset($_GET['start'])
            ->limit($_GET['length'])
            ->get();

        $report = [
            'qty_refund' => $dataCount->sum('qty_refund'),
            'idr_refund' => $dataCount->sum('idr_refund'),
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

    public function refundDataGetAvailable()
    {
        $buyDateRequest = $_GET['columns'][2]['search']['value'] == "" ? '1980-01-01||2999-01-01' : $_GET['columns'][2]['search']['value'];
        $buyDateStart = explode("||", $buyDateRequest)[0];
        $buyDateEnd = explode("||", $buyDateRequest)[1];

        $dataCount = AdvanceReceive::with('customers', 'branches', 'products.categories')
            ->whereRelation('customers', 'customer_id', 'ILIKE', '%'.$_GET['columns'][3]['search']['value'].'%')
            ->whereRelation('customers', 'name', 'ILIKE', '%'.$_GET['columns'][4]['search']['value'].'%')
            ->whereRelation('branches', function (Builder $query) {
                if ($_GET['columns'][0]['search']['value'] != ""){
                    $query->where('id', $_GET['columns'][0]['search']['value']);
                }
            })
            ->whereBetween('buy_date', [$buyDateStart, $buyDateEnd])
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
            ->whereBetween('buy_date', [$buyDateStart, $buyDateEnd])
            ->where('status', 'AVAILABLE')
            ->offset($_GET['start'])
            ->limit($_GET['length'])
            ->get();

        $report = [
            'qty_refund' => $data->sum('qty_refund'),
            'idr_refund' => $data->sum('idr_refund'),
            'qty_remains' => $data->sum('qty_remains'),
            'idr_remains' => $data->sum('idr_remains')
        ];

        return response()->json([
            'draw' => intval($_GET['draw']),
            'recordsTotal' => intval(count($dataCount)),
            'recordsFiltered' => intval(count($dataCount)),
            'data' => $data,
            'report' => $report
        ]);
    }

    public function branchList()
    {
        $branches = Branch::all();
        return response()->json([
           'data' => $branches
        ]);;
    }
}
