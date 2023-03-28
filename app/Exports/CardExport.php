<?php

namespace App\Exports;

use App\Models\MrmandoobCardsDetails;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;

class CardExport implements FromQuery, WithTitle
{
    private $type;
    private $mrmandoob_card_id;
    public function __construct(int $type,int $mrmandoob_card_id)
    {

        $this->type = $type;
        $this->mrmandoob_card_id = $mrmandoob_card_id;
    }

    /**
     * @return Builder
     */
    public function query()
    {
        return MrmandoobCardsDetails::query()->select('type','code')->where('type', $this->type)->where('mrmandoob_card_id', $this->mrmandoob_card_id);
    }
    /**
     * @return string
     */
    public function title(): string
    {
        return 'Cards of ' . $this->type;
    }
}