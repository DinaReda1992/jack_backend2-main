<?php

namespace App\Http\Controllers\Website;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;

class FilesController extends Controller {
	public function __construct() {
      //  $this->middleware('auth');
	}

	public function test()
	{
		echo "string";
	}
    public function uploadimage(Request $request)
    {

        $file=$request->file('file');
        $fileName = 'file-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();

        $destinationPath = 'temp';
        $upload_success= $file->move($destinationPath, $fileName);

//        $img = \Intervention\Image\Facades\Image::make('temp/'.$fileName);
//
//
//        // and insert a watermark for example
//        $img->insert('site/images/logo.png', 'bottom-right', 10, 10);
//
//        // finally we save the image as a new file
//        $img->save('temp/'.$fileName);

        if( $upload_success ) {
             return response()->json(['fileName' => $fileName]);
        } else {
            return Response::json('error', 400);
        }
    }


    public function uploadImageReturnLink(Request $request){
        $file=$request->file('file');
        $fileName = 'file-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();

        $destinationPath = 'uploads';
        $upload_success= $file->move($destinationPath, $fileName);


        if( $upload_success ) {
            return response()->json(['fileName' => url('/uploads/'.$fileName) ]);
        } else {
            return Response::json('error', 400);
        }
    }


    public function removefile(Request $request){
        $old_file = 'temp/'.$request->photo;
        if(is_file($old_file))	unlink($old_file);
        return $request->photo;
    }
//    public function postUploadProfileImage(Request $request){
//        $file=$request->file('file');
//        $fileName = 'file-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
//
//        $destinationPath = 'uploads';
//        $upload_success= $file->move($destinationPath, $fileName);
//        if( $upload_success ) {
//            $user=User::find(Auth::User()->id);
//            $user->photo=$fileName;
//            $user->save();
//            return response()->json(['fileName' => $fileName]);
//        } else {
//            return Response::json('error', 400);
//        }
//
//    }


    public function postUploadProfileImage(Request $request){
        $file=$request->file('file');
        $fileName="";
        $oldFileName="";
        $user=User::find(Auth::user()->id);
            $fileName='file-'.time().'-'.uniqid().'-profile.'.$file->getClientOriginalExtension();
            $oldFileName=$user->photo;
            $user->photo=$fileName;

        $destinationPath = 'uploads';
        $upload_success= $file->move($destinationPath, $fileName);
        $old_file = $destinationPath."/".$oldFileName;
        if(is_file($old_file)) {
            unlink($old_file);
        }
        if( $upload_success ) {
            $user->save();
            return response()->json(['fileName' => $fileName]);
        } else {
            return Response::json('error', 400);
        }

    }
}
