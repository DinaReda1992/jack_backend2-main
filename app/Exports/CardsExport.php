<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class CardsExport implements WithMultipleSheets
{
    use Exportable;

    private $mrmandoob_card_id;

    public function __construct(int $mrmandoob_card_id)
    {
        $this->mrmandoob_card_id=$mrmandoob_card_id;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets=[];
        $cards_types = [25,50,75,100,200];
        foreach ($cards_types as $type ){
            $sheets[] = new CardExport($type,$this->mrmandoob_card_id);

        }

        return $sheets;
    }
}