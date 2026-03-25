<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRatingRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'score'         => ['required', 'integer', 'min:1', 'max:5'],
            'comment'       => ['nullable', 'string', 'max:300'],
            'reviewer_name' => ['nullable', 'string', 'max:80'],
        ];
    }

    public function messages()
    {
        return [
            'score.required' => 'Selecciona una calificación.',
            'score.min'      => 'La calificación mínima es 1.',
            'score.max'      => 'La calificación máxima es 5.',
            'comment.max'    => 'El comentario no debe superar 300 caracteres.',
        ];
    }
}
