<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTanggapansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tanggapans', function (Blueprint $table) {
            $table->bigIncrements('id_tanggapan');
            $table->unsignedBigInteger('id_pengaduan');
            $table->dateTime('tgl_tanggapan',  $precission = 0);
            $table->text('tanggapan');
            $table->unsignedBigInteger('id_petugas');
            $table->timestamps();

            $table->foreign('id_petugas')->references('id')->on('users');
            $table->foreign('id_pengaduan')->references('id_pengaduan')->on('pengaduans');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tanggapans');
    }
}
