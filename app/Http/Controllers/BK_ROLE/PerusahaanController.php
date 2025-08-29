<?php

namespace App\Http\Controllers\BK_ROLE;

use App\Http\Controllers\Controller;
use App\Models\Lowongan;
use App\Models\Perusahaan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PerusahaanController extends Controller
{
    // CRUD PERUSAHAAN
    public function index(){
        $perusahan = Perusahaan::with('user')->get();
        return response()->json($perusahan, 200);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:225',
            'emailuser' => 'required|email|max:225',
            'password' => 'required|string|max:225',
            'nama_perusahaan' => 'required|string|max:255',
            'alamat'          => 'required|string|max:255',
            'email'           => 'required|email|unique:perusahaan,email',
            'no_telp'         => 'required|string|max:100',
            'profil'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->emailuser,
            'password' => Hash::make($request->password)
        ]);


        $profilPath = null;
        if ($request->hasFile('profil')) {
            $profilPath = $request->file('profil')->store('profil_perusahaan', 'public');
        }

        $perusahaan = Perusahaan::create([
            'nama_perusahaan' => $request->nama_perusahaan,
            'alamat'          => $request->alamat,
            'email'           => $request->email,
            'no_telp'         => $request->no_telp,
            'user_id'         => $user->id,
            'profil'          => $profilPath,
        ]);

        return response()->json([
            'message' => 'Berhasil menambah data perusahaan',
            'data'    => $perusahaan
        ], 201);
    }



    public function show($id){
        $perusahan = Perusahaan::where('id', $id)->with('user')->first();
        $lowongan = Lowongan::where('perusahaan_id', $id)->get();
        if(!$perusahan){
            return response()->json([
                'message' => 'Data perusahaan tidak ditemukan!'
            ], 401);
        }

        return response()->json([
            'perusahaan' => $perusahan,
            'lowongan' => $lowongan
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $perusahaan = Perusahaan::find($id);

        if(!$perusahaan){
            return response()->json([
                'message' => 'Data perusahaan tidak ditemukan!'
            ], 401);
        }


        $validator = Validator::make($request->all(), [
        'nama_perusahaan' => 'required|string|max:255',
        'alamat'          => 'required|string|max:255',
        'email'           => 'required|email',
        'no_telp'         => 'required|string|max:100',
        'user_id'         => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $perusahaan->update([
            'nama_perusahaan' => $request->nama_perusahaan,
            'alamat' => $request->alamat,
            'email' => $request->email,
            'no_telp' => $request->no_telp,
            'user_id' => $request->user_id,
        ]);

        return response()->json([
            'message' => 'Berhasil mengedit data perusahaan',
            'data'    => $perusahaan
        ], 200);
    }

    public function destroy($id){
        $perusahaan = Perusahaan::find($id);

        if(!$perusahaan){
            return response()->json([
                'message' => 'Data perusahaan tidak ditemukan!'
            ], 401);
        }

        $perusahaan->delete();

        return response()->json([
            'message' => 'Berhasil menghapus data'
        ]);
    }
}
