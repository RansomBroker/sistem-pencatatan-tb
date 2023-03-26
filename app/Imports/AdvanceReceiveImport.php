<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
class AdvanceReceiveImport implements ToCollection, WithCalculatedFormulas
{
    public function collection(Collection $collection)
    {
        // TODO: Implement collection() method.
    }
}
