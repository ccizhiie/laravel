<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIzinTable extends Migration
{
    public function up()
    {
        Schema::create('izin', function (Blueprint $table) {
            $table->id('IzinID');
            $table->foreignId('KaryawanID')->constrained('karyawan', 'KaryawanID');
            $table->date('TanggalMulai')->nullable();
            $table->date('TanggalSelesai')->nullable();
            $table->string('JenisIzin')->nullable();
            $table->text('Keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('izin');
    }
}
