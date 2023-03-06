<?php

namespace App\Models;

use App\Http\Controllers\AdvanceReceiveController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consumption extends Model
{
    use HasFactory;

    public function history()
    {
        return $this->hasMany(Consumption::class, 'parent_id', 'id')->orderBy('used_count', 'asc');
    }

    public function branches()
    {
        return $this->hasMany(Branch::class, 'id', 'branch_id');
    }
}
