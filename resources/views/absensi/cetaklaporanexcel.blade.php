<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>A4</title>

    <!-- Normalize or reset CSS with your favorite library -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/7.0.0/normalize.min.css">

    <!-- Load paper.css for happy printing -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.4.1/paper.css">

    <!-- Set page size here: A5, A4 or A3 -->
    <!-- Set also "landscape" if you need -->
    <style>
        @page {
            size: A4
        }

        #title {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 18px;
            font-weight: bold;
        }

        .tabeldatakaryawan {
            margin-top: 40px;
        }

        .tabeldatakaryawan td {
            padding: 5px;
        }

        .tabelabsensi {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        .tabelabsensi tr th {
            border: 1px solid #131212;
            padding: 8px;
            background-color: #dbdbdb;
        }

        .tabelabsensi tr td {
            border: 1px solid #131212;
            padding: 5px;
            font-size: 12px;
        }

        .foto {
            width: 40px;
            height: 30px;
        }
    </style>
</head>

<!-- Set "A5", "A4" or "A3" for class name -->
<!-- Set also "landscape" if you need -->

<body class="A4">

    <!-- Each sheet element should have the class "sheet" -->
    <!-- "padding-**mm" is optional: you can set 10, 15, 20 or 25 -->
    <section class="sheet padding-10mm">
        <table style="width: 100%">
            <tr>
                <td style="width: 30px;">
                    <img src="{{ asset('assets/img/kanesapresence.png') }}" width="70" height="70" alt="">
                </td>
                <td>
                    <span id="title">LAPORAN ABSENSI KARYAWAN<br>
                        PERIODE{{ strtoupper($namabulan[$bulan]) }} {{ $tahun }}<br>
                        KANESAPRESENCE<br>
                    </span>
                    <span><i>Jl, Ngadiluwih, Kedungpedaringan, Kec. Kepanjen, Kabupaten Malang, Jawa Timur 65163</i></span>
                </td>
            </tr>
        </table>
        <table class="tabeldatakaryawan">
            <tr>
                <td rowspan="6">
                    @php
                    $path = Storage::url('uploads/karyawan/'.$karyawan->foto);
                    @endphp
                    <img src="{{ url($path) }}" alt="" width="100px" height="100px">
                </td>
            </tr>
            <tr>
                <td>NISN</td>
                <td>:</td>
                <td>{{ $karyawan->nisn }}</td>
            </tr>
            <tr>
                <td>Nama Karyawan</td>
                <td>:</td>
                <td>{{ $karyawan->nama_lengkap }}</td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>:</td>
                <td>{{ $karyawan->jabatan }}</td>
            </tr>
            <tr>
                <td>No.HP</td>
                <td>:</td>
                <td>{{ $karyawan->no_hp }}</td>
            </tr>
        </table>
        <table class="tabelabsensi">
            <tr>
                <th>No.</th>
                <th>Tanggal</th>
                <th>Jam Masuk</th>
                <th>Jam Pulang</th>
                <th>keterangan</th>
            </tr>
            @foreach ($absensi as $d)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ date("d-m-Y", strtotime($d->tgl_presensi)) }}</td>
                <td>{{ $d->jam_in}}</td>
                <td>{{ $d->jam_out != null ? $d->jam_out : 'Belum absen'}}</td>
                <td>
                    @if ($d->jam_in > '07:00')
                    Terlambat
                    @else
                    Tepat Waktu
                    @endif
                </td>
            </tr>
            @endforeach

        </table>

        <table width="100%" style="margin-top: 100px;">
            <tr>
                <td></td>
                <td style="text-align:center;">Malang, {{ date('d-m-Y') }}</td>
            </tr>
            <tr>
                <td style="text-align: center; vertical-align:bottom; height: 150px; color: rgba(255, 255, 255, 0.25)">
                    <u>Lasmono, S.Pd,MM</u><br>
                    <i><b>Kepala SMKN 1 Kepanjen</b></i>
                </td>
                <td style="text-align: center; vertical-align:bottom; height: 150px">
                    <u>Lasmono, S.Pd,MM</u><br>
                    <i><b>Kepala SMKN 1 Kepanjen</b></i>
                </td>
            </tr>
        </table>
    </section>

</body>

</html>