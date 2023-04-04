<?php

namespace App\Exports;

use App\Models\AdvanceReceive;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExpiredExport implements FromQuery, WithHeadings, WithMapping
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
        // TODO: Implement query() method.
        return AdvanceReceive::with('customers', 'branches', 'products.categories')
            ->whereRelation('customers', 'customer_id', 'ILIKE', '%'.$this->filter['id-filter'].'%')
            ->whereRelation('customers', 'name', 'ILIKE', '%'.$this->filter['name-filter'].'%')
            ->whereRelation('branches', function (Builder $query) {
                if ($this->filter['branch-filter']  != null ){
                    $query->where('id', $this->filter['branch-filter']);
                }
            })
            ->whereBetween('expired_date', [$this->filter['start-expired-date'], $this->filter['end-expired-date']])
            ->where('status', $this->filter['status-filter'] ?? 'EXPIRED');
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
            'QTY Produk',
            'Produk',
            'Memo/Model',
            'Category Produk',
            'Notes',
            'QTY Expired Advance Receive',
            'IDR Expired Advance Receive',
            'QTY Total Sisa Advance Receive',
            'IDR Total Sisa Advance Receive'
        ];
    }

    public function map($advanceReceive): array
    {
        // TODO: Implement map() method.
        return [
            $advanceReceive->branches[0]->name,
            $advanceReceive->buy_date,
            $advanceReceive->expired_date,
            $advanceReceive->customers[0]->customer_id,
            $advanceReceive->customers[0]->name,
            Str::ucfirst($advanceReceive->type),
            $advanceReceive->qty,
            $advanceReceive->products[0]->name,
            $advanceReceive->memo,
            $advanceReceive->products[0]->categories[0]->name,
            $advanceReceive->notes == "" ? '-' : $advanceReceive->notes,
            $advanceReceive->qty_expired ?? "-",
            $advanceReceive->idr_expired ? $this->formatNumberPrice($advanceReceive->idr_expired) : "-",
            $advanceReceive->qty_remains ?? "-",
            $advanceReceive->idr_remains ? $this->formatNumberPrice($advanceReceive->idr_remains) : "-"
        ];
    }

    private function formatNumberPrice($n)
    {
        $explodePrice = explode('.', $n)[0];
        return explode('.', preg_replace("/\B(?=(\d{3})+(?!\d))/", ",", $n))[0];
    }
}
