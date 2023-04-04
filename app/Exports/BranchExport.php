<?php

namespace App\Exports;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BranchExport implements FromQuery, WithHeadings, WithMapping
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
        return Branch::orderBy('name', 'asc')
            ->where('name', 'ILIKE',  '%'.$this->filter['name-filter'].'%')
            ->where('branch', 'ILIKE',  '%'.$this->filter['branch-filter'].'%')
            ->where('address', 'ILIKE',  '%'.$this->filter['address-filter'].'%')
            ->where('telephone', 'ILIKE',  '%'.$this->filter['telephone-filter'].'%')
            ->where('npwp', 'ILIKE',  '%'.$this->filter['npwp-filter'].'%')
            ->where('company', 'ILIKE',  '%'.$this->filter['company-filter'].'%');
    }

    public function headings(): array
    {
        // TODO: Implement headings() method.
        return [
            'name',
            'branch',
            'address',
            'No.Telp',
            'NPWP',
            'Company',
        ];
    }

    public function map($row): array
    {
        // TODO: Implement map() method.
        return [
            $row->name,
            $row->branch,
            $row->address,
            $row->telephone,
            $row->npwp,
            $row->company
        ];
    }
}
