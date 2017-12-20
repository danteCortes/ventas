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

Route::prefix('reporte')->group(function(){
  Route::get('/', 'ReporteController@frmKardex');
  Route::post('kardex', 'ReporteController@crearKardex');
  Route::post('inventario', 'ReporteController@crearInventario');
  Route::post('cierre', 'ReporteController@crearCierre');
  Route::get('por-vencer', 'ReporteController@porVencer');
  Route::get('vencidos', 'ReporteController@vencidos');
  Route::post('ventas', 'ReporteController@ventas');
  Route::post('resumenVentas', 'ReporteController@resumenVentas');
});

Route::post('cierre-caja/{id}', 'CierreController@cierreCaja');

Route::prefix('descuento')->group(function(){
  Route::get('listar-todos', 'DescuentoController@listarTodos');
  Route::post('listar-todos', 'DescuentoController@llenarTablaTodos');
  Route::get('listar-vencidos', 'DescuentoController@listarVencidos');
  Route::post('listar-vencidos', 'DescuentoController@llenarTablaVencidos');
  Route::get('listar-vigentes', 'DescuentoController@listarVigentes');
  Route::post('listar-vigentes', 'DescuentoController@llenarTablaVigentes');
  Route::post('modificar/{id}', 'DescuentoController@modificar');
  Route::delete('eliminar/{id}', 'DescuentoController@eliminar');
  Route::post('buscar', 'DescuentoController@buscar');
  Route::post('guardar', 'DescuentoController@guardar');
});

Route::prefix('separacion')->group(function(){
  Route::get('/', 'SeparacionController@nuevo');
  Route::post('agregar-detalle', 'SeparacionController@agregarDetalle');
  Route::delete('quitar-detalle/{id}', 'SeparacionController@quitarDetalle');
  Route::post('terminar/{id}', 'SeparacionController@terminar');
  Route::get('listar', 'SeparacionController@listar');
  Route::post('listar', 'SeparacionController@llenarTabla');
  Route::post('buscar', 'SeparacionController@buscar');
  Route::get('modificar/{id}', 'SeparacionController@editar');
  Route::post('modificar-detalle/{id}', 'SeparacionController@modificarDetalle');
  Route::post('modificar/{id}', 'SeparacionController@modificar');
  Route::post('pagar/{id}', 'SeparacionController@pagar');
  Route::delete('eliminar/{id}', 'SeparacionController@eliminar');
});

// Prestamos de productos.
Route::prefix('prestamo')->group(function(){
  Route::get('/', 'PrestamoController@nuevo');
  Route::post('agregar-detalle', 'PrestamoController@agregarDetalle');
  Route::delete('quitar-detalle/{id}', 'PrestamoController@quitarDetalle');
  Route::post('terminar/{id}', 'PrestamoController@terminar');
  Route::get('listar', 'PrestamoController@listar');
  Route::post('listar', 'PrestamoController@llenarTabla');
  Route::post('buscar', 'PrestamoController@buscar');
  Route::get('editar/{id}', 'PrestamoController@editar');
  Route::delete('quitar-detalle-editar/{id}', 'PrestamoController@quitarDetalleEditar');
  Route::post('agregar-detalle-editar/{id}', 'PrestamoController@agregarDetalleEditar');
  Route::post('modificar/{id}', 'PrestamoController@modificar');
  Route::post('devolver/{id}', 'PrestamoController@devolver');
  Route::get('listar-devolver', 'PrestamoController@listarDevolver');
  Route::post('listar-devolver', 'PrestamoController@llenarTablaDevolver');
  Route::get('listar-pedir', 'PrestamoController@listarRecoger');
  Route::post('listar-pedir', 'PrestamoController@llenarTablaRecoger');
  Route::delete('eliminar/{id}', 'PrestamoController@eliminar');
});

// Créditos de productos.
Route::get('credito', 'CreditoController@index');
Route::post('agregar-detalle-credito', 'CreditoController@agregarDetalle');
Route::delete('quitar-detalle-credito/{id}', 'CreditoController@quitarDetalle');
Route::post('terminar-credito', 'CreditoController@terminar');
Route::get('listar-creditos', 'CreditoController@listar');
Route::post('listar-creditos', 'CreditoController@llenarTabla');
Route::post('buscar-credito', 'CreditoController@buscar');
Route::get('modificar-credito/{id}', 'CreditoController@editar');
Route::post('modificar-credito/{id}', 'CreditoController@modificar');
Route::post('modificar-detalle-credito/{id}', 'CreditoController@modificarDetalle');
Route::delete('eliminar-credito/{id}', 'CreditoController@eliminar');
Route::post('pagar-credito/{id}', 'CreditoController@pagar');
Route::get('listar-cobrar-creditos', 'CreditoController@listarCobrar');
Route::post('listar-cobrar-creditos', 'CreditoController@llenarTablaCobrar');
Route::get('listar-pagados-creditos', 'CreditoController@listarPagados');
Route::post('listar-pagados-creditos', 'CreditoController@llenarTablaPagados');

//TRASLADOS DE PRODUCTOS
Route::resource('traslado', 'TrasladoController');
Route::post('traslado/terminar', 'TrasladoController@terminar');

// Cambio de productos en venta
Route::post('detalle-cambio', 'CambioController@agregarDetalle');
Route::delete('quitar-detalle-cambio/{id}', 'CambioController@quitarDetalleCambio');
Route::post('vuelto-cambio', 'CambioController@vuelto');
Route::post('tipo-cambio-cambio', 'CambioController@tipoCambio');
Route::post('comision-cambio', 'CambioController@comisionTarjeta');
Route::post('pago-tarjeta-cambio', 'CambioController@pagoTarjeta');
Route::post('terminar-cambio', 'CambioController@terminar');

Route::resource('tarjeta-venta', 'TarjetaVentaController');

Route::post('buscar-persona', 'PersonaController@buscar');
Route::post('buscar-empresa', 'EmpresaController@buscar');

Route::resource('caja', 'CierreController');

Route::resource('tarjeta', 'TarjetaController');
Route::post('comision', 'TarjetaController@comision');

Route::resource('venta', 'VentaController');
Route::post('vuelto', 'VentaController@vuelto');
Route::post('tipo-cambio', 'VentaController@tipoCambio');
Route::get('imprimir-recibo/{id}', 'VentaController@imprimirRecibo');
Route::post('listar-ventas', 'VentaController@listar');
Route::post('buscar-venta', 'VentaController@buscar');

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
Route::post('cambio', 'ConfiguracionController@agregarTipoCambio');

Route::middleware('administrador')->resource('tienda', 'TiendaController');

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
Route::post('imprimir-codigo', 'ProductoController@imprimirCodigo');

Route::resource('proveedor', 'ProveedorController');
Route::post('buscar-proveedor', 'ProveedorController@buscar');
Route::post('listar-proveedores', 'ProveedorController@listar');

Route::resource('producto-tienda', 'ProductoTiendaController');

Route::resource('linea', 'LineaController');

Route::resource('familia', 'FamiliaController');

Route::resource('marca', 'MarcaController');
