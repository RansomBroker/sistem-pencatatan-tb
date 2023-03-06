<?php

namespace App\Http\Controllers;

use App\Models\AdvanceReceive;
use App\Models\Branch;
use App\Models\Consumption;
use App\Models\Customer;
use Dotenv\Repository\AdapterRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\Builder;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\StyleMerger;

class ConsumptionController extends Controller
{
    public function consumption()
    {
        $branches = Branch::all();
        $data['branches'] = $branches;
        return view('consumptions/consumption', $data);
    }

    public function getColumn()
    {
        // generate column header
        $dataWithoutLimit = AdvanceReceive::orderBy('buy_date', 'asc')
            ->with('customers', 'branches', 'products.categories', 'consumptions.history.branches')
            ->get();

        // create columns header for consumption list
        $consumptionsHeaderRaw = [];
        foreach ($dataWithoutLimit as $advanceReceive) {
            foreach ($advanceReceive->consumptions as $consumptions) {
                foreach ($consumptions->history as $history) {
                    $consumptionsHeaderRaw[] = [
                        [
                            'data' => 'consumption-date-'.$history->used_count,
                            'title' => 'tanggal pemakaian ke-'. $history->used_count,
                        ],
                        [
                            'data' => 'consumption-branch-'.$history->used_count,
                            'title' => 'tempat consumption  ke-'. $history->used_count,
                        ]

                    ];
                }
            }
        }

        $consumptionsHeader = array_map("unserialize", array_unique(array_map("serialize", $consumptionsHeaderRaw)));

        // membuat columns agar tidak perlu di proses banyak oleh client
        $consumptionsColumns = [];
        foreach ($consumptionsHeader as $header) {
            foreach ($header as $data) {
                $consumptionsColumns[] = $data;
            }
        }

        $columnsShow = [
            [
                'data' => 'action',
                'title' => 'Action'
            ],
            [
                'data' => 'branch',
                'title' => 'Cabang penjualan'
            ],
            [
                'data' => 'buy_date',
                'title' => 'Tanggal penjualan'
            ],
            [
                'data' => 'expired_date',
                'title' => 'Expired Date'
            ],
            [
                'data' => 'customer_id',
                'title' => 'ID Customer'
            ],
            [
                'data' => 'customer_name',
                'title' => 'Nama Customer'
            ],
            [
                'data' => 'type',
                'title' => 'Tipe'
            ],
            [
                'data' => 'qty',
                'title' => 'Qty Produk'
            ],
            [
                'data' => 'product',
                'title' => 'Produk'
            ],
            [
                'data' => 'category',
                'title' => 'Kategory produk'
            ],
            [
                'data' => 'notes',
                'title' => 'Notes'
            ],
        ];

        /* menambahkan kolom baru untuk harga*/
        $idrAndQtyColumns = [
            [
                'data' => 'qty_total',
                'title' => 'QTY Total Consumption Advance Receive'
            ],
            [
                'data' => 'idr_total',
                'title' => 'IDR Total Consumption Advance Receive'
            ],
            [
                'data' => 'qty_expired',
                'title' => 'QTY Expired Advance Receive'
            ],
            [
                'data' => 'idr_expired',
                'title' => 'IDR Expired Advance Receive'
            ],
            [
                'data' => 'qty_refund',
                'title' => 'QTY Refund Advance Receive'
            ],
            [
                'data' => 'idr_refund',
                'title' => 'IDR Refund Advance Receive'
            ],
            [
                'data' => 'qty_remains',
                'title' => 'QTY Total Sisa Advance Receive'
            ],
            [
                'data' => 'idr_remains',
                'title' => 'IDR Total Sisa Advance Receive'
            ],
        ];

        return response()->json([
            'columns' => array_merge(array_merge($columnsShow, $consumptionsColumns), $idrAndQtyColumns),
        ]);
    }

    public function consumptionDataGET()
    {
        $consumptionDateRequest = isset($_GET['columns'][2]['search']['value']) && $_GET['columns'][2]['search']['value'] != ""  ? $_GET['columns'][2]['search']['value']: '1980-01-01||2999-12-31';

        $idFilter = $_GET['columns'][4]['search']['value'] ?? '';
        $nameFilter = $_GET['columns'][5]['search']['value'] ?? '';
        $branchFilter = $_GET['columns'][1]['search']['value'] ?? '';

        // generate data
        $columnsRecord = AdvanceReceive::orderBy('buy_date', 'asc')
            ->with('customers', 'branches', 'products.categories', 'consumptions.history.branches')
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
            ->with('customers', 'branches', 'products.categories', 'consumptions.history.branches')
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
            ->offset($_GET['start'] ?? 0)
            ->limit($_GET['length'] ?? 10)
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
        foreach ($data as $advanceReceive) {
            $tmp = [];
            foreach ($advanceReceive->consumptions as $consumptions) {
                foreach ($consumptions->history as $history) {
                    $tmp[] = [
                        'consumption-date-'.$history->used_count => $history->consumption_date,
                        'consumption-branch-'.$history->used_count => $history->branches[0]->name,
                        'status' => $history->status?? ''
                    ];
                }
            }

            $userDataConsumption[] = [
                'memo'=> $advanceReceive->products[0]->memo,
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
                'payment' => $this->formatNumberPrice($advanceReceive->payment),
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
                'qty_remains' => $advanceReceive->qty_remains ?? "-",
                'idr_remains' => $this->formatNumberPrice($advanceReceive->idr_remains) ?? "-",
                'status' => $advanceReceive->status,
                $tmp
            ];
        }

        /*
         * in case lebih dari 50 tambah lagi limitnya
         * worst case 50 consumption
         * */
        // assign empty consumption count
        foreach ($userDataConsumption as $key => $data){
            for ($i = 0; $i < 50 ; $i++) {
                if (!array_key_exists($i, $data[0])) {
                    if (($data['status'] == "EXPIRED") &&  ($data['qty'] > $i )) {
                        $consumptionDate = 'EXPIRED';
                        $consumptionBranch = $data['branch'];
                    } else {
                        $consumptionDate = '-';
                        $consumptionBranch = '-';
                    }

                    $userDataConsumption[$key][0][$i] = [
                        'consumption-date-'.$i +1  => $consumptionDate,
                        'consumption-branch-'.$i +1 => $consumptionBranch,
                    ];
                }
            }
        }

        //  membuat data agar tidak perlu banyak di proses oleh client
        $consumptionData = [];
        foreach ($userDataConsumption as $data) {
            $consumptionData[] = [
                array_merge(...$data[0]), $data
            ];
        }

        $consumptionRow = [];
        foreach ($consumptionData as $data) {
            $consumptionRow[] = array_merge(...$data);
        }

        $totalBranchFilterConsumption = 0;
        foreach ($getBranchFilter as $branch) {
            $totalBranchFilterConsumption += count($branch->consumptions[0]->history);
        }

        // idr
        $idrBranchFilterConsumption = 0;
        foreach ($getBranchFilter as $branch) {
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
            'draw' => intval($_GET['draw'] ?? 0),
            'recordsTotal' => intval($recordsTotal),
            'recordsFiltered' => intval($recordsTotal),
            'data' => $consumptionRow,
            'report' => $reportData
        ]);
    }

    public function consumptionGetAvailableData()
    {
        $consumptionDateRequest = isset($_GET['columns'][2]['search']['value']) ? $_GET['columns'][2]['search']['value'] : '1980-01-01||2999-01-01';

        $idFilter = $_GET['columns'][4]['search']['value'] ?? '';
        $nameFilter = $_GET['columns'][5]['search']['value'] ?? '';
        $branchFilter = $_GET['columns'][1]['search']['value'] ?? '';

        // generate column header
        $dataWithoutLimit = AdvanceReceive::orderBy('buy_date', 'asc')
            ->with('customers', 'branches', 'products.categories', 'consumptions.history.branches')
            ->get();

        // generate data
        $columnsRecord = AdvanceReceive::orderBy('buy_date', 'asc')
            ->with('customers', 'branches', 'products.categories', 'consumptions.history.branches')
            ->whereRelation('customers', 'customer_id', 'ILIKE', '%'.$idFilter.'%')
            ->whereRelation('customers', 'name', 'ILIKE', '%'.$nameFilter.'%')
            ->whereRelation('consumptions', function (Builder $query)  use($branchFilter) {
                if ($branchFilter != "") {
                    $query->whereRelation('history', 'branch_id', $branchFilter);
                }
            })
            ->whereRelation('consumptions', function (Builder $query)  use($consumptionDateRequest) {
                if (isset($_GET['columns'][2]['search']['value']) && $_GET['columns'][2]['search']['value'] != "" ) {
                    $query->whereRelation('history', function (Builder $query) use($consumptionDateRequest) {
                        $query->whereBetween('consumption_date', [explode('||', $consumptionDateRequest)[0], explode('||', $consumptionDateRequest)[1]]);
                    });
                }
            })
            ->where('status', 'AVAILABLE')
            ->get();


        $data = AdvanceReceive::orderBy('buy_date', 'asc')
            ->with('customers', 'branches', 'products.categories', 'consumptions.history.branches')
            ->whereRelation('customers', 'customer_id', 'ILIKE', '%'.$idFilter.'%')
            ->whereRelation('customers', 'name', 'ILIKE', '%'.$nameFilter.'%')
            ->whereRelation('consumptions', function (Builder $query)  use($branchFilter) {
                if ($branchFilter != "") {
                    $query->whereRelation('history', 'branch_id', $branchFilter);
                }
            })
            ->whereRelation('consumptions', function (Builder $query)  use($consumptionDateRequest) {
                if (isset($_GET['columns'][2]['search']['value']) && $_GET['columns'][2]['search']['value'] != "" ) {
                    $query->whereRelation('history', function (Builder $query) use($consumptionDateRequest) {
                        $query->whereBetween('consumption_date', [explode('||', $consumptionDateRequest)[0], explode('||', $consumptionDateRequest)[1]]);
                    });
                }
            })
            ->where('status', 'AVAILABLE')
            ->offset($_GET['start'] ?? 0)
            ->limit($_GET['length'] ?? 10)
            ->get();

        $recordsTotal = count($columnsRecord);


        // create columns header for consumption list
        $consumptionsHeaderRaw = [];
        $userDataConsumption = [];
        foreach ($dataWithoutLimit as $advanceReceive) {
            foreach ($advanceReceive->consumptions as $consumptions) {
                foreach ($consumptions->history as $history) {
                    $consumptionsHeaderRaw[] = [
                        [
                            'data' => 'consumption-date-'.$history->used_count,
                            'title' => 'tanggal pemakaian ke-'. $history->used_count,
                        ],
                        [
                            'data' => 'consumption-branch-'.$history->used_count,
                            'title' => 'tempat consumption  ke-'. $history->used_count,
                        ]

                    ];
                }
            }
        }

        foreach ($data as $advanceReceive) {
            $tmp = [];
            foreach ($advanceReceive->consumptions as $consumptions) {
                foreach ($consumptions->history as $history) {
                    $tmp[] = [
                        'consumption-date-'.$history->used_count => $history->consumption_date,
                        'consumption-branch-'.$history->used_count => $history->branches[0]->name,
                        'status' => $history->status?? ''
                    ];
                }
            }

            $userDataConsumption[] = [
                'memo'=> $advanceReceive->products[0]->memo,
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
                'payment' => $this->formatNumberPrice($advanceReceive->payment),
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
                'qty_remains' => $advanceReceive->qty_remains ?? "-",
                'idr_remains' => $this->formatNumberPrice($advanceReceive->idr_remains) ?? "-",
                'status' => $advanceReceive->status,
                $tmp
            ];
        }


        $consumptionsHeader = array_map("unserialize", array_unique(array_map("serialize", $consumptionsHeaderRaw)));

        // assign empty consumption count
        foreach ($userDataConsumption as $key => $data){
            for ($i = 0; $i < count($consumptionsHeader) ; $i++) {
                if (!array_key_exists($i, $data[0])) {
                    if (($data['status'] == "EXPIRED") &&  ($data['qty'] > $i )) {
                        $consumptionDate = 'EXPIRED';
                        $consumptionBranch = $data['branch'];
                    } else {
                        $consumptionDate = '-';
                        $consumptionBranch = '-';
                    }

                    $userDataConsumption[$key][0][$i] = [
                        'consumption-date-'.$i +1  => $consumptionDate,
                        'consumption-branch-'.$i +1 => $consumptionBranch,
                    ];
                }
            }
        }

        // membuat columns agar tidak perlu di proses banyak oleh client
        $consumptionsColumns = [];
        foreach ($consumptionsHeader as $header) {
            foreach ($header as $data) {
                $consumptionsColumns[] = $data;
            }
        }

        //  membuat data agar tidak perlu banyak di proses oleh client
        $consumptionData = [];
        foreach ($userDataConsumption as $data) {
            $consumptionData[] = [
                array_merge(...$data[0]), $data
            ];
        }

        $consumptionRow = [];
        foreach ($consumptionData as $data) {
            $consumptionRow[] = array_merge(...$data);
        }

        $columnsShow = [
            [
                'data' => 'action',
                'title' => 'Action'
            ],
            [
                'data' => 'branch',
                'title' => 'Cabang penjualan'
            ],
            [
                'data' => 'buy_date',
                'title' => 'Tanggal penjualan'
            ],
            [
                'data' => 'expired_date',
                'title' => 'Expired Date'
            ],
            [
                'data' => 'customer_id',
                'title' => 'ID Customer'
            ],
            [
                'data' => 'customer_name',
                'title' => 'Nama Customer'
            ],
            [
                'data' => 'type',
                'title' => 'Tipe'
            ],
            [
                'data' => 'qty',
                'title' => 'Qty Produk'
            ],
            [
                'data' => 'product',
                'title' => 'Produk'
            ],
            [
                'data' => 'category',
                'title' => 'Kategory produk'
            ],
            [
                'data' => 'notes',
                'title' => 'Notes'
            ],
        ];

        /* menambahkan kolom baru untuk harga*/
        $idrAndQtyColumns = [
            [
                'data' => 'qty_total',
                'title' => 'QTY Total Consumption Advance Receive'
            ],
            [
                'data' => 'idr_total',
                'title' => 'IDR Total Consumption Advance Receive'
            ],
            [
                'data' => 'qty_expired',
                'title' => 'QTY Expired Advance Receive'
            ],
            [
                'data' => 'idr_expired',
                'title' => 'IDR Expired Advance Receive'
            ],
            [
                'data' => 'qty_refund',
                'title' => 'QTY Refund Advance Receive'
            ],
            [
                'data' => 'idr_refund',
                'title' => 'IDR Refund Advance Receive'
            ],
            [
                'data' => 'qty_remains',
                'title' => 'QTY Total Sisa Advance Receive'
            ],
            [
                'data' => 'idr_remains',
                'title' => 'IDR Total Sisa Advance Receive'
            ],
        ];


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
            'draw' => intval($_GET['draw'] ?? 0),
            'recordsTotal' => intval($recordsTotal),
            'recordsFiltered' => intval($recordsTotal),
            'data' => $consumptionRow,
            'columns' => array_merge(array_merge($columnsShow, $consumptionsColumns), $idrAndQtyColumns),
            'report' => $reportData
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

        /* update advance receive */
        $updateAdvanceReceive = AdvanceReceive::find($request['advance-receive-id']);
        $updateAdvanceReceive->qty_total = $qtyTotal;
        $updateAdvanceReceive->idr_total = $idrTotal;
        $updateAdvanceReceive->qty_sum_all = $qtySumAll;
        $updateAdvanceReceive->idr_sum_all = $idrSumAll;
        $updateAdvanceReceive->qty_remains = $qtyRemains;
        $updateAdvanceReceive->idr_remains = $idrRemains;

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

    private function formatNumberPrice($n) {
        $explodePrice = explode('.', $n)[0];
        return explode('.', preg_replace("/\B(?=(\d{3})+(?!\d))/", ",", $n))[0];
    }


}
