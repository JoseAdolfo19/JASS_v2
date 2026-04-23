<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\SectorManager;
use App\Livewire\Admin\AssociateManager;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Livewire\Admin\Home;
use App\Livewire\Admin\PaymentTable;
use App\Livewire\Admin\PaymentHistory;
use App\Models\Payment;
use App\Livewire\Admin\SettingsManager;
use App\Livewire\Admin\ExpenseManager;
use App\Livewire\Admin\ReportManager;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('welcome');
})->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials, $request->boolean('remember'))) {
        $request->session()->regenerate();

        return redirect()->intended(route('admin.home'));
    }

    return back()->withErrors([
        'email' => 'Estas credenciales no coinciden con nuestros registros.',
    ])->onlyInput('email');
})->name('login.attempt');

Route::post('/logout', function (Request $request) {
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/');
})->name('logout');

Route::get('/dashboard', function () {
    return redirect()->route('admin.home');
})->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/home', Home::class)->name('admin.home');
Route::get('/sectores', SectorManager::class);
Route::get('/asociados', AssociateManager::class);
Route::get('/pagos', PaymentTable::class)->name('admin.pagos');
Route::get('/historial-pagos', PaymentHistory::class)->name('admin.historial-pagos');
Route::get('/recibo/{id}', function ($id) {
    $payment = Payment::with('associate')->findOrFail($id);
    $pdf = Pdf::loadView('pdf.recibo', compact('payment'));
    return $pdf->stream('recibo-'.$id.'.pdf'); // stream para ver en navegador, download para descargar
})->name('recibo.pdf');
    Route::get('/admin/reportes', ReportManager::class)->name('admin.reportes');
    Route::get('/admin/egresos', ExpenseManager::class)->name('admin.egresos');
    Route::get('/admin/configuracion', SettingsManager::class)->name('admin.settings');
Route::get('/admin/asistencia', \App\Livewire\Admin\AttendanceManager::class)->name('admin.asistencia');
});