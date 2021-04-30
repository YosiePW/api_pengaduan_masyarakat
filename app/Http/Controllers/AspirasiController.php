<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Aspirasi;
use JWTAuth;
use DB;
use Auth;


class AspirasiController extends Controller
{
    public $response;
    public $user;
    public function __construct(){
        $this->response = new ResponseHelper();

        $this->user = JWTAuth::parseToken()->authenticate();
    }

    public function getAllAspirasi($limit = NULL, $offset = NULL)
    {
        if($this->user->level == 'masyarakat'){
            $data["count"] = Aspirasi::where('id_user', '=', $this->user->id)->count();

            if($limit == NULL && $offset == NULL){
                $data["aspirasi"] = Aspirasi::where('id_user', '=', $this->user->id)->orderBy('id_aspirasi')->with('kategori')->get();
            } else {
                $data["aspirasi"] = Aspirasi::where('id_user', '=', $this->user->id)->orderBy('id_aspirasi')->with('kategori')->take($limit)->skip($offset)->get();
            }
        } else {
            $data["count"] = Aspirasi::count();

            if($limit == NULL && $offset == NULL){
                $data["aspirasi"] = Aspirasi::orderBy('id_aspirasi')->with('kategori')->get();
            } else {
                $data["aspirasi"] = Aspirasi::orderBy('id_aspirasi')->with('kategori')->take($limit)->skip($offset)->get();
            }
        }

        return $this->response->successData($data);
    }

    public function getById($id)
    {   
        $data["aspirasi"] = Aspirasi::where('id_aspirasi', $id)->with(['kategori'])->get();

        return $this->response->successData($data);
    }

    public function insert(Request $request)
    {
        $validator = Validator::make($request->all(), [
			// 'tgl_pengaduan' => 'required|string',
			'isi_aspirasi'   => 'required|string',
			'id_kategori'   => 'required',
		]);

		if($validator->fails()){
            return $this->response->errorResponse($validator->errors());
		}


		$aspirasi = new Aspirasi();
		$aspirasi->id_user         = $this->user->id;
		$aspirasi->id_kategori     = $request->id_kategori;
		$aspirasi->isi_aspirasi    = $request->isi_aspirasi;
		$aspirasi->save();

        $data = Aspirasi::where('id_aspirasi','=', $aspirasi->id)->first();
        return $this->response->successResponseData('Data aspirasi berhasil terkirim', $data);
    }

    public function getAll() {
        if(Auth::user()->level=="masyarakat"){
        $id = Auth::user()->id;
        $aspirasi=DB::table('aspirasis')
        ->join('users','users.id','=','aspirasis.id_user')
        ->select('aspirasis.id_aspirasi', 'aspirasis.id_user','aspirasis.isi_aspirasi', 'aspirasis.id_kategori')
        ->where('aspirasis.id_user',$id)
        ->get();
        return $this->response->successData($aspirasi);
        }
    }
    
}
