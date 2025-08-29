<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lamaran extends Model
{
    use HasFactory;
    protected $table = 'lamaran';
    protected $guarded = [];

    public function alumni(){
        return $this->belongsTo(Alumni::class);
    }
    public function perusahaan(){
        return $this->belongsTo(Perusahaan::class, 'perusahaan_id');
    }
    public function lowongan(){
        return $this->belongsTo(Lowongan::class, 'lowongan_id');
    }
    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
}
