<?php

namespace App\Enums;

enum ChannelPermissionEnum
{
    case ARTICLE_LIST;
    case ARTICLE_READ;
    case ARTICLE_CREATE;
    case ARTICLE_UPDATE;
    case ARTICLE_DELETE;
    case COMMENT_LIST;
    case COMMENT_CREATE;
    case COMMENT_UPDATE;
    case COMMENT_DELETE;
    case LIKE;
    case UNLIKE;
}
