<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CommentRequest extends FormRequest
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
            
            'post_id'=>'required',
            'file' => 'mimes:jpg,png,docs,txt,mp4,pdf,ppt|max:10000',
            'body' => 'required|string|between:2,100',
        ];
    }

    public function failedValidation(Validator $val){
        throw new HttpResponseException(response()->json($val->errors()->toJson(),400));
    }
}
