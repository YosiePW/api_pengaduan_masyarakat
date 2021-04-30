<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Pengaduan;
use JWTAuth;
use DB;
use Auth;


class PengaduanController extends Controller
{
    public $response;
    public $user;
    public function __construct(){
        $this->response = new ResponseHelper();

        $this->user = JWTAuth::parseToken()->authenticate();
    }

    public function getAllPengaduan($limit = NULL, $offset = NULL)
    {
        if($this->user->level == 'masyarakat'){
            $data["count"] = Pengaduan::where('id_user', '=', $this->user->id)->count();

            if($limit == NULL && $offset == NULL){
                $data["pengaduan"] = Pengaduan::where('id_user', '=', $this->user->id)->orderBy('tgl_pengaduan', 'desc')->with('kategori' ,'tanggapan', 'user')->get();
            } else {
                $data["pengaduan"] = Pengaduan::where('id_user', '=', $this->user->id)->orderBy('tgl_pengaduan', 'desc')->with('kategori' ,'tanggapan', 'user')->take($limit)->skip($offset)->get();
            }
        } else {
            $data["count"] = Pengaduan::count();

            if($limit == NULL && $offset == NULL){
                $data["pengaduan"] = Pengaduan::orderBy('tgl_pengaduan', 'desc')->with('kategori' ,'tanggapan', 'user')->get();
            } else {
                $data["pengaduan"] = Pengaduan::orderBy('tgl_pengaduan', 'desc')->with('kategori' ,'tanggapan', 'user')->take($limit)->skip($offset)->get();
            }
        }

        return $this->response->successData($data);
    }

    public function getById($id)
    {   
        $data["pengaduan"] = Pengaduan::where('id_pengaduan', $id)->with(['kategori','tanggapan', 'user'])->get();

        return $this->response->successData($data);
    }

    public function insert(Request $request)
    {
        $validator = Validator::make($request->all(), [
			'tgl_pengaduan' => 'required|string',
			'isi_laporan'   => 'required|string',
			'id_kategori'   => 'required',
			'foto'          => 'required',
		]);

		if($validator->fails()){
            return $this->response->errorResponse($validator->errors());
		}

        $foto = rand().$request->file('foto')->getClientOriginalName();
        $request->file('foto')->move(base_path("./public/uploads"), $foto);

		$pengaduan = new Pengaduan();
		$pengaduan->id_user         = $this->user->id;
		$pengaduan->id_kategori     = $request->id_kategori;
		$pengaduan->tgl_pengaduan   = $request->tgl_pengaduan;
		$pengaduan->isi_laporan     = $request->isi_laporan;
        $pengaduan->foto            = $foto;
        $pengaduan->status          = 'terkirim';
		$pengaduan->save();

        $data = Pengaduan::where('id_pengaduan','=', $pengaduan->id)->first();
        return $this->response->successResponseData('Data pengaduan berhasil terkirim', $data);
    }

    public function changeStatus(Request $request, $id_pengaduan)
    {
        $validator = Validator::make($request->all(), [
			'status'        => 'required|string',
		]);

		if($validator->fails()){
            return $this->response->errorResponse($validator->errors());
		}

		$pengaduan          = Pengaduan::where('id_pengaduan', $id_pengaduan)->first();
		$pengaduan->status  = $request->status;
		$pengaduan->save();

        return $this->response->successResponse('Status berhasil diubah');
    }

    public function report(Request $request)
    {
        $validator = Validator::make($request->all(), [
			'tahun' => 'required|numeric',
		]);

		if($validator->fails()){
            return $this->response->errorResponse($validator->errors());
		}

        $query = DB::table('pengaduans')
                    ->select('pengaduans.tgl_pengaduan', 'pengaduans.isi_laporan', 'pengaduans.status', 'kategoris.nama_kategori', 'users.nama')
                    ->join('users', 'users.id', '=', 'pengaduans.id_user')
                    ->join('kategoris', 'kategoris.id_kategori', '=', 'pengaduans.id_kategori')
                    ->whereYear('pengaduans.tgl_pengaduan', '=', $request->tahun);

        if($request->bulan != NULL){
            $query->WhereMonth('pengaduans.tgl_pengaduan', '=', $request->bulan);
        }
        if($request->tgl != NULL){
            $query->WhereDay('pengaduans.tgl_pengaduan', '=', $request->tgl);
        }
        
        $data = $query->get();
        
        return $this->response->successData($data);
    }

    public function findPengaduan(Request $request, $limit = NULL, $offset = NULL)
    {
        $find = $request->find;
        $data["count"] = Pengaduan::count();
        // $query = DB::table('pengaduans')->join('users', 'users.id', '=', 'pengaduans.id_user')
        //                                        ->join('kategoris', 'kategoris.id_kategori', '=', 'pengaduans.id_kategori')
        //                                        ->select('pengaduans.tgl_pengaduan', 'pengaduans.isi_laporan', 'pengaduans.status', 'kategoris.nama_kategori', 'users.nama')
        //                                        ->whereYear('pengaduans.tgl_pengaduan', 'like', "%$find%");
        if($limit == NULL && $offset == NULL){
            $data["pengaduan"] = Pengaduan::where('tgl_pengaduan','like', "%$find%")->orderBy('tgl_pengaduan', 'desc')->with('kategori', 'user')->get();
        } else {
            $data["pengaduan"] = Pengaduan::where('tgl_pengaduan','like', "%$find%")->orderBy('tgl_pengaduan', 'desc')->with('kategori', 'user')->take($limit)->skip($offset)->get();
        }

        return $this->response->successData($data);
        
    }

    //users
    public function getAll() {
        if(Auth::user()->level=="masyarakat"){
        $id = Auth::user()->id;
        $pengaduan=DB::table('pengaduans')
        ->join('users','users.id','=','pengaduans.id_user')
        ->select('pengaduans.id_pengaduan', 'pengaduans.id_user','pengaduans.isi_laporan', 'pengaduans.id_kategori','pengaduans.tgl_pengaduan', 'pengaduans.foto', 'pengaduans.status')
        ->where('pengaduans.id_user',$id)
        ->get();
        return $this->response->successData($pengaduan);
        }
    }
    

}
