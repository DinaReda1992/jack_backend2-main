<?php

namespace App\Http\Controllers\Panel;

use App\Models\DeviceTokens;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Stores;
use App\Imports\StoresImport;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;

use Maatwebsite\Excel\Facades\Excel;
class StoresController extends Controller
{

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->check_settings(210);
            return $next($request);
        });
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.stores.all',['objects'=>Stores::orderBy('id','DESC')->get()]);
    }

    public function import_excel_file(){
        Excel::import(new StoresImport(), request()->file('excel_sheet'));
        return redirect()->back()->with('success','تم اضافة المتاجر بنجاح');

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.stores.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
        	'name' => 'required',
            'category_id' => 'required',
            'address' => 'required',
            'name_en' => 'required',
            'address_en' => 'required',
//            'photo' => 'required',
            'longitude' => 'required',
            'latitude' => 'required'
        ]);
		 $object = new Stores;
		 $object->name = $request->name;
        $object->category_id = $request->category_id;
         $object->address = $request->address;
         $object->name_en = $request->name_en;
         $object->address_en = $request->address_en;
        $object->longitude = $request->longitude;
         $object->latitude = $request->latitude;
         $file = $request->file('photo');
            if ($request->hasFile('photo')) {
                $fileName = 'store-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
                $destinationPath = 'uploads';
                $request->file('photo')->move($destinationPath, $fileName);
                $object->photo=$fileName;
            }
         $object->save();
		 return redirect()->back()->with('success','تم اضافة المتجر بنجاح');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    	return view('admin.stores.add',['object'=> Stores::find($id) ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $object= Stores::find($id);
        $this->validate($request, [
            'name' => 'required',
            'category_id' => 'required',
            'address' => 'required',
            'name_en' => 'required',
            'address_en' => 'required',
//            'photo' => 'required',
            'longitude' => 'required',
            'latitude' => 'required'
         ]);

        $object->name = $request->name;
        $object->category_id = $request->category_id;
        $object->address = $request->address;
        $object->name_en = $request->name_en;
        $object->address_en = $request->address_en;
        $object->longitude = $request->longitude;
        $object->latitude = $request->latitude;

        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $old_file = 'uploads/'.$object->photo;
            if(is_file($old_file))	unlink($old_file);
            $fileName = 'store-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileName);
            $object->photo=$fileName;
        }
        $object->save();

		 return redirect()->back()->with('success','تم تعديل المتجر بنجاح');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $object = Stores::find($id);
        $old_file = 'uploads/'.$object->photo;
        if(is_file($old_file))	unlink($old_file);
        $user = User::find($object->user_id);
        if($user){
            $user->store=0;
            $user->save();
        }

        $object->delete();
    }

    public function delete_photo_store($id=0){
        $object = Stores::find($id);
        $old_file = 'uploads/'.$object->photo;
        if(is_file($old_file))	unlink($old_file);
        $object->photo="";
        $object->save();
        return redirect()->back()->with('success','تم حذف صورة المتجر بنجاح');
    }


    public function approve_store($id=0)
    {
        $store = Stores::where('id',$id)->first();
      if($store){
          $store ->approved = 1 ;
          $store ->save();
      }

      $user = User::find($store->user_id);
      if($user){
          $user->store=1;
          $user->save();
      }


        $notification55 = new Notification();
        $notification55->sender_id = 1;
        $notification55->reciever_id = $user->id;
        $notification55->type = 1;
        $notification55->message = "وافقت  الادارة على متجرك";
        $notification55->save();

        $notification_title="موافقة على المتجر";
        $notification_message = $notification55->message;


        if(@$notification55->getReciever->notification==1){
            $this->send_fcm_notification($notification_title,$notification_message,$notification55,null,'default');
        }






        return redirect()->back()->with('success','تمت الموافقة على المتجر  بنجاح .');
    }

    public function send_fcm_notification($notification_title,$notification_message,$notification55,$object_in_push,$sound="default"){
        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60*20);
        $notificationBuilder = new PayloadNotificationBuilder($notification_title);
        $notificationBuilder->setBody($notification_message)
            ->setSound('default');
        $notificationBuilder->setClickAction('FLUTTER_NOTIFICATION_CLICK');
        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(['data' =>[
            'notification_type'=> (int)$notification55->type,
            'notification_title'=> $notification_title ,
            'notification_message'=> $notification_message ,
            'notification_data' => $object_in_push
        ]
        ]);
        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();
        $token = @$notification55->getReciever->devices->count();
        $tokens = DeviceTokens::where('user_id',$notification55->reciever_id)->pluck('device_token')->toArray();
        if($token > 0 ) {
            $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);
            $downstreamResponse->numberSuccess();
            $downstreamResponse->numberFailure();
            $downstreamResponse->numberModification();
        }
    }



}
