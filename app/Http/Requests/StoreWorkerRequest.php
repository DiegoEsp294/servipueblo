<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWorkerRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'category_ids'    => ['required', 'array', 'min:1', 'max:5'],
            'category_ids.*'  => ['required', 'integer', 'exists:categories,id'],
            'type'            => ['nullable', 'in:worker,entrepreneur'],
            'name'            => ['required', 'string', 'max:120'],
            'description'     => ['nullable', 'string', 'max:500'],
            'rate_info'       => ['nullable', 'string', 'max:100'],
            'years_experience' => ['nullable', 'integer', 'min:1', 'max:60'],
            'availability'     => ['nullable', 'in:available,on_request,unavailable'],
            'phone'           => ['required', 'string', 'max:20'],
            'email'           => ['nullable', 'email', 'max:150'],
            'town'           => ['required', 'string', 'max:100'],
            'photo'          => ['nullable', 'image', 'max:10240'],
            'is_active'      => ['nullable', 'boolean'],
        ];
    }

    public function messages()
    {
        return [
            'years_experience.integer' => 'Los años de experiencia deben ser un número.',
            'years_experience.min'     => 'Mínimo 1 año de experiencia.',
            'years_experience.max'     => 'Máximo 60 años de experiencia.',
            'category_ids.required'  => 'Seleccioná al menos un oficio.',
            'category_ids.min'       => 'Seleccioná al menos un oficio.',
            'category_ids.max'       => 'Podés seleccionar hasta 5 oficios.',
            'category_ids.*.exists'  => 'Una de las categorías seleccionadas no es válida.',
            'name.required'          => 'El nombre es obligatorio.',
            'name.max'               => 'El nombre no puede superar 120 caracteres.',
            'phone.required'         => 'El teléfono es obligatorio.',
            'town.required'          => 'El pueblo es obligatorio.',
            'photo.image'            => 'El archivo debe ser una imagen.',
            'photo.max'              => 'La imagen no debe superar 10MB.',
        ];
    }
}
