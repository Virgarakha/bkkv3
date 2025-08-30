<?php

namespace App\Http\Controllers\ADMIN_ROLE;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request){
        $query = User::query();

        if($request->has('role')){
            $query->where('role', $request->role);
        }

        $users = $query->get();

        $count = User::select('role')
            ->selectRaw('count(*) as total')
            ->groupBy('role')
            ->get();

        return response()->json([
            'users' => $users,
            'count_per_role' => $count
        ]);
    }

    public function show($id){
        $user = User::find($id);
        if(!$user){
            return response()->json(['message'=>'User tidak ditemukan'],404);
        }
        return response()->json($user);
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => ['required', Rule::in(['admin','perusahaan','bkk'])]
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);

        return response()->json([
            'message'=>'User berhasil dibuat',
            'user'=>$user
        ],201);
    }

    public function update(Request $request, $id){
        $user = User::find($id);
        if(!$user){
            return response()->json(['message'=>'User tidak ditemukan'],404);
        }

        $request->validate([
            'name' => 'sometimes|required|string|max:100',
            'email' => 'sometimes|required|email|unique:users,email,'.$id,
            'password' => 'nullable|string|min:6',
            'role' => ['sometimes','required', Rule::in(['admin','perusahaan','bkk'])]
        ]);

        $user->update([
            'name' => $request->name ?? $user->name,
            'email' => $request->email ?? $user->email,
            'password' => $request->password ? Hash::make($request->password) : $user->password,
            'role' => $request->role ?? $user->role
        ]);

        return response()->json([
            'message'=>'User berhasil diupdate',
            'user'=>$user
        ]);
    }

    public function destroy($id){
        $user = User::find($id);
        if(!$user){
            return response()->json(['message'=>'User tidak ditemukan'],404);
        }

        $user->delete();

        return response()->json(['message'=>'User berhasil dihapus']);
    }
}
