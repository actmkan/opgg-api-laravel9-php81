<?php

namespace App\Services;

use App\Constants;
use App\Enums\CacheKeyEnum;
use App\Models\Article;
use App\Models\Channel;
use App\Models\Comment;
use App\Models\Talk;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

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
     * @throws ValidationException
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
     * @throws ValidationException
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
            return Channel::where(['talk_id' => $validated['id']])->with(['permissions'])->get();
        });
    }

    /**
     * @param array $attribute
     * @return mixed
     * @throws ValidationException
     */
    public function getArticles(array $attribute): mixed
    {
        $validated = $this->validate(
            $attribute,
            [
                'talkId' => 'required',
                'channelId' => 'required',
                'sort' => 'required',
                'page' => 'required',
                'perPage' => 'required',
                'searchType' => 'string',
                'searchText' => 'string',
            ],
            [
                'talkId.required' => '톡 id는 필수 입력값입니다.',
                'channelId.required' => '채널 id는 필수 입력값입니다.',
                'sort.required' => '정렬형식은 필수 입력값입니다.',
                'page.required' => '페이지는 필수 입력값입니다.',
                'perPage.required' => '제한값은 필수 입력값입니다.',
            ]
        );

        $noticeType = $validated['channelId'] === 'all' ? 'is_all_notice' : 'is_notice';

        $andWhereArr = [
            ['talk_id', '=', $validated['talkId']]
        ];

        $orWhereArr = [
            ['talk_id', '=', $validated['talkId']],
            [$noticeType, '=', true]
        ];

        $query = Article::withCount('comments')->with(['user']);
        if($validated['channelId'] !== 'all'){
            $andWhereArr[] = ['channel_id', '=', $validated['channelId']];
            $orWhereArr[] = ['channel_id', '=', $validated['channelId']];
        }

        $searchText = $validated['searchText'];

        //검색일 경우
        if($searchText){
            match ($validated['searchType']){
                'title' => $andWhereArr[] = ['title', 'like', "%{$validated['searchText']}%"],
                'user_name' => $query = $query->whereHas('user', function ($whereHasQuery) use($searchText) {
                    $whereHasQuery->where('nickname', $searchText);
                })
            };
        }else{
            match ($validated['sort']){
                'hot' => $andWhereArr = array_merge($andWhereArr, [
                    ['like_count', '>=', '20'],
                    ['created_at', '>=', Carbon::now()->subDay(5)],
                ]),
                'boost' => $andWhereArr[] = ['like_count', '>=', '10'],
                default => null
            };
        }

        $query = $query->where($andWhereArr);
        $query = $query->orWhere($orWhereArr);

        //공지사항 맨 위로 보내기
        $query = $query->orderByDesc($noticeType);

        //top 슨일 경우 like_count 순으로 정렬한다.
        if($validated['sort'] === 'top'){
            $query = $query->orderByRaw('like_count - unlike_count desc');
        }

        $query = $query->orderByDesc('id');

        return $query->paginate($validated['perPage']);
    }

    /**
     * @param array $attribute
     * @return array|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     * @throws ValidationException
     */
    public function getArticle(array $attribute): array|null|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
    {
        $validated = $this->validate(
            $attribute,
            [
                'talkId' => 'required',
                'channelId' => 'required',
                'articleId' => 'required',
            ],
            [
                'talkId.required' => '톡 id는 필수 입력값입니다.',
                'channelId.required' => '채널 id는 필수 입력값입니다.',
                'articleId.required' => '글 id는 필수 입력값입니다.'
            ]
        );

        return Article::withCount(['wards' => function($query){
                $query->where('user_id', Auth::guard('sanctum')->id());
            }])
            ->with(['user', 'channel', 'channel.permissions'])
            ->where([
                ['talk_id', $validated['talkId']],
                ['channel_id', $validated['channelId']],
            ])
            ->find($validated['articleId']);

    }

    /**
     * @param array $attribute
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     * @throws ValidationException
     */
    public function getComments(array $attribute): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $validated = $this->validate(
            $attribute,
            [
                'talkId' => 'required',
                'channelId' => 'required',
                'articleId' => 'required',
                'sort' => 'required',
                'page' => 'required',
                'perPage' => 'required',
            ],
            [
                'talkId.required' => '글 id는 필수 입력값입니다.',
                'channelId.required' => '글 id는 필수 입력값입니다.',
                'articleId.required' => '글 id는 필수 입력값입니다.',
                'sort.required' => '정렬형식은 필수 입력값입니다.',
                'page.required' => '페이지는 필수 입력값입니다.',
                'perPage.required' => '제한값은 필수 입력값입니다.',
            ]
        );

        $query = Comment::with(['user', 'childes'])->where([
            ['article_id', '=', $validated['articleId']]
        ])->whereNull('parent_id');

        if($validated['sort'] === 'top'){
            $query = $query->orderByRaw('like_count - unlike_count desc');
        }

        $query = $query->orderByDesc('id');

        return $query->paginate($validated['perPage']);
    }


}
