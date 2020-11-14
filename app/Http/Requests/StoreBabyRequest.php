<?php

namespace App\Http\Requests;

use App\Models\Baby;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreBabyRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('baby_create');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
            ],
        ];
    }
}
