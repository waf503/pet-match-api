<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'nombre'      => ['required', 'string', 'max:100'],
            'especie'     => ['required', 'in:Perro,Gato,Otro'],
            'raza'        => ['nullable', 'string', 'max:100'],
            'edad'        => ['nullable', 'integer', 'min:0', 'max:30'],
            'descripcion' => ['nullable', 'string', 'max:500'],
            'fotos'       => ['required', 'array', 'min:1', 'max:3'],
            'fotos.*'     => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }
}