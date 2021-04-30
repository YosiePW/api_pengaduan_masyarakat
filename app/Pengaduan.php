<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengaduan extends Model
{
    protected $fillable = ['id_pengaduan', 'tgl_pengaduan', 'id_user', 'isi_laporan', 'foto', 'status', 'id_kategori'];
    protected $table = "pengaduans";
    protected $primaryKey = 'id_pengaduan';

    public function kategori() {
        return $this->belongsTo('App\Kategori','id_kategori','id_kategori');
    }

    public function tanggapan() {
        return $this->belongsTo('App\Tanggapan','id_pengaduan','id_pengaduan');
    }

    public function user() {
        return $this->belongsTo('App\User','id_user','id');
    }

}
