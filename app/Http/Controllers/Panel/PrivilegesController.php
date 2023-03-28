<?php

namespace App\Http\Controllers\Panel;

use App\Models\PrivilegesCountConditions;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;

use App\Models\Privileges;
use App\Models\ProductSpecification;

class PrivilegesController extends Controller
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
    public function index(Request $request)
    {
        if($request->type && $request->type=='hall'){
          $object= Privileges::where('parent_id',0)->where('hidden',0)->where('is_provider',1)->orderBy('orders','ASC')->get();
        }
        else{
            $object= Privileges::where('parent_id',0)->where('hidden',0)->where('is_provider',0)->orderBy('orders','ASC')->get();

        }
        return view('admin.control-privileges.all',['objects'=>$object]);
    }

    public function privileges_hidden(Request $request){
        if($request->type && $request->type=='hall') {
            $object=Privileges::where('parent_id',0)->where('hidden',1)->where('is_provider',1)->orderBy('orders','ASC')->get();
        }
        else{
            $object=Privileges::where('parent_id',0)->where('hidden',1)->where('is_provider',0)->orderBy('orders','ASC')->get();

        }
            return view('admin.control-privileges.all-hidden',['objects'=>$object]);

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
        $this->validate($request, [
        	'privilge' => 'required',
            'icon' => 'required',
//            'url' => 'required',
        ]);
		 $object = new Privileges();
		 $object->privilge = $request->privilge ? $request->privilge: " " ;
         $object->icon = $request->icon;
         $object->is_provider=$request->is_provider?1:0;
         $object->url = $request->url ? $request->url: "" ;
        $object->controller = $request->controller ? $request->controller: "" ;
        $object->model = $request->model ? $request->model: "" ;

        $object->save();
        if($request->property){
            foreach ($request->property as $key=>$value){
                if( $request->property[$key] ) {
                    $adv = new Privileges();
                    $adv->parent_id = $object->id;
                    $adv->privilge = $value ? $value : "" ;
                    $adv->url = $request->value[$key]?:'';
                    $adv->hidden=@$request->is_hidden[$key]?1:0;
                    $adv->model = $request->adv_model[$key]?:'';
                    $adv->card_color = $request->adv_card_color[$key]?:'' ;
                    $adv->controller = $request->adv_controller[$key] ? :'' ;
                    $adv->icon = $request->adv_icon[$key]?:'';

                    $adv->save();
                }
            }
        }
        if($request->conditions){
            foreach ($request->conditions as $condition){
                if($condition) {
                    $cond = new PrivilegesCountConditions();
                    $cond->privilege_id = $object->id;
                    $cond->condition = $condition;
                    $cond->save();
                }
                }
        }





        return redirect()->back()->with('success','تم اضافة القائمة بنجاح .');
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
        $object->is_provider=$request->is_provider?1:0;
        $object->url = $request->url ? $request->url : "" ;
        $object->controller = $request->controller ? $request->controller: "" ;
        $object->model = $request->model ? $request->model: "" ;
        $object->card_color = $request->card_color ? $request->card_color: "" ;

        $object->save();

//        Privileges::where('parent_id',$object->id)->delete();
        if($request->property){
            foreach ($request->property as $key=>$value){
                if( $request->property[$key] ) {
                    if(@$request->pr_id[$key]){
                        $adv=Privileges::find($request->pr_id[$key]);
                    }
                    else{
                        $adv = new Privileges();
                    }
                    $adv->parent_id = $object->id;
                    $adv->privilge = $value;
                    $adv->hidden=@$request->is_hidden[$key]?1:0;
                    $adv->url = $request->value[$key]?:'';
                    $adv->model = $request->adv_model[$key]?:'';
                    $adv->card_color = $request->adv_card_color[$key]?:'' ;
                    $adv->controller = $request->adv_controller[$key] ? :'' ;
                    $adv->icon = $request->adv_icon[$key]?:'';

                    $adv->save();
                }
            }
        }

        PrivilegesCountConditions::where('privilege_id',$object->id)->delete();
        if($request->conditions){
            foreach ($request->conditions as $condition){
                if($condition) {
                    $cond = new PrivilegesCountConditions();
                    $cond->privilege_id = $object->id;
                    $cond->condition = $condition;
                    $cond->save();
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
public function deletePrivilegeItem($id=0){
        $object=Privileges::find($id);
        $object->delete();
        return 1;
}
    public function privilegeItems(){

        return view('admin.control-privileges.item');
    }


}
