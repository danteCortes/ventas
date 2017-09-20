<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::resource('caja', 'CierreController');

Route::resource('venta', 'VentaController');

Route::get('/', 'LoginController@inicio');
Route::get('login', 'LoginController@frmInicioSesion');
Route::post('ingresar', 'LoginController@ingresar');
Route::get('verificar-tipo', 'LoginController@verificarTipo');
Route::get('salir', 'LoginController@salir');

Route::post('primer-usuario', 'ConfiguracionController@primero');
Route::get('administrador', 'ConfiguracionController@administrador');
Route::get('cajero', 'ConfiguracionController@cajero');
Route::get('editar-usuario', 'ConfiguracionController@editarUsuario');
Route::post('cambiar-foto', 'ConfiguracionController@cambiarFoto');
Route::post('cambiar-password', 'ConfiguracionController@cambiarPassword');

Route::resource('tienda', 'TiendaController');

Route::resource('usuario', 'UsuarioController');
Route::post('restaurar-contrasenia', 'UsuarioController@restaurarContrasenia');
Route::post('abrir-caja', 'UsuarioController@abrirCaja');
Route::post('cerrar-caja', 'UsuarioController@cerrarCaja');
Route::get('caja-cerrada', 'UsuarioController@cajaCerrada');

Route::resource('compra', 'CompraController');
Route::post('listar-compras', 'CompraController@listar');
Route::post('buscar-compra', 'CompraController@buscar');
Route::post('generar-codigo', 'CompraController@generarCodigo');

Route::resource('detalle', 'DetalleController');

Route::resource('producto', 'ProductoController');
Route::post('buscar-producto', 'ProductoController@buscarProducto');
Route::post('listar-productos', 'ProductoController@listar');

Route::resource('proveedor', 'ProveedorController');
Route::post('buscar-proveedor', 'ProveedorController@buscar');
Route::post('listar-proveedores', 'ProveedorController@listar');

Route::resource('producto-tienda', 'ProductoTiendaController');

Route::resource('linea', 'LineaController');

Route::resource('familia', 'FamiliaController');

Route::resource('marca', 'MarcaController');
