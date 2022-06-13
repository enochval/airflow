<?php

namespace App\Enums;

enum AccountTypesEnum: string
{
    use PHP8BaseEnum;

    case VIRTUAL_ACCOUNT = 'virtual_account';
}
