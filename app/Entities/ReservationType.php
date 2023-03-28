<?php

namespace App\Entities;

use App\Interfaces\Enum;

class ReservationType extends Enum
{
    const DOCTOR = 1;
    const ANALYSIS = 2;
    const RADIOLOGY = 3;
    const DOCTOR_CONSULTATION = 4;
    const PHARMACY_CONSULTATION = 5;
    const MEETING = 6;
    const MEDICAL_SERVICES = 7;
}
