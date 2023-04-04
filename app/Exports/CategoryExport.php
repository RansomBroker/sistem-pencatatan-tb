<?php

namespace App\Exports;

use App\Models\Category;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CategoryExport implements FromQuery, WithHeadings, WithMapping
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
        return Category::orderBy('name', 'asc')
            ->where('name', 'ILIKE', '%'.$this->filter['name-filter'].'%');
    }

    public function headings(): array
    {
        // TODO: Implement headings() method.
        return [
            'Name'
        ];
    }

    public function map($row): array
    {
        // TODO: Implement map() method.
        return [
          $row->name
        ];
    }
}
