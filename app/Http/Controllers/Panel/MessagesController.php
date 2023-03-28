<?php

namespace App\Http\Controllers\Panel;

use App\Models\Notification;
use App\Models\Notifications;
use App\Models\Tickets;
use App\Models\User;
use Illuminate\Http\Request;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;
use App\Http\Controllers\Controller;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Messages;
class MessagesController extends Controller
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
    public function admin_messages(){
        $messages = Messages::select("*")
            ->whereIn('ticket_id', function($query){
                $query->select('id')
                    ->from(with(new Tickets())->getTable())
                    ->where('admin', 1);
            })
            ->groupBy('ticket_id')
            ->where(function ($query) {
                $query->where('sender_id', 1);
                $query->orWhere('reciever_id', 1);
            })
            ->orderBy('created_at','DESC')->get();

        return view('admin.messages.all',[
            'objects'=>$messages
        ]);
    }

    public function index(Request $request)
    {
//        $user_id = \auth()->user()->user_type_id==3?auth()->id():\auth()->user()->main_provider;
        $messages = Messages::select("*")
            ->groupBy('ticket_id')
            ->where(function ($query)  {
                $query->where('sender_id', 1);
                $query->orWhere('reciever_id', 1);
            })
            ->where(function ($query) use($request){
                if($request->status=="opened"){
                   $query->whereIn('ticket_id', function($query){
                        $query->select('id')
                            ->from(with(new Tickets())->getTable())
                        ->where('closed',0);
                    });
                }elseif ($request->status=='closed'){
                    $query->whereIn('ticket_id', function($query){
                        $query->select('id')
                            ->from(with(new Tickets())->getTable())
                            ->where('closed',1);
                    });
                }
            })
//            ->where('status',0)
            ->orderBy('created_at','DESC')->get();
        return view('admin.messages.all',[
            'objects'=>$messages
        ]);
    }
    
    public function add_ticket_post(Request $request,$id=0){
        if($id==0){
            $this->validate($request, [
                'name' => 'required',
                'user_id' => 'required',
            ]);
            $object = new Tickets();
            $object->name = $request->name;
            $object->admin = 1;
            $object->user_id = $request->user_id;
            $object->save();

            $object1 = new Messages();
            $object1->reciever_id = $request->user_id;
            $object1->sender_id = 1;
            $object1->ticket_id = $object->id;
            $object1->message = "قامت الادارة بفتح تذكرة جديدة";
            $object1->save();
            $object1->{"created_time"}= Carbon::parse($object->created_at)->diffForHumans();

            $notification55 = new Notification();
            $notification55 -> sender_id = 1 ;
            $notification55 -> reciever_id = $object1->reciever_id;
            $notification55 -> type = 2;
            $notification55->ads_id=$object->id;
            $notification55 -> message =  "قامت الادارة   بانشاء تذكرة معك" ;
            $notification55 -> message_en =  "Admin created ticket with you" ;
            $notification55 ->save();



            $optionBuilder = new OptionsBuilder();
            $optionBuilder->setTimeToLive(60*20);
            $notification_title="رسالة من الادارة";
            $notificationBuilder = new PayloadNotificationBuilder($notification_title);
            $notificationBuilder->setBody($notification55 -> message)
                ->setSound('default');

            $dataBuilder = new PayloadDataBuilder();

            $dataBuilder->addData(['data' =>[
                'notification_type'=> 2,
                'notification_title'=> $notification_title ,
                'notification_message'=> $notification55 -> message ,
                'key'=>$object->id,
                'notification_data' => $object1
            ]
            ]);
            $option = $optionBuilder->build();
            $notification = $notificationBuilder->build();
            $data = $dataBuilder->build();

            $token = @$notification55->getReciever->device_token;

            if($token) {
                $downstreamResponse = FCM::sendTo($token, $option,  @$notification55->getReciever->device_type == "android" ?  null : $notification, $data);
                $downstreamResponse->numberSuccess();
                $downstreamResponse->numberFailure();
                $downstreamResponse->numberModification();
            }


            return redirect()->back()->with('success','تم انشاء التذكرة بنجاح.');
        }else{

            $this->validate($request, [
                'name' => 'required',
                'user_id' => 'required',
            ]);
            $object = Tickets::find($id);
            $object->name = $request->name;
            $object->admin = 1;
            $object->user_id = $request->user_id;
            $object->save();
            return redirect()->back()->with('success','تم تعديل التذكرة بنجاح.');

        }
    }
    public function add_ticket(){
        return view('admin.messages.add_ticket',['users'=>User::where('user_type_id','!=',1)->where('user_type_id','!=',2)->orderBy('id','DESC')->get()]);
    }
    public function new_messages()
    {
        return view('admin.messages.new_messages',[
            'objects'=>Messages::where('reciever_id',1)
                ->whereIn('ticket_id', function($query){
                    $query->select('id')
                        ->from(with(new Tickets())->getTable());
//                        ->where('admin', 1);
                })
                ->where('status',0)->orderBy('id','DESC')->get()
        ]);
    }
    public function message($id=0)
    {
        $one_message = Messages::where('ticket_id',$id)->first();
        $user2=$one_message->sender_id == 1 ? User::find($one_message->reciever_id): User::find($one_message->sender_id);
        $all_messages = Messages::where('ticket_id',$id)
            ->orderBy('id','DESC')
            ->get();

        return view('admin.messages.add',[
            'objects' => $all_messages ,
            'other_user'=>$user2,
            'ticket_id'=>$id,
            'ticket'=>Tickets::find($id)
        ]);
    }
    public function closeTicket($id){
        $object = Tickets::find($id);
        $object->closed=1;
        $object->save();
        return redirect()->back()->with('success','تم اغلاق التذكرة , لاعادة فتحها قم بإرسال رساله للعضو');


    }
    public function last($id=0)
    {
        $user1=0;
        $user2=$id;
        $all_messages = Messages::where(function ($query) use($user1,$user2) {
            $query->where('sender_id', $user1)
                ->where('reciever_id', $user2);
        })->orWhere(function($query) use($user1,$user2) {
            $query->where('sender_id', $user2)
                ->where('reciever_id', $user1);
        })
            ->orderBy('id','DESC')
            ->get();

        return view('admin.messages.getchat',[
            'objects' => $all_messages ,
            'other_user'=>$user2
        ]);
    }
    public function cancelled_orders()
    {
        return view('admin.messages.cancelled_orders',['objects'=>Orders::where('status',5)->orderBy('id','DESC')->get()]);
    }
    public function approved_orders()
    {
        return view('admin.messages.approved_orders',['objects'=>Orders::where('status',1)->orderBy('id','DESC')->get()]);
    }
    public function payed_orders()
    {
        return view('admin.messages.payed_orders',['objects'=>Orders::where('status',2)->orderBy('id','DESC')->get()]);
    }
    public function on_progress_orders()
    {
        return view('admin.messages.on_progress_orders',['objects'=>Orders::where('status',3)->orderBy('id','DESC')->get()]);
    }
    public function done_orders()
    {
        return view('admin.messages.done_orders',['objects'=>Orders::where('status',4)->orderBy('id','DESC')->get()]);
    }
    public function normal_ads()
    {
        return view('admin.messages.normal',['objects'=>Messages::where('adv',0)->get()]);
    }
    public function adv_ads($id=0)
    {
        $ads = Messages::find($id);
        if(!$ads){
            return redirect()->back()->with('error','لا يوجد اعلان بهذا العنوان');
        }
        if($ads->adv==0){
            $ads -> adv = 1 ;
            $ads -> save();
            return redirect()->back()->with('success','تم تثبيت الاعلان في الرئيسية بنجاح .');
        }else{
            $ads -> adv = 0 ;
            $ads -> save();
            return redirect()->back()->with('success','تم ازالة التثبيت من الرئيسية بنجاح .');
        }

    }
    public function approve_order($id=0)
    {
        $order = Orders::find($id);
        $order->status=1;
        $order->save();
        return redirect()->back()->with('success','تمت الموافقة على الطلب بنجاح .');
    }
    public function on_progress_order($id=0)
    {
        $order = Orders::find($id);
        $order->status=3;
        $order->save();
        return redirect()->back()->with('success','تمت تحويل الطلب الى جاري العمل عليه لاصدار التقرير بنجاح .');
    }
    public function finish_order($id=0)
    {
        $order = Orders::find($id);
        $order->status=4;
        $order->save();
        return redirect()->back()->with('success','تم انهاء الطلب بنجاح .');
    }
    public function approve_payment($id=0)
    {
        $order = Orders::find($id);
        $order->status=2;
        $order->save();
        return redirect()->back()->with('success','تمت تحويل الطلب الى مدفوع .');


    }
    public function cancel_order($id=0)
    {
        $order = Orders::find($id);
        $order->status=5;
        $order->save();
        return redirect()->back()->with('success','تمت الغاء الطلب بنجاح .');


    }
    public function adv_slider($id=0)
    {
        $ads = Messages::find($id);
        if(!$ads){
            return redirect()->back()->with('error','لا يوجد اعلان بهذا العنوان');
        }
        if($ads->adv_slider==0){
            $ads -> adv_slider = 1 ;
            $ads -> save();
            return redirect()->back()->with('success','تم تثبيت الاعلان في القسم بنجاح .');
        }else{
            $ads -> adv_slider = 0 ;
            $ads -> save();
            return redirect()->back()->with('success','تم ازالة التثبيت من القسم  بنجاح .');
        }

    }
    public function orders_adv()
    {
        return view('admin.messages.ask_orders',['objects'=>AdsOrders::all()]);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.messages.add');
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
            'message' => 'required',
            'ticket_id' => 'required'
        ]);
        $ticket=Tickets::find($request->ticket_id);
        if(!$ticket) return abort(404);
        $object = new Messages();
        $object->reciever_id = $request->reciever_id;
        $object->sender_id = 1;
        $object->ticket_id = $request->ticket_id;
        $object->message = $request->message;
        $object->save();
        $object->{"created_time"}= Carbon::parse($object->created_at)->diffForHumans();

        $ticket->closed=0;
$ticket->save();

        $notification55 = new Notification();
        $notification55 -> sender_id = 1 ;
        $notification55 -> reciever_id = $request->reciever_id;
//        $notification55 -> message_id = $object->id;
        $notification55 -> type = 2;
        $notification55->ads_id=$request->ticket_id;

        $notification55 -> message =   "قام ".@$object->getSenderUser->username." بالرد على تذكرتك" ;
        $notification55 -> message_en =  @$object->getSenderUser->username. "replied on ticket " ;

        $notification55 ->save();

        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60*20);

        $notification_title = "رسالة من الادارة";
        $notificationBuilder = new PayloadNotificationBuilder($notification_title);
        $notificationBuilder->setBody($notification55 -> message)
            ->setSound('default');

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(['data' =>[
            'notification_type'=>2,
            'notification_title'=> $notification_title ,
            'notification_message'=> $notification55 -> message ,
            'notification_data' => $object,
            'key'=>$request->ticket_id

        ]
        ]);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $token = @$notification55->getReciever->device_token;

        if($token) {
            $downstreamResponse = FCM::sendTo($token, $option,  @$notification55->getReciever->device_type == "android" ?  null : $notification, $data);
            $downstreamResponse->numberSuccess();
            $downstreamResponse->numberFailure();
            $downstreamResponse->numberModification();
        }


//        $this->sendMessage($notification55,@$object->getRecieverUser->player_id,$notification55->message);


        return redirect()->back()->with('success','تم اضافة الرد بنجاح .');

    }


    public function sendMessage($content,$player_id,$message){

//        $contenta      = array(
//            "en" => $message
//        );
//        $fields = array(
//            'app_id' => "48509ab2-d54b-408b-ad23-91f73e425894",
//            'include_player_ids' => array($player_id),
//            'data' => array("data" => $content),
//            'contents' => $contenta
//        );
//
//        $fields = json_encode($fields);
////        print("\nJSON sent:\n");
////        print($fields);
//
//        $ch = curl_init();
//        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
//        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
//            'Authorization: Basic Mjg3MjgzYWQtZGI0Yi00YTI1LWFiMGEtYTFiNDU4MTRjZmI2'));
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
//        curl_setopt($ch, CURLOPT_HEADER, FALSE);
//        curl_setopt($ch, CURLOPT_POST, TRUE);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
//
//        $response = curl_exec($ch);
//        curl_close($ch);
//
//        return $response;
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
        return view('admin.messages.add',['object'=> Messages::find($id)]);
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
        $object= Messages::find($id);
        $this->validate($request, [
            'name' => 'required|max:100|min:3|unique:categories,name,'.$object->id.',id',
        ]);

        $object->name = $request->name;
        $object->save();
        return redirect()->back()->with('success','تم تعديل العضو بنجاح .');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $ads = Messages::find($id);
        if($ads!=false){
            $ads = Messages::find($ads->id);
            foreach (AdsPhotos::where('ads_id',$id)->get() as  $photo) {
                $old_file = 'uploads/'.$photo->photo;
                if(is_file($old_file))	unlink($old_file);
                $photo->delete();
            }
            $ads -> delete();
        }
    }

    public function delete_ticket($id=0)
    {
        $ticket = Tickets::find($id);
//        dd($ticket);
        if($ticket){
            foreach (Messages::where('ticket_id',$ticket->id)->get() as  $message) {
                foreach (Notification::where('message_id',$message->id)->get() as $notification) {
                    $notification->delete();
                }
                $message->delete();
            }
            $ticket -> delete();
        }

        return  redirect()->back()->with('success','تم حذف التذكرة بنجاح');
    }


}
