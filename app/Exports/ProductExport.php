<?php

namespace App\Exports;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductExport implements FromQuery, WithHeadings, WithMapping
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
        return Product::orderBy('name', 'asc')
            ->with('categories')
            ->whereRelation('categories', 'name', 'ILIKE', '%'.$this->filter['category-filter'].'%')
            ->where('name', 'ILIKE', '%'.$this->filter['product-filter'].'%')
            ->where('product_id', 'ILIKE', '%'.$this->filter['id-filter'].'%');
    }

    public function headings(): array
    {
        // TODO: Implement headings() method.
        return [
            'Product ID',
            'Category',
            'Name'
        ];
    }

    public function map($row): array
    {
        // TODO: Implement map() method.
        return [
            $row->product_id,
            $row->categories[0]->name,
            $row->name
        ];
    }
}
