<?php

namespace App\Http\Controllers\BK_ROLE;

use App\Http\Controllers\Controller;
use App\Models\Lamaran;
use Illuminate\Http\Request;

class LamaranController extends Controller
{
    public function index(){
        $lamarans = Lamaran::with(['alumni', 'lowongan.perusahaan.user'])->get();
        return response()->json($lamarans, 200);
    }

    public function show($id){
        $lamaran = Lamaran::where('id', $id)->with(['alumni', 'lowongan.perusahaan.user'])->first();
        if(!$lamaran){
            return response()->json([
                'message' => 'Lamaran tidak ditemukan!'
            ], 422);
        }
        return response()->json($lamaran, 200);
    }

    public function lamaranFilter(){
        $lamaranditerima = Lamaran::where('status', 'diterima')->with(['alumni', 'lowongan.perusahaan.user'])->get();
        $lamaranditolak = Lamaran::where('status', 'ditolak')->with(['alumni', 'lowongan.perusahaan.user'])->get();
        $lamarandikirim = Lamaran::where('status', 'dikirim')->with(['alumni', 'lowongan.perusahaan.user'])->get();

        return response()->json([
            'diterima' => [
                'count' => $lamaranditerima->count(),
                'lamaran' => $lamaranditerima
            ],
            'ditolak' => [
                'count' => $lamaranditolak->count(),
                'lamaran' => $lamaranditolak
            ],
            'dikirim' => [
                'count' => $lamarandikirim->count(),
                'lamaran' => $lamarandikirim
            ],
        ], 200);
    }
}
