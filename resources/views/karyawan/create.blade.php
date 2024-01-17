@extends('layouts.app') {{-- Assuming you have a layout file --}}

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Create Karyawan</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('karyawan.store') }}">
                            @csrf

                            <div class="form-group">
                                <label for="Nama">Nama</label>
                                <input type="text" class="form-control" id="Nama" name="Nama" required>
                            </div>

                            <div class="form-group">
                                <label for="NomorKaryawan">Nomor Karyawan</label>
                                <input type="text" class="form-control" id="NomorKaryawan" name="NomorKaryawan">
                            </div>

                            <div class="form-group">
                                <label for="Jabatan">Jabatan</label>
                                <input type="text" class="form-control" id="Jabatan" name="Jabatan">
                            </div>

                            <button type="submit" class="btn btn-primary">Create Karyawan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
