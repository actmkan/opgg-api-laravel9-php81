<?php

namespace App\Enums;

enum CacheKeyEnum
{
    case GET_TALKS;
    case GET_TALK;
    case GET_CHANNELS;

    case SHOW_ARTICLE;
    case LIKE_ARTICLE;
    case CREATE_ARTICLE;

    case CREATE_COMMENT;
    case LIKE_COMMENT;
}
