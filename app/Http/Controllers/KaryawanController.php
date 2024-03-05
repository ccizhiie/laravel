<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

use function Laravel\Prompts\table;

class KaryawanController extends Controller
{
    public function index(request $request)
    {

        $query = Karyawan::query();
        $query->select('karyawan.*');
        $query->orderBy('nama_lengkap');
        if (!empty($request->nama_karyawan)) {
            $query->where('nama_lengkap', 'like', '%' . $request->nama_karyawan . '%');
        }
        $karyawan = $query->paginate(10);

        return view('karyawan.index', compact('karyawan'));
    }

    public function store(request $request)
    {
        $nisn = $request->nisn;
        $nama_lengkap = $request->nama_lengkap;
        $jabatan = $request->jabatan;
        $no_hp = $request->no_hp;
        $password = Hash::make('12345');
        if ($request->hasFile('foto')) {
            $foto = $nisn . "." . $request->file('foto')->getClientOriginalExtension();
        } else {
            $foto = null;
        }

        try {
            $data = [
                'nisn' => $nisn,
                'nama_lengkap' => $nama_lengkap,
                'jabatan' => $jabatan,
                'no_hp' => $no_hp,
                'foto' => $foto,
                'password' => $password
            ];

            // $cek = DB::table('karyawan')->where('nisn', $nisn)->count();
            // if($cek=0){
            //     return Redirect::back()->with(['warning'=>'Nisn sudah digunakan']);
            // }
            $simpan = DB::table('karyawan')->insert($data);
            if ($simpan) {
                if ($request->hasFile('foto')) {
                    $folderPath = "public/uploads/karyawan/";
                    $request->file('foto')->storeAs($folderPath, $foto);
                }
                return Redirect::back()->with(['success' => 'data berhasil disimpan']);
            }
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'data gagal disimpan']);
        }
    }

    public function edit(Request $request)
    {
        $nisn = $request->nisn;
        $karyawan = DB::table('karyawan')->where('nisn', $nisn)->first();
        return view('karyawan.edit', compact('karyawan'));
    }

    public function update($nisn, request $request)
    {
        $nisn = $request->nisn;
        $nama_lengkap = $request->nama_lengkap;
        $jabatan = $request->jabatan;
        $no_hp = $request->no_hp;
        $password = Hash::make('12345');
        $old_foto = $request->old_foto;
        if ($request->hasFile('foto')) {
            $foto = $nisn . "." . $request->file('foto')->getClientOriginalExtension();
        } else {
            $foto = $old_foto;
        }

        try {
            $data = [
                'nama_lengkap' => $nama_lengkap,
                'jabatan' => $jabatan,
                'no_hp' => $no_hp,
                'foto' => $foto,
                'password' => $password
            ];
            $update = DB::table('karyawan')->where('nisn', $nisn)->update($data);
            if ($update) {
                if ($request->hasFile('foto')) {
                    $folderPath = "public/uploads/karyawan/";
                    $folderPathold = "public/uploads/karyawan/" . $old_foto;
                    Storage::delete($folderPathold);
                    $request->file('foto')->storeAs($folderPath, $foto);
                }
                return Redirect::back()->with(['success' => 'data berhasil diupdate']);
            }
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'data gagal diupdate']);
        }
    }

    public function delete($nisn){
        $delete = DB::table('karyawan')->where('nisn', $nisn)->delete();
        if($delete){
            return Redirect::back()->with(['success' => 'Data berhasil dihapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data gagal dihapus']);

        }
    }
}
