<?php

namespace App\Http\Controllers;

use App\Models\Alumni;
use App\Models\Lamaran;
use App\Models\Lowongan;
use Illuminate\Http\Request;

class GrafikController extends Controller
{
    public function PersentaseStatus(){
        $alumniBekerja = Alumni::where('status_pekerjaan', 'Bekerja')->get()->count();
        $alumniKuliah = Alumni::where('status_pekerjaan', 'Kuliah')->get()->count();
        $alumniWirausaha = Alumni::where('status_pekerjaan', 'Wirausaha')->get()->count();
        $alumniBelum = Alumni::where('status_pekerjaan', 'Belum Bekerja')->get()->count();
        $alumniLainnya = Alumni::where('status_pekerjaan', 'Lainnya')->get()->count();

        return response()->json([
            'Bekerja' => $alumniBekerja,
            'Kuliah' => $alumniKuliah,
            'Belum Bekerja' => $alumniBelum,
            'Lainnya' => $alumniLainnya,
        ], 200);
    }

    public function PersentaseKesesuainJurusan() {

        $lamaran = Lamaran::with(['alumni', 'lowongan'])->get();

        $tidakSesuai = 0;
        $sesuai = 0;

        foreach($lamaran as $l) {
            if ($l->alumni && $l->lowongan) {
                if ($l->alumni->jurusan === $l->lowongan->jurusan) {
                    $sesuai += 1;
                } else {
                    $tidakSesuai += 1;
                }
            }
        }

        return response()->json([
            'total_lamaran' => $lamaran->count(),
            'sesuai_jurusan' => $sesuai,
            'tidak_sesuai_jurusan' => $tidakSesuai
        ]);
    }

    public function PersentasePendaftarTerbanyak() {

        $lamaranPerLowongan = Lamaran::select('lowongan_id')
            ->selectRaw('count(*) as total_pendaftar')
            ->groupBy('lowongan_id')
            ->orderByDesc('total_pendaftar')
            ->get();

        $result = $lamaranPerLowongan->map(function($item){
            $lowongan = Lowongan::find($item->lowongan_id);
            return [
                'lowongan_id' => $item->lowongan_id,
                'judul_lowongan' => $lowongan ? $lowongan->judul_lowongan : null,
                'total_pendaftar' => $item->total_pendaftar
            ];
        });

        return response()->json($result);
    }


}
