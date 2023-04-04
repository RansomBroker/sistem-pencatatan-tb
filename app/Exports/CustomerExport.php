<?php

namespace App\Exports;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Throwable;

class CustomerExport implements FromQuery, WithHeadings, WithMapping
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
        return Customer::orderBy('name', 'asc')
            ->where('customer_id', 'ILIKE', '%'.$this->filter['id-filter'].'%')
            ->where('name', 'ILIKE', '%'.$this->filter['name-filter'].'%')
            ->where('nickname', 'ILIKE', '%'.$this->filter['nickname-filter'].'%')
            ->where('address', 'ILIKE', '%'.$this->filter['address-filter'].'%')
            ->where('birth_date', 'ILIKE', '%'.$this->filter['birth-filter'].'%')
            ->orWhereNull('birth_date')
            ->where('phone', 'ILIKE', '%'.$this->filter['phone-filter'].'%')
            ->where('identity_number', 'ILIKE', '%'.$this->filter['identity-filter'].'%')
            ->where('payment_number', 'ILIKE', '%'.$this->filter['payment-filter'].'%')
            ->where('email', 'ILIKE', '%'.$this->filter['email-filter'].'%');
    }

    public function headings(): array
    {
        // TODO: Implement headings() method.
        return [
            'ID',
            'Name',
            'Nickname',
            'Alamat',
            'Tanggal Lahir',
            'No.Telp',
            'No.KTP',
            'No.Rekening',
            'Email',
        ];
    }

    public function map($row): array
    {
        // TODO: Implement map() method.
        return [
            $row->customer_id,
            $row->name,
            $row->nickname,
            $row->address,
            $row->birth_date,
            $row->phone,
            $row->identity_number,
            $row->payment_number,
            $row->email
        ];
    }
}
