<?php

namespace App\Imports;

use App\Models\AutoPart;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Throwable;

class AutopartImport implements ToModel ,WithHeadingRow, SkipsOnError
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
//        $check=AutoPart::where('name',@$row['name'])->where('name_en',@$row['name_en'])->first();

        if($row['name']!=''&&$row['name_en']!=''){
            return new AutoPart([
                'name'=>@$row['name']?:'',
                'name_en'=>@$row['name_en']?:''
            ]);
        }
    }
    public function onError(Throwable $e)
    {
        // TODO: Implement onError() method.
    }
}
