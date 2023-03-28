<?php

namespace App\Http\Controllers\Panel;

use App\Models\MessagesNotifications;
use App\Models\Services;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;

use App\Models\Privileges;
use App\Models\ProductSpecification;

class MessagesNotificationsController extends Controller
{
    public function __construct()
    {
            $this->middleware(function ($request, $next) {
            $this->check_settings(216);
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
        return view('admin.messages-notifications.add',['objects'=>Services::all()]);
    }

    public function privileges_hidden(){
        return view('admin.control-privileges.all-hidden',['objects'=>Privileges::where('parent_id',0)->where('hidden',1)->orderBy('orders','ASC')->get()]);

    }

    public function adv_adss()
    {
        return view('admin.control-privileges.adv',['objects'=>Privileges::where('adv',1)->get()]);
    }

    public function normal_ads()
    {
        return view('admin.control-privileges.normal',['objects'=>Privileges::where('adv',0)->get()]);
    }




    public function show_privileges($id=0)
    {
      $prev = Privileges::find($id);
      if(!$prev){
        return 0;
      }
      if($prev->hidden==0){
          $prev -> hidden = 1 ;
          $prev -> save();
          return redirect()->back()->with('success','تم الاخفاء بنجاح .');
      }else{
          $prev -> hidden = 0 ;
          $prev -> save();
          return redirect()->back()->with('success','تم الاظهار بنجاح .');
      }

    }

    public function adv_slider($id=0)
    {
        $ads = Privileges::find($id);
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




    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.control-privileges.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        MessagesNotifications::truncate();

        foreach (Services::all() as $service){
            if($request->message[$service->id]){
                foreach ($request->message[$service->id] as $key=>$value){
                    if($request->message[$service->id][$key] && $request->message_en[$service->id][$key] && $request->user_type_id[$service->id][$key]  ) {
                        $adv = new MessagesNotifications();
                        $adv->service_id = $service->id;
                        $adv->message = $request->message[$service->id][$key] ;
                        $adv->message_en = $request->message_en[$service->id][$key];
                        $adv->user_type_id = $request->user_type_id[$service->id][$key];
                        $adv->save();
                    }
                }
            }
        }

        return redirect()->back()->with('success','تم حفظ الرسائل بنجاح بنجاح .');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    	return view('admin.control-privileges.add',['object'=> Privileges::find($id)]);
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
        $object= Privileges::find($id);
        $this->validate($request, [
            'privilge' => 'required',
            'icon' => 'required',
//            'url' => 'required',
         ]);

        $object->privilge = $request->privilge;
        $object->icon = $request->icon;
        $object->url = $request->url ? $request->url : "" ;
		$object->save();

        Privileges::where('parent_id',$object->id)->delete();
        if($request->property){
            foreach ($request->property as $key=>$value){
                if($request->value[$key] && $request->property[$key] ) {
                    $adv = new Privileges();
                    $adv->parent_id = $object->id;
                    $adv->privilge = $value;
                    $adv->url = $request->value[$key];
                    $adv->save();
                }
            }
        }
		 return redirect()->back()->with('success','تم تعديل القائمة بنجاح .');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $prev = Privileges::find($id);
        if(!$prev){
            return 0;
        }
        if($prev->hidden==0){
            $prev -> hidden = 1 ;
            $prev -> save();
            return 1;
        }else{
            $prev -> hidden = 0 ;
            $prev -> save();
            return 1;
        }
    }

    public function change_sort(Request $request){
        foreach ($request->position as $key=>$value){
            $privilege= Privileges::find($value);
            $privilege->orders = $key;
            $privilege->save();

        }

    }


}
