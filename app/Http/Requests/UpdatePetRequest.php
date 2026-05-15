<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'nombre'              => ['sometimes', 'required', 'string', 'max:100'],
            'especie'             => ['sometimes', 'required', 'in:Perro,Gato,Otro'],
            'raza'                => ['nullable', 'array'],
            'raza.*'              => ['string', 'max:150'],
            'edad'                => ['nullable', 'integer', 'min:0', 'max:30'],
            'descripcion'         => ['nullable', 'string', 'max:500'],
            'fotos'               => ['nullable', 'array', 'max:3'],
            'fotos.*'             => ['image', 'mimes:jpg,jpeg,png,webp,heic,heif', 'max:5120'],
            'delete_photo_ids'    => ['nullable', 'array'],
            'delete_photo_ids.*'  => ['integer'],
        ];
    }
}