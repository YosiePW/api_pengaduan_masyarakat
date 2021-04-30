<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Tanggapan;
use DB;
use JWTAuth;
use Carbon\Carbon;

class TanggapanController extends Controller
{
    public $response;
    public $user;
    public function __construct(){
        $this->response = new ResponseHelper();

        $this->user = JWTAuth::parseToken()->authenticate();
    }

    public function send(Request $request, $id_pengaduan)
    {
        $validator = Validator::make($request->all(), [
			'tanggapan' => 'required|string'
		]);

		if($validator->fails()){
            return $this->response->errorResponse($validator->errors());
		}

		$tanggapan =  Tanggapan::where('id_pengaduan', $id_pengaduan)->first();
        
        //jika belum ada tanggapan brarti insert data baru
        if($tanggapan == NULL){
            $tanggapan = new Tanggapan();
        }
		
		$tanggapan->id_pengaduan   = $request->id_pengaduan;
		$tanggapan->tgl_tanggapan  = Carbon::now();
		$tanggapan->tanggapan      = $request->tanggapan;
		$tanggapan->id_petugas     = $this->user->id; //ambil id_petugas dari JWT token yang sedang aktif
		$tanggapan->save();

        return $this->response->successResponse('Data tanggapan berhasil dikirim');
    }

    public function getAllTanggapan($limit = NULL, $offset = NULL)
    {
        if($this->user->level == 'masyarakat'){
            $data["count"] = Tanggapan::where('id_user', '=', $this->user->id)->count();

            if($limit == NULL && $offset == NULL){
                $data["tanggapan"] = Tanggapan::where('id_user', '=', $this->user->id)->orderBy('tgl_tanggapan', 'desc')->get();
            } else {
                $data["tanggapan"] = Tanggapan::where('id_user', '=', $this->user->id)->orderBy('tgl_tanggapan', 'desc')->take($limit)->skip($offset)->get();
            }
        } else {
            $data["count"] = Tanggapan::count();

            if($limit == NULL && $offset == NULL){
                $data["tanggapan"] = Tanggapan::orderBy('tgl_tanggapan', 'desc')->get();
            } else {
                $data["tanggapan"] = Tanggapan::orderBy('tgl_tanggapan', 'desc')->take($limit)->skip($offset)->get();
            }
        }

        return $this->response->successData($data);
    }
}
