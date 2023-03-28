<?php

namespace App\Http\Controllers\Providers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Branches;
class BranchesController extends Controller
{
    public function __construct()
    {
            $this->middleware(function ($request, $next) {
            $this->check_settings(41);
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
        return view('providers.branches.all',['objects'=>Branches::all()]);
    }


    public function save_order($cat_id=0,$order=0)
    {
        $cat = Branches::find($cat_id);
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
        return view('providers.branches.add');
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
            'address' => 'required',
            'phone' => 'required',
            'longitude' => 'required',
            'latitude' => 'required',
            'notes' => 'required',
        ]);


		 $object = new Branches;
		 $object->name = $request->name;
         $object->address = $request->address;
         $object->phone = $request->phone;
         $object->longitude = $request->longitude;
         $object->latitude = $request->latitude;
        $object->notes = $request->notes;

        $object->save();
		 return redirect()->back()->with('success','تم اضافة الفرع بنجاح');
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
    	return view('providers.branches.add',['object'=> Branches::find($id)]);
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
        $object= Branches::find($id);
        $this->validate($request, [
            'name' => 'required',
            'address' => 'required',
            'phone' => 'required',
            'notes' => 'required',
            'longitude' => 'required',
            'latitude' => 'required',
         ]);

        $object->name = $request->name;
        $object->address = $request->address;
        $object->phone = $request->phone;
        $object->longitude = $request->longitude;
        $object->latitude = $request->latitude;
        $object->notes = $request->notes;


         $object->save();
		 return redirect()->back()->with('success','تم تعديل الفرع بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $object = Branches::find($id);
        $object ->delete();
    }
}
