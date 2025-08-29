<?php

namespace App\Http\Controllers\ALUMNI_ROLE;

use App\Http\Controllers\Controller;
use App\Models\Alumni;
use App\Models\Lamaran;
use App\Models\Lowongan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LowonganController extends Controller
{
    public function index(){
        $lowongans = Lowongan::where('status', 'open')->with('perusahaan.user')->get();
        return response()->json($lowongans, 200);
    }

    public function show($id){
        $lowongan = Lowongan::where('id', $id)->with('perusahaan.user')->first();
        return response()->json($lowongan, 200);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'nisn' => 'required',
            'lowongan_id' => 'required|integer',
            'cv' => 'required|file|mimes:pdf|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $alumni = Alumni::where('nisn', $request->nisn)->first();
        if (!$alumni) {
            return response()->json([
                'message' => 'nisn tidak ditemukan atau nisn salah'
            ], 422);
        }

        $cvPath = null;
        if ($request->hasFile('cv')) {
            $cvPath = $request->file('cv')->store('cv', 'public');
        }

        Lamaran::create([
            'alumni_id' => $alumni->id,
            'lowongan_id' => $request->lowongan_id,
            'tanggal_lamar' => now(),
            'status' => 'dikirim',
            'cv' => $cvPath,
        ]);

        return response()->json([
            'message' => 'Berhasil mendaftar lowongan, pantau akun email kamu ya untuk informasi selanjutnya!'
        ], 200);
    }

}
