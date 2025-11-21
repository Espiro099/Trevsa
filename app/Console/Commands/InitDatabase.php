<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use MongoDB\Client;
use App\Models\Cliente;
use App\Models\Proveedor;
use App\Models\Transportista;
use App\Models\UnidadDisponible;
use App\Models\TransporteProveedor;
use App\Models\Servicio;
use App\Models\Tarifa;

class InitDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:init 
                            {--force : Forzar creación incluso si las colecciones existen}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inicializa la base de datos con las colecciones según los modelos nuevos';

    protected $client;
    protected $db;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Inicializando base de datos...');
        
        // Conectar a MongoDB
        $this->connectMongoDB();
        
        $force = $this->option('force');
        
        // Definir todas las colecciones según los modelos
        $collections = [
            'clientes' => [
                'description' => 'Prospectos y clientes',
                'indexes' => [
                    ['nombre_empresa' => 1],
                    ['email' => 1],
                ]
            ],
            'proveedores' => [
                'description' => 'Prospectos de proveedores básicos',
                'indexes' => [
                    ['nombre_empresa' => 1],
                ]
            ],
            'transportistas' => [
                'description' => 'Transportistas en inventario',
                'indexes' => [
                    ['transportista' => 1],
                    ['estatus' => 1],
                ]
            ],
            'unidades_disponibles' => [
                'description' => 'Unidades de transporte disponibles',
                'indexes' => [
                    ['tipo_unidad' => 1],
                    ['fecha_disponible' => 1],
                    ['estado' => 1],
                ]
            ],
            'transportes_proveedores' => [
                'description' => 'Proveedores de transporte completos (con documentación)',
                'indexes' => [
                    ['razon_social' => 1],
                    ['rfc' => 1],
                    ['status' => 1],
                ]
            ],
            'servicios' => [
                'description' => 'Servicios/Solicitudes de transporte',
                'indexes' => [
                    ['cliente_id' => 1],
                    ['proveedor_id' => 1],
                    ['estado' => 1],
                    ['fecha_servicio' => 1],
                    ['created_at' => -1], // Descendente para obtener los más recientes primero
                ]
            ],
            'tarifas' => [
                'description' => 'Tarifas de transporte',
                'indexes' => [
                    ['origen' => 1, 'destino' => 1],
                    ['activa' => 1],
                    ['vigente_desde' => 1, 'vigente_hasta' => 1],
                ]
            ],
        ];

        $this->info("\n📦 Creando colecciones...\n");

        foreach ($collections as $collectionName => $config) {
            $this->createCollection($collectionName, $config, $force);
        }

        $this->info("\n✅ Base de datos inicializada correctamente!");
        $this->showCollectionInfo();
    }

    protected function connectMongoDB()
    {
        try {
            $dsn = config('database.connections.mongodb.dsn');
            if (empty($dsn)) {
                $this->error('❌ MONGO_DSN no configurado en .env');
                $this->line('   Configura MONGO_DSN en tu archivo .env');
                exit(1);
            }

            $this->client = new Client($dsn, [], ['serverSelectionTimeoutMS' => 5000]);
            $this->db = $this->client->selectDatabase(config('database.connections.mongodb.database'));
            $this->info('✓ Conectado a MongoDB: ' . config('database.connections.mongodb.database'));
        } catch (\Exception $e) {
            $this->error('❌ Error conectando a MongoDB: ' . $e->getMessage());
            $this->line('   Verifica tu configuración en .env');
            exit(1);
        }
    }

    protected function createCollection($collectionName, $config, $force)
    {
        $exists = false;
        foreach ($this->db->listCollections() as $c) {
            if ($c->getName() === $collectionName) {
                $exists = true;
                break;
            }
        }

        if ($exists && !$force) {
            $count = $this->db->selectCollection($collectionName)->countDocuments();
            $this->warn("  ⚠️  Colección '{$collectionName}' ya existe ({$count} documentos)");
            $this->line("     Usa --force para recrearla");
            
            // Crear índices de todas formas
            $this->createIndexes($collectionName, $config['indexes']);
            return;
        }

        if ($exists && $force) {
            $this->warn("  🔄 Eliminando colección existente: {$collectionName}");
            $this->db->dropCollection($collectionName);
        }

        // Crear la colección (en MongoDB se crea automáticamente al insertar, pero la creamos explícitamente)
        try {
            $this->db->createCollection($collectionName);
            $this->info("  ✅ Colección '{$collectionName}' creada");
            $this->line("     {$config['description']}");
        } catch (\Exception $e) {
            // Si ya existe, está bien
            if (strpos($e->getMessage(), 'already exists') === false) {
                $this->error("  ❌ Error creando colección {$collectionName}: " . $e->getMessage());
            } else {
                $this->info("  ✅ Colección '{$collectionName}' ya existe");
            }
        }

        // Crear índices
        $this->createIndexes($collectionName, $config['indexes']);
    }

    protected function createIndexes($collectionName, $indexes)
    {
        $collection = $this->db->selectCollection($collectionName);
        
        foreach ($indexes as $index) {
            try {
                // Si el índice es un array asociativo, crear índice compuesto
                if (count($index) > 1) {
                    $collection->createIndex($index);
                    $indexNames = implode(', ', array_keys($index));
                    $this->line("     📌 Índice creado: ({$indexNames})");
                } else {
                    // Índice simple
                    $field = key($index);
                    $direction = $index[$field];
                    $collection->createIndex([$field => $direction]);
                    $directionText = $direction === 1 ? 'asc' : 'desc';
                    $this->line("     📌 Índice creado: {$field} ({$directionText})");
                }
            } catch (\Exception $e) {
                // Si el índice ya existe, está bien
                if (strpos($e->getMessage(), 'already exists') === false && 
                    strpos($e->getMessage(), 'duplicate key') === false) {
                    $this->warn("     ⚠️  Error creando índice: " . $e->getMessage());
                }
            }
        }
    }

    protected function showCollectionInfo()
    {
        $this->info("\n📊 Estado de las colecciones:\n");
        
        $collections = [
            'clientes',
            'proveedores',
            'transportistas',
            'unidades_disponibles',
            'transportes_proveedores',
            'servicios',
            'tarifas',
        ];

        $tableData = [];
        foreach ($collections as $name) {
            $exists = false;
            foreach ($this->db->listCollections() as $c) {
                if ($c->getName() === $name) {
                    $exists = true;
                    break;
                }
            }

            if ($exists) {
                $count = $this->db->selectCollection($name)->countDocuments();
                $indexes = $this->db->selectCollection($name)->listIndexes();
                $indexCount = iterator_count($indexes);
                
                $tableData[] = [
                    'Colección' => $name,
                    'Documentos' => $count,
                    'Índices' => $indexCount,
                    'Estado' => '✓ Lista'
                ];
            } else {
                $tableData[] = [
                    'Colección' => $name,
                    'Documentos' => '0',
                    'Índices' => '0',
                    'Estado' => '✗ No existe'
                ];
            }
        }

        $this->table(
            ['Colección', 'Documentos', 'Índices', 'Estado'],
            $tableData
        );

        $this->info("\n💡 Las colecciones están listas para recibir datos.");
        $this->line("   Puedes empezar a usar los formularios para crear registros.");
    }
}

