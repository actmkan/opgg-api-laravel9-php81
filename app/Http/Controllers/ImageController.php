<?php

namespace App\Http\Controllers;

use App\Http\Resources\ImageResource;
use App\Services\ImageService;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    private ImageService $imageService;

    /**
     * @param ImageService $imageService
     */
    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     * @throws \App\Exceptions\NotFoundResourceException
     */
    public function checkHash(Request $request): ImageResource
    {
        return new ImageResource($this->imageService->checkHash($request->all()));
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function upload(Request $request): ImageResource
    {
        return new ImageResource($this->imageService->upload($request->all()));
    }
}
