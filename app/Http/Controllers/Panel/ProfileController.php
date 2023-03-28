<?php

namespace App\Http\Controllers\Panel;

use App\Models\BankTransfer;
use App\Models\Countries;
use App\Models\Messages;
use App\Models\Orders;
use App\Models\Packages;
use App\Models\Projects;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{





    public function edit_profile()
    {
        return view('admin.users.edit_profile',['object'=>User::find(Auth::user()->id) ]);
    }

    /**
     * @return string
     */
    public function edit_profile_post(Request $request)
    {
        $object = User::find(Auth::user()->id);
        $this->validate($request, [
            'username' => 'required|unique:users,username,'.$object->id.',id',
//            'last_name' => 'required|max:60|min:3',
            'phone' => 'required|regex:/[0-9]/|min:9|unique:users,phone,'.$object->id.',id',
            'email' => 'required|email|unique:users,email,'.$object->id.',id' ,
//            'gender' => 'required',
//            'phonecode' => 'required',
            'password' => $request->password ? 'same:password_confirmation|min:6' : '',
            'password_confirmation' => $request->password ? 'same:password' : '',
        ]);

        $object->username= $request->username;
        $object->email = $request->email;
        $object->phone = $request->phone;
//        $object->phonecode = $request->phonecode;
//        $object->gender = $request->gender;
//        $object->privilege_id =$request->privilege_id;
        if($request->password) {
            $object->password = bcrypt($request->password);
        }
        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $old_file = 'uploads/'.$object->photo;
            if(is_file($old_file))	unlink($old_file);
            $fileName = 'profile-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileName);
            $object->photo=$fileName;
        }
        $object->save();


        return redirect()->back()->with('success','تم تعديل بياناتك بنجاح .');
    }




}
