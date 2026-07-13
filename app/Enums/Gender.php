<?php

namespace App\Enums;

enum Gender: int
{
    case Male = 1;
    case Female = 2;
    case Other = 3;

    public function label(): string
    {
        return match($this) {
            self::Male => '男性',
            self::Female => '女性',
            self::Other => 'その他',
        };
    }
}