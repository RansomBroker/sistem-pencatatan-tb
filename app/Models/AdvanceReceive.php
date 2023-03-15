<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvanceReceive extends Model
{
    use HasFactory;

    public function products()
    {
        return $this->hasMany(Product::class, 'id', 'product_id');
    }

    public function branches()
    {
        return $this->hasMany(Branch::class, 'id', 'branch_id');
    }

    public function customers()
    {
        return $this->hasMany(Customer::class, 'id', 'customer_id');
    }

    public function consumptions()
    {
        return $this->hasMany(Consumption::class, 'id', 'consumption_id');
    }

    public function refund_branches()
    {
        return $this->hasMany(Branch::class, 'id', 'branch_id');
    }
}
