<?php

namespace App\Exports;

use App\Models\Stores;
use Maatwebsite\Excel\Concerns\FromCollection;

class StoresExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Stores::all();
    }
}
