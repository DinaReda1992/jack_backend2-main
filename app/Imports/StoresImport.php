<?php

namespace App\Imports;

use App\Models\Stores;
use Maatwebsite\Excel\Concerns\ToModel;

class StoresImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {

        $store = new Stores();
        $store->name = $row[0];
        $store->name_en = $row[1];
        $store->address = $row[2];
        $store->address_en = $row[3];
        $store->category_id = $row[4];
        $store->latitude = $row[5];
        $store->longitude = $row[6];


        return $store;

    }
}
