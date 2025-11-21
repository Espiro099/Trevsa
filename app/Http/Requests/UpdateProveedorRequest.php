<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateProveedorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('proveedores.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            'nombre_empresa' => ['required', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'cantidad_unidades' => ['nullable', 'integer', 'min:0'],
            'tipos_unidades' => ['required', 'array', 'min:1'],
            'tipos_unidades.*' => ['string', 'max:255'],
            'cantidades_unidades' => ['required', 'array'],
            'cantidades_unidades.*' => ['nullable', 'integer', 'min:1'],
            'base_linea_transporte' => ['nullable', 'string', 'max:255'],
            'corredor_linea_transporte' => ['nullable', 'string', 'max:255'],
            'nombre_quien_registro' => ['nullable', 'string', 'max:255'],
            'notas' => ['nullable', 'string'],
            'estado_prospecto' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre_empresa.required' => 'El nombre de la empresa es obligatorio.',
            'tipos_unidades.required' => 'Debes seleccionar al menos un tipo de unidad.',
            'tipos_unidades.min' => 'Debes seleccionar al menos un tipo de unidad.',
            'cantidades_unidades.required' => 'Debes indicar la cantidad para cada tipo de unidad seleccionado.',
            'cantidades_unidades.*.min' => 'Cada cantidad de unidad debe ser al menos 1.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $tipos = $this->input('tipos_unidades', []);
            $cantidades = $this->input('cantidades_unidades', []);

            if (is_array($tipos)) {
                foreach ($tipos as $tipo) {
                    $cantidad = $cantidades[$tipo] ?? null;
                    if (is_null($cantidad) || (int) $cantidad < 1) {
                        $validator->errors()->add("cantidades_unidades.$tipo", "La cantidad para '$tipo' debe ser al menos 1.");
                    }
                }
            }
        });
    }
}


