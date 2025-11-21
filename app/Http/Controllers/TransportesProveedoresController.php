<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransporteProveedor;
use Illuminate\Validation\ValidationException;

class TransportesProveedoresController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Usar _id para ordenar en MongoDB, que siempre funciona
        $proveedores = TransporteProveedor::orderBy('_id', 'desc')->paginate(10);
        return view('transportes_proveedores.index', compact('proveedores'));
    }

    public function show($id)
    {
        $proveedor = TransporteProveedor::findOrFail($id);
        return view('transportes_proveedores.show', compact('proveedor'));
    }

    public function create()
    {
        return view('transportes_proveedores.create');
    }

    public function buscarProveedor(Request $request)
    {
        $term = $request->get('term');
        $proveedores = TransporteProveedor::buscarPorNombre($term);
        return response()->json($proveedores);
    }

    public function obtenerUnidades(Request $request)
    {
        $proveedorId = $request->get('proveedor_id');
        $proveedor = TransporteProveedor::find($proveedorId);
        $unidades = $proveedor ? $proveedor->obtenerUnidades() : [];
        return response()->json($unidades);
    }

    public function store(Request $request)
    {
        try {
            $rules = [
                'nombre_solicita' => 'required|string|max:255',
                'unidades' => 'nullable|array',
                'unidades.*' => 'string',
                'unidades_otros' => 'nullable|string|max:500',
                
                // files validations
                'contrato_files' => 'nullable|array|max:5',
                'contrato_files.*' => 'file|mimes:pdf,doc,docx,txt,rtf,xls,xlsx,csv,jpeg,png,gif|mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,image/jpeg,image/png,image/gif,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,text/plain|max:10240',

                'formato_alta_file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,csv,txt|max:10240',

                'ine_dueno_files' => 'nullable|array|max:5',
                'ine_dueno_files.*' => 'file|mimes:pdf,doc,docx,txt,xls,xlsx,csv,jpeg,png,gif|mimetypes:application/pdf,image/jpeg,image/png,image/gif,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,text/plain,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet|max:102400',

                'rfc_consta_files' => 'nullable|array|max:5',
                'rfc_consta_files.*' => 'file|mimes:pdf,doc,docx,txt,xls,xlsx,csv,jpeg,png,gif|max:102400',

                'comprobante_domicilio_file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,csv,jpeg,png,gif|max:10240',

                'cuenta_bancaria_file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,csv,jpeg,png,gif,dwg,dxf|max:10240',

                'seguro_unidades_files' => 'nullable|array|max:5',
                'seguro_unidades_files.*' => 'file|mimes:pdf,doc,docx,txt,xls,xlsx,csv,jpeg,png,gif,dwg,dxf|max:10240',

                'tarjetas_circulacion_files' => 'nullable|array|max:10',
                'tarjetas_circulacion_files.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,csv,jpeg,png,gif,dwg,dxf|max:10240',

                'ine_conductor_files' => 'nullable|array|max:5',
                'ine_conductor_files.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,csv,jpeg,png,gif,dwg,dxf|max:10240',

                'licencia_federal_files' => 'nullable|array|max:5',
                'licencia_federal_files.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,csv,jpeg,png,gif,dwg,dxf|max:10240',

                'foto_tractor_files' => 'nullable|array|max:5',
                'foto_tractor_files.*' => 'image|max:10240',

                'foto_caja_files' => 'nullable|array|max:5',
                'foto_caja_files.*' => 'image|max:10240',
            ];

            // Validar con mensajes personalizados
            $messages = [
                'nombre_solicita.required' => 'El campo nombre solicita es obligatorio.',
                'contrato_files.*.file' => 'El archivo de contrato debe ser un archivo válido.',
                'contrato_files.*.mimes' => 'El archivo de contrato debe ser de tipo: pdf, doc, docx, txt, xls, xlsx, csv, jpeg, png, gif.',
                'comprobante_domicilio_file.file' => 'El archivo de comprobante de domicilio debe ser un archivo válido.',
                'comprobante_domicilio_file.mimes' => 'El archivo de comprobante de domicilio debe ser de tipo: pdf, doc, docx, xls, xlsx, csv, jpeg, png, gif.',
                'cuenta_bancaria_file.file' => 'El archivo de cuenta bancaria debe ser un archivo válido.',
                'cuenta_bancaria_file.mimes' => 'El archivo de cuenta bancaria debe ser de tipo: pdf, doc, docx, xls, xlsx, csv, jpeg, png, gif, dwg, dxf.',
            ];
            
            $data = $request->validate($rules, $messages);

            // Handle file uploads
            $storeArray = [];
            $fieldsMultiple = [
                'contrato_files', 'ine_dueno_files', 'rfc_consta_files', 'seguro_unidades_files', 'tarjetas_circulacion_files', 'ine_conductor_files', 'licencia_federal_files', 'foto_tractor_files', 'foto_caja_files'
            ];
            foreach ($fieldsMultiple as $f) {
                $storeArray[$f] = [];
                if ($request->hasFile($f)) {
                    foreach ($request->file($f) as $file) {
                        if (!$file->isValid()) continue;
                        $path = $file->store('transportes_proveedores/' . $f, 'public');
                        $storeArray[$f][] = $path;
                    }
                }
            }

            $singleFiles = ['formato_alta_file', 'comprobante_domicilio_file', 'cuenta_bancaria_file'];
            foreach ($singleFiles as $sf) {
                $storeArray[$sf] = null;
                if ($request->hasFile($sf)) {
                    $file = $request->file($sf);
                    if ($file->isValid()) {
                        $storeArray[$sf] = $file->store('transportes_proveedores/' . $sf, 'public');
                    }
                }
            }

            // Prepare model data
            $modelData = [
                'nombre_solicita' => $data['nombre_solicita'] ?? null,
                'unidades' => $data['unidades'] ?? null,
                'unidades_otros' => $data['unidades_otros'] ?? null,
                'contrato_files' => $storeArray['contrato_files'] ?? null,
                'formato_alta_file' => $storeArray['formato_alta_file'] ?? null,
                'ine_dueno_files' => $storeArray['ine_dueno_files'] ?? null,
                'rfc_consta_files' => $storeArray['rfc_consta_files'] ?? null,
                'comprobante_domicilio_file' => $storeArray['comprobante_domicilio_file'] ?? null,
                'cuenta_bancaria_file' => $storeArray['cuenta_bancaria_file'] ?? null,
                'seguro_unidades_files' => $storeArray['seguro_unidades_files'] ?? null,
                'tarjetas_circulacion_files' => $storeArray['tarjetas_circulacion_files'] ?? null,
                'ine_conductor_files' => $storeArray['ine_conductor_files'] ?? null,
                'licencia_federal_files' => $storeArray['licencia_federal_files'] ?? null,
                'foto_tractor_files' => $storeArray['foto_tractor_files'] ?? null,
                'foto_caja_files' => $storeArray['foto_caja_files'] ?? null,
            ];

            $modelData['created_by'] = auth()->id();
            $modelData['status'] = $modelData['status'] ?? 'pendiente';
            $tp = TransporteProveedor::create($modelData);

            return redirect()->route('transportes_proveedores.index')->with('success', 'Registro de transporte/proveedor creado correctamente.');
        } catch (ValidationException $e) {
            // Las excepciones de validación se manejan automáticamente por Laravel
            // Solo necesitamos relanzarlas para que se muestren los errores correctamente
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Error al guardar transporte proveedor: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Hubo un error al guardar el registro. Por favor, intenta nuevamente. Error: ' . $e->getMessage()]);
        }
    }
}
