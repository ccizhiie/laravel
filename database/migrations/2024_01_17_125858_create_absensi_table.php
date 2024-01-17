<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbsensiTable extends Migration
{
    public function up()
    {
        Schema::create('absensi', function (Blueprint $table) {
            $table->id('AbsensiID');
            $table->foreignId('KaryawanID')->constrained('karyawan', 'KaryawanID');
            $table->date('Tanggal')->nullable();
            $table->time('JamMasuk')->nullable();
            $table->string('Status')->nullable();
            $table->text('foto');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('absensi');
    }
}
