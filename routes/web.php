
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
use App\Http\Controllers\EpsController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\FormulaMedicaController;
use App\Http\Controllers\ValidarFormulaController;
use App\Http\Controllers\OrdenEntregaController;
use App\Http\Controllers\ReclamoMedicamentoController;


// =====================================================
// AUTENTICACIÓN: LOGIN, REGISTRO Y RECUPERACIÓN
// =====================================================

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', [AuthController::class, 'login'])
    ->name('login');

Route::post('/login', [AuthController::class, 'validar'])
    ->name('login.validar');

Route::get('/registrarse', [AuthController::class, 'registrarvista'])
    ->name('registrarse');

Route::post('/registrar', [AuthController::class, 'guardar'])
    ->name('registrar');

Route::get('/logout', [AuthController::class, 'logout'])
    ->name('logout');

Route::get('/recuperar', [AuthController::class, 'vista'])
    ->name('recuperar');

Route::post('/recuperar', [AuthController::class, 'enviarCodigo'])
    ->name('recuperar.enviar');

Route::get('/validar-codigo', [AuthController::class, 'vistaCodigo'])
    ->name('codigo.vista');

Route::post('/validar-codigo', [AuthController::class, 'validarCodigo'])
    ->name('codigo.validar');

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

    Route::get('/medicamentos/detectar', [MedicamentoController::class, 'detectarMedicamento'])
        ->name('medicamentos.detectar');

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

    Route::post(
        '/medicamentos/{idMedicamento}/lotes/{idLote}/estado',
        [MedicamentoController::class, 'cambiarEstadoLote']
    )->name('medicamentos.lotes.estado');
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
// GESTIÓN DE EPS: SOLO ADMIN
// =====================================================

Route::middleware('auth.custom:admin')->group(function () {

    Route::get('/eps', [EpsController::class, 'index'])
        ->name('eps.index');

    Route::post('/eps', [EpsController::class, 'store'])
        ->name('eps.store');

    Route::get('/eps/{id}/editar', [EpsController::class, 'edit'])
        ->name('eps.edit');

    Route::put('/eps/{id}', [EpsController::class, 'update'])
        ->name('eps.update');

    Route::patch('/eps/{id}/estado', [EpsController::class, 'cambiarEstado'])
        ->name('eps.estado');
});


// =====================================================
// CONSULTA DE PACIENTES: ADMIN Y ENTIDAD
// =====================================================

Route::middleware('auth.custom:admin,entidad')->group(function () {

    Route::get('/pacientes', [PacienteController::class, 'index'])
        ->name('pacientes.index');
});


// =====================================================
// HU-013: VALIDAR FÓRMULAS - SOLO ENTIDAD
// =====================================================

Route::middleware('auth.custom:entidad')->group(function () {

    Route::get('/empresa/validar-formulas', [ValidarFormulaController::class, 'index'])
        ->name('empresa.formulas.index');
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


// =====================================================
// CONSULTA MÉDICA: DOCTOR Y ADMIN
// =====================================================

Route::middleware('auth.custom:doctor,admin')->group(function () {

    Route::get('/doctor/pacientes', [DoctorController::class, 'pacientes'])
        ->name('doctor.pacientes');
});


// =====================================================
// REGISTRO DE OBSERVACIONES: SOLO DOCTOR
// =====================================================

Route::middleware('auth.custom:doctor')->group(function () {

    Route::post('/doctor/observaciones', [DoctorController::class, 'guardarObservacion'])
        ->name('doctor.observaciones.guardar');
});


// =====================================================
// FÓRMULAS MÉDICAS: SOLO DOCTOR
// =====================================================

Route::middleware('auth.custom:doctor')->group(function () {

    Route::get('/doctor/formulas', [FormulaMedicaController::class, 'index'])
        ->name('doctor.formulas');

    Route::post('/doctor/formulas', [FormulaMedicaController::class, 'guardar'])
        ->name('doctor.formulas.guardar');

    Route::get('/doctor/formulas/{id}/editar', [FormulaMedicaController::class, 'editar'])
        ->name('doctor.formulas.editar');

    Route::put('/doctor/formulas/{id}', [FormulaMedicaController::class, 'actualizar'])
        ->name('doctor.formulas.actualizar');

    Route::get(
        '/doctor/medicamentos/{id}/disponibilidad',
        [MedicamentoController::class, 'consultarDisponibilidad']
    )->name('doctor.medicamentos.disponibilidad');
});


// =====================================================
// HU-014: ÓRDENES DE ENTREGA - ENTIDAD
// =====================================================

Route::middleware('auth.custom:entidad')->group(function () {

    Route::get(
        '/empresa/ordenes-entrega',
        [OrdenEntregaController::class, 'index']
    )->name('empresa.ordenes.index');

    Route::post(
        '/empresa/ordenes-entrega',
        [OrdenEntregaController::class, 'guardar']
    )->name('empresa.ordenes.guardar');
});


// =====================================================
// HU-020: RECLAMOS DE MEDICAMENTOS - PACIENTE
// =====================================================

Route::middleware('auth.custom:paciente')->group(function () {

    Route::get(
        '/paciente/reclamos',
        [ReclamoMedicamentoController::class, 'index']
    )->name('paciente.reclamos.index');

    Route::post(
        '/paciente/reclamos',
        [ReclamoMedicamentoController::class, 'guardar']
    )->name('paciente.reclamos.guardar');
});
