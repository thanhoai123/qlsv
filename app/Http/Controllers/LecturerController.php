<?php

namespace App\Http\Controllers;

use App\Giangvien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Database\QueryException;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class LecturerController extends Controller
{
    public function index(){
        return view('lecturers.index');
    }
    public function datajson(Request $request){
        $where = [];
//        if (isset($request->search['custom']['typesearch'])){
//            if(($request->search['custom']['typesearch'])=="0"){
//                if($request->search['custom']['name']){
//                    $where[]= ['name','like', '%' . trim($request->search['custom']['name']) . '%'];
//                }
//                if($request->search['custom']['email']){
//                    $where[]= ['email','like', '%' . trim($request->search['custom']['email']) . '%'];
//                }
//            }
//            if (($request->search['custom']['typesearch'])=="1"){
//                if($request->search['custom']['name']){
//                    $where[]= ['name',trim($request->search['custom']['name'])];
//                }
//                if($request->search['custom']['email']){
//                    $where[]= ['email',trim($request->search['custom']['email'])];
//                }
//            }
//        }

        DB::statement(DB::raw('set @rownum=0'));
        $subject=Giangvien::select([
            DB::raw('@rownum  := @rownum  + 1 AS rownum'),
            'id',
            'magv',
            'hogv',
            'tengv',
            'ngaysinh',
            'gioitinh',
            'hocham',
            'hocvi',
        ])->orderBy('giangviens.id', 'desc')->get();
        $datatables = DataTables::of($subject)
            ->addColumn('hotengv',function ($data){
                return $data->hogv." ".$data->tengv;
            })
            ->addColumn('gioitinhgv',function ($data){
                if($data->gioitinh==1){
                    return 'Nam';
                }else{
                    return 'N???';
                }

            })
            ->addColumn('action', function ($data) {

                return view('modals.btn-action-modal',
                    [
                        'edit' => '#edit_lecturer',
                        'id' => $data->id,
                        'urlEdit' => route('lecturer.postEdit', ['id' => $data->id]),
                        'detail' => route('lecturer.getDetail', ['id' => $data->id]),
                        'destroy' => route('lecturer.getDestroy',['id' => $data->id])
                    ]);
            })
            ->rawColumns([ 'rownum', 'action']);
        if ($keyword = $request->get('search')['value']) {
            $datatables->filterColumn('rownum', 'whereRaw', '@rownum  + 1 like ?', ["%{$keyword}%"]);
        }
        return $datatables->make(true);
    }
    public function addlecturer(Request $request){
        $data=$request->only(['add_magv','add_hogv','add_tengv','add_gioitinh','add_ngaysinh','add_hocham','add_hocvi']);
        $lecturer=new Giangvien;
        $lecturer->magv=$data['add_magv'];
        $lecturer->hogv=$data['add_hogv'];
        $lecturer->tengv=$data['add_tengv'];
        $lecturer->gioitinh=$data['add_gioitinh'];
        $lecturer->ngaysinh=$data['add_ngaysinh'];
        $lecturer->hocham=$data['add_hocham'];
        $lecturer->hocvi=$data['add_hocvi'];
        $lecturer->save();
//        foreach ($monhoc as $mh) {
//            DB::table('diems')->insert(
//                ['monhoc_id' => $mh->monhoc_id, 'sinhvien_id' => $id]
//            );
//        }
//        foreach ($data['add_monhoc'] as $mh) {
//            DB::table('diems')->insert(
//                ['monhoc_id' => $mh, 'sinhvien_id' => $id]
//            );
//        }

        //tau mu???n l??u v??o b???ng ??i???m 2 gi?? tr??? 1,2,3 l??c n??y cho 3 h??ng


        //        $table->name=$data['name'];
//        $table->email=$data['email'];
//        $table->password=bcrypt($data['password']);
//        $table->level=$data['level'];
//        $table->picture='user.png';
//        $table->save();
//        return redirect('admin/lockscreen');
        try{
            return Response::json([
                'error' => 0,
                //c??i n??y nek $monhocs th?? =1,2,3
                'message' =>  'Th??m th??nh c??ng '
            ]);
        }catch (QueryException $e){
            return Response::json([
                'error' => 1,
                'message' =>$e
            ]);
        }
    }
    /**
     * X??a 1 m???c
     */
    public function destroy($id)
    {
        try {
            Giangvien::destroy($id);
            return Response::json([
                'error' => 0,
                'message' => 'X??a th??nh c??ng '
            ]);
        } catch (QueryException $e) {
            return Response::json([
                'error' => 1,
                'message' => $e
            ]);
        }

    }
    public function detail($id)
    {
        $model = Giangvien::find($id);
        return Response::json($model);
    }
    public function postEdit(Request $request,$id){
        $model = Giangvien::find($id);
        $model->magv = $request->edit_magv;
        $model->hogv = $request->edit_hogv;
        $model->tengv = $request->edit_tengv;
        $model->gioitinh =  $request->edit_gioitinh;
        $model->ngaysinh =  $request->edit_ngaysinh;
        $model->hocham =  $request->edit_hocham;
        $model->hocvi =  $request->edit_hocvi;
        $model->save();
        try{
            return Response::json([
                'error' => 0,
                'message' => 'S???a Th??nh C??ng '.$request->name
            ]);
        }catch (QueryException $e){
            return Response::json([
                'error' => 1,
                'message' =>$e
            ]);
        }
    }
}
