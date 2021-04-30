<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aspirasi extends Model
{
    protected $fillable = ['id_aspirasi', 'id_user', 'isi_aspirasi', 'id_kategori'];
    protected $table = "aspirasis";
    protected $primaryKey = 'id_aspirasi';

    public function kategori() {
        return $this->belongsTo('App\Kategori','id_kategori','id_kategori');
    }

    // public function tanggapan() {
    //     return $this->belongsTo('App\Tanggapan','id_aspirasi','id_aspirasi');
    // }

}
