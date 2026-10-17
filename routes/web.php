<?php

use App\Enums\UserRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

// Pengalihan dashboard sesuai role pengguna.
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        $user = Auth::user();

        return match (true) {
            $user->isCs() => redirect()->route('staff.dashboard'),
            $user->isPimpinan() => redirect()->route('manager.dashboard'),
            default => redirect()->route('customer.complaints.index'),
        };
    })->name('dashboard');
});

// Area Nasabah.
Route::middleware(['auth', 'verified', 'role:'.UserRole::Nasabah->value])
    ->prefix('nasabah')
    ->name('customer.')
    ->group(function () {
        Route::livewire('pengaduan', 'pages::customer.complaints.index')->name('complaints.index');
        Route::livewire('pengaduan/buat', 'pages::customer.complaints.create')->name('complaints.create');
        Route::livewire('pengaduan/{complaint}', 'pages::customer.complaints.show')->name('complaints.show');
    });

// Area Customer Service (CS).
Route::middleware(['auth', 'verified', 'role:'.UserRole::Cs->value])
    ->prefix('cs')
    ->name('staff.')
    ->group(function () {
        Route::livewire('dashboard', 'pages::staff.dashboard')->name('dashboard');
        Route::livewire('tiket', 'pages::staff.complaints.index')->name('complaints.index');
        Route::livewire('tiket/{complaint}', 'pages::staff.complaints.show')->name('complaints.show');
        Route::livewire('master/kategori', 'pages::staff.categories.index')->name('categories.index');
        Route::livewire('master/departemen', 'pages::staff.departments.index')->name('departments.index');
        Route::livewire('master/pengguna', 'pages::staff.users.index')->name('users.index');
    });

// Area Pimpinan (laporan, read-only).
Route::middleware(['auth', 'verified', 'role:'.UserRole::Pimpinan->value])
    ->prefix('pimpinan')
    ->name('manager.')
    ->group(function () {
        Route::livewire('dashboard', 'pages::manager.dashboard')->name('dashboard');
        Route::get('laporan/pdf', [\App\Http\Controllers\Manager\ReportController::class, 'exportPdf'])->name('report.pdf');
    });

require __DIR__.'/settings.php';
