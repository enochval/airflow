<?php

namespace App\Enums;

trait PHP8BaseEnum
{
    /**
     * Returns enum values as an array.
     */
    public static function valueArray(): array
    {
        $values = [];

        foreach (self::cases() as $enum) {
            $values[] = $enum->value ?? $enum->name;
        }

        return $values;
    }

    /**
     * Returns enum values as a list.
     */
    public static function valueList(string $separator = ', '): string
    {
        return implode($separator, self::valueArray());
    }
}
