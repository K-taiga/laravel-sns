<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleRequest extends FormRequest
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
            'title' => 'required|max:50',
            'body'  => 'required|max:500',
            'tags' => 'json|regex:/^(?!.*\s).+$/u|regex:/^(?!.*\/).*$/u',
        ];
    }

    public function attibutes()
    {
        return [
            'title' => 'タイトル',
            'body'  => '本文',
            'tags'  => 'タグ',
        ];
    }

    public function passedValidation()
    {
        // decodeしたあとにcollectしてsliceが使えるようにする
        $this->tags = collect(json_decode($this->tags))
        // sliceで最初の5個の要素を取得
         ->slice(0,5)
        //  slice後の値をmapで$requestTagとして作成
         ->map(function ($requestTag) {
             return $requestTag->text;
         });
    }
}
