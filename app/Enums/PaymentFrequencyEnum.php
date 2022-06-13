<?php

namespace App\Enums;

enum PaymentFrequencyEnum: string
{
    use PHP8BaseEnum;

    case MONTHLY = 'monthly';
    case QUARTERLY = 'quarterly';
    case ANNUALLY = 'annually';
}
