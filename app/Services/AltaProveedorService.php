<?php

namespace App\Services;

use App\Http\Requests\StoreAltaProveedorRequest;
use App\Models\Proveedor;
use App\Models\TransporteProveedor;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AltaProveedorService
{
    private const BASE_PATH = 'altas_proveedores/';
    private const STORAGE_DISK = 'public';

    private array $multipleFileFields = [
        'contrato_files',
        'ine_dueno_files',
        'rfc_consta_files',
        'seguro_unidades_files',
        'tarjetas_circulacion_files',
        'ine_conductor_files',
        'licencia_federal_files',
        'foto_tractor_files',
        'foto_caja_files',
        'repuve_files',
    ];

    private array $singleFileFields = [
        'formato_alta_file',
        'comprobante_domicilio_file',
        'cuenta_bancaria_file',
    ];

    /**
     * Procesa la alta o actualización de un proveedor.
     *
     * @return array{mensaje:string, prospecto_actualizado:bool}
     */
    public function guardarAlta(Proveedor $prospecto, StoreAltaProveedorRequest $request): array
    {
        $validated = $request->validated();
        $prospectoActualizado = $this->actualizarProspecto($prospecto, $request);

        $cantidades = $this->filtrarCantidadesUnidades($validated);
        $alta = $prospecto->altaProveedor;
        $archivos = $this->procesarArchivos($request, $alta);
        if ($alta) {
            $this->actualizarAltaExistente($alta, $prospecto, $validated, $cantidades, $archivos, $request);
        } else {
            $this->crearNuevaAlta($prospecto, $validated, $cantidades, $archivos, $request);
        }

        if ($prospecto->estado_prospecto !== 'alta') {
            $prospecto->estado_prospecto = 'en_proceso';
            $prospecto->save();
        }

        $mensaje = 'Datos de alta guardados correctamente.';
        if ($prospectoActualizado) {
            $mensaje .= ' La información del prospecto también ha sido actualizada.';
        }

        return [
            'mensaje' => $mensaje,
            'prospecto_actualizado' => $prospectoActualizado,
        ];
    }

    private function actualizarProspecto(Proveedor $prospecto, StoreAltaProveedorRequest $request): bool
    {
        $datos = [];

        $map = [
            'prospecto_nombre_empresa' => 'nombre_empresa',
            'prospecto_telefono' => 'telefono',
            'prospecto_email' => 'email',
            'prospecto_cantidad_unidades' => 'cantidad_unidades',
            'prospecto_base_linea_transporte' => 'base_linea_transporte',
            'prospecto_corredor_linea_transporte' => 'corredor_linea_transporte',
        ];

        foreach ($map as $input => $attribute) {
            if ($request->exists($input)) {
                $value = $request->input($input);
                $datos[$attribute] = $attribute === 'cantidad_unidades' ? (int) ($value ?? 0) : $value;
            }
        }

        if ($request->has('unidades')) {
            $datos['tipos_unidades'] = $request->input('unidades', []);
        }

        $cantidades = $this->filtrarCantidadesUnidades($request->validated());
        if (!empty($cantidades)) {
            $datos['cantidades_unidades'] = $cantidades;
        } elseif ($request->has('unidades')) {
            $datos['cantidades_unidades'] = [];
        }

        if (empty($datos)) {
            return false;
        }

        Log::info('Actualizando prospecto', ['prospecto_id' => $prospecto->_id, 'data' => $datos]);
        $prospecto->fill($datos);
        $resultado = $prospecto->save();

        if (!$resultado) {
            Log::warning('No se pudo actualizar el prospecto', ['prospecto_id' => $prospecto->_id]);
        }

        $prospecto->refresh();

        return $resultado;
    }

    private function filtrarCantidadesUnidades(array $validated): array
    {
        $cantidadesFiltradas = [];

        $unidades = Arr::get($validated, 'unidades', []);
        $cantidades = Arr::get($validated, 'cantidades_unidades', []);

        if (!is_array($unidades) || !is_array($cantidades)) {
            return $cantidadesFiltradas;
        }

        foreach ($unidades as $tipo) {
            if (isset($cantidades[$tipo]) && (int) $cantidades[$tipo] > 0) {
                $cantidadesFiltradas[$tipo] = (int) $cantidades[$tipo];
            }
        }

        return $cantidadesFiltradas;
    }

    private function procesarArchivos(StoreAltaProveedorRequest $request, ?TransporteProveedor $alta = null): array
    {
        $resultados = [];

        foreach ($this->multipleFileFields as $campo) {
            $resultados[$campo] = [];
            if ($request->hasFile($campo)) {
                foreach ($request->file($campo) as $file) {
                    if ($file->isValid()) {
                        $resultados[$campo][] = $file->store(self::BASE_PATH . $campo, self::STORAGE_DISK);
                    }
                }
            }
        }

        foreach ($this->singleFileFields as $campo) {
            $resultados[$campo] = null;
            if ($request->hasFile($campo)) {
                $file = $request->file($campo);
                if ($file->isValid()) {
                    if ($alta && !empty($alta->$campo)) {
                        Storage::disk(self::STORAGE_DISK)->delete($alta->$campo);
                    }
                    $resultados[$campo] = $file->store(self::BASE_PATH . $campo, self::STORAGE_DISK);
                }
            }
        }

        return $resultados;
    }

    private function actualizarAltaExistente(
        TransporteProveedor $alta,
        Proveedor $prospecto,
        array $validated,
        array $cantidades,
        array $archivos,
        StoreAltaProveedorRequest $request
    ): void {
        $modelData = [];

        if ($request->has('unidades')) {
            $modelData['unidades'] = $validated['unidades'] ?? [];
        } else {
            $modelData['unidades'] = $alta->unidades ?? [];
        }

        if (!empty($cantidades)) {
            $modelData['cantidades_unidades'] = $cantidades;
        } elseif ($request->has('unidades')) {
            $modelData['cantidades_unidades'] = [];
        } else {
            $modelData['cantidades_unidades'] = $alta->cantidades_unidades ?? [];
        }

        if ($request->has('unidades_otros')) {
            $modelData['unidades_otros'] = $validated['unidades_otros'] ?? null;
        } else {
            $modelData['unidades_otros'] = $alta->unidades_otros ?? null;
        }

        foreach ($this->multipleFileFields as $campo) {
            if (!empty($archivos[$campo])) {
                $modelData[$campo] = array_merge($alta->$campo ?? [], $archivos[$campo]);
            } else {
                $modelData[$campo] = $alta->$campo ?? [];
            }
        }

        foreach ($this->singleFileFields as $campo) {
            $modelData[$campo] = $archivos[$campo] ?? $alta->$campo ?? null;
        }

        if (isset($validated['prospecto_nombre_empresa'])) {
            $modelData['nombre_solicita'] = $validated['prospecto_nombre_empresa'];
        } else {
            $modelData['nombre_solicita'] = $alta->nombre_solicita ?? $prospecto->nombre_empresa;
        }

        $alta->update($modelData);
    }

    private function crearNuevaAlta(
        Proveedor $prospecto,
        array $validated,
        array $cantidades,
        array $archivos,
        StoreAltaProveedorRequest $request
    ): TransporteProveedor {
        $modelData = [
            'proveedor_id' => $prospecto->_id,
            'nombre_solicita' => $prospecto->nombre_empresa,
            'unidades' => $validated['unidades'] ?? $prospecto->tipos_unidades ?? [],
            'cantidades_unidades' => !empty($cantidades) ? $cantidades : ($prospecto->cantidades_unidades ?? []),
            'unidades_otros' => $validated['unidades_otros'] ?? null,
            'status' => 'pendiente',
            'created_by' => Auth::id(),
        ];

        foreach ($this->multipleFileFields as $campo) {
            $modelData[$campo] = $archivos[$campo] ?? null;
        }

        foreach ($this->singleFileFields as $campo) {
            $modelData[$campo] = $archivos[$campo] ?? null;
        }

        return TransporteProveedor::create($modelData);
    }
}

