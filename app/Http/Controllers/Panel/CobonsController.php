<?php

namespace App\Http\Controllers\Panel;

use App\Models\Categories;
use App\Models\CobonsCategories;
use App\Models\CobonsProviders;
use App\Models\Settings;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Cobons;
use App\Models\Subcategories;
class CobonsController extends Controller
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
        return view('admin.cobons.all',['objects'=>Cobons::orderBy('id','DESC')->get()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.cobons.add');
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
        	'code' => 'required|unique:cobons,code',
            'percent' => 'required|numeric',
            'days' => 'required',

            'usage_quota' => 'required',

        ]);
		 $object = new Cobons;
		 $object->code = $request->code;
         $object->percent = $request->percent;
         $object->days = $request->days;
        $object->usage_quota = $request->usage_quota;
        $object->max_money = $request->max_money?:0;
        $object->link_type = $request->link_type;

         $object->save();
        if($request->link_type=='provider'){
            if($request->provider){
                foreach ($request->provider as $key=>$value){
                    if($request->provider[$key]  ) {
                        $adv = new CobonsProviders();
                        $adv->user_id = $request->provider[$key];
                        $adv->cobon_id = $object->id;
                        $adv->save();
                    }
                }
            }
        }else{
            if($request->category){
                foreach ($request->category as $key=>$value){
                    if($request->category[$key]  ) {
                        $adv = new CobonsCategories();
                        $adv->category_id = $request->category[$key];
                        $adv->cobon_id = $object->id;
                        $adv->save();
                    }
                }
            }

        }


        return redirect()->back()->with('success','تم اضافة الكوبون بنجاح');
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
        $object = Cobons::find($id);
        if($object->checkCobon()>0){
            return redirect()->back()->with('error','لا يمكن تعديل كوبون مستخدم');
        }
        $prev_categories = CobonsCategories::where('cobon_id', $id)->pluck('category_id')->toArray();
        $prev_providers = CobonsProviders::where('cobon_id', $id)->pluck('user_id')->toArray();

        return view('admin.cobons.add',['object'=> Cobons::find($id),'prev_categories'=>$prev_categories ,'prev_providers'=>$prev_providers ]);
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
        $object= Cobons::find($id);
        $this->validate($request, [
            'code' => 'required|unique:cobons,code,'.$object->id.',id',
            'percent' => 'required|numeric',
            'days' => 'required',

            'usage_quota' => 'required',

        ]);

		 $object->code = $request->code;
         $object->percent = $request->percent;
        $object->days = $request->days;
        $object->usage_quota = $request->usage_quota;
        $object->max_money = $request->max_money?:0;
        $object->link_type = $request->link_type;

        $object->save();
        CobonsCategories::where('cobon_id',$object->id)->delete();
        CobonsProviders::where('cobon_id',$object->id)->delete();
        if($request->link_type=='provider'){
            if($request->provider){
                foreach ($request->provider as $key=>$value){
                    if($request->provider[$key]  ) {
                        $adv = new CobonsProviders();
                        $adv->user_id = $request->provider[$key];
                        $adv->cobon_id = $object->id;
                        $adv->save();
                    }
                }
            }
        }else{
            if($request->category){
                foreach ($request->category as $key=>$value){
                    if($request->category[$key]  ) {
                        $adv = new CobonsCategories();
                        $adv->category_id = $request->category[$key];
                        $adv->cobon_id = $object->id;
                        $adv->save();
                    }
                }
            }
        }



		 return redirect()->back()->with('success','تم تعديل الكوبون بنجاح');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $object = Cobons::find($id);
        $old_file = 'uploads/'.$object->photo;
        if(is_file($old_file))	unlink($old_file);
        $object->delete();
    }
}
