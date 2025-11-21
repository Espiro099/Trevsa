<?php
// Bootstrap Laravel to use Eloquent models
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Clientes;
use App\Models\Proveedores;
use App\Models\UnidadesDisponibles;
use App\Models\RegistroSolicitudes;

echo "Inserting test records via Eloquent models...\n";

try {
    $c = Clientes::create([
        'nombre_empresa_contacto' => 'TEST Cliente ' . time(),
        'telefono' => '555-TEST',
        'email' => 'test@example.com',
        'ciudad' => 'Ciudad Test',
        'industria' => 'Logistica',
        'comentarios' => 'Registro de prueba',
        'nombre_quien_contacto' => 'Tester'
    ]);
    echo "Clientes inserted with _id: " . ($c->_id ?? '[no id]') . "\n";

    $p = Proveedores::create([
        'nombre_empresa' => 'TEST Proveedor ' . time(),
        'telefono' => '555-PROV',
        'cantidad_unidades' => 3,
        'tipos_unidades' => 'Caja Seca 53,Plataforma',
        'base_linea_transp' => 'Base Test',
        'corredor_linea_transp' => 'Corredor Test',
        'nombre_quien_registro' => 'Tester',
        'notas' => 'Registro prueba'
    ]);
    echo "Proveedores inserted with _id: " . ($p->_id ?? '[no id]') . "\n";

    $u = UnidadesDisponibles::create([
        'unidad_tipo' => 'Plataforma',
        'lugar_disponible' => 'Ciudad Test',
        'fecha' => new \Carbon\Carbon(),
        'hora' => '09:00',
        'destino_sugerido' => 'Destino X',
        'notas' => 'Prueba unidades'
    ]);
    echo "UnidadesDisponibles inserted with _id: " . ($u->_id ?? '[no id]') . "\n";

    $r = RegistroSolicitudes::create([
        'nombre_cliente' => 'Cliente Test',
        'tipo_transporte' => 'Terrestre',
        'tipo_carga' => 'General',
        'peso_carga' => '1000',
        'origen' => 'Origen Test',
        'destino' => 'Destino Test',
        'fecha_servicio' => new \Carbon\Carbon(),
        'hora_servicio' => '10:00',
        'tarifa_cliente' => 1200.50,
        'tarifa_proveedor' => 1000.00,
        'contacto_comentarios' => 'Prueba registro'
    ]);
    echo "RegistroSolicitudes inserted with _id: " . ($r->_id ?? '[no id]') . "\n";

    echo "All done.\n";
} catch (Throwable $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
