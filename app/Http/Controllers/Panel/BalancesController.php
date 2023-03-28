<?php

namespace App\Http\Controllers\Panel;

use App\Models\Balance;
use App\Models\DeviceTokens;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Categories;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;
class BalancesController extends Controller
{
    public function __construct()
    {

        $this->middleware(function ($request, $next) {
            $this->check_settings((new \ReflectionClass($this))->getShortName());
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
        return view('admin.balances.all',['objects'=>Balance::whereIn('balance_type_id',[3,11,13])->latest()->get()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.balances.add',['users'=>User::where('user_type_id',5)->get()]);
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
        	'user_id' => 'required',
            'price' => 'required|numeric',
//            'price' => 'required|numeric|min:1',
        ],[
            'price.min'=>'يجب ان يكون الرصيد اكبر من 0'
        ]);





		 $object = new Balance();
		 $object->user_id = $request->user_id;
         $object->price = $request->price;
//         $object->balance_type_id = 11;
        $object->balance_type_id = floatval($request->price)>0?11:13;
        $object->status = 1;
         $object->notes = $request->notes ? $request->notes :'' ;
         $object->save();


        $notification55 = new Notification();
        $notification55 -> sender_id = 1 ;
        $notification55 -> reciever_id = $object->user_id;
        $notification55 -> type = $object->balance_type_id;
        if($object->balance_type_id==11){
            $notification55 -> message =  "قامت الادارة بإضافة ".$object->price." ريال في حسابك " ;
            $notification55 -> message_en =  "Administration added ".$object->price." SAR to your Account" ;
        }else{
            $notification55 -> message =  "قامت الادارة بخصم ".$object->price." ريال في حسابك " ;
            $notification55 -> message_en =  "Administration deduct ".$object->price." SAR to your Account" ;

        }

        $notification55 ->save();



        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60*20);
        if($object->balance_type_id==11){
            if(@$notification55->getReciever->lang == "en"){
                $notification_title="Add balance to yours";
                $notification_message = $notification55->message_en;
            }else{
                $notification_title="إضافة رصيد لحسابك";
                $notification_message = $notification55->message;
            }
        }else{
            if(@$notification55->getReciever->lang == "en"){
                $notification_title="Deduct balance to yours";
                $notification_message = $notification55->message_en;
            }else{
                $notification_title="خصم رصيد لحسابك";
                $notification_message = $notification55->message;
            }

        }

        $notificationBuilder = new PayloadNotificationBuilder($notification_title);
        $notificationBuilder->setBody($notification_message)
            ->setSound('default');
        $notificationBuilder->setClickAction('FLUTTER_NOTIFICATION_CLICK');


        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(['data' =>[
            'notification_type'=> (int)$notification55 -> type,
            'notification_title'=> $notification_title ,
            'notification_message'=> $notification_message ,
            'notification_data' => null
        ]
        ]);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $token = @$notification55->getReciever->devices->count();
        $tokens = DeviceTokens::where('user_id',$notification55->reciever_id)->pluck('device_token')->toArray();
        $notification_ = @$notification55->getReciever->notification;
        if($token > 0 && $notification_) {
            $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);
            $downstreamResponse->numberSuccess();
            $downstreamResponse->numberFailure();
            $downstreamResponse->numberModification();
        }



		 return redirect()->back()->with('success','تم اضافة الرصيد بنجاح');
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
//    public function edit($id)
//    {
//    	return view('admin.balances.add',['object'=> Categories::find($id)]);
//    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
//    public function update(Request $request, $id)
//    {
//        $object= Categories::find($id);
//        $this->validate($request, [
//        'name' => 'required|max:100|min:3|unique:categories,name,'.$object->id.',id',
//            'name_en' => 'required|max:100|min:3|unique:categories,name_en,'.$object->id.',id',
//         ]);
//
//		$object->name = $request->name;
//        $object->name_en = $request->name_en;
////        $object->description = $request->description;
//
//        $file = $request->file('photo');
//        if ($request->hasFile('photo')) {
//            $old_file = 'uploads/'.$object->photo;
//            if(is_file($old_file))	unlink($old_file);
//            $fileName = 'category-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
//            $destinationPath = 'uploads';
//            $request->file('photo')->move($destinationPath, $fileName);
//            $object->photo=$fileName;
//        }
//
//         $object->save();
//		 return redirect()->back()->with('success','تم تعديل القسم الرئيسي بنجاح');
//    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
//    public function destroy($id)
//    {
//        $object = Categories::find($id);
//        $object ->delete();
//    }
}
