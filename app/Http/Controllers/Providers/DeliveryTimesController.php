<?php

namespace App\Http\Controllers\Providers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\DeliveryTimes;
class DeliveryTimesController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->check_settings(251);
            return $next($request);
        });
    }


    public function hide_category($id=0)
    {
        $user = DeliveryTimes::find($id);
        if(!$user){
            return redirect()->back()->with('error','لا يوجد قسم بهذا العنوان');
        }
        if($user->hidden==0){
            $user -> hidden = 1 ;
            $user -> save();
            return redirect()->back()->with('success','تم إخفاء وقت التوصيل بنجاح');
        }else{
            $user -> hidden = 0 ;
            $user -> save();
            return redirect()->back()->with('success','تم إظهار وقت التوصيل بنجاح .');
        }


    }


    public function change_sort(Request $request){
        foreach ($request->position as $key=>$value){
            $privilege= DeliveryTimes::find($value);
            $privilege->orders = $key;
            $privilege->save();

        }

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
            $categories = DeliveryTimes::orderBy('orders','asc')->get();

        return view('providers.delivery-times.all',['objects'=>$categories]);
    }




    public function save_order($cat_id=0,$order=0)
    {
        $cat = DeliveryTimes::find($cat_id);
        $cat ->orderat = $order;
        $cat->save();
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('providers.delivery-times.add');
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
        	'name' => 'required|unique:categories,name|max:100|min:3',
            'name_en' => 'required|unique:categories,name_en|max:100|min:3',
        ]);
		 $object = new DeliveryTimes;
		 $object->name = $request->name;
         $object->name_en = $request->name_en;
//         $object->description = $request->description;

        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $fileName = 'category-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileName);
            $object->photo=$fileName;
        }

         $object->save();
		 return redirect()->back()->with('success','تم اضافة وقت التوصيل الرئيسي بنجاح');
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
    	return view('providers.delivery-times.add',['object'=> DeliveryTimes::find($id)]);
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
        $object= DeliveryTimes::find($id);
        $this->validate($request, [
        'name' => 'required|max:100|min:3|unique:categories,name,'.$object->id.',id',
            'name_en' => 'required|max:100|min:3|unique:categories,name_en,'.$object->id.',id',
         ]);

		$object->name = $request->name;
        $object->name_en = $request->name_en;
//        $object->description = $request->description;

        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $old_file = 'uploads/'.$object->photo;
            if(is_file($old_file))	unlink($old_file);
            $fileName = 'category-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileName);
            $object->photo=$fileName;
        }

         $object->save();
		 return redirect()->back()->with('success','تم تعديل وقت التوصيل الرئيسي بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
//        $object = DeliveryTimes::find($id);
//        $object ->delete();
    }
}
