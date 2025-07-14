<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmpleadosController;
use App\Http\Controllers\ClientesController;
use App\Http\Controllers\ProductosController;
use App\Http\Controllers\PermisoController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\ComprasController;
use App\Http\Controllers\PerfilesController;
use App\Http\Controllers\PerfilXPermisoController;
use App\Http\Controllers\DetalleComprasController;
use App\Http\Controllers\MetodoPagoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Usuario autenticado
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

// Empresa
Route::post('/login-empresa', [EmpresaController::class, 'login']);
Route::post('/register-empresa', [EmpresaController::class, 'register']);

// Perfil
Route::get('/indexperfil', [PerfilesController::class, 'index'])->name('perfil.index');
Route::middleware('auth:sanctum')->get('/showperfil', [PerfilesController::class, 'showPerfil']);
Route::get('/showp', [PerfilesController::class, 'show'])->name('perfil.show');
Route::post('/storeperfil', [PerfilesController::class, 'store'])->name('perfil.store');
Route::put('/updateperfil/{id}', [PerfilesController::class, 'update'])->name('perfil.update');
Route::delete('/destroyperfil/{id}', [PerfilesController::class, 'destroy'])->name('perfil.destroy');

// Perfil x Permiso
Route::get('/pxp-index', [PerfilXPermisoController::class, 'index'])->name('perfilxpermiso.index');
Route::get('/pxp-show', [PerfilXPermisoController::class, 'show'])->name('perfilxpermiso.show');
Route::post('/pxp-store', [PerfilXPermisoController::class, 'store'])->name('perfilxpermiso.store');
Route::put('/pxp-update/{id}', [PerfilXPermisoController::class, 'update'])->name('perfilxpermiso.update');
Route::delete('/pxp-destroy/{id}', [PerfilXPermisoController::class, 'destroy'])->name('perfilxpermiso.destroy');

// Permisos
Route::get('/per-index', [PermisoController::class, 'index'])->name('permiso.index');
Route::get('/per-show', [PermisoController::class, 'show'])->name('permiso.show');
Route::post('/per-store', [PermisoController::class, 'store'])->name('permiso.store');
Route::put('/per-update/{id}', [PermisoController::class, 'update'])->name('permiso.update');
Route::delete('/per-destroy/{id}', [PermisoController::class, 'destroy'])->name('permiso.destroy');

// Productos
Route::get('/P-index', [ProductosController::class, 'index'])->name('productos.index');
Route::get('/P-show', [ProductosController::class, 'show'])->name('productos.show');
Route::get('/P-imagen/{nombre_foto}', [ProductosController::class, 'mostrar_imagen']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/P-store', [ProductosController::class, 'store'])->name('productos.store');
    Route::put('/P-update/{id}', [ProductosController::class, 'update'])->name('productos.update');
    Route::delete('/P-destroy/{id}', [ProductosController::class, 'destroy'])->name('productos.destroy');
});

// Detalle Compras
Route::get('/dc-index', [DetalleComprasController::class, 'index'])->name('detallecompras.index');
Route::get('/dc-show', [DetalleComprasController::class, 'show'])->name('detallecompras.show');
Route::put('/dc-update/{id}', [DetalleComprasController::class, 'update'])->name('detallecompras.update');
Route::delete('/dc-destroy/{id}', [DetalleComprasController::class, 'destroy'])->name('detallecompras.destroy');
Route::middleware('auth:sanctum')->get('/detalle-compras-reporte', [DetalleComprasController::class, 'reporteDetalleComprasPorEmpresa']);


// Compras
Route::middleware('auth:sanctum')->post('/compra', [ComprasController::class, 'crearCompra']);
Route::post('/actualizar_estado_compra', [ComprasController::class, 'actualizarEstadoCompra']);
Route::get('/total-compras', [ComprasController::class, 'getTotalCompras']);

Route::get('/c-index', [ComprasController::class, 'index'])->name('compras.index');
Route::get('/c-show', [ComprasController::class, 'show'])->name('compras.show');
Route::post('/c-store', [ComprasController::class, 'store'])->name('compras.store');
Route::put('/c-update/{id}', [ComprasController::class, 'update'])->name('compras.update');
Route::delete('/c-destroy/{id}', [ComprasController::class, 'destroy'])->name('compras.destroy');
Route::get('/reporte-compras-nombres', [ComprasController::class, 'reporteComprasConNombres']);


// Clientes
Route::prefix('clientes')->group(function () {
    Route::get('/', [ClientesController::class, 'index']);
    Route::post('/', [ClientesController::class, 'store']);
    Route::get('{id}', [ClientesController::class, 'show']);
    Route::put('{id}', [ClientesController::class, 'update']);
    Route::delete('{id}', [ClientesController::class, 'destroy']);
});

// Empleados
Route::prefix('empleados')->group(function () {
    Route::get('/', [EmpleadosController::class, 'index']);
    Route::post('/', [EmpleadosController::class, 'store']);
    Route::get('{id}', [EmpleadosController::class, 'show']);
    Route::put('{id}', [EmpleadosController::class, 'update']);
    Route::delete('{id}', [EmpleadosController::class, 'destroy']);
});

//metodos_pago
Route::get('/metodos-pago', [MetodoPagoController::class, 'index']);   // Listar todos
Route::post('/metodos-pago', [MetodoPagoController::class, 'store']);  // Crear nuevo
Route::get('/metodos-pago/{id}', [MetodoPagoController::class, 'show']);      // Mostrar uno
Route::put('/metodos-pago/{id}', [MetodoPagoController::class, 'update']);    // Actualizar uno
Route::delete('/metodos-pago/{id}', [MetodoPagoController::class, 'destroy']); // Eliminar uno
