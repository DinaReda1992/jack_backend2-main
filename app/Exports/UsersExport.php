<?php

namespace App\Exports;

use App\Models\Orders;
use App\Models\User;
use App\Models\UsersRegions;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class  UsersExport  implements FromView
{
    public function view(): View
    {
        $objects=User::where('user_type_id',5)
            ->where(['is_archived'=>0,'approved'=>1,'block'=>0])
            ->when(auth()->user()->user_type_id!=1, function ($query) {
                $query->whereIn('region_id', function ($query)  {
                    $query->select('region_id')
                        ->from(with(new UsersRegions())->getTable())
                        ->where('user_id', auth()->id());
                });
            })
            ->orderBy('id','DESC')->get();
        return view('exports.export-users', [
            'objects' => $objects,
        ]);
    }
}
