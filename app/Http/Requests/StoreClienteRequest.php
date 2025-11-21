<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('clientes.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            'nombre_empresa' => ['required', 'string', 'max:255'],
            'nombre_contacto' => ['nullable', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'ciudad' => ['nullable', 'string', 'max:255'],
            'estado' => ['nullable', 'string', 'max:255'],
            'industria' => ['nullable', 'string', 'max:255'],
            'comentarios' => ['nullable', 'string'],
            'estado_prospecto' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre_empresa.required' => 'El nombre de la empresa es obligatorio.',
            'nombre_empresa.max' => 'El nombre de la empresa no puede exceder 255 caracteres.',
            'email.email' => 'El correo electrónico debe tener un formato válido.',
        ];
    }
}


