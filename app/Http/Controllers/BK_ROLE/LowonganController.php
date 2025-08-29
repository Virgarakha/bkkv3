<?php

namespace App\Http\Controllers\BK_ROLE;

use App\Http\Controllers\Controller;
use App\Models\Lowongan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class LowonganController extends Controller
{
    public function index(){
        $lowongans = Lowongan::with('perusahaan.user')->get();
        return response()->json($lowongans, 200);
    }

    public function show($id){
        $lowongans = Lowongan::where('id', $id)->with('perusahaan.user')->get();
        return response()->json($lowongans, 200);
    }

    public function destroy($id) {
        $lowongan = Lowongan::find($id);

        if(!$lowongan) {
            return response()->json([
                'message' => 'Lowongan tidak ditemukan!'
            ], 401);
        }

        $lowongan->delete();

        return response()->json([
            'message' => 'Berhasil menghapus lowongan'
        ], 200);
    }

    public function verifikasi(Request $request, $id){
        $lowongan = Lowongan::find($id);

        $request->validate([
            'verifikasi' => 'required'
        ]);

        $lowongan->update([
            'verifikasi' => $request->verifikasi
        ]);

        return response()->json([
            'message' => 'Berhasil mengupdate verifikasi lowongan!'
        ], 200);
    }


public function manage(){
    $lowonganApprove = Lowongan::with('perusahaan.user')
        ->where('verifikasi', 'approved')
        ->get();

    $lowonganRejected = Lowongan::with('perusahaan.user')
        ->where('verifikasi', 'rejected')
        ->get();

    $lowonganPending = Lowongan::with('perusahaan.user')
        ->where('verifikasi', 'pending')
        ->get();

    return response()->json([
        'approve' => [
            'count' => $lowonganApprove->count(),
            'lowongan' => $lowonganApprove
        ],
        'rejected' => [
            'count' => $lowonganRejected->count(),
            'lowongan' => $lowonganRejected
        ],
        'pending' => [
            'count' => $lowonganPending->count(),
            'lowongan' => $lowonganPending
        ]
    ], 200);
}


}
