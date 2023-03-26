<?php

namespace App\Http\Controllers;

use App\Models\AdvanceReceive;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class OutstandingController extends Controller
{
    public function outstanding()
    {
        $branches = Branch::all();
        $data['branches'] = $branches;
        return view('outstanding/outstanding', $data);
    }

    public function outstandingDataGET()
    {
        $outstandingDateRequest = $_GET['columns'][2]['search']['value'] == "" ? '1980-01-01||2999-01-01' : $_GET['columns'][2]['search']['value'];
        $outstandingDateStart = explode("||", $outstandingDateRequest)[0];
        $outstandingDateEnd = explode("||", $outstandingDateRequest)[1];

        $dataCount = AdvanceReceive::with('customers', 'branches', 'products.categories' )
            ->whereRelation('customers', 'customer_id', 'ILIKE', '%'.$_GET['columns'][3]['search']['value'].'%')
            ->whereRelation('customers', 'name', 'ILIKE', '%'.$_GET['columns'][4]['search']['value'].'%')
            ->whereRelation('branches', function (Builder $query) {
                if ($_GET['columns'][0]['search']['value'] != ""){
                    $query->where('id', $_GET['columns'][0]['search']['value']);
                }
            })
            ->whereBetween('buy_date', [$outstandingDateStart, $outstandingDateEnd])
            ->get();

        $data = AdvanceReceive::with('customers', 'branches', 'products.categories')
            ->whereRelation('customers', 'customer_id', 'ILIKE', '%'.$_GET['columns'][3]['search']['value'].'%')
            ->whereRelation('customers', 'name', 'ILIKE', '%'.$_GET['columns'][4]['search']['value'].'%')
            ->whereRelation('branches', function (Builder $query) {
                if ($_GET['columns'][0]['search']['value'] != ""){
                    $query->where('id', $_GET['columns'][0]['search']['value']);
                }
            })
            ->whereBetween('buy_date', [$outstandingDateStart, $outstandingDateEnd])
            ->offset($_GET['start'])
            ->limit($_GET['length'])
            ->get();

        $report = [
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

}
