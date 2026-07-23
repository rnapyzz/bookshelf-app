<?php

namespace App\Enums;

enum ReviewComment: string
{
    case One = 'あまりおすすめできない...';
    case Two = 'いまいちでした';
    case Three = '普通です';
    case Four = '参考になりました';
    case Five = 'とても良かったです';

    /**
     * 評価を整数からラベルに変換する
     */
    public static function fromInt(int $rating): string
    {
        return match ($rating) {
            1 => self::One->value,
            2 => self::Two->value,
            4 => self::Four->value,
            5 => self::Five->value,
            default => self::Three->value,
        };
    }
}
