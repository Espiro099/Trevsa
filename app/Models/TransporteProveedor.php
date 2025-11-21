<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

/**
 * Modelo para Transportes Proveedores
 * 
 * Contiene información completa de proveedores de transporte con documentación
 */
class TransporteProveedor extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'transportes_proveedores';
    
    public $timestamps = true;

    protected $fillable = [
        // Relación con Prospecto
        'proveedor_id',
        
        // Relación con User (Transportista)
        'user_id',
        
        // Información básica
        'nombre_solicita',
        
        // Información de unidades
        'unidades',
        'cantidades_unidades',
        'unidades_otros',
        
        // Documentos - Contratos y formatos
        'contrato_files',
        'formato_alta_file',
        
        // Documentos - Identificación
        'ine_dueno_files',
        'rfc_consta_files',
        'comprobante_domicilio_file',
        'cuenta_bancaria_file',
        
        // Documentos - Seguros
        'seguro_unidades_files',
        
        // Documentos - Vehículos
        'tarjetas_circulacion_files',
        'ine_conductor_files',
        'licencia_federal_files',
        'foto_tractor_files',
        'foto_caja_files',
        'repuve_files',
        
        // Estado y control
        'status',
        'created_by',
    ];

    protected $casts = [
        'unidades' => 'array',
        'cantidades_unidades' => 'array',
        'contrato_files' => 'array',
        'ine_dueno_files' => 'array',
        'rfc_consta_files' => 'array',
        'seguro_unidades_files' => 'array',
        'tarjetas_circulacion_files' => 'array',
        'ine_conductor_files' => 'array',
        'licencia_federal_files' => 'array',
        'foto_tractor_files' => 'array',
        'foto_caja_files' => 'array',
        'repuve_files' => 'array',
    ];

    /**
     * Buscar proveedores por nombre de quien solicita
     */
    public static function buscarPorNombre($term)
    {
        return self::where('nombre_solicita', 'like', "%{$term}%")
                   ->select('nombre_solicita', 'unidades', 'status')
                   ->get();
    }

    /**
     * Relación con Proveedor (Prospecto)
     */
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

    /**
     * Relación con User (Transportista)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación con Unidades Disponibles
     */
    public function unidadesDisponibles()
    {
        return $this->hasMany(UnidadDisponible::class, 'transporte_proveedor_id');
    }

    /**
     * Obtener unidades de un proveedor
     */
    public function obtenerUnidades()
    {
        return $this->unidades ?? [];
    }

    /**
     * Verificar si el alta está completa
     */
    public function estaCompleta()
    {
        // Verificar que tenga los documentos esenciales
        $tieneDocumentos = !empty($this->contrato_files) || !empty($this->formato_alta_file);
        $tieneIdentificacion = !empty($this->ine_dueno_files) || !empty($this->rfc_consta_files);
        
        return $tieneDocumentos && $tieneIdentificacion && $this->status === 'alta';
    }

    /**
     * Validar que todos los documentos requeridos estén presentes
     * Retorna un array con los documentos faltantes
     */
    public function validarDocumentosRequeridos()
    {
        $documentosFaltantes = [];

        // Documentos Legales y Contratos (al menos uno de los dos)
        if (empty($this->contrato_files) && empty($this->formato_alta_file)) {
            $documentosFaltantes[] = 'Contrato Firmado o Formato Alta Proveedor';
        }

        // Identificación del Propietario
        if (empty($this->ine_dueno_files)) {
            $documentosFaltantes[] = 'INE del Dueño';
        }

        if (empty($this->rfc_consta_files)) {
            $documentosFaltantes[] = 'RFC o Constancia Situación Fiscal';
        }

        if (empty($this->comprobante_domicilio_file)) {
            $documentosFaltantes[] = 'Comprobante de Domicilio del Dueño';
        }

        if (empty($this->cuenta_bancaria_file)) {
            $documentosFaltantes[] = 'Cuenta Bancaria';
        }

        // Documentos de Seguro
        if (empty($this->seguro_unidades_files)) {
            $documentosFaltantes[] = 'Seguro de Unidades';
        }

        // Documentos Vehiculares
        if (empty($this->tarjetas_circulacion_files)) {
            $documentosFaltantes[] = 'Tarjetas de Circulación (Tractor y Caja o Planas)';
        }

        if (empty($this->ine_conductor_files)) {
            $documentosFaltantes[] = 'INE del Conductor';
        }

        if (empty($this->licencia_federal_files)) {
            $documentosFaltantes[] = 'Licencia Federal Vigente del Conductor';
        }

        if (empty($this->foto_tractor_files)) {
            $documentosFaltantes[] = 'Foto del Tractor con Placa';
        }

        if (empty($this->foto_caja_files)) {
            $documentosFaltantes[] = 'Foto de la Caja o Plataforma con Placa';
        }

        // Unidades (debe tener al menos una unidad seleccionada)
        if (empty($this->unidades) || (is_array($this->unidades) && count($this->unidades) === 0)) {
            $documentosFaltantes[] = 'Tipos de Unidades (debe seleccionar al menos una)';
        }

        return $documentosFaltantes;
    }

    /**
     * Verificar si tiene todos los documentos requeridos
     */
    public function tieneTodosLosDocumentos()
    {
        return empty($this->validarDocumentosRequeridos());
    }

    /**
     * Obtener ID formateado con nomenclatura ALT-xxx
     * Basado en el orden de creación (más antiguo = número menor)
     */
    public function getFormattedIdAttribute()
    {
        // Obtener el número secuencial basado en la posición del registro por fecha de creación
        $count = self::where('created_at', '<=', $this->created_at)
            ->orderBy('created_at', 'asc')
            ->orderBy('_id', 'asc')
            ->count();
        
        return 'ALT-' . str_pad($count, 3, '0', STR_PAD_LEFT);
    }
}

