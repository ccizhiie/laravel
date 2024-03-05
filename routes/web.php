<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KaryawanController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/



route::middleware(['guest:karyawan'])->group(function () {
    route::get('/', function () {
        return view('auth.login');
    })->name('login');
    route::post('/proseslogin', [AuthController::class,'proseslogin']);
});

route::middleware(['guest:user'])->group(function () {
    Route::get('/panel', function () {
        return view('auth.loginadmin');
    })->name('loginadmin');
    route::post('/prosesloginadmin', [AuthController::class,'prosesloginadmin']);
});

route::middleware(['auth:karyawan'])->group(function () {
    route::get('/dashboard', [DashboardController::class, 'index']);
    route::get('/proseslogout', [AuthController::class,'proseslogout']);

    //absensi
    route::get('/absensi/create', [AbsensiController::class,'create']);
    route::post('/absensi/store', [AbsensiController::class,'store']);

    //edit profile
    route::get('/editprofile', [AbsensiController::class,'editprofile']);
    route::post('/absensi/{nisn}/updateprofile', [AbsensiController::class,'updateprofile']);

    //histori
    route::get('/absensi/histori', [AbsensiController::class, 'histori']);
    route::post('/gethistori', [AbsensiController::class, 'gethistori']);

    //izin
    route::get('/absensi/izin', [AbsensiController::class,'izin']);
    route::get('/absensi/buatizin', [AbsensiController::class,'buatizin']);
    route::post('/absensi/storeizin', [AbsensiController::class,'storeizin']);
    route::post('/absensi/cekpengajuanizin', [AbsensiController::class,'cekpengajuanizin']);
});

route::middleware(['auth:user'])->group(function () {
    route::get('/proseslogoutadmin',[AuthController::class, 'proseslogoutadmin']);
    route::get('/panel/dashboardadmin',[DashboardController::class, 'dashboardadmin']);

    //karyawan
    route::get('/karyawan',[KaryawanController::class, 'index']);
    route::post('/karyawan/store',[KaryawanController::class, 'store']);
    route::post('/karyawan/edit',[KaryawanController::class, 'edit']);
    route::post('/karyawan/{nisn}/update',[KaryawanController::class, 'update']);
    route::post('/karyawan/{nisn}/delete',[KaryawanController::class, 'delete']);

    //absensi
    route::get('/absensi/monitoring', [AbsensiController::class,'monitoring']);
    route::post('/getabsensi', [AbsensiController::class,'getabsensi']);
    route::post('/tampilkanpeta', [AbsensiController::class,'tampilkanpeta']);
    route::get('/absensi/laporan', [AbsensiController::class,'laporan']);
    route::post('/absensi/cetaklaporan', [AbsensiController::class,'cetaklaporan']);
    route::get('/absensi/izinsakit', [AbsensiController::class,'izinsakit']);
    route::post('/absensi/approveizinsakit', [AbsensiController::class,'approveizinsakit']);
    route::get('/absensi/{id}/batalkanizinsakit', [AbsensiController::class,'batalkanizinsakit']);

});