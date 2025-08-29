<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alumni extends Model
{
    use HasFactory;
    protected $table = 'alumni';
    protected $guarded = [];

    public function perusahaan(){
        return $this->belongsTo(Perusahaan::class, 'perusahaan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
