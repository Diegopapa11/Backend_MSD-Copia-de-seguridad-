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
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\PerfilXPermisoController;
use App\Http\Controllers\DetalleComprasController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/empresa/register', [EmpresaController::class, 'register']);
Route::post('/empresa/login', [EmpresaController::class, 'login']);

//Rutas Auth
// Ruta para registrar usuarios
Route::post('/register', [AuthController::class, 'register']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
// Ruta para el inicio de sesión
Route::post('/login', [AuthController::class, 'login']);
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
// Ruta para cerrar sesión
Route::middleware('auth:sanctum')-> post('/logout', [AuthController::class, 'logout']);

//Rutas de Perfil
Route::get('/indexperfil', [PerfilController::class, 'index'])->name('perfil.index');
Route::middleware('auth:sanctum')->get('/showperfil', [PerfilController::class, 'showPerfil']);
Route::get('/showp', [PerfilController::class, 'show'])->name('perfil.show');
Route::post('/storeperfil', [PerfilController::class, 'store'])->name('perfil.store');
Route::put('/updateperfil/{id}', [PerfilController::class, 'update'])->name('perfil.update');
Route::delete('/destroyperfil/{id}', [PerfilController::class, 'destroy'])->name('perfil.destroy');

//Rutas de PerfilxPermiso
Route::get('/pxp-index', [PerfilXPermisoController::class, 'index'])->name('perfilxpermiso.index');
Route::get('/pxp-show', [PerfilXPermisoController::class, 'show'])->name('perfilxpermiso.show');
Route::post('/pxp-store', [PerfilXPermisoController::class, 'store'])->name('perfilxpermiso.store');
Route::put('/pxp-update/{id}', [PerfilXPermisoController::class, 'update'])->name('perfilxpermiso.update');
Route::delete('/pxp-destroy/{id}', [PerfilXPermisoController::class, 'destroy'])->name('perfilxpermiso.destroy');

//Rutas de Permiso
Route::get('/per-index', [PermisoController::class, 'index'])->name('permiso.index');
Route::get('/per-show', [PermisoController::class, 'show'])->name('permiso.show');
Route::post('/per-store', [PermisoController::class, 'store'])->name('permiso.store');
Route::put('/per-update/{id}', [PermisoController::class, 'update'])->name('permiso.update');
Route::delete('/per-destroy/{id}', [PermisoController::class, 'destroy'])->name('permiso.destroy');

//Rutas de Productos
Route::get('/P-index', [ProductosController::class, 'index'])->name('productos.index');
Route::get('/P-show', [ProductosController::class, 'show'])->name('productos.show');
Route::post('/P-store', [ProductosController::class, 'store'])->name('productos.store');
Route::put('/P-update/{id}', [ProductosController::class, 'update'])->name('productos.update');
Route::delete('/P-destroy/{id}', [ProductosController::class, 'destroy'])->name('productos.destroy');
Route::get('/P-imagen/{nombre_foto}', [ProductosController::class, 'mostrar_imagen']);
    

//Rutas de Detalle_Compras
Route::get('/dc-index', [DetalleComprasController::class, 'index'])->name('detallecompras.index');
Route::get('/dc-show', [DetalleComprasController::class, 'show'])->name('detallecompras.show');
Route::put('/dc-update/{id}', [DetalleComprasController::class, 'update'])->name('detallecompras.update');
Route::delete('/dc-destroy/{id}', [DetalleComprasController::class, 'destroy'])->name('detallecompras.destroy');

//Rutas de Compras
//apis 1ras
Route::middleware('auth:sanctum')->post('/compra', [ComprasController::class, 'crearCompra']);
Route::post('/actualizar_estado_compra', [ComprasController::class, 'actualizarEstadoCompra']);
Route::get('/total-compras', [ComprasController::class, 'getTotalCompras']);

//Route::middleware('auth:sanctum')->post('/checkout', [CheckoutController::class, 'checkout']);

//catalogo
Route::get('/c-index', [ComprasController::class, 'index'])->name('compras.index');
Route::get('/c-show', [ComprasController::class, 'show'])->name('compras.show');
Route::post('/c-store', [ComprasController::class, 'store'])->name('compras.store');
Route::put('/c-update/{id}', [ComprasController::class, 'update'])->name('compras.update');
Route::delete('/c-destroy/{id}', [ComprasController::class, 'destroy'])->name('compras.destroy');