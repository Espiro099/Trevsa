<?php

use App\Http\Controllers\AltasProveedoresController;
use App\Http\Controllers\CalculoTarifaController;
use App\Http\Controllers\ClientesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EstadoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProveedoresController;
use App\Http\Controllers\RegistroSolicitudesController;
use App\Http\Controllers\TarifasController;
use App\Http\Controllers\UnidadesController;
use App\Http\Controllers\TransportistaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'permission:dashboard.view'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Transportistas
    Route::middleware('permission:transportistas.view')->group(function () {
        Route::get('/transportistas', [TransportistaController::class, 'index'])->name('transportistas.index');
    });
    Route::middleware('permission:transportistas.manage')->group(function () {
        Route::get('/transportistas/create', [TransportistaController::class, 'create'])->name('transportistas.create');
        Route::post('/transportistas', [TransportistaController::class, 'store'])->name('transportistas.store');
        Route::get('/transportistas/{id}/edit', [TransportistaController::class, 'edit'])->name('transportistas.edit');
        Route::put('/transportistas/{id}', [TransportistaController::class, 'update'])->name('transportistas.update');
        Route::delete('/transportistas/{id}', [TransportistaController::class, 'destroy'])->name('transportistas.destroy');
    });

    // Registro de servicios
    Route::middleware('permission:registro.view')->group(function () {
        Route::get('/registro', [RegistroSolicitudesController::class, 'index'])->name('registro.index');
    });
    Route::middleware('permission:registro.manage')->group(function () {
        Route::get('/registro/create', [RegistroSolicitudesController::class, 'create'])->name('registro.create');
        Route::post('/registro', [RegistroSolicitudesController::class, 'store'])->name('registro.store');
        Route::get('/registro/{id}/edit', [RegistroSolicitudesController::class, 'edit'])->name('registro.edit');
        Route::put('/registro/{id}', [RegistroSolicitudesController::class, 'update'])->name('registro.update');
        Route::get('/servicio/{id}/estado', [EstadoController::class, 'show'])->name('estado.show');
        Route::put('/servicio/{id}/estado', [EstadoController::class, 'update'])->name('estado.update');
    });

    // API de cálculo de tarifas
    Route::middleware('permission:tarifas.manage')->group(function () {
        Route::post('/api/calcular-distancia', [CalculoTarifaController::class, 'calcularDistancia'])->name('api.calcular.distancia');
        Route::post('/api/calcular-tarifa', [CalculoTarifaController::class, 'calcularTarifa'])->name('api.calcular.tarifa');
    });

    // Clientes
    Route::middleware('permission:clientes.view')->group(function () {
        Route::get('/clientes', [ClientesController::class, 'index'])->name('clientes.index');
    });
    Route::middleware('permission:clientes.manage')->group(function () {
        Route::get('/clientes/create', [ClientesController::class, 'create'])->name('clientes.create');
        Route::post('/clientes', [ClientesController::class, 'store'])->name('clientes.store');
        Route::get('/clientes/{id}/edit', [ClientesController::class, 'edit'])->name('clientes.edit');
        Route::put('/clientes/{id}', [ClientesController::class, 'update'])->name('clientes.update');
    });

    // Proveedores / Prospectos
    Route::middleware('permission:proveedores.view')->group(function () {
        Route::get('/prospectos-proveedores', [ProveedoresController::class, 'index'])->name('prospectos_proveedores.index');
        Route::get('/prospectos-proveedores/export/xlsx', [ProveedoresController::class, 'exportTiposUnidades'])->name('prospectos_proveedores.export.xlsx');
        Route::get('/proveedores', [ProveedoresController::class, 'index'])->name('proveedores.index');
        Route::get('/proveedores/export/xlsx', [ProveedoresController::class, 'exportTiposUnidades'])->name('proveedores.export.xlsx');
    });
    Route::middleware('permission:proveedores.manage')->group(function () {
        Route::get('/prospectos-proveedores/create', [ProveedoresController::class, 'create'])->name('prospectos_proveedores.create');
        Route::post('/prospectos-proveedores', [ProveedoresController::class, 'store'])->name('prospectos_proveedores.store');
        Route::get('/prospectos-proveedores/{id}/edit', [ProveedoresController::class, 'edit'])->name('prospectos_proveedores.edit');
        Route::put('/prospectos-proveedores/{id}', [ProveedoresController::class, 'update'])->name('prospectos_proveedores.update');

        Route::get('/proveedores/create', [ProveedoresController::class, 'create'])->name('proveedores.create');
        Route::post('/proveedores', [ProveedoresController::class, 'store'])->name('proveedores.store');
        Route::get('/proveedores/{id}/edit', [ProveedoresController::class, 'edit'])->name('proveedores.edit');
        Route::put('/proveedores/{id}', [ProveedoresController::class, 'update'])->name('proveedores.update');
    });

    // Unidades
    Route::middleware('permission:unidades.view')->group(function () {
        Route::get('/unidades', [UnidadesController::class, 'index'])->name('unidades.index');
    });
    Route::middleware('permission:unidades.manage')->group(function () {
        Route::get('/unidades/create', [UnidadesController::class, 'create'])->name('unidades.create');
        Route::post('/unidades', [UnidadesController::class, 'store'])->name('unidades.store');
        Route::get('/unidades/{id}/edit', [UnidadesController::class, 'edit'])->name('unidades.edit');
        Route::put('/unidades/{id}', [UnidadesController::class, 'update'])->name('unidades.update');
        Route::get('/api/unidades/buscar-proveedores', [UnidadesController::class, 'buscarProveedores'])->name('unidades.buscar_proveedores');
    });

    // Altas de proveedores
    Route::middleware('permission:altas.view')->group(function () {
        Route::get('/altas-proveedores', [AltasProveedoresController::class, 'index'])->name('altas_proveedores.index');
        Route::get('/altas-proveedores/{prospectoId}', [AltasProveedoresController::class, 'show'])->name('altas_proveedores.show');
        Route::get('/altas-proveedores/export/all', [AltasProveedoresController::class, 'exportAll'])->name('altas_proveedores.export.all');
        Route::get('/altas-proveedores/export/{prospectoId}', [AltasProveedoresController::class, 'exportSpecific'])->name('altas_proveedores.export.specific');
    });
    Route::middleware('permission:altas.manage')->group(function () {
        Route::get('/altas-proveedores/{prospectoId}/create', [AltasProveedoresController::class, 'create'])->name('altas_proveedores.create');
        Route::post('/altas-proveedores/{prospectoId}', [AltasProveedoresController::class, 'store'])->name('altas_proveedores.store');
        Route::get('/altas-proveedores/{prospectoId}/edit', [AltasProveedoresController::class, 'edit'])->name('altas_proveedores.edit');
        Route::put('/altas-proveedores/{prospectoId}', [AltasProveedoresController::class, 'update'])->name('altas_proveedores.update');
        Route::post('/altas-proveedores/{prospectoId}/dar-alta', [AltasProveedoresController::class, 'darAlta'])->name('altas_proveedores.dar_alta');
    });

    // Tarifas
    Route::middleware('permission:tarifas.view')->group(function () {
        Route::get('/tarifas', [TarifasController::class, 'index'])->name('tarifas.index');
        Route::get('/tarifas/precio-diesel', [TarifasController::class, 'precioDiesel'])->name('tarifas.precio-diesel');
        Route::get('/tarifas/historial', [TarifasController::class, 'historial'])->name('tarifas.historial');
    });
    Route::middleware('permission:tarifas.manage')->group(function () {
        Route::post('/tarifas/calcular', [TarifasController::class, 'calcular'])->name('tarifas.calcular');
        Route::post('/tarifas/guardar', [TarifasController::class, 'guardarCalculo'])->name('tarifas.guardar');
    });
    Route::middleware('permission:tarifas.precio')->group(function () {
        Route::post('/tarifas/precio-diesel', [TarifasController::class, 'actualizarPrecioDiesel'])->name('tarifas.actualizar-precio-diesel');
    });
});

require __DIR__.'/auth.php';
