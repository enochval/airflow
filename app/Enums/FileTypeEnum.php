<?php

namespace App\Enums;

enum FileTypeEnum: string
{
    use PHP8BaseEnum;

    case AVATAR = 'avatar';
    case BULK_UPLOAD = 'bulk_upload';
}
