<?php

namespace App\Services;

use App\Constants;
use App\Enums\CacheKeyEnum;
use App\Models\Channel;
use App\Models\Talk;
use Illuminate\Support\Facades\Cache;

class TalkService extends Service
{
    /**
     * @return mixed
     */
    public function getTalks(): mixed
    {
        return Cache::remember(CacheKeyEnum::GET_TALKS->name, Constants::LIST_CACHE_EXPIRE, function () {
            return Talk::with(['banner_image', 'logo_image', 'background_image'])->get();
        });
    }

    /**
     * @param array $attribute
     * @return mixed
     * @throws \Illuminate\Validation\ValidationException
     */
    public function getTalk(array $attribute): mixed
    {
        $validated = $this->validate(
            $attribute,
            [
                'id' => 'required'
            ],
            [
                'id.required' => '톡 id는 필수 입력값입니다.'
            ]
        );

        return Cache::remember(CacheKeyEnum::GET_TALK->name . "-" . $validated['id'], Constants::SHOW_CACHE_EXPIRE, function () use ($validated) {
            return Talk::with(['banner_image', 'logo_image', 'background_image'])->find($validated['id']);
        });
    }

    /**
     * @param array $attribute
     * @return mixed
     * @throws \Illuminate\Validation\ValidationException
     */
    public function getChannels(array $attribute): mixed
    {
        $validated = $this->validate(
            $attribute,
            [
                'id' => 'required'
            ],
            [
                'id.required' => '톡 id는 필수 입력값입니다.'
            ]
        );

        return Cache::remember(CacheKeyEnum::GET_CHANNELS->name . "-" . $validated['id'], Constants::LIST_CACHE_EXPIRE, function () use ($validated) {
            return Channel::where(['talk_id' => $validated['id']])->get();
        });
    }
}
