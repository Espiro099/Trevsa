<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateUnidadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('unidades.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            'transporte_proveedor_id' => ['required', 'string'],
            'unidades_disponibles' => ['required', 'array', 'min:1', 'max:3'],
            'unidades_disponibles.*' => ['required', 'string', 'max:255'],
            'cantidades_unidades' => ['required', 'array'],
            'cantidades_unidades.*' => ['nullable', 'integer', 'min:1'],
            'unidad_otro_texto' => ['nullable', 'array'],
            'unidad_otro_texto.*' => ['nullable', 'string', 'max:255'],
            'lugar_disponible' => ['nullable', 'string', 'max:255'],
            'fecha_disponible' => ['required', 'date'],
            'hora_disponible' => ['nullable', 'string', 'max:50'],
            'destino_sugerido' => ['nullable', 'string', 'max:255'],
            'notas' => ['nullable', 'string'],
            'estatus' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'transporte_proveedor_id.required' => 'El nombre del transportista es obligatorio.',
            'unidades_disponibles.required' => 'Debe seleccionar al menos una unidad disponible.',
            'unidades_disponibles.min' => 'Debe seleccionar al menos una unidad disponible.',
            'unidades_disponibles.max' => 'Puede seleccionar máximo 3 unidades disponibles.',
            'unidades_disponibles.*.required' => 'Cada unidad seleccionada debe tener un tipo válido.',
            'fecha_disponible.required' => 'La fecha disponible es obligatoria.',
            'fecha_disponible.date' => 'La fecha disponible debe ser válida.',
            'cantidades_unidades.*.integer' => 'La cantidad debe ser un número entero.',
            'cantidades_unidades.*.min' => 'La cantidad debe ser al menos 1.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $unidadesDisponibles = $this->input('unidades_disponibles', []);
            $unidadOtroTexto = $this->input('unidad_otro_texto', []);

            foreach ($unidadesDisponibles as $index => $unidad) {
                if ($unidad === 'Otros') {
                    if (empty($unidadOtroTexto[$index]) || trim($unidadOtroTexto[$index]) === '') {
                        $validator->errors()->add(
                            "unidad_otro_texto.{$index}",
                            'Debe especificar el tipo de unidad cuando selecciona "Otros".'
                        );
                    }
                }
            }
        });
    }
}


