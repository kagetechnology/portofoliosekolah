<?php

use App\Http\Controllers\Admin\ContactController as AdminContactController;
use App\Http\Controllers\Admin\CertificateController as AdminCertificateController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PortfolioController as AdminPortfolioController;
use App\Http\Controllers\Admin\SchoolController as AdminSchoolController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Public\CertificateController as PublicCertificateController;
use App\Http\Controllers\Public\ContactController;
use App\Http\Controllers\Public\ForCompaniesController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\PortfolioController;
use App\Http\Controllers\Public\StudentDirectoryController;
use App\Http\Controllers\Siswa\CertificateController as SiswaCertificateController;
use App\Http\Controllers\Siswa\DashboardController as SiswaDashboardController;
use App\Http\Controllers\Siswa\PortfolioController as SiswaPortfolioController;
use App\Http\Controllers\Siswa\ProfileController as SiswaProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/portfolios', [PortfolioController::class, 'index'])->name('portfolios.index');
Route::get('/portfolios/{portfolio:slug}', [PortfolioController::class, 'show'])->name('portfolios.show');
Route::get('/certificates/{certificate:slug}', [PublicCertificateController::class, 'show'])->name('certificates.show');

Route::get('/contact', [ContactController::class, 'create'])->name('contact.create');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store')->middleware('throttle:contact');

Route::get('/untuk-perusahaan', [ForCompaniesController::class, 'index'])->name('for-companies.index');

// Guest auth
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:register');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::patch('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

    Route::get('/school', [AdminSchoolController::class, 'edit'])->name('school.edit');
    Route::put('/school', [AdminSchoolController::class, 'update'])->name('school.update');

    Route::get('/messages', [AdminContactController::class, 'index'])->name('contacts.index');
    Route::get('/messages/{contact}', [AdminContactController::class, 'show'])->name('contacts.show');
    Route::delete('/messages/{contact}', [AdminContactController::class, 'destroy'])->name('contacts.destroy');

    Route::get('/portfolios', [AdminPortfolioController::class, 'index'])->name('portfolios.index');
    Route::get('/portfolios/{portfolio}', [AdminPortfolioController::class, 'show'])->name('portfolios.show');
    Route::patch('/portfolios/{portfolio}/approve', [AdminPortfolioController::class, 'approve'])->name('portfolios.approve');
    Route::patch('/portfolios/{portfolio}/reject', [AdminPortfolioController::class, 'reject'])->name('portfolios.reject');
    Route::patch('/portfolios/{portfolio}/featured', [AdminPortfolioController::class, 'toggleFeatured'])->name('portfolios.featured');
    Route::delete('/portfolios/{portfolio}', [AdminPortfolioController::class, 'destroy'])->name('portfolios.destroy');

    Route::get('/certificates', [AdminCertificateController::class, 'index'])->name('certificates.index');
    Route::get('/certificates/{certificate}', [AdminCertificateController::class, 'show'])->name('certificates.show');
    Route::patch('/certificates/{certificate}/approve', [AdminCertificateController::class, 'approve'])->name('certificates.approve');
    Route::patch('/certificates/{certificate}/reject', [AdminCertificateController::class, 'reject'])->name('certificates.reject');
});

// Siswa
Route::middleware(['auth', 'active', 'role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/dashboard', [SiswaDashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [SiswaProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [SiswaProfileController::class, 'update'])->name('profile.update');

    Route::resource('portfolios', SiswaPortfolioController::class)
        ->except(['show'])
        ->parameters(['portfolios' => 'portfolio']);

    Route::get('/certificates', [SiswaCertificateController::class, 'index'])->name('certificates.index');
    Route::post('/certificates', [SiswaCertificateController::class, 'store'])->name('certificates.store');
    Route::delete('/certificates/{certificate}', [SiswaCertificateController::class, 'destroy'])->name('certificates.destroy');
});

// Public siswa profile (PALING AKHIR agar tidak bentrok dengan /siswa/portfolios)
Route::get('/siswa/{user:slug}', [PortfolioController::class, 'byUser'])->name('portfolios.user');

// Direktori siswa — letakkan setelah route bertingkat /siswa/* agar tidak konflik
Route::get('/siswa', [StudentDirectoryController::class, 'index'])->name('students.index');
