<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TalkResource extends JsonResource
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
            'banner_image' => new ImageResource($this->whenLoaded('banner_image')),
            'logo_image' => new ImageResource($this->whenLoaded('logo_image')),
            'background_image' => new ImageResource($this->whenLoaded('background_image')),
            'name' => $this->name,
            'display_name' => $this->display_name,
        ];
    }

}
