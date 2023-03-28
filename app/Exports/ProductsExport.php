<?php

namespace App\Exports;

use App\Models\Products;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ProductsExport implements FromView
{
    public function view(): View
    {
        $objects=Products::where('is_archived',0)->get();
        return view('exports.export-product', [
            'objects' => $objects,
        ]);
    }
}
