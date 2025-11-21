<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cliente;
use App\Models\Proveedor;
use App\Models\Transportista;
use App\Models\UnidadDisponible;
use App\Models\TransporteProveedor;
use App\Models\Servicio;
use App\Models\Tarifa;
use Carbon\Carbon;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Creando datos de prueba...');

        // 1. Crear Clientes
        $this->command->info('📋 Creando clientes...');
        $clientes = [];
        $clientes[] = Cliente::create([
            'nombre_empresa' => 'Transportes del Norte SA',
            'nombre_contacto' => 'Juan Pérez',
            'telefono' => '555-1234',
            'email' => 'juan@transportesnorte.com',
            'ciudad' => 'Monterrey',
            'estado' => 'Nuevo León',
            'industria' => 'Logística',
            'comentarios' => 'Cliente frecuente, requiere servicios semanales',
            'estado_prospecto' => 'activo',
        ]);
        $clientes[] = Cliente::create([
            'nombre_empresa' => 'Distribuidora Central',
            'nombre_contacto' => 'María González',
            'telefono' => '555-5678',
            'email' => 'maria@distcentral.com',
            'ciudad' => 'Guadalajara',
            'estado' => 'Jalisco',
            'industria' => 'Retail',
            'comentarios' => 'Nuevo cliente, potencial de crecimiento',
            'estado_prospecto' => 'activo',
        ]);
        $clientes[] = Cliente::create([
            'nombre_empresa' => 'Grupo Industrial del Sur',
            'nombre_contacto' => 'Carlos Rodríguez',
            'telefono' => '555-9012',
            'email' => 'carlos@grupodelsur.com',
            'ciudad' => 'Mérida',
            'estado' => 'Yucatán',
            'industria' => 'Manufactura',
            'comentarios' => 'Servicios intermitentes',
            'estado_prospecto' => 'prospecto',
        ]);

        // 2. Crear Proveedores
        $this->command->info('📦 Creando proveedores...');
        $proveedores = [];
        $proveedores[] = Proveedor::create([
            'nombre_empresa' => 'Fletes Express',
            'telefono' => '555-1111',
            'cantidad_unidades' => 15,
            'tipos_unidades' => ['Caja Seca 53', 'Plataforma', 'Torton'],
            'base_linea_transporte' => 'Monterrey',
            'corredor_linea_transporte' => 'Norte',
            'nombre_quien_registro' => 'Admin',
            'notas' => 'Proveedor confiable con buena cobertura',
            'estado_prospecto' => 'activo',
        ]);
        $proveedores[] = Proveedor::create([
            'nombre_empresa' => 'Logística Rápida',
            'telefono' => '555-2222',
            'cantidad_unidades' => 8,
            'tipos_unidades' => ['Caja Seca 48', 'Rabon', 'Full Plataforma'],
            'base_linea_transporte' => 'Guadalajara',
            'corredor_linea_transporte' => 'Centro',
            'nombre_quien_registro' => 'Admin',
            'notas' => 'Especialistas en carga refrigerada',
            'estado_prospecto' => 'activo',
        ]);
        $proveedores[] = Proveedor::create([
            'nombre_empresa' => 'Transportes del Sureste',
            'telefono' => '555-3333',
            'cantidad_unidades' => 12,
            'tipos_unidades' => ['Caja Seca 53', 'Plataforma'],
            'base_linea_transporte' => 'Mérida',
            'corredor_linea_transporte' => 'Sureste',
            'nombre_quien_registro' => 'Admin',
            'notas' => 'Cobertura en península de Yucatán',
            'estado_prospecto' => 'activo',
        ]);

        // 3. Crear Transportistas
        $this->command->info('🚛 Creando transportistas...');
        $transportistas = [];
        $transportistas[] = Transportista::create([
            'transportista' => 'Transportes del Norte',
            'nombre' => 'Roberto Sánchez',
            'estatus' => 'activo',
            'telefono' => '555-4444',
            'cantidad_unidades_53ft' => 5,
            'tipo_viaje' => 'Larga distancia',
            'notas' => 'Transportista confiable',
        ]);
        $transportistas[] = Transportista::create([
            'transportista' => 'Fletes Express',
            'nombre' => 'Luis Martínez',
            'estatus' => 'activo',
            'telefono' => '555-5555',
            'cantidad_unidades_53ft' => 3,
            'tipo_viaje' => 'Corta distancia',
            'notas' => 'Disponible para servicios locales',
        ]);

        // 4. Crear Unidades Disponibles
        $this->command->info('🚚 Creando unidades disponibles...');
        $fechas = [
            Carbon::now()->addDays(1),
            Carbon::now()->addDays(2),
            Carbon::now()->addDays(3),
            Carbon::now()->addDays(5),
        ];
        $unidades = [];
        $unidades[] = UnidadDisponible::create([
            'tipo_unidad' => 'Caja Seca 53',
            'lugar_disponible' => 'Monterrey, NL',
            'fecha_disponible' => $fechas[0],
            'hora_disponible' => '08:00',
            'destino_sugerido' => 'Guadalajara, Jal',
            'notas' => 'Unidad disponible para carga general',
            'estado' => 'disponible',
        ]);
        $unidades[] = UnidadDisponible::create([
            'tipo_unidad' => 'Plataforma',
            'lugar_disponible' => 'Guadalajara, Jal',
            'fecha_disponible' => $fechas[1],
            'hora_disponible' => '09:00',
            'destino_sugerido' => 'México, CDMX',
            'notas' => 'Ideal para carga pesada',
            'estado' => 'disponible',
        ]);
        $unidades[] = UnidadDisponible::create([
            'tipo_unidad' => 'Torton',
            'lugar_disponible' => 'México, CDMX',
            'fecha_disponible' => $fechas[2],
            'hora_disponible' => '10:00',
            'destino_sugerido' => 'Puebla, Pue',
            'notas' => 'Capacidad media',
            'estado' => 'disponible',
        ]);
        $unidades[] = UnidadDisponible::create([
            'tipo_unidad' => 'Caja Seca 48',
            'lugar_disponible' => 'Mérida, Yuc',
            'fecha_disponible' => $fechas[3],
            'hora_disponible' => '07:00',
            'destino_sugerido' => 'Cancún, QR',
            'notas' => 'Ruta turística',
            'estado' => 'disponible',
        ]);

        // 5. Crear Transportes Proveedores (completos)
        $this->command->info('📄 Creando transportes proveedores...');
        $transportesProveedores = [];
        $transportesProveedores[] = TransporteProveedor::create([
            'correo' => 'contacto@fletesexpress.com',
            'nombre_solicita' => 'Pedro Ramírez',
            'razon_social' => 'Fletes Express SA de CV',
            'rfc' => 'FEX123456ABC',
            'telefono' => '555-1111',
            'direccion' => 'Av. Industrial 123',
            'ciudad' => 'Monterrey',
            'estado' => 'Nuevo León',
            'codigo_postal' => '64000',
            'nombre_contacto' => 'Pedro Ramírez',
            'tipo_unidades' => ['Caja Seca 53', 'Plataforma'],
            'capacidad_carga' => '25 toneladas',
            'zonas_servicio' => ['Norte', 'Centro', 'Noreste'],
            'unidades' => ['Unit-001', 'Unit-002', 'Unit-003'],
            'status' => 'aprobado',
        ]);
        $transportesProveedores[] = TransporteProveedor::create([
            'correo' => 'info@logisticarapida.com',
            'nombre_solicita' => 'Ana López',
            'razon_social' => 'Logística Rápida SA',
            'rfc' => 'LOR789012DEF',
            'telefono' => '555-2222',
            'direccion' => 'Blvd. Libertad 456',
            'ciudad' => 'Guadalajara',
            'estado' => 'Jalisco',
            'codigo_postal' => '44100',
            'nombre_contacto' => 'Ana López',
            'tipo_unidades' => ['Caja Seca 48', 'Rabon'],
            'capacidad_carga' => '20 toneladas',
            'zonas_servicio' => ['Centro', 'Occidente'],
            'unidades' => ['Unit-004', 'Unit-005'],
            'status' => 'aprobado',
        ]);

        // 6. Crear Tarifas
        $this->command->info('💰 Creando tarifas...');
        $tarifas = [];
        $tarifas[] = Tarifa::create([
            'origen' => 'Monterrey, NL',
            'destino' => 'Guadalajara, Jal',
            'precio' => 8500.00,
            'moneda' => 'MXN',
            'vigente_desde' => Carbon::now()->subDays(30),
            'vigente_hasta' => Carbon::now()->addDays(60),
            'activa' => true,
        ]);
        $tarifas[] = Tarifa::create([
            'origen' => 'Guadalajara, Jal',
            'destino' => 'México, CDMX',
            'precio' => 7200.00,
            'moneda' => 'MXN',
            'vigente_desde' => Carbon::now()->subDays(30),
            'vigente_hasta' => Carbon::now()->addDays(60),
            'activa' => true,
        ]);
        $tarifas[] = Tarifa::create([
            'origen' => 'Mérida, Yuc',
            'destino' => 'Cancún, QR',
            'precio' => 5500.00,
            'moneda' => 'MXN',
            'vigente_desde' => Carbon::now()->subDays(30),
            'vigente_hasta' => Carbon::now()->addDays(60),
            'activa' => true,
        ]);
        $tarifas[] = Tarifa::create([
            'origen' => 'Monterrey, NL',
            'destino' => 'México, CDMX',
            'precio' => 12000.00,
            'moneda' => 'MXN',
            'vigente_desde' => Carbon::now()->subDays(30),
            'vigente_hasta' => Carbon::now()->addDays(60),
            'activa' => true,
        ]);

        // 7. Crear Servicios (conectados a clientes y proveedores)
        $this->command->info('📝 Creando servicios...');
        $servicios = [];
        
        // Servicio 1: Cliente 1 con Proveedor 1
        $servicios[] = Servicio::create([
            'cliente_id' => (string)$clientes[0]->_id,
            'cliente_nombre' => $clientes[0]->nombre_empresa,
            'proveedor_id' => (string)$proveedores[0]->_id,
            'proveedor_nombre' => $proveedores[0]->nombre_empresa,
            'tipo_transporte' => 'Terrestre',
            'tipo_carga' => 'General',
            'peso_carga' => 15000.5,
            'origen' => 'Monterrey, NL',
            'destino' => 'Guadalajara, Jal',
            'fecha_servicio' => Carbon::now()->addDays(2),
            'hora_servicio' => '08:00',
            'tarifa_cliente' => 9500.00,
            'tarifa_proveedor' => 8500.00,
            'estado' => 'en_transito',
            'comentarios' => 'Carga urgente, entrega prioritaria',
        ]);

        // Servicio 2: Cliente 2 con Proveedor 2
        $servicios[] = Servicio::create([
            'cliente_id' => (string)$clientes[1]->_id,
            'cliente_nombre' => $clientes[1]->nombre_empresa,
            'proveedor_id' => (string)$proveedores[1]->_id,
            'proveedor_nombre' => $proveedores[1]->nombre_empresa,
            'tipo_transporte' => 'Terrestre',
            'tipo_carga' => 'Refrigerada',
            'peso_carga' => 12000.0,
            'origen' => 'Guadalajara, Jal',
            'destino' => 'México, CDMX',
            'fecha_servicio' => Carbon::now()->addDays(3),
            'hora_servicio' => '09:00',
            'tarifa_cliente' => 8200.00,
            'tarifa_proveedor' => 7200.00,
            'estado' => 'pendiente',
            'comentarios' => 'Requiere cadena de frío',
        ]);

        // Servicio 3: Cliente 1 con Proveedor 3
        $servicios[] = Servicio::create([
            'cliente_id' => (string)$clientes[0]->_id,
            'cliente_nombre' => $clientes[0]->nombre_empresa,
            'proveedor_id' => (string)$proveedores[2]->_id,
            'proveedor_nombre' => $proveedores[2]->nombre_empresa,
            'tipo_transporte' => 'Terrestre',
            'tipo_carga' => 'Maquinaria',
            'peso_carga' => 25000.0,
            'origen' => 'Monterrey, NL',
            'destino' => 'México, CDMX',
            'fecha_servicio' => Carbon::now()->addDays(5),
            'hora_servicio' => '07:00',
            'tarifa_cliente' => 13000.00,
            'tarifa_proveedor' => 12000.00,
            'estado' => 'pendiente',
            'comentarios' => 'Carga pesada, requiere plataforma',
        ]);

        // Servicio 4: Cliente 3 con Proveedor 2
        $servicios[] = Servicio::create([
            'cliente_id' => (string)$clientes[2]->_id,
            'cliente_nombre' => $clientes[2]->nombre_empresa,
            'proveedor_id' => (string)$proveedores[1]->_id,
            'proveedor_nombre' => $proveedores[1]->nombre_empresa,
            'tipo_transporte' => 'Terrestre',
            'tipo_carga' => 'Electrodomésticos',
            'peso_carga' => 8000.0,
            'origen' => 'Mérida, Yuc',
            'destino' => 'Cancún, QR',
            'fecha_servicio' => Carbon::now()->addDays(1),
            'hora_servicio' => '10:00',
            'tarifa_cliente' => 6000.00,
            'tarifa_proveedor' => 5500.00,
            'estado' => 'en_transito',
            'comentarios' => 'Entrega en destino turístico',
        ]);

        // Servicio 5: Cliente 2 con Proveedor 1 (completado)
        $servicios[] = Servicio::create([
            'cliente_id' => (string)$clientes[1]->_id,
            'cliente_nombre' => $clientes[1]->nombre_empresa,
            'proveedor_id' => (string)$proveedores[0]->_id,
            'proveedor_nombre' => $proveedores[0]->nombre_empresa,
            'tipo_transporte' => 'Terrestre',
            'tipo_carga' => 'Alimentos',
            'peso_carga' => 10000.0,
            'origen' => 'Monterrey, NL',
            'destino' => 'Guadalajara, Jal',
            'fecha_servicio' => Carbon::now()->subDays(5),
            'hora_servicio' => '08:00',
            'tarifa_cliente' => 9000.00,
            'tarifa_proveedor' => 8500.00,
            'estado' => 'finalizado',
            'comentarios' => 'Servicio completado exitosamente',
        ]);

        $this->command->info('✅ Datos de prueba creados exitosamente!');
        $this->command->info('📊 Resumen:');
        $this->command->info('   - Clientes: ' . count($clientes));
        $this->command->info('   - Proveedores: ' . count($proveedores));
        $this->command->info('   - Transportistas: ' . count($transportistas));
        $this->command->info('   - Unidades Disponibles: ' . count($unidades));
        $this->command->info('   - Transportes Proveedores: ' . count($transportesProveedores));
        $this->command->info('   - Tarifas: ' . count($tarifas));
        $this->command->info('   - Servicios: ' . count($servicios));
    }
}

