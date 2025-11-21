<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreServicioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('registro.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            'cliente_id' => ['nullable', 'string'],
            'cliente_nombre' => ['required', 'string', 'max:255'],
            'proveedor_id' => ['nullable', 'string'],
            'proveedor_nombre' => ['nullable', 'string', 'max:255'],
            'tipo_transporte' => ['required', 'string', 'max:255'],
            'tipo_carga' => ['nullable', 'string', 'max:255'],
            'peso_carga' => ['nullable', 'numeric'],
            'origen' => ['required', 'string', 'max:255'],
            'destino' => ['required', 'string', 'max:255'],
            'fecha_servicio' => ['required', 'date'],
            'hora_servicio' => ['nullable', 'string', 'max:50'],
            'tarifa_cliente' => ['nullable', 'numeric'],
            'tarifa_proveedor' => ['nullable', 'numeric'],
            'distancia_km' => ['nullable', 'numeric', 'min:0'],
            'costo_diesel' => ['nullable', 'numeric', 'min:0'],
            'margen_calculado' => ['nullable', 'numeric'],
            'estado' => ['nullable', 'string', 'max:255'],
            'comentarios' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'cliente_nombre.required' => 'El nombre del cliente es obligatorio.',
            'tipo_transporte.required' => 'El tipo de transporte es obligatorio.',
            'origen.required' => 'El origen es obligatorio.',
            'destino.required' => 'El destino es obligatorio.',
            'fecha_servicio.required' => 'La fecha del servicio es obligatoria.',
            'fecha_servicio.date' => 'La fecha del servicio debe tener un formato válido.',
        ];
    }
}


