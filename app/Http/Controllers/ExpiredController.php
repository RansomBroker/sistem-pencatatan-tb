<?php

namespace App\Http\Controllers;

use App\Models\AdvanceReceive;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

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
            ->where('status', 'EXPIRED')
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
            ->where('status', 'EXPIRED')
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
            'recordsTotal' => intval(count($dataCount)),
            'recordsFiltered' => intval(count($dataCount)),
            'data' => $data,
            'report' => $report
        ]);
    }
}
