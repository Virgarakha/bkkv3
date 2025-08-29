<?php

namespace App\Http\Controllers\PERUSAHAAN_ROLE;

use App\Http\Controllers\Controller;
use App\Models\Alumni;
use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\Perusahaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LowonganController extends Controller
{
    public function index(){
        $user = auth()->user()->id;
        $perusahaan = Perusahaan::where('user_id', $user)->first();
        $lowongans = Lowongan::where('perusahaan_id', $perusahaan->id)->with('perusahaan')->get();
        return response()->json($lowongans, 200);
    }

    public function store(Request $request){
        $user = auth()->user()->id;
        $perusahaan = Perusahaan::where('user_id', $user)->first();

        $validator = Validator::make($request->all(), [
            'judul_lowongan' => 'required|string|max:225',
            'deskripsi' => 'required',
            'kualifikasi' => 'required',
            'jenis_pekerjaan' => 'required|string|max:225',
            'jurusan' => 'required|string|max:225',
            'lokasi' => 'required|string|max:225',
            'tanggal_mulai' => 'required',
            'tanggal_selesai' => 'required',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        Lowongan::create([
            'perusahaan_id' => $perusahaan->id,
            'judul_lowongan' => $request->judul_lowongan,
            'deskripsi' => $request->deskripsi,
            'kualifikasi' => $request->kualifikasi,
            'jenis_pekerjaan' => $request->jenis_pekerjaan,
            'jurusan' => $request->jurusan,
            'lokasi' => $request->lokasi,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'status' => 'open',
            'verifikasi' => 'pending'
        ]);

        return response()->json([
            'message' => 'Berhasil membuat lowongan baru!'
        ], 200);
    }

        public function update(Request $request, $id){
        $lowongan = Lowongan::find($id);

        $validator = Validator::make($request->all(), [
            'judul_lowongan' => 'required|string|max:225',
            'deskripsi' => 'required',
            'kualifikasi' => 'required',
            'jenis_pekerjaan' => 'required|string|max:225',
            'jurusan' => 'required|string|max:225',
            'lokasi' => 'required|string|max:225',
            'tanggal_mulai' => 'required',
            'tanggal_selesai' => 'required',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        $lowongan->update([
            'judul_lowongan' => $request->judul_lowongan,
            'deskripsi' => $request->deskripsi,
            'kualifikasi' => $request->kualifikasi,
            'jenis_pekerjaan' => $request->jenis_pekerjaan,
            'jurusan' => $request->jurusan,
            'lokasi' => $request->lokasi,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'status' => 'open',
        ]);

        return response()->json([
            'message' => 'Berhasil mengedit lowongan!'
        ], 200);
    }

    public function editStatus(Request $request, $id){
        $lowongan = Lowongan::find($id);

        $validator = Validator::make($request->all(), [
            'status' => 'required',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        $lowongan->update([
            'status' => $request->status,
        ]);

        return response()->json([
            'message' => 'Berhasil mengedit status!'
        ], 200);
    }

    public function show($id){
        $lowongan = Lowongan::where('id', $id)->with('perusahaan')->first();
        $lamaran = Lamaran::where('lowongan_id', $id)->with('alumni')->get();

        return response()->json([
            'lowongan' => $lowongan,
            'pelamar' => $lamaran
        ], 200);
    }

    public function accAlumni(Request $request, $id){
        $lamaran = Lamaran::find($id);

        if (!$lamaran) {
            return response()->json([
                'message' => 'Lamaran tidak ditemukan'
            ], 404);
        }

        $request->validate([
            'status' => 'required'
        ]);

        $lamaran->update([
            'status' => $request->status
        ]);

        $alumni = Alumni::find($lamaran->alumni_id);
        $lowongan = Lowongan::find($lamaran->lowongan_id);

        if ($alumni && $lowongan) {
            $alumni->update([
                'status_pekerjaan' => 'Bekerja',
                'perusahaan_id' => $lowongan->perusahaan_id
            ]);
        }

        return response()->json([
            'message' => 'Berhasil mengedit status pelamar'
        ], 200);
    }


}
