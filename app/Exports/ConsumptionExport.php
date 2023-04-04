<?php

namespace App\Exports;

use App\Models\AdvanceReceive;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ConsumptionExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;
    public $filter;

    public function __construct($filter)
    {
        $this->filter = $filter;
    }

    /**
     * @return EloquentBuilder
     */
    public function query()
    {
        $filter = $this->filter;
        // TODO: Implement query() method.
        return AdvanceReceive::query()->orderBy('buy_date', 'asc')
            ->with('customers', 'branches', 'products.categories', 'consumptions.history.branches', 'refund_branches')
            ->whereRelation('customers', 'customer_id', 'ILIKE', '%'.$this->filter['id-filter'] ?? ''.'%')
            ->whereRelation('customers', 'name', 'ILIKE', '%'.$this->filter['name-filter'] ?? ''.'%')
            ->whereRelation('consumptions', function (\Illuminate\Database\Eloquent\Builder $query)  use($filter) {
                if ($filter['branch-filter'] != "") {
                    $query->whereRelation('history', function (Builder $q) use($filter) {
                        if ($filter['branch-filter'] !=  "") {
                            $q
                                ->where( 'branch_id', $filter['branch-filter'])
                                ->whereBetween('consumption_date', [$this->filter['start-consumption-date'], $this->filter['end-consumption-date']]);
                        } else {
                            $q->whereBetween('consumption_date', [$this->filter['start-consumption-date'], $this->filter['end-consumption-date']]);
                        }
                    });
                }
            });
    }

    public function headings(): array
    {
        // TODO: Implement headings() method.
        return  [
            'Cabang Penjualan',
            'Tanggal Penualan',
            'Expired Date',
            'ID Customer',
            'Nama Customer',
            'Tipe',
            'IDR Harga Beli',
            'IDR Net Sale',
            'IDR PPN',
            'Pembayaran',
            'QTY Produk',
            'Produk',
            'Memo/Model',
            'Category Produk',
            'Notes',
            'Tanggal Pemakaian Ke-1',
            'Cabang Pemakaian Ke-1',
            'Tanggal Pemakaian Ke-2',
            'Cabang Pemakaian Ke-2',
            'Tanggal Pemakaian Ke-3',
            'Cabang Pemakaian Ke-3',
            'Tanggal Pemakaian Ke-4',
            'Cabang Pemakaian Ke-4',
            'Tanggal Pemakaian Ke-5',
            'Cabang Pemakaian Ke-5',
            'Tanggal Pemakaian Ke-6',
            'Cabang Pemakaian Ke-6',
            'Tanggal Pemakaian Ke-7',
            'Cabang Pemakaian Ke-7',
            'Tanggal Pemakaian Ke-8',
            'Cabang Pemakaian Ke-8',
            'Tanggal Pemakaian Ke-9',
            'Cabang Pemakaian Ke-9',
            'Tanggal Pemakaian Ke-10',
            'Cabang Pemakaian Ke-10',
            'Tanggal Pemakaian Ke-11',
            'Cabang Pemakaian Ke-11',
            'Tanggal Pemakaian Ke-12',
            'Cabang Pemakaian Ke-12',
            'QTY Total Consumption Advance Receive',
            'IDR Total Consumption Advance Receive',
            'QTY Expired Advance Receive',
            'QTY Refund Advance Receive',
            'QTY Total Sisa Advance Receive',
            'IDR Total Sisa Advance Receive'
        ];
    }

    public function map($advanceReceive): array
    {
        // TODO: Implement map() method.
        $advanceReceiveConsumption = [];
        $advanceReceiveData = [
            $advanceReceive->branches[0]->name,
            $advanceReceive->buy_date,
            $advanceReceive->expired_date,
            $advanceReceive->customers[0]->customer_id,
            $advanceReceive->customers[0]->name,
            ucfirst(strtolower($advanceReceive->type)),
            $this->formatNumberPrice($advanceReceive->buy_price),
            $this->formatNumberPrice($advanceReceive->net_sale),
            $this->formatNumberPrice($advanceReceive->tax),
            $advanceReceive->payment,
            $advanceReceive->qty,
            $advanceReceive->products[0]->name,
            $advanceReceive->memo,
            $advanceReceive->products[0]->categories[0]->name,
            $advanceReceive->notes == "" ? '-' : $advanceReceive->notes,
        ];

        $advanceReceiveReport = [
            $advanceReceive->qty_total ?? '-',
            $advanceReceive->idr_total != null ? $this->formatNumberPrice($advanceReceive->idr_total): '-',
            $advanceReceive->qty_expired ?? "-",
            $advanceReceive->qty_refund ?? "-",
            $advanceReceive->qty_remains ?? "-",
            $advanceReceive->idr_remains ? $this->formatNumberPrice($advanceReceive->idr_remains) : "-"
        ];

        for ($i = 0; $i < 12 ; $i++) {
            $usedCount = $advanceReceive->consumptions[0]->history->count();
            if ($i < $usedCount) {
                $history = $advanceReceive->consumptions[0]->history[$i];
                $advanceReceiveConsumption[] = $history->consumption_date;
                $advanceReceiveConsumption[] = $history->branches[0]->name;
            }else {
                if (($advanceReceive->status == "EXPIRED") &&  ($advanceReceive->qty > $i )) {
                    $consumptionDate = 'EXPIRED';
                    $consumptionBranch = $advanceReceive->branches[0]->name;
                }else if (($advanceReceive->status == "REFUND") &&  ($advanceReceive->qty > $i )) {
                    $consumptionDate = 'REFUND';
                    $consumptionBranch = $advanceReceive->refund_branches[0]->name;
                } else {
                    $consumptionDate = '-';
                    $consumptionBranch = '-';
                }
                $advanceReceiveConsumption[] = $consumptionDate;
                $advanceReceiveConsumption[] = $consumptionBranch;
            }
        }

        $data = array_merge(array_merge($advanceReceiveData, $advanceReceiveConsumption), $advanceReceiveReport);

        return $data;
    }

    private function formatNumberPrice($n)
    {
        $explodePrice = explode('.', $n)[0];
        return explode('.', preg_replace("/\B(?=(\d{3})+(?!\d))/", ",", $n))[0];
    }
}
