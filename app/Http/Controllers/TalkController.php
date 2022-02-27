<?php

namespace App\Http\Controllers;

use App\Http\Resources\ArticleResource;
use App\Http\Resources\ArticleResourceCollection;
use App\Http\Resources\ChannelResourceCollection;
use App\Http\Resources\CommentResource;
use App\Http\Resources\CommentResourceCollection;
use App\Http\Resources\TalkResource;
use App\Http\Resources\TalkResourceCollection;
use App\Services\TalkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

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

    /**
     * @param \Illuminate\Http\Request $request
     * @param int $talkId
     * @param int|string $channelId
     * @return \App\Http\Resources\ArticleResourceCollection
     * @throws \Illuminate\Validation\ValidationException
     */
    public function getArticles(Request $request, int|string $talkId, int|string $channelId): ArticleResourceCollection
    {
        return new ArticleResourceCollection($this->talkService->getArticles([
            'talkId' => $talkId,
            'channelId' => $channelId,
            'sort' => $request->get('sort') ?? 'new',
            'page' => $request->get('page') ?? 1,
            'perPage' => $request->get('per_page') ?? 30,
            'searchType' => $request->get('search_type') ?? '',
            'searchText' => $request->get('search_text') ?? '',
        ]));
    }

    /**
     * @param int $talkId
     * @param int $channelId
     * @param int $articleId
     * @return \App\Http\Resources\ArticleResource
     * @throws \Illuminate\Validation\ValidationException
     */
    public function getArticle(int $talkId, int $channelId, int $articleId): ArticleResource
    {
        return new ArticleResource($this->talkService->getArticle([
            'talkId' => $talkId,
            'channelId' => $channelId,
            'articleId' => $articleId,
        ]));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param int|string $talkId
     * @param int|string $channelId
     * @param int|string $articleId
     * @return \App\Http\Resources\CommentResourceCollection
     * @throws \Illuminate\Validation\ValidationException
     */
    public function getComments(Request $request, int|string $talkId, int|string $channelId, int|string $articleId): CommentResourceCollection
    {
        return new CommentResourceCollection($this->talkService->getComments([
            'talkId' => $talkId,
            'channelId' => $channelId,
            'articleId' => $articleId,
            'sort' => $request->get('sort') ?? 'new',
            'page' => $request->get('page') ?? 1,
            'perPage' => $request->get('per_page') ?? 30,
        ]));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\CommentResource
     * @throws \Illuminate\Validation\ValidationException
     */
    public function createComment(Request $request): CommentResource
    {
        return new CommentResource($this->talkService->createComment($request->all()));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\ArticleResource
     * @throws \Illuminate\Validation\ValidationException
     * @throws \App\Exceptions\ManyRequestException
     */
    public function createArticle(Request $request): ArticleResource
    {
        return new ArticleResource($this->talkService->createArticle($request->all()));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\ArticleResource
     * @throws \App\Exceptions\BadRequestException
     * @throws \App\Exceptions\ManyRequestException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function updateArticle(Request $request): ArticleResource
    {
        return new ArticleResource($this->talkService->updateArticle($request->all()));
    }

    /**
     * @param int|string $articleId
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException|\App\Exceptions\BadRequestException
     */
    public function deleteArticle(int|string $articleId): JsonResponse
    {
        $this->talkService->deleteArticle(['articleId' => $articleId]);
        return response()->json(true);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException|\App\Exceptions\ManyRequestException
     */
    public function like(Request $request): JsonResponse
    {
        $this->talkService->like($request->all());
        return response()->json(true);
    }
}
