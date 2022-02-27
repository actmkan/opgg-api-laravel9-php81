<?php

namespace App\Services;

use App\Constants;
use App\Enums\CacheKeyEnum;
use App\Enums\LikeTargetEnum;
use App\Enums\LikeTypeEnum;
use App\Exceptions\BadRequestException;
use App\Exceptions\ManyRequestException;
use App\Models\Article;
use App\Models\ArticleLike;
use App\Models\Channel;
use App\Models\Comment;
use App\Models\CommentLike;
use App\Models\Talk;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
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
                'boost' => $andWhereArr[] = [DB::raw('like_count-unlike_count'), '>=', '10'],
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

        $article = Article::withCount(['wards' => function($query){
            $query->where('user_id', Auth::guard('sanctum')->id());
        }])
            ->withCount(['onLike' => function($query){
                $query->where([
                    ['type', LikeTypeEnum::LIKE->name],
                    ['user_id', Auth::guard('sanctum')->id()],
                ]);
            }])
            ->withCount(['onUnlike' => function($query){
                $query->where([
                    ['type', LikeTypeEnum::UNLIKE->name],
                    ['user_id', Auth::guard('sanctum')->id()],
                ]);
            }])
            ->withCount('comments')
            ->with(['user', 'channel', 'channel.permissions'])
            ->where([
                ['talk_id', $validated['talkId']],
                ['channel_id', $validated['channelId']],
            ])
            ->find($validated['articleId']);

        $cacheKey = CacheKeyEnum::SHOW_ARTICLE->name . "_" . $article->id . "_" . (Request::header('x-forwarded-for') ?? Request::ip());;
        if(!Cache::has($cacheKey)){
            return DB::transaction(function () use ($article, $cacheKey){

                $article->view_count++;
                $article->save();
                Cache::put($cacheKey, true, 86400);

                return $article;
            });
        }

        return $article;

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

        $query = Comment::with(['user', 'childes', 'childes.user'])
            ->withCount(['onLike' => function($query){
                $query->where([
                    ['type', LikeTypeEnum::LIKE->name],
                    ['user_id', Auth::guard('sanctum')->id()],
                ]);
            }])
            ->withCount(['onUnlike' => function($query){
                $query->where([
                    ['type', LikeTypeEnum::UNLIKE->name],
                    ['user_id', Auth::guard('sanctum')->id()],
                ]);
            }])
            ->where([
                ['article_id', '=', $validated['articleId']]
            ])
            ->whereNull('parent_id');

        if($validated['sort'] === 'top'){
            $query = $query->orderByRaw('like_count - unlike_count desc');
        }

        $query = $query->orderByDesc('id');

        return $query->paginate($validated['perPage']);
    }

    /**
     * @param array $attribute
     * @return \App\Models\Comment
     * @throws ValidationException
     * @throws \App\Exceptions\ManyRequestException
     */
    public function createComment(array $attribute): Comment
    {

        $validated = $this->validate(
            $attribute,
            [
                'articleId' => 'required',
                'parentId' => 'nullable',
                'imageId' => 'nullable',
                'content' => 'required|max:1000|min:2'
            ],
            [
                'articleId.required' => '글 id는 필수 입력값입니다.',
                'content.required' => '댓글내용은 필수 입력값입니다.',
                'content.max' => ':max자 이하로 입력해주세요.',
                'content.min' => ':min자 이상으로 입력해주세요.',
            ]
        );


        $cacheKey = CacheKeyEnum::CREATE_COMMENT->name . "_" . Auth::guard('sanctum')->id();
        if(Cache::has($cacheKey)){
            throw new ManyRequestException("댓글 작성은 30초마다 작성 가능합니다.");
        }

        $comment = new Comment();

        $comment->fill([
            'article_id' => $validated['articleId'],
            'user_id' => Auth::guard('sanctum')->id(),
            'parent_id' => $validated['parentId'] ?? null,
            'image_id' => $validated['imageId'] ?? null,
            'content' => strip_tags($validated['content']),
            'like_count' => 0,
            'unlike_count' => 0,
        ]);

        $comment->save();

        Cache::put($cacheKey, true, 30);

        return $comment;
    }

    /**
     * @param array $attribute
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     * @throws \App\Exceptions\BadRequestException
     */
    public function deleteArticle(array $attribute): void
    {

        $validated = $this->validate(
            $attribute,
            [
                'articleId' => 'required',
            ],
            [
                'articleId.required' => '글 id는 필수 입력값입니다.',
            ]
        );

        $article = Article::find($validated['articleId']);

        if(!$article){
            throw new BadRequestException("해당 글은 이미 삭제되었습니다.");
        }

        if($article->user_id !== Auth::guard('sanctum')->id()){
            throw new BadRequestException("본인이 작성한 글만 삭제하실 수 있습니다..");
        }

        $article->delete();
    }

    /**
     * @param array $attribute
     * @return \App\Models\Article
     * @throws ValidationException
     * @throws \App\Exceptions\ManyRequestException
     */
    public function createArticle(array $attribute): Article
    {

        $validated = $this->validate(
            $attribute,
            [
                'talkId' => 'required',
                'channelId' => 'required',
                'title' => 'required|max:50|min:2',
                'content' => 'required|max:10000|min:2'
            ],
            [
                'talkId.required' => '톡 id는 필수 입력값입니다.',
                'channelId.required' => '채널 id는 필수 입력값입니다.',
                'content.required' => '글 내용은 필수 입력값입니다.',
                'content.max' => '글 내용을 :max자 이하로 입력해주세요.',
                'content.min' => '글 내용을 :min자 이상으로 입력해주세요.',
                'title.required' => '제목은 필수 입력값입니다.',
                'title.max' => '제목을 :max자 이하로 입력해주세요.',
                'title.min' => '제목을 :min자 이상으로 입력해주세요.',
            ]
        );


        $cacheKey = CacheKeyEnum::CREATE_ARTICLE->name . "_" . Auth::guard('sanctum')->id();
        if(Cache::has($cacheKey)){
            throw new ManyRequestException("글 작성은 60초마다 작성 가능합니다.");
        }

        $article = new Article([
            'is_notice' => false,
            'is_all_notice' => false,
            'talk_id' => $validated['talkId'],
            'channel_id' => $validated['channelId'],
            'user_id' => Auth::guard('sanctum')->id(),
            'title' => strip_tags($validated['title']),
            'content' => strip_tags($validated['content']),
            'like_count' => 0,
            'unlike_count' => 0,
            'view_count' => 0,
        ]);

        $article->save();

        Cache::put($cacheKey, true, 60);

        return $article;
    }

    /**
     * @param array $attribute
     * @return \App\Models\Article
     * @throws ValidationException
     * @throws \App\Exceptions\ManyRequestException
     * @throws \App\Exceptions\BadRequestException
     */
    public function updateArticle(array $attribute): Article
    {

        $validated = $this->validate(
            $attribute,
            [
                'talkId' => 'required',
                'channelId' => 'required',
                'articleId' => 'required',
                'title' => 'required|max:50|min:2',
                'content' => 'required|max:10000|min:2'
            ],
            [
                'talkId.required' => '톡 id는 필수 입력값입니다.',
                'channelId.required' => '채널 id는 필수 입력값입니다.',
                'articleId.required' => '글 id는 필수 입력값입니다.',
                'content.required' => '글 내용은 필수 입력값입니다.',
                'content.max' => '글 내용을 :max자 이하로 입력해주세요.',
                'content.min' => '글 내용을 :min자 이상으로 입력해주세요.',
                'title.required' => '제목은 필수 입력값입니다.',
                'title.max' => '제목을 :max자 이하로 입력해주세요.',
                'title.min' => '제목을 :min자 이상으로 입력해주세요.',
            ]
        );

        return DB::transaction(function () use ($validated){

            $article = Article::find($validated['articleId']);

            if(!$article){
                throw new BadRequestException("해당 글은 이미 삭제되었습니다.");
            }

            if($article->user_id !== Auth::guard('sanctum')->id()){
                throw new BadRequestException("본인이 작성한 글만 수정하실 수 있습니다.");
            }

            $article->channel_id = $validated['channelId'];
            $article->title = strip_tags($validated['title']);
            $article->content = strip_tags($validated['content']);

            $article->save();
            return $article;
        });

    }

    /**
     * @param array $attribute
     * @return void
     * @throws \App\Exceptions\ManyRequestException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function like(array $attribute): void
    {

        $validated = $this->validate(
            $attribute,
            [
                'targetId' => 'required',
                'target' => 'required',
                'type' => 'required',
            ],
            [
                'targetId.required' => '글 id는 필수 입력값입니다.',
                'target.required' => '글 id는 필수 입력값입니다.',
                'type.required' => '추천 타입은 필수 입력값입니다.',
            ]
        );


        $userId = Auth::guard('sanctum')->id();
        $cacheKey = CacheKeyEnum::LIKE_ARTICLE->name . "_" . $validated['target'] . "_". $userId;
        if(Cache::has($cacheKey)){
            throw new ManyRequestException("추천은 10초마다 변경 가능합니다.");
        }

        Cache::put($cacheKey, true, 10);

        $targetModel = null;

        switch ($validated['target']){
            case LikeTargetEnum::ARTICLE->name:
                $targetModel = new Article();
                $likeModel = new ArticleLike();
                $newLikeModel = new ArticleLike();
                $targetIdName = "article_id";
                break;
            case LikeTargetEnum::COMMENT->name:
                $targetModel = new Comment();
                $likeModel = new CommentLike();
                $newLikeModel = new CommentLike();
                $targetIdName = "comment_id";
                break;
        }


        $likes = $likeModel->where([
            [$targetIdName, '=', $validated['targetId']],
            ['user_id', '=', $userId],
        ])->withTrashed()->get();

        $target = $targetModel->find($validated['targetId']);

        DB::transaction(function () use($validated, $userId, $likes, $target, $newLikeModel, $targetIdName) {

            $newLikeModel->fill([
                $targetIdName => $validated['targetId'],
                'user_id' => $userId,
                'type' => $validated['type']
            ]);

            if($likes->count() === 0){
                $newLikeModel->save();
                $this->likeSyncOnce($validated, $target);
            } elseif ($likes->count() === 1) {
                $like = $likes->first();

                if($like->type === $validated['type']){
                    if($like->deleted_at === null){
                        $like->delete();
                        $this->likeSyncOnceMinus($validated, $target);
                    }else{
                        $like->restore();
                        $this->likeSyncOnce($validated, $target);
                    }
                }else{
                    if($like->deleted_at === null){
                        $like->delete();
                        $newLikeModel->save();
                        $this->likeSyncMultiple($validated, $target);
                    }else{
                        $newLikeModel->save();
                        $this->likeSyncOnce($validated, $target);
                    }
                }

            } elseif ($likes->count() === 2) {
                $likes->map(function ($like) use($validated, $target) {
                    if($like->type === $validated['type']){
                        if($like->deleted_at === null){
                            $like->delete();
                            $this->likeSyncOnceMinus($validated, $target);
                        }else{
                            $like->restore();
                            $this->likeSyncOnce($validated, $target);
                        }
                    }else{
                        if($like->deleted_at === null){
                            $like->delete();
                            $this->likeSyncOnceMinus(['type' => $like->type], $target);
                        }
                    }
                });
            }

            $target->save();
        });
    }

    private function likeSyncOnceMinus($validated, &$target){
        match ($validated['type']){
            LikeTypeEnum::LIKE->name => $target->like_count--,
            LikeTypeEnum::UNLIKE->name => $target->unlike_count--,
            default => null
        };
    }

    private function likeSyncOnce($validated, &$target){
        match ($validated['type']){
            LikeTypeEnum::LIKE->name => $target->like_count++,
            LikeTypeEnum::UNLIKE->name => $target->unlike_count++,
            default => null
        };
    }

    private function likeSyncMultiple($validated, &$target){
        switch ($validated['type']){
            case LikeTypeEnum::LIKE->name:
                $target->like_count++;
                $target->unlike_count--;
                break;
            case LikeTypeEnum::UNLIKE->name:
                $target->like_count--;
                $target->unlike_count++;
                break;
        }
    }
}
