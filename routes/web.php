<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MedicamentoController;
use App\Http\Controllers\AdministradorController;
use App\Http\Controllers\GestionUsuariosController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\SedeController;


// =====================================================
// AUTENTICACIÓN: LOGIN, REGISTRO Y RECUPERACIÓN
// =====================================================

// Página principal: redirige al login.
Route::get('/', function () {
    return redirect('/login');
});

// Mostrar el formulario de inicio de sesión.
// La vista está en resources/views/login.blade.php.
Route::get('/login', [AuthController::class, 'login'])
    ->name('login');

// Procesar el inicio de sesión.
Route::post('/login', [AuthController::class, 'validar'])
    ->name('login.validar');

// Registro.
Route::get('/registrarse', [AuthController::class, 'registrarvista'])
    ->name('registrarse');

Route::post('/registrar', [AuthController::class, 'guardar'])
    ->name('registrar');

// Cerrar sesión.
Route::get('/logout', [AuthController::class, 'logout'])
    ->name('logout');

// Recuperación de contraseña.
Route::get('/recuperar', [AuthController::class, 'vista'])
    ->name('recuperar');

Route::post('/recuperar', [AuthController::class, 'enviarCodigo'])
    ->name('recuperar.enviar');

// Validación del código.
Route::get('/validar-codigo', [AuthController::class, 'vistaCodigo'])
    ->name('codigo.vista');

Route::post('/validar-codigo', [AuthController::class, 'validarCodigo'])
    ->name('codigo.validar');

// Nueva contraseña.
Route::get('/nueva-password', [AuthController::class, 'vistaNuevaPassword'])
    ->name('password.nueva');

Route::post('/nueva-password', [AuthController::class, 'actualizarPassword'])
    ->name('password.actualizar');


// =====================================================
// PANEL, PERFIL Y CONTRASEÑA
// =====================================================

Route::middleware('auth.custom')->group(function () {

    Route::get('/home', [HomeController::class, 'home'])
        ->name('home');

    Route::get('/perfil', [HomeController::class, 'perfil'])
        ->name('perfil');

    Route::post('/perfil', [HomeController::class, 'actualizarPerfil'])
        ->name('perfil.actualizar');

    Route::get('/perfil/password', [HomeController::class, 'formularioPassword'])
        ->name('perfil.password');

    Route::post('/perfil/password', [HomeController::class, 'actualizarPassword'])
        ->name('perfil.password.actualizar');
});


// =====================================================
// CONSULTA DE MEDICAMENTOS
// =====================================================

Route::middleware('auth.custom')->group(function () {

    Route::get('/medicamentos', [MedicamentoController::class, 'listarMedicamentos'])
        ->name('medicamentos');
});


// =====================================================
// GESTIÓN DE MEDICAMENTOS: ADMIN Y ENTIDAD
// =====================================================

Route::middleware('auth.custom:admin,entidad')->group(function () {

    Route::get('/medicamentos/crear', [MedicamentoController::class, 'mostrarFormulario'])
        ->name('medicamentos.crear');

    Route::post('/medicamentos/guardar', [MedicamentoController::class, 'store'])
        ->name('medicamentos.guardar');

    Route::get('/medicamentos/editar/{id}', [MedicamentoController::class, 'mostrarEditar'])
        ->name('medicamentos.editar');

    Route::post('/medicamentos/actualizar/{id}', [MedicamentoController::class, 'actualizarMedicamento'])
        ->name('medicamentos.actualizar');

    Route::get('/medicamentos/eliminar/{id}', [MedicamentoController::class, 'mostrarEliminar'])
        ->name('medicamentos.eliminar');

    Route::post('/medicamentos/eliminar/{id}', [MedicamentoController::class, 'eliminarMedicamento'])
        ->name('medicamentos.eliminar.confirmar');
});


// =====================================================
// CREAR ADMINISTRADORES Y DOCTORES: SOLO ADMIN
// =====================================================

Route::middleware('auth.custom:admin')->group(function () {

    Route::get('/administradores/crear', [AdministradorController::class, 'crear'])
        ->name('administradores.crear');

    Route::post('/usuarios', [AdministradorController::class, 'guardar'])
        ->name('usuarios.guardar');
});


// =====================================================
// GESTIÓN DE USUARIOS: SOLO ADMIN
// =====================================================

Route::middleware('auth.custom:admin')->group(function () {

    Route::get('/usuarios', [GestionUsuariosController::class, 'index'])
        ->name('usuarios.index');

    Route::post('/usuarios/{usuario}/estado', [GestionUsuariosController::class, 'cambiarEstado'])
        ->name('usuarios.estado');
});


// =====================================================
// CONSULTA DE PACIENTES: ADMIN Y ENTIDAD
// =====================================================

Route::middleware('auth.custom:admin,entidad')->group(function () {

    Route::get('/pacientes', [PacienteController::class, 'index'])
        ->name('pacientes.index');
});


// =====================================================
// GESTIÓN DE SEDES: SOLO ADMIN
// =====================================================

Route::middleware('auth.custom:admin')->group(function () {

    Route::get('/sedes', [SedeController::class, 'index'])
        ->name('sedes.index');

    Route::post('/sedes', [SedeController::class, 'store'])
        ->name('sedes.store');

    Route::get('/sedes/{id}/editar', [SedeController::class, 'edit'])
        ->name('sedes.edit');

    Route::put('/sedes/{id}', [SedeController::class, 'update'])
        ->name('sedes.update');

    Route::patch('/sedes/{id}/estado', [SedeController::class, 'cambiarEstado'])
        ->name('sedes.estado');

    Route::delete('/sedes/{id}', [SedeController::class, 'destroy'])
        ->name('sedes.destroy');
});


// =====================================================
// CAMBIAR SEDE ACTIVA
// =====================================================

Route::post('/cambiar-sede', function (Request $request) {

    $datos = $request->validate([
        'idSede' => 'required|integer|exists:sedes,idSede',
    ]);

    $sede = \App\Models\Sede::where('estado', true)
        ->findOrFail($datos['idSede']);

    session([
        'idSede' => $sede->idSede,
        'nombreSede' => $sede->nombre,
    ]);

    return redirect()->back()
        ->with('success', 'Sede cambiada correctamente.');

})->middleware('auth.custom')->name('sedes.cambiar');
