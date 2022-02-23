<?php

namespace App\Services;

use App\Exceptions\NotFoundResourceException;
use App\Models\Image as ImageModel;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ImageService extends Service
{
    /**
     * @param array $attribute
     * @return ImageModel|null
     * @throws \App\Exceptions\NotFoundResourceException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function checkHash(array $attribute): ImageModel|null
    {
        $validated = $this->validate(
            $attribute,
            [
                'hash' => 'required|size:32',
            ],
            [
                'hash.required' => '필수 입력 값이 누락되었습니다.',
                'hash.size' => '파일 해시는 :size자 이어야 합니다.',
            ]
        );

        $duplicate = ImageModel::where(['hash' => $validated['hash']])->first();

        return $duplicate ?? throw new NotFoundResourceException();
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function upload(array $attribute, $forceUpload = false): ImageModel
    {
        $rules = ['type' => 'required'];
        if(!$forceUpload){
            $rules['file'] = 'required|image|mimes:jpg,png,jpeg,gif,webp|max:8192';
        }
        $validated = $this->validate(
            $attribute,
            $rules,
            [
                'file.required' => '필수 입력 값이 누락되었습니다.',
                'file.image' => '이미지 파일만 허용 합니다.',
                'file.mimes' => 'jpg,png,jpeg,gif,webp 파일만 허용 합니다.',
                'file.max' => '8MB 이하 파일만 허용 합니다.',
                'type.required' => '필수 입력 값이 누락되었습니다.',
            ]
        );

        /** @var UploadedFile $file */
        $file = $attribute['file'];
        $type = $validated['type'];
        $hash = md5_file($file);

        $year = now()->format("Y");
        $month = now()->format("m");
        $day = now()->format("d");

        $originalFilePath = "/images/{$type}/original/{$year}/{$month}/{$day}";
        $optimizeFilePath = "/images/{$type}/optimize/{$year}/{$month}/{$day}";
        $filename = "{$hash}.{$file->extension()}";

        try{
            return $this->checkHash(['hash' => md5_file($file)]);
        } catch (NotFoundResourceException){

            $optimizeFile = Image::make($file)->save($file->extension(), 80);
            Storage::disk('cdn')->putFileAs($originalFilePath, $file, $filename);
            Storage::disk('cdn')->putFileAs($optimizeFilePath, $file->extension() === 'gif' ? $file : $optimizeFile->filename, $filename);

            $image = new ImageModel([
                'hash' => $hash,
                'created_user_id' => Auth::id() ?? null,
                'origin_path' => "{$originalFilePath}/{$filename}",
                'optimize_path' => "{$optimizeFilePath}/{$filename}",
                'extension' => $file->extension(),
                'size' => $file->getSize(),
                'width' => $optimizeFile->getWidth(),
                'height' => $optimizeFile->getHeight(),
            ]);

            $image->save();
        }

        return $image;
    }
}
