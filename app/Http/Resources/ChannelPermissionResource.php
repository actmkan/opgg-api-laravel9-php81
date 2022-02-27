<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ChannelPermissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'type' => $this->type,
            'grade_id' => $this->grade_id,
            'is_writer' => (bool) $this->is_writer
        ];
    }

}
