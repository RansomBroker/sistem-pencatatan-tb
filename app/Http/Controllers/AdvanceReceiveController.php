<?php

namespace App\Http\Controllers;

use App\Models\AdvanceReceive;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Consumption;
use App\Models\Customer;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdvanceReceiveController extends Controller
{
    public function advanceReceive()
    {
        $branches = Branch::all();
        $data['branches'] = $branches;
        return view('advance_receives/advance_receive', $data);
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
                'data' => 'buy_price',
                'title' => 'IDR Harga Beli'
            ],
            [
                'data' => 'net_sales',
                'title' => 'IDR Net Sales'
            ],
            [
                'data' => 'tax',
                'title' => 'IDR PPN'
            ],
            [
                'data' => 'payment',
                'title' => 'Pembayaran'
            ],
            [
                'data' => 'qty',
                'title' => 'Qty Produk'
            ],
            [
                'data' => 'unit_price',
                'title' => 'IDR Harga Satuan'
            ],
            [
                'data' => 'product',
                'title' => 'Produk'
            ],
            [
                'data' => 'memo',
                'title' => 'Memo/Model'
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
                'data' => 'qty_sum_all',
                'title' => 'QTY Consumption + Expired + Refund'
            ],
            [
                'data' => 'idr_sum_all',
                'title' => 'IDR Consumption + Expired + Refund',
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
            ->with('customers', 'branches', 'products.categories', 'consumptions.history.branches')
            ->whereRelation('customers', 'customer_id', 'ILIKE', '%'.$idFilter.'%')
            ->whereRelation('customers', 'name', 'ILIKE', '%'.$nameFilter.'%')
            ->whereRelation('branches', 'name', 'ILIKE', '%'.$branchFilter.'%')
            ->whereBetween('buy_date', [$buyDateStart, $buyDateEnd])
            ->get();

        $recordsTotal = count($columnsRecord);

        $data = AdvanceReceive::orderBy('buy_date', 'asc')
            ->with('customers', 'branches', 'products.categories', 'consumptions.history.branches')
            ->whereRelation('customers', 'customer_id', 'ILIKE', '%'.$idFilter.'%')
            ->whereRelation('customers', 'name', 'ILIKE', '%'.$nameFilter.'%')
            ->whereRelation('branches', 'name', 'ILIKE', '%'.$branchFilter.'%')
            ->whereBetween('buy_date', [$buyDateStart, $buyDateEnd])
            ->offset($request['start'] ?? 0)
            ->limit($request['length'] ?? 10)
            ->get();

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
                'qty_sum_all' => $advanceReceive->qty_sum_all ?? "-",
                'idr_sum_all' => $this->formatNumberPrice($advanceReceive->idr_sum_all) ?? "-",
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
            'recordsTotal' => intval($recordsTotal),
            'recordsFiltered' => intval($recordsTotal),
            'data' => $consumptionRow,
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
            'qty' => 'numeric|required|min:1',
            'unit-price' => 'required',
            'product' => 'required',
            'category' => 'required',
        ]);

        $regex = "/[,]/";
        $buyPrice = preg_replace($regex, '', $validator['buy-price']);
        $netSales = preg_replace($regex, '', $validator['net-sales']);
        $tax = preg_replace($regex, '', $validator['tax']);
        $unitPrice = preg_replace($regex, '', $validator['unit-price']);

        /* check expired date */
        $expiredDate = date($validator['expired-date']);
        if ($expiredDate > date("Y-m-d")) {
            $status = "AVAILABLE";
        } else {
            $status = "EXPIRED";
        }

        /* get customer id */
        $customer =  Customer::where('customer_id', 'ILIKE' , '%'.$validator['customer-id'].'%')->first();

        /* write to db */
        $advanceReceive = new AdvanceReceive();
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
            'qty' => 'numeric|required|min:1',
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
        if (($currDate < $expiredDate) &&  ($advanceReceive->qty_remains > 0 || $advanceReceive->qty_remains == null)) {
            $status = "AVAILABLE";
            $qtyExpired = 0;
            $idrExpired = $qtyExpired * $unitPrice;
        } else {
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

    private function formatNumberPrice($n) {
        $explodePrice = explode('.', $n)[0];
        return explode('.', preg_replace("/\B(?=(\d{3})+(?!\d))/", ",", $n))[0];
    }
}
