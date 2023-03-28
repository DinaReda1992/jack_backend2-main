<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromCollection;

class SupplierExport implements FromView
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function view(): View
    {
        $objects = User::where('user_type_id', 3)
            ->with('region', 'state', 'supplier', 'products')
            ->where(['is_archived' => 0, 'approved' => 1])
            ->orderBy('id', 'asc')->get();
        return view('exports.export-suppliers', [
            'objects' => $objects,
        ]);
    }
}
