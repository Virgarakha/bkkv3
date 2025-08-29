<?php

namespace App\Http\Controllers\ALUMNI_ROLE;

use App\Http\Controllers\Controller;
use App\Models\Alumni;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SurveyController extends Controller
{
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'nisn' => 'required',
            'alamat' => 'required',
            'tahun_lulus' => 'required',
            'status_pekerjaan' => 'nullable',
            'perusahaan_id' => 'nullable'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        $alumni = Alumni::where('nisn', $request->nisn)->first();
        if(!$alumni){
            return response()->json([
                'message' => 'nisn tidak ditemukan atau nisn salah'
            ], 422);
        }

        $alumni->update([
            'alamat' => $request->alamat,
            'tahun_lulus' => $request->tahun_lulus,
            'status_pekerjaan' => $request->status_pekerjaan,
            'perusahaan_id' => $request->perusahaan_id
        ]);

        return response()->json([
            'message' => 'survey berhasil'
        ], 200);
    }
}
