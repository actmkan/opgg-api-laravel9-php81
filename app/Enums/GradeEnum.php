<?php

namespace App\Enums;

enum GradeEnum
{
    case IRON;
    case BRONZE;
    case SILVER;
    case GOLD;
    case PLATINUM;
    case DIAMOND;
    case MASTER;
    case GRANDMASTER;
    case CHALLENGER;
    case ADMIN;

    use GetIdTrait;

    public function name(): string
    {
        return match($this)
        {
            self::IRON => 'iron',
            self::BRONZE => 'bronze',
            self::SILVER => 'silver',
            self::GOLD => 'gold',
            self::PLATINUM => 'platinum',
            self::DIAMOND => 'diamond',
            self::MASTER => 'master',
            self::GRANDMASTER => 'grandmaster',
            self::CHALLENGER => 'challenger',
            self::ADMIN => 'admin',
        };
    }

    public function displayName(): string
    {
        return match($this)
        {
            self::IRON => '아이언',
            self::BRONZE => '브론즈',
            self::SILVER => '실버',
            self::GOLD => '골드',
            self::PLATINUM => '플래티넘',
            self::DIAMOND => '다이아몬드',
            self::MASTER => '마스터',
            self::GRANDMASTER => '그랜드마스터',
            self::CHALLENGER => '챌린저',
            self::ADMIN => '관리자',
        };
    }

    public function point(): int|null
    {
        return match($this)
        {
            self::IRON => 0,
            self::BRONZE => 100,
            self::SILVER => 1000,
            self::GOLD => 5000,
            self::PLATINUM => 10000,
            self::DIAMOND => 20000,
            self::MASTER => 50000,
            self::GRANDMASTER => 100000,
            self::CHALLENGER => 200000,
            self::ADMIN => null,
        };
    }
}
