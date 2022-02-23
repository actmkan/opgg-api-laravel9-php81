<?php

namespace App\Http\Controllers;

use App\Http\Resources\ChannelResourceCollection;
use App\Http\Resources\TalkResource;
use App\Http\Resources\TalkResourceCollection;
use App\Services\TalkService;

class TalkController extends Controller
{
    private TalkService $talkService;

    /**
     * @param TalkService $talkService
     */
    public function __construct(TalkService $talkService)
    {
        $this->talkService = $talkService;
    }

    /**
     * @return \App\Http\Resources\TalkResourceCollection
     */
    public function getTalks(): TalkResourceCollection
    {
        return new TalkResourceCollection($this->talkService->getTalks());
    }

    /**
     * @param int $id
     * @return \App\Http\Resources\TalkResource
     * @throws \Illuminate\Validation\ValidationException
     */
    public function getTalk(int $id): TalkResource
    {
        return new TalkResource($this->talkService->getTalk(['id' => $id]));
    }

    /**
     * @param int $id
     * @return \App\Http\Resources\ChannelResourceCollection
     * @throws \Illuminate\Validation\ValidationException
     */
    public function getChannels(int $id): ChannelResourceCollection
    {
        return new ChannelResourceCollection($this->talkService->getChannels(['id' => $id]));
    }
}
