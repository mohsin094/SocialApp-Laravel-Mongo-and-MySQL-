<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class PostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'caption' => 'required|string|between:2,100',
            'body'=> 'required|string|max:1000',
            'file' => 'mimes:jpg,png,docs,txt,mp4,pdf,ppt|max:10000',
            'visibile'=>'boolean',
        ];
    }

    public function failedValidation(Validator $v){
        throw new HttpResponseException(response()->json( $v->errors()->toJson(),400));
    }
}
