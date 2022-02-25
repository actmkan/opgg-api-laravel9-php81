<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'is_notice' => $this->is_notice,
            'user_nickname' => $this->user->nickname,
            'title' => $this->title,
            'content' => $this->content,
            'comment_count' => $this->comments_count,
            'like_count' => $this->like_count,
            'unlike_count' => $this->unlike_count,
            'view_count' => $this->view_count,
            'created_at' => $this->created_at,
            'created_at_string' => Carbon::now()->sub($this->created_at)->diffForHumans()
        ];
    }

}
