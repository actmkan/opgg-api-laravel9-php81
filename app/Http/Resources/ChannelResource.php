<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ChannelResource extends JsonResource
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
            'talk_id' => $this->talk_id,
            'group' => $this->group,
            'display_group' => $this->display_group,
            'name' => $this->name,
            'display_name' => $this->display_name,
            'permissions' => collect($this->whenLoaded('permissions'))
                ->keyBy('type')
                ->mapInto(ChannelPermissionResource::class),
        ];
    }

}
