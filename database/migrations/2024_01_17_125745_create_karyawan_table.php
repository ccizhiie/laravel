<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKaryawanTable extends Migration
{
    public function up()
    {
        Schema::create('karyawan', function (Blueprint $table) {
            $table->char('nisn');
            $table->string('nama_lengkap');
            $table->string('jabatan');
            $table->string('no_hp');
            $table->string('foto');
            $table->string('password');
            $table->string('remember_token')->nullable;
        });
    }

    public function down()
    {
        Schema::dropIfExists('karyawan');
    }
}
  