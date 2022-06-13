<?php

namespace App\Enums;

enum PaymentTypesEnum: string
{
    case NET = 'net';
    case PENSION = 'pension';
    case NHF = 'nhf';
    case TAX = 'tax';
    case NSITF = 'nsitf';
    case ITF = 'itf';
}
