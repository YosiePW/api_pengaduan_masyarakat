<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $fillable = ['id_kategori', 'nama_kategori'];
    protected $hidden = ['created_at', 'updated_at'];
    protected $table = "kategoris";
    protected $primaryKey = 'id_kategori';
}
