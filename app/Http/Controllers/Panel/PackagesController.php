<?php

namespace App\Http\Controllers\Panel;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Packages;
class PackagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {

        $this->middleware(function ($request, $next) {
            $this->check_settings(124);
            return $next($request);
        });
    }
    public function index()
    {
        return view('admin.packages.all',['objects'=>Packages::all()]);
    }


    public function save_order($cat_id=0,$order=0)
    {
        $cat = Packages::find($cat_id);
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
        return view('admin.packages.add');
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
        	'name' => 'required|unique:categories|max:100|min:2',
            'price' => 'required',
            'name_en' => 'required',
            'currency_id' => 'required',
            'days' => 'required',
        ]);
		 $object = new Packages();
		 $object->name = $request->name;
        $object->name_en = $request->name_en;
         $object->price = $request->price;
        $object->currency_id = $request->currency_id;
        $object->days = $request->days;

         $object->save();
		 return redirect()->back()->with('success','تم اضافة الباقة بنجاح');
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
    	return view('admin.packages.add',['object'=> Packages::find($id)]);
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
        $object= Packages::find($id);
        $this->validate($request, [
            'name' => 'required|max:100|min:2|unique:packages,name,'.$object->id.',id',
//            'name_en' => 'required|max:100|min:2|unique:packages,name_en,'.$object->id.',id',
            'price' => 'required',
            'currency_id' => 'required',
            'days' => 'required',
            'allowed_ads' => 'required',

        ]);

        $object->name = $request->name;
//        $object->name_en = $request->name_en;
        $object->price = $request->price;
        $object->days = $request->days;
        $object->currency_id = $request->currency_id;
        $object->allowed_ads = $request->allowed_ads;

         $object->save();
		 return redirect()->back()->with('success','تم تعديل الباقة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $object = Packages::find($id);
        $object ->delete();
    }
}
