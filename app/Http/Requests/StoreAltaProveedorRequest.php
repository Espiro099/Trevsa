<?php

namespace App\Http\Requests;

use App\Rules\ValidFileMime;
use Illuminate\Foundation\Http\FormRequest;

class StoreAltaProveedorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('altas.manage') ?? false;
    }

    public function rules(): array
    {
        $documentMimes = $this->documentMimes();
        $imageMimes = $this->imageMimes();
        $cadMimes = $this->cadMimes();
        $presentationMimes = $this->presentationMimes();

        return [
            'prospecto_nombre_empresa' => 'nullable|string|max:255',
            'prospecto_telefono' => 'nullable|string|max:50',
            'prospecto_email' => 'nullable|email|max:255',
            'prospecto_cantidad_unidades' => 'nullable|integer|min:0',
            'prospecto_base_linea_transporte' => 'nullable|string|max:255',
            'prospecto_corredor_linea_transporte' => 'nullable|string|max:255',
            'unidades' => 'nullable|array',
            'unidades.*' => 'string',
            'cantidades_unidades' => 'nullable|array',
            'unidades_otros' => 'nullable|string|max:500',
            'contrato_files' => 'nullable|array|max:5',
            'contrato_files.*' => [
                'file',
                'max:10240',
                'mimes:pdf,doc,docx,txt,rtf,xls,xlsx,csv,jpeg,png,gif',
                new ValidFileMime(array_merge($documentMimes, $imageMimes), 'El archivo de contrato no tiene un formato permitido.'),
            ],
            'formato_alta_file' => [
                'nullable',
                'file',
                'max:10240',
                'mimes:pdf,doc,docx,xls,xlsx,csv,txt',
                new ValidFileMime($documentMimes, 'El formato de alta debe ser un documento válido.'),
            ],
            'ine_dueno_files' => 'nullable|array|max:5',
            'ine_dueno_files.*' => [
                'file',
                'max:102400',
                'mimes:pdf,doc,docx,txt,xls,xlsx,csv,jpeg,png,gif',
                new ValidFileMime(array_merge($documentMimes, $imageMimes), 'El archivo de INE no es válido.'),
            ],
            'rfc_consta_files' => 'nullable|array|max:5',
            'rfc_consta_files.*' => [
                'file',
                'max:102400',
                'mimes:pdf,doc,docx,txt,xls,xlsx,csv,jpeg,png,gif',
                new ValidFileMime(array_merge($documentMimes, $imageMimes), 'El archivo RFC/Constancia no es válido.'),
            ],
            'comprobante_domicilio_file' => [
                'nullable',
                'file',
                'max:10240',
                'mimes:pdf,doc,docx,xls,xlsx,csv,jpeg,png,gif',
                new ValidFileMime(array_merge($documentMimes, $imageMimes), 'El comprobante de domicilio debe ser un documento o imagen válido.'),
            ],
            'cuenta_bancaria_file' => [
                'nullable',
                'file',
                'max:10240',
                'mimes:pdf,doc,docx,xls,xlsx,csv,jpeg,png,gif,dwg,dxf',
                new ValidFileMime(array_merge($documentMimes, $imageMimes, $cadMimes), 'El archivo de cuenta bancaria no es válido.'),
            ],
            'seguro_unidades_files' => 'nullable|array|max:5',
            'seguro_unidades_files.*' => [
                'file',
                'max:10240',
                'mimes:pdf,doc,docx,txt,xls,xlsx,csv,jpeg,png,gif,dwg,dxf',
                new ValidFileMime(array_merge($documentMimes, $imageMimes, $cadMimes), 'El archivo de seguro no es válido.'),
            ],
            'tarjetas_circulacion_files' => 'nullable|array|max:10',
            'tarjetas_circulacion_files.*' => [
                'file',
                'max:10240',
                'mimes:pdf,doc,docx,xls,xlsx,csv,jpeg,png,gif,dwg,dxf',
                new ValidFileMime(array_merge($documentMimes, $imageMimes, $cadMimes), 'El archivo de tarjeta de circulación no es válido.'),
            ],
            'ine_conductor_files' => 'nullable|array|max:5',
            'ine_conductor_files.*' => [
                'file',
                'max:10240',
                'mimes:pdf,doc,docx,xls,xlsx,csv,jpeg,png,gif,dwg,dxf',
                new ValidFileMime(array_merge($documentMimes, $imageMimes, $cadMimes), 'El archivo de INE del conductor no es válido.'),
            ],
            'licencia_federal_files' => 'nullable|array|max:5',
            'licencia_federal_files.*' => [
                'file',
                'max:10240',
                'mimes:pdf,doc,docx,xls,xlsx,csv,jpeg,png,gif,dwg,dxf',
                new ValidFileMime(array_merge($documentMimes, $imageMimes, $cadMimes), 'El archivo de licencia federal no es válido.'),
            ],
            'foto_tractor_files' => 'nullable|array|max:5',
            'foto_tractor_files.*' => [
                'image',
                'max:10240',
                new ValidFileMime($imageMimes, 'La foto del tractor debe ser una imagen válida.'),
            ],
            'foto_caja_files' => 'nullable|array|max:5',
            'foto_caja_files.*' => [
                'image',
                'max:10240',
                new ValidFileMime($imageMimes, 'La foto de la caja debe ser una imagen válida.'),
            ],
            'repuve_files' => 'nullable|array|max:10',
            'repuve_files.*' => [
                'file',
                'max:10240',
                'mimes:pdf,ppt,pptx',
                new ValidFileMime(array_merge($documentMimes, $presentationMimes), 'El archivo REPUVE no es válido.'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'contrato_files.*.file' => 'El archivo de contrato debe ser un archivo válido.',
            'contrato_files.*.mimes' => 'El archivo de contrato debe ser de tipo: pdf, doc, docx, txt, xls, xlsx, csv, jpeg, png, gif.',
            'formato_alta_file.mimes' => 'El formato de alta debe ser de tipo: pdf, doc, docx, xls, xlsx, csv o txt.',
            'ine_dueno_files.*.mimes' => 'Los archivos de INE deben ser de tipo permitido (pdf, doc, docx, txt, xls, xlsx, csv, jpeg, png, gif).',
            'ine_dueno_files.*.max' => 'Cada archivo de INE del dueño no puede superar 100MB.',
            'rfc_consta_files.*.mimes' => 'Los archivos de RFC/constancia deben ser de tipo permitido.',
            'comprobante_domicilio_file.mimes' => 'El comprobante de domicilio debe ser de tipo permitido.',
            'cuenta_bancaria_file.mimes' => 'La cuenta bancaria debe ser de tipo permitido.',
            'seguro_unidades_files.*.mimes' => 'Los seguros de unidades deben ser de tipo permitido.',
            'tarjetas_circulacion_files.*.mimes' => 'Las tarjetas de circulación deben ser de tipo permitido.',
            'ine_conductor_files.*.mimes' => 'Los archivos de INE del conductor deben ser de tipo permitido.',
            'licencia_federal_files.*.mimes' => 'Las licencias federales deben ser de tipo permitido.',
            'foto_tractor_files.*.image' => 'Las fotos de tractor deben ser imágenes válidas.',
            'foto_caja_files.*.image' => 'Las fotos de caja deben ser imágenes válidas.',
            'repuve_files.*.mimes' => 'Los archivos REPUVE deben ser de tipo pdf, ppt o pptx.',
        ];
    }

    private function documentMimes(): array
    {
        return [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain',
            'text/csv',
            'application/csv',
            'application/rtf',
            'text/rtf',
        ];
    }

    private function imageMimes(): array
    {
        return [
            'image/jpeg',
            'image/png',
            'image/gif',
        ];
    }

    private function cadMimes(): array
    {
        return [
            'image/vnd.dwg',
            'application/acad',
            'image/x-dwg',
            'application/dxf',
            'image/vnd.dxf',
        ];
    }

    private function presentationMimes(): array
    {
        return [
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        ];
    }
}

