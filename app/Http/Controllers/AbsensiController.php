<?php

namespace App\Http\Controllers;

use App\Models\Pengajuanizin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class AbsensiController extends Controller
{
    public function create()
    {
        $hariini = date("Y-m-d");
        $nisn = Auth::guard('karyawan')->user()->nisn;
        $cek = DB::table('absensi')->where('tgl_presensi', $hariini)->where('nisn', $nisn)->count();
        return view('absensi.create', compact('cek'));
    }

    public function store(Request $request)
    {
        $nisn = Auth::guard('karyawan')->user()->nisn;
        $tgl_presensi = date("Y-m-d");
        $jam = date("h:i:s");
        $latitudekantor = -8.142644;
        $longitudekantor = 112.585186;
        $lokasi = $request->lokasi;
        $lokasiuser = explode(",", $lokasi);
        $latitudeuser = $lokasiuser[0];
        $longitudeuser = $lokasiuser[1];

        $jarak = $this->distance($latitudekantor, $longitudekantor, $latitudeuser, $longitudeuser);
        $radius = round($jarak["meters"]);

        $absensi = DB::table('absensi')->where('tgl_presensi', $tgl_presensi)->where('nisn', $nisn);
        $cek = $absensi->count();
        $dataabsensi = $absensi->first();
        if ($cek > 0) {
            $ket = "out";
        } else {
            $ket = "in";
        }

        $image = $request->image;
        $folderPath = "public/uploads/absensi/";
        $formatName = $nisn . "-" . $tgl_presensi . "-" . $ket;
        $image_parts = explode(";base64", $image);
        $image_base64 = base64_decode($image_parts[1]);
        $fileName = $formatName . ".png";
        $file = $folderPath . $fileName;

        if ($radius > 180) {
            echo "error|anda berada diluar jangkauan, jarak anda " . $radius . "meter dari Kanesa|";
        } else {

            if ($cek > 0) {
                if (!empty($dataabsensi->jam_out)){
                    echo "Error|Anda sudah melakukan absen pulang!|out";
                } else {
                    $data_pulang = [
                        'jam_out' => $jam,
                        'foto_out' => $fileName,
                        'location_out' => $lokasi
                    ];
                    $update = DB::table('absensi')->where('tgl_presensi', $tgl_presensi)->where('nisn', $nisn)->update($data_pulang);
                    if ($update) {
                        echo "success|Terimakasih, hati-hati di jalan|out";
                        Storage::put($file, $image_base64);
                    } else {
                        echo "Error|Gagal absen!|out";
                    }
                }
                
            } else {
                $data = [
                    'nisn' => $nisn,
                    'tgl_presensi' => $tgl_presensi,
                    'jam_in' => $jam,
                    'foto_in' => $fileName,
                    'location_in' => $lokasi
                ];

                $simpan = DB::table('absensi')->insert($data);
                if ($simpan) {
                    echo "success|Terimakasih, selamat bekerja!|in";
                    Storage::put($file, $image_base64);
                } else {
                    echo "Error|Gagal absen!|in";
                }
            }
        }
    }

    //Menghitung Jarak
    function distance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $miles = (sin(deg2rad($lat1)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)));
        $miles = acos($miles);
        $miles = rad2deg($miles);
        $miles = $miles * 60 * 1.1515;
        $feet = $miles * 5280;
        $yards = $feet / 3;
        $kilometers = $miles * 1.609344;
        $meters = $kilometers * 1000;
        return compact('meters');
    }

    public function editprofile()
    {
        $nisn = Auth::guard('karyawan')->user()->nisn;
        $karyawan = DB::table('karyawan')->where('nisn', $nisn)->first();
        return view('absensi.editprofile', compact('karyawan'));
    }

    public function updateprofile(request $request)
    {
        $nisn = Auth::guard('karyawan')->user()->nisn;
        $nama_lengkap = $request->nama_lengkap;
        $no_hp = $request->no_hp;
        $password = Hash::make($request->password);
        $karyawan = DB::table('karyawan')->where('nisn', $nisn)->first();
        if($request->hasFile('foto')){
            $foto = $nisn . "." . $request->file('foto')->getClientOriginalExtension();
        } else {
            $foto = $karyawan->foto;
        }

        if (empty($request->password)) {
            $data = [
                'nama_lengkap' => $nama_lengkap,
                'no_hp' => $no_hp,
                'foto' => $foto
            ];
        } else {
            $data = [
                'nama_lengkap' => $nama_lengkap,
                'no_hp' => $no_hp,
                'password' => $password,
                'foto' => $foto
            ];
        }

        $update = DB::table('karyawan')->where('nisn', $nisn)->update($data);
        if( $update ) {
            if( $request->hasFile('foto')){
                $folderPath = "public/uploads/karyawan/";
                $request->file('foto')->storeAs($folderPath, $foto);
            }
            return Redirect::back()->with(['success'=>'data berhasil di update']);
        } else {
            return Redirect::back()->with(['error'=>'data gagal di update']);
        }
    }

    public function histori(){

        $namabulan = ["","Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember"];
        return view('absensi.histori', compact ('namabulan'));
    }

    public function gethistori(request $request){

        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $nisn = Auth::guard('karyawan')->user()->nisn;
        
        $histori = DB::table('absensi')
        ->whereRaw('MONTH(tgl_presensi)="'.$bulan.'"')
        ->whereRaw('YEAR(tgl_presensi)="'.$tahun.'"')
        ->where('nisn', $nisn)
        ->orderBy('tgl_presensi')
        ->get();

        return view('absensi.gethistori', compact ('histori'));
    }

    public function izin(){

        $nisn = Auth::guard('karyawan')->user()->nisn;
        $dataizin = DB::table('pengajuan_izin')->where('nisn', $nisn)->get();

        return view('absensi.izin', compact ('dataizin'));
    }

    public function buatizin(){

        return view('absensi.buatizin');
    }

    public function storeizin(Request $request){
        $nisn = Auth::guard('karyawan')->user()->nisn;
        $tgl_izin = $request->tgl_izin;
        $status = $request->status;
        $keterangan = $request->keterangan;
        $foto_bukti = $request->foto_bukti;

        $data = [
            'nisn'=> $nisn,
            'tgl_izin'=> $tgl_izin,
            'status'=> $status,
            'keterangan'=> $keterangan,
            'foto_bukti'=> $foto_bukti
        ];

        $simpan = DB::table('pengajuan_izin')->insert($data);

        if($simpan){
            return redirect('/absensi/izin')->with(['success'=>'data berhasil disimpan']);
        } else {
            return redirect('/absensi/izin')->with(['error'=>'data gagal disimpan']);
        }
    }

    public function monitoring(){
        return view('absensi.monitoring');
    }

    public function getabsensi(request $request){
        $tanggal = $request->tanggal;
        $absensi = DB::table('absensi')
        ->select('absensi.*','nama_lengkap')
        ->join('karyawan','absensi.nisn','=','karyawan.nisn')
        ->where('tgl_presensi', $tanggal)
        ->get();

        return view('absensi.getabsensi', compact ('absensi'));
    }

    public function tampilkanpeta(Request $request) {
        $id = $request->id;
        $absensi = DB::table('absensi')->where('id', $id)
        ->join('karyawan', 'absensi.nisn', '=', 'karyawan.nisn')
        ->first();
        return view('absensi.showmap', compact('absensi')); 
    }

    public function laporan(Request $request){
        $namabulan = ["","Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember"];
        $karyawan = DB::table('karyawan')->orderBy('nama_lengkap')->get();
        return view('absensi.laporan', compact('namabulan', 'karyawan'));
    }

    public function cetaklaporan(Request $request){
        $nisn = $request->nisn;
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $namabulan = ["","Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember"];
        $karyawan = DB::table('karyawan')->where('nisn', $nisn)->first();

        $absensi = DB::table('absensi')
        ->where('nisn', $nisn)
        ->whereRaw('MONTH(tgl_presensi)="'.$bulan.'"')
        ->whereRaw('YEAR(tgl_presensi)="'.$tahun.'"')
        ->orderBy('tgl_presensi')
        ->get();

        if(isset($_POST['excel'])) {
            $time = date("H:i:s");
            header("Content-type: application/vnd-ms-excel");
            header("Content-Disposition: attachment; filename=Laporan Karyawan $time.xls");
        }
        return view('absensi.cetaklaporanexcel', compact('bulan','tahun','namabulan', 'karyawan', 'absensi'));
    }

    public function izinsakit(Request $request){

        $query = Pengajuanizin::query();
        $query->select('id', 'tgl_izin', 'pengajuan_izin.nisn', 'nama_lengkap', 'jabatan', 'status', 'status_approved', 'keterangan');
        $query->join('karyawan', 'pengajuan_izin.nisn', '=', 'karyawan.nisn');
        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('tgl_izin', [$request->dari, $request->sampai]);
        }

        if (!empty($request->nisn)){
            $query->where('pengajuan_izin.nisn', $request->nisn);
        }

        if (!empty($request->nama_lengkap)){
            $query->where('nama_lengkap','like', '%' . $request->nama_lengkap . '%');
        }

        if ($request->status_approved === '0' || $request->status_approved === '1' || $request->status_approved === '2'){
            $query->where('status_approved', $request->status_approved);
        }
        $query->orderBy('tgl_izin', 'desc');
        $izinsakit = $query->paginate(10);
        $izinsakit->appends($request->all());
        return view('absensi.izinsakit', compact('izinsakit'));
    }

    public function approveizinsakit(request $request) {
        $status_approved = $request->status_approved;
        $id_izinsakit_form = $request->id_izinsakit_form;
        $update = DB::table('pengajuan_izin')->where('id', $id_izinsakit_form)->update([
            'status_approved' => $status_approved
        ]);

        if($update){
            return Redirect::back()->with(['success'=>'Data Berhasil di Update']);
        }else{
            return Redirect::back()->with(['warning'=>'Data Gagal di Update']);
        }
    }

    public function batalkanizinsakit($id){
        $update = DB::table('pengajuan_izin')->where('id', $id)->update([
            'status_approved' => 0
        ]);

        if($update){
            return Redirect::back()->with(['success'=>'Data Berhasil di Update']);
        }else{
            return Redirect::back()->with(['warning'=>'Data Gagal di Update']);
        }
    }

    public function cekpengajuanizin(Request $request) {
        $tgl_izin = $request->tgl_izin;
        $nisn = Auth::guard('karyawan')->user()->nisn;

        $cek = DB::table('pengajuan_izin')->where('nisn', $nisn)->where('tgl_izin', $tgl_izin)->count();
        return $cek;
    }
}
