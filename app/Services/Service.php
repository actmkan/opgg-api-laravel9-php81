<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class Service
{
    /**
     * @param array $attribute
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validate(array $attribute, array $rules, array $messages = [], array $customAttributes = []): array
    {
        $validator = Validator::make(
            $attribute, $rules, $messages, $customAttributes
        );

        if($validator->fails()){
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}
