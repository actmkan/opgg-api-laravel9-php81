<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property $id
 * @property string $name
 * @property string $display_name
 * @property integer $banner_image_id
 * @property integer $logo_image_id
 * @property integer $background_image_id
 */
class Talk extends Model
{
    use HasFactory;

    public function banner_image(): HasOne
    {
        return $this->hasOne(Image::class, 'id', 'banner_image_id');
    }

    public function logo_image(): HasOne
    {
        return $this->hasOne(Image::class, 'id', 'logo_image_id');
    }

    public function background_image(): HasOne
    {
        return $this->hasOne(Image::class, 'id', 'background_image_id');
    }
}
