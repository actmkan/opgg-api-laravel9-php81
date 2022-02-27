<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $comment = [
            'id' => $this->id,
            'article_id' => $this->article_id,
            'user_nickname' => $this->user->nickname,
            'content' => $this->content,
            'like_count' => $this->like_count,
            'unlike_count' => $this->unlike_count,
            'on_like' => (bool)($this->on_like_count ?? 0),
            'on_unlike' => (bool)($this->on_unlike_count ?? 0),
            'created_at' => $this->created_at,
            'childes' => [],
            'created_at_string' => Carbon::now()->sub($this->created_at)->diffForHumans()
        ];

        //자식 n+1 방지
        if($this->parent_id === null){
            $comment['childes'] = new CommentResourceCollection($this->childes);
        }

        return $comment;
    }

}
