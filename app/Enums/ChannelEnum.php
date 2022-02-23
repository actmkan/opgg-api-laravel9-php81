<?php

namespace App\Enums;

enum ChannelEnum
{
    case OPGG;
    case USER_NEWS;
    case TIP;
    case FREE;
    case HUMOR;
    case VIDEO;
    case FIND_USER;
    case ISSUE;
    case FAN_ART;
    case ESPORTS;
    case QNA;


    public function group(): string
    {
        return match($this)
        {
            self::OPGG, self::USER_NEWS, self::TIP
                => 'information',
            self::FREE, self::HUMOR, self::VIDEO, self::FIND_USER, self::ISSUE, self::FAN_ART, self::ESPORTS, self::QNA
                => 'community'
        };
    }

    public function displayGroup(): string
    {
        return match($this)
        {
            self::OPGG, self::USER_NEWS, self::TIP
                => '정보',
            self::FREE, self::HUMOR, self::VIDEO, self::FIND_USER, self::ISSUE, self::FAN_ART, self::ESPORTS, self::QNA
                => '커뮤니티'
        };
    }

    public function name(): string
    {
        return match($this)
        {
            self::OPGG => 'opgg',
            self::USER_NEWS => 'user-news',
            self::TIP => 'tip',
            self::FREE => 'free',
            self::HUMOR => 'humor',
            self::VIDEO => 'video',
            self::FIND_USER => 'find-user',
            self::ISSUE => 'issue',
            self::FAN_ART => 'fan-art',
            self::ESPORTS => 'esports',
            self::QNA => 'qna',
        };
    }

    public function displayName(): string
    {
        return match($this)
        {
            self::OPGG => 'OP.GG 기획',
            self::USER_NEWS => '유저 뉴스',
            self::TIP => '팁과 노하우',
            self::FREE => '자유',
            self::HUMOR => '유머',
            self::VIDEO => '영상',
            self::FIND_USER => '유저 찾기',
            self::ISSUE => '사건 사고',
            self::FAN_ART => '팬 아트',
            self::ESPORTS => 'e스포츠',
            self::QNA => '질문답변',
        };
    }

    public function permissions(): array
    {
        return match($this)
        {
            self::OPGG => [
                [ChannelPermissionEnum::ARTICLE_LIST, null, 0],
                [ChannelPermissionEnum::ARTICLE_READ, null, 0],
                [ChannelPermissionEnum::ARTICLE_CREATE, GradeEnum::ADMIN, 0],
                [ChannelPermissionEnum::ARTICLE_UPDATE, GradeEnum::ADMIN, 1],
                [ChannelPermissionEnum::ARTICLE_DELETE, GradeEnum::ADMIN, 1],
                [ChannelPermissionEnum::COMMENT_LIST, null, 0],
                [ChannelPermissionEnum::COMMENT_CREATE, GradeEnum::IRON, 0],
                [ChannelPermissionEnum::COMMENT_UPDATE, GradeEnum::ADMIN, 1],
                [ChannelPermissionEnum::COMMENT_DELETE, GradeEnum::ADMIN, 1],
                [ChannelPermissionEnum::LIKE, GradeEnum::IRON, 0],
                [ChannelPermissionEnum::UNLIKE, GradeEnum::IRON, 0],
            ],
            self::USER_NEWS, self::TIP, self::QNA, self::ESPORTS, self::FAN_ART,
            self::ISSUE, self::FIND_USER, self::VIDEO, self::HUMOR, self::FREE => [
                [ChannelPermissionEnum::ARTICLE_LIST, null, 0],
                [ChannelPermissionEnum::ARTICLE_READ, null, 0],
                [ChannelPermissionEnum::ARTICLE_CREATE, GradeEnum::IRON, 0],
                [ChannelPermissionEnum::ARTICLE_UPDATE, GradeEnum::IRON, 1],
                [ChannelPermissionEnum::ARTICLE_DELETE, GradeEnum::IRON, 1],
                [ChannelPermissionEnum::COMMENT_LIST, null, 0],
                [ChannelPermissionEnum::COMMENT_CREATE, GradeEnum::IRON, 0],
                [ChannelPermissionEnum::COMMENT_UPDATE, GradeEnum::IRON, 1],
                [ChannelPermissionEnum::COMMENT_DELETE, GradeEnum::IRON, 1],
                [ChannelPermissionEnum::LIKE, GradeEnum::IRON, 0],
                [ChannelPermissionEnum::UNLIKE, GradeEnum::IRON, 0],
            ],
        };
    }
}
