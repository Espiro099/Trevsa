<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use MongoDB\Client;
use MongoDB\Laravel\Eloquent\Model;

class MigrateModelsData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'models:migrate 
                            {--dry-run : Ejecutar sin hacer cambios reales}
                            {--backup : Crear backup antes de migrar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migra datos de colecciones antiguas a las nuevas estructuras';

    protected $client;
    protected $db;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Iniciando migración de datos...');
        
        // Conectar a MongoDB
        $this->connectMongoDB();
        
        $dryRun = $this->option('dry-run');
        $backup = $this->option('backup');
        
        if ($dryRun) {
            $this->warn('⚠️  MODO DRY-RUN: No se realizarán cambios reales');
        }

        // Mapeo de colecciones antiguas -> nuevas
        $mappings = [
            'registro_solicitudes' => [
                'target' => 'servicios',
                'field_mapping' => [
                    'nombre_cliente' => 'cliente_nombre',
                    'contacto_comentarios' => 'comentarios',
                    // Mantener otros campos igual
                ],
            ],
            'transportistas_inv' => [
                'target' => 'transportistas',
                'field_mapping' => [
                    'estatus_tpttes' => 'estatus',
                    'qty_unidades_53ft' => 'cantidad_unidades_53ft',
                ],
            ],
            'tarifas_trevsa' => [
                'target' => 'tarifas',
                'field_mapping' => [
                    // Sin cambios de nombre, pero agregar campos nuevos
                ],
            ],
            // Clientes, Proveedores, UnidadesDisponibles, TransportesProveedores
            // mantienen los mismos nombres de colección, solo actualizamos campos
        ];

        $timestamp = date('Ymd_His');

        // Migrar colecciones que cambian de nombre
        foreach ($mappings as $sourceCollection => $config) {
            $this->migrateCollection($sourceCollection, $config['target'], $config['field_mapping'], $timestamp, $dryRun, $backup);
        }

        // Actualizar colecciones que mantienen nombre pero cambian campos
        $this->updateExistingCollections($timestamp, $dryRun, $backup);

        $this->info('✅ Migración completada!');
        $this->showCollectionCounts();
    }

    protected function connectMongoDB()
    {
        try {
            $dsn = config('database.connections.mongodb.dsn');
            if (empty($dsn)) {
                $this->error('MONGO_DSN no configurado en .env');
                return;
            }

            $this->client = new Client($dsn, [], ['serverSelectionTimeoutMS' => 5000]);
            $this->db = $this->client->selectDatabase(config('database.connections.mongodb.database'));
            $this->info('✓ Conectado a MongoDB');
        } catch (\Exception $e) {
            $this->error('Error conectando a MongoDB: ' . $e->getMessage());
            exit(1);
        }
    }

    protected function migrateCollection($sourceCollection, $targetCollection, $fieldMapping, $timestamp, $dryRun, $backup)
    {
        $this->info("\n📦 Migrando {$sourceCollection} → {$targetCollection}");

        // Verificar si existe la colección fuente
        $sourceExists = false;
        foreach ($this->db->listCollections() as $c) {
            if ($c->getName() === $sourceCollection) {
                $sourceExists = true;
                break;
            }
        }

        if (!$sourceExists) {
            $this->warn("  ⚠️  Colección {$sourceCollection} no existe, omitiendo...");
            return;
        }

        $sourceCol = $this->db->selectCollection($sourceCollection);
        $targetCol = $this->db->selectCollection($targetCollection);
        
        $count = $sourceCol->countDocuments();
        $this->info("  📊 Documentos encontrados: {$count}");

        if ($count === 0) {
            $this->warn("  ⚠️  No hay documentos para migrar");
            return;
        }

        // Crear backup si se solicita
        if ($backup && !$dryRun) {
            $backupName = $sourceCollection . '_backup_' . $timestamp;
            $this->info("  💾 Creando backup: {$backupName}");
            $this->createBackup($sourceCol, $backupName);
        }

        if ($dryRun) {
            $this->info("  🔍 DRY-RUN: Se migrarían {$count} documentos");
            return;
        }

        // Migrar documentos
        $migrated = 0;
        $cursor = $sourceCol->find();
        
        foreach ($cursor as $doc) {
            $newDoc = $this->transformDocument($doc, $fieldMapping);
            
            try {
                $targetCol->replaceOne(['_id' => $doc->_id], $newDoc, ['upsert' => true]);
                $migrated++;
                
                if ($migrated % 100 === 0) {
                    $this->info("  ⏳ Migrados: {$migrated}/{$count}");
                }
            } catch (\Exception $e) {
                $this->error("  ❌ Error migrando documento {$doc->_id}: " . $e->getMessage());
            }
        }

        $this->info("  ✅ Migrados: {$migrated} documentos");
        
        // No eliminar la colección fuente por seguridad
        $this->info("  ℹ️  Colección fuente {$sourceCollection} se mantiene (puedes eliminarla manualmente después de verificar)");
    }

    protected function transformDocument($doc, $fieldMapping)
    {
        // Convertir BSONDocument a array nativo de PHP
        $docArray = [];
        foreach ($doc as $key => $value) {
            $docArray[$key] = $value;
        }
        
        $newDoc = [];

        // Mapear campos
        foreach ($docArray as $key => $value) {
            if (isset($fieldMapping[$key])) {
                $newDoc[$fieldMapping[$key]] = $value;
            } else {
                $newDoc[$key] = $value;
            }
        }

        // Agregar campos nuevos si no existen
        if (!isset($newDoc['created_by']) && isset($newDoc['created_at'])) {
            $newDoc['created_by'] = null; // Se puede actualizar después con datos reales
        }

        // Transformaciones específicas para Servicio
        if (isset($newDoc['cliente_nombre']) && !isset($newDoc['cliente_id'])) {
            // Intentar encontrar cliente_id por nombre (buscar en ambos campos)
            $cliente = $this->db->selectCollection('clientes')
                ->findOne([
                    '$or' => [
                        ['nombre_empresa_contacto' => $newDoc['cliente_nombre']],
                        ['nombre_empresa' => $newDoc['cliente_nombre']]
                    ]
                ]);
            if ($cliente) {
                $newDoc['cliente_id'] = (string)$cliente->_id;
            }
        }
        
        // Buscar proveedor_id si existe proveedor_nombre
        if (isset($newDoc['proveedor_nombre']) && !isset($newDoc['proveedor_id'])) {
            $proveedor = $this->db->selectCollection('proveedores')
                ->findOne(['nombre_empresa' => $newDoc['proveedor_nombre']]);
            if ($proveedor) {
                $newDoc['proveedor_id'] = (string)$proveedor->_id;
            }
        }

        // Transformaciones específicas para Transportista
        if (isset($newDoc['estatus_tpttes'])) {
            $newDoc['estatus'] = $newDoc['estatus_tpttes'];
            unset($newDoc['estatus_tpttes']);
        }

        if (isset($newDoc['qty_unidades_53ft'])) {
            $newDoc['cantidad_unidades_53ft'] = $newDoc['qty_unidades_53ft'];
            unset($newDoc['qty_unidades_53ft']);
        }

        // Agregar estado si no existe
        if (!isset($newDoc['estado'])) {
            $newDoc['estado'] = 'activo'; // Valor por defecto
        }

        return $newDoc;
    }

    protected function updateExistingCollections($timestamp, $dryRun, $backup)
    {
        $this->info("\n🔄 Actualizando colecciones existentes...");

        $collectionsToUpdate = ['clientes', 'proveedores', 'unidades_disponibles', 'transportes_proveedores'];

        foreach ($collectionsToUpdate as $collectionName) {
            $collectionExists = false;
            foreach ($this->db->listCollections() as $c) {
                if ($c->getName() === $collectionName) {
                    $collectionExists = true;
                    break;
                }
            }

            if (!$collectionExists) {
                $this->warn("  ⚠️  Colección {$collectionName} no existe");
                continue;
            }

            $col = $this->db->selectCollection($collectionName);
            $count = $col->countDocuments();

            if ($count === 0) {
                continue;
            }

            $this->info("  📝 Actualizando {$collectionName} ({$count} documentos)");

            // Backup si se solicita
            if ($backup && !$dryRun) {
                $backupName = $collectionName . '_backup_' . $timestamp;
                $this->info("  💾 Creando backup: {$backupName}");
                $this->createBackup($col, $backupName);
            }

            if ($dryRun) {
                $this->info("  🔍 DRY-RUN: Se actualizarían {$count} documentos");
                continue;
            }

            // Actualizar campos según el modelo
            $updated = 0;
            $cursor = $col->find();

            foreach ($cursor as $doc) {
                $update = [];
                // Convertir BSONDocument a array nativo de PHP
                $docArray = [];
                foreach ($doc as $key => $value) {
                    $docArray[$key] = $value;
                }

                // Clientes: mapear campos
                if ($collectionName === 'clientes') {
                    if (isset($docArray['nombre_empresa_contacto']) && !isset($docArray['nombre_empresa'])) {
                        $update['nombre_empresa'] = $docArray['nombre_empresa_contacto'];
                    }
                    if (isset($docArray['nombre_quien_contacto']) && !isset($docArray['nombre_contacto'])) {
                        $update['nombre_contacto'] = $docArray['nombre_quien_contacto'];
                    }
                }

                // Proveedores: mapear campos
                if ($collectionName === 'proveedores') {
                    if (isset($docArray['base_linea_transp']) && !isset($docArray['base_linea_transporte'])) {
                        $update['base_linea_transporte'] = $docArray['base_linea_transp'];
                    }
                    if (isset($docArray['corredor_linea_transp']) && !isset($docArray['corredor_linea_transporte'])) {
                        $update['corredor_linea_transporte'] = $docArray['corredor_linea_transp'];
                    }
                }

                // UnidadesDisponibles: mapear campos
                if ($collectionName === 'unidades_disponibles') {
                    if (isset($docArray['unidad_tipo']) && !isset($docArray['tipo_unidad'])) {
                        $update['tipo_unidad'] = $docArray['unidad_tipo'];
                    }
                    if (isset($docArray['fecha']) && !isset($docArray['fecha_disponible'])) {
                        $update['fecha_disponible'] = $docArray['fecha'];
                    }
                    if (isset($docArray['hora']) && !isset($docArray['hora_disponible'])) {
                        $update['hora_disponible'] = $docArray['hora'];
                    }
                }

                // Agregar created_by si no existe
                if (!isset($docArray['created_by'])) {
                    $update['created_by'] = null;
                }

                // Agregar estado si no existe
                if (!isset($docArray['estado']) && in_array($collectionName, ['unidades_disponibles'])) {
                    $update['estado'] = 'disponible';
                }

                if (!empty($update)) {
                    try {
                        $col->updateOne(['_id' => $doc->_id], ['$set' => $update]);
                        $updated++;
                    } catch (\Exception $e) {
                        $this->error("  ❌ Error actualizando documento {$doc->_id}: " . $e->getMessage());
                    }
                }
            }

            if ($updated > 0) {
                $this->info("  ✅ Actualizados: {$updated} documentos");
            }
        }
    }

    protected function createBackup($collection, $backupName)
    {
        $backupCol = $this->db->selectCollection($backupName);
        $cursor = $collection->find();
        $copied = 0;

        foreach ($cursor as $doc) {
            try {
                // Convertir BSONDocument a array para insertar
                $docArray = [];
                foreach ($doc as $key => $value) {
                    $docArray[$key] = $value;
                }
                
                $backupCol->insertOne($docArray);
                $copied++;
                
                if ($copied % 100 === 0) {
                    $this->line("     💾 Copiados: {$copied} documentos...");
                }
            } catch (\Exception $e) {
                $this->warn("     ⚠️  Error copiando documento: " . $e->getMessage());
            }
        }

        $this->info("  💾 Backup creado: {$copied} documentos en {$backupName}");
    }

    protected function showCollectionCounts()
    {
        $this->info("\n📊 Conteo de colecciones actual:");
        $cols = $this->db->listCollections();
        $counts = [];
        
        foreach ($cols as $c) {
            $name = $c->getName();
            if (strpos($name, 'backup_') === false) {
                $count = $this->db->selectCollection($name)->countDocuments();
                $counts[$name] = $count;
            }
        }

        ksort($counts);
        foreach ($counts as $name => $count) {
            $this->line("  " . str_pad($name, 30) . " : {$count}");
        }
    }
}

