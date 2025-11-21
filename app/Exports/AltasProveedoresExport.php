<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Support\Collection;

class AltasProveedoresExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithDrawings, WithCustomStartCell, WithEvents
{
    protected $prospectos;

    public function __construct($prospectos)
    {
        $this->prospectos = $prospectos;
    }

    public function collection()
    {
        return new Collection($this->prospectos);
    }

    public function startCell(): string
    {
        return 'A6'; // Empezar después del logo y título
    }

    public function headings(): array
    {
        return [
            'ID Alta',
            'ID Prospecto',
            'Nombre Empresa',
            'Teléfono',
            'Email',
            'Cantidad Unidades',
            'Tipos de Unidades',
            'Unidades (Alta)',
            'Unidades Otros',
            'Estado Alta',
            'Nombre Quien Registró',
            'Fecha Registro Prospecto',
            'Fecha Registro Alta',
        ];
    }

    public function map($prospecto): array
    {
        $alta = $prospecto->altaProveedor;
        $formattedIdAlta = $alta ? $alta->formatted_id : 'N/A';
        $formattedIdProv = $prospecto->formatted_id ?? 'N/A';
        
        $tiposUnidades = is_array($prospecto->tipos_unidades) 
            ? implode(', ', $prospecto->tipos_unidades) 
            : ($prospecto->tipos_unidades ?? 'N/A');
        
        $unidadesAlta = $alta && is_array($alta->unidades)
            ? implode(', ', $alta->unidades)
            : ($alta && $alta->unidades ? $alta->unidades : 'N/A');
        
        $estadoAlta = $alta ? $alta->status : 'Sin alta';
        
        return [
            $formattedIdAlta,
            $formattedIdProv,
            $prospecto->nombre_empresa ?? 'N/A',
            $prospecto->telefono ?? 'N/A',
            $prospecto->email ?? 'N/A',
            $prospecto->cantidad_unidades ?? 0,
            $tiposUnidades,
            $unidadesAlta,
            ($alta && isset($alta->unidades_otros)) ? $alta->unidades_otros : 'N/A',
            ucfirst(str_replace('_', ' ', $estadoAlta)),
            $prospecto->nombre_quien_registro ?? 'N/A',
            $prospecto->created_at ? $prospecto->created_at->format('d/m/Y H:i') : 'N/A',
            $alta && $alta->created_at ? $alta->created_at->format('d/m/Y H:i') : 'N/A',
        ];
    }

    public function drawings()
    {
        $logoPath = public_path('images/Trevsa.jpeg');
        
        if (file_exists($logoPath)) {
            $drawing = new Drawing();
            $drawing->setPath($logoPath);
            $drawing->setHeight(65);
            $drawing->setWidth(140);
            $drawing->setCoordinates('A1');
            $drawing->setOffsetX(10);
            $drawing->setOffsetY(5);
            $drawing->setResizeProportional(true);
            return [$drawing];
        }
        
        return [];
    }

    public function styles(Worksheet $sheet)
    {
        // Estilos para el encabezado de la tabla (fila 6)
        return [
            6 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 11,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FF0000'], // Rojo TREVSA
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Ajustar altura de filas para el logo y título
                $sheet->getRowDimension(1)->setRowHeight(35);
                $sheet->getRowDimension(2)->setRowHeight(20);
                $sheet->getRowDimension(3)->setRowHeight(15);
                $sheet->getRowDimension(4)->setRowHeight(5);
                $sheet->getRowDimension(5)->setRowHeight(5);
                
                // Título y encabezado (colocados después del logo en la columna D)
                $sheet->setCellValue('D1', 'TREVSA LOGISTICS');
                $sheet->setCellValue('D2', 'Reporte de Altas de Proveedores');
                $sheet->setCellValue('D3', 'Fecha de Exportación: ' . now()->format('d/m/Y H:i'));
                
                // Estilo del título
                $sheet->getStyle('D1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 18,
                        'color' => ['rgb' => 'FF0000'],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                
                $sheet->getStyle('D2')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                        'color' => ['rgb' => '000000'],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                
                $sheet->getStyle('D3')->applyFromArray([
                    'font' => [
                        'size' => 10,
                        'color' => ['rgb' => '666666'],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                
                // Unir celdas para el logo (A1:C3)
                $sheet->mergeCells('A1:C3');
                $sheet->getStyle('A1:C3')->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                
                // Ajustar ancho de columnas
                $sheet->getColumnDimension('A')->setWidth(15);
                $sheet->getColumnDimension('B')->setWidth(15);
                $sheet->getColumnDimension('C')->setWidth(25);
                $sheet->getColumnDimension('D')->setWidth(30);
                $sheet->getColumnDimension('E')->setWidth(25);
                $sheet->getColumnDimension('F')->setWidth(15);
                $sheet->getColumnDimension('G')->setWidth(25);
                $sheet->getColumnDimension('H')->setWidth(20);
                $sheet->getColumnDimension('I')->setWidth(20);
                $sheet->getColumnDimension('J')->setWidth(15);
                $sheet->getColumnDimension('K')->setWidth(20);
                $sheet->getColumnDimension('L')->setWidth(20);
                $sheet->getColumnDimension('M')->setWidth(20);
                
                // Aplicar bordes y estilos a todas las celdas de datos
                $lastRow = $sheet->getHighestRow();
                $lastColumn = $sheet->getHighestColumn();
                
                // Estilo para filas de datos (alternando colores)
                for ($row = 7; $row <= $lastRow; $row++) {
                    $fillColor = ($row % 2 == 0) ? 'F5F5F5' : 'FFFFFF';
                    $sheet->getStyle("A{$row}:{$lastColumn}{$row}")->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => $fillColor],
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => 'DDDDDD'],
                            ],
                        ],
                        'alignment' => [
                            'vertical' => Alignment::VERTICAL_CENTER,
                            'wrapText' => true,
                        ],
                    ]);
                }
                
                // Altura de fila para el encabezado
                $sheet->getRowDimension(6)->setRowHeight(25);
                
                // Centrar columnas específicas
                $sheet->getStyle('F7:F' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('J7:J' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                // Congelar paneles (encabezado visible al hacer scroll)
                $sheet->freezePane('A7');
            },
        ];
    }
}

