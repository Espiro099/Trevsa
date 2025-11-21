<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class TiposUnidadesExport implements FromCollection, WithHeadings
{
    protected $tipos;

    public function __construct($tipos)
    {
        $this->tipos = $tipos;
    }

    public function collection()
    {
        return new Collection($this->tipos);
    }

    public function headings(): array
    {
        return ['Proveedor', 'Tipos de Unidades'];
    }
}
