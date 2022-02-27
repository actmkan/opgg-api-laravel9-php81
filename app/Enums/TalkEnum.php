<?php

namespace App\Enums;


enum TalkEnum
{
    case LOL;
    case PUBG;
    case OW;
    case R6;

    use GetIdTrait;

    public function channels(): array
    {
        return match($this)
        {
            self::LOL => [
                ChannelEnum::OPGG,
                ChannelEnum::USER_NEWS,
                ChannelEnum::TIP,
                ChannelEnum::FREE,
                ChannelEnum::HUMOR,
                ChannelEnum::VIDEO,
                ChannelEnum::FIND_USER,
                ChannelEnum::ISSUE,
                ChannelEnum::FAN_ART,
            ],
            self::PUBG => [
                ChannelEnum::USER_NEWS,
                ChannelEnum::TIP,
                ChannelEnum::FREE,
                ChannelEnum::ESPORTS,
                ChannelEnum::QNA,
                ChannelEnum::FIND_USER,
                ChannelEnum::VIDEO,
            ],
            self::OW => [
                ChannelEnum::USER_NEWS,
                ChannelEnum::TIP,
                ChannelEnum::FREE,
                ChannelEnum::QNA,
                ChannelEnum::FIND_USER,
                ChannelEnum::VIDEO,
                ChannelEnum::FAN_ART,
            ],
            self::R6 => [
                ChannelEnum::USER_NEWS,
                ChannelEnum::TIP,
                ChannelEnum::FREE,
                ChannelEnum::QNA,
                ChannelEnum::VIDEO,
            ],
        };
    }

    public function name(): string
    {
        return match($this)
        {
            self::LOL => 'lol',
            self::PUBG => 'pubg',
            self::OW => 'ow',
            self::R6 => 'r6',
        };
    }

    public function displayName(): string
    {
        return match($this)
        {
            self::LOL => '리그오브레전드',
            self::PUBG => '배틀그라운드',
            self::OW => '오버워치',
            self::R6 => '레인보우 식스 시즈',
        };
    }

    public function backgroundImage(): string
    {
        return resource_path() . "/images/talk/bg/{$this->name()}.jpg";
    }

    public function bannerImage(): string
    {
        return resource_path() . "/images/talk/banner/{$this->name()}.jpg";
    }

    public function logoImage(): string
    {
        return resource_path() . "/images/talk/logo/{$this->name()}.png";
    }
}
