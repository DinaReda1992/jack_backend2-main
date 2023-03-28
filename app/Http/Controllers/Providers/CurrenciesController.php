<?php

namespace App\Http\Controllers\Providers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests;

use App\Models\Currencies;
class CurrenciesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->check_settings(130);
            return $next($request);
        });
    }
    public function index()
    {
        return view('providers.currencies.all',['objects'=>Currencies::all()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('providers.currencies.add');
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
        	'name' => 'required|unique:currencies,name|max:100|min:3',
            'name_en' => 'required|unique:currencies,name_en|max:100|min:3',
            'code' => 'required|unique:currencies,code|max:100|min:3',
        ]);
		 $object = new Currencies;
		 $object->name = $request->name;
         $object->name_en = $request->name_en;
         $object->code = $request->code;
		 $object->save();
		 return redirect()->back()->with('success','تم اضافة العملة بنجاح');
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
    	return view('providers.currencies.add',['object'=> Currencies::find($id)]);
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
        $object= Currencies::find($id);
        $this->validate($request, [
        'name' => 'required|max:100|min:3|unique:currencies,name,'.$object->id.',id',
            'name_en' => 'required|max:100|min:3|unique:currencies,name_en,'.$object->id.',id',
            'code' => 'required|max:100|min:3|unique:currencies,code,'.$object->id.',id',

        ]);
		
		 $object->name = $request->name;
        $object->name_en = $request->name_en;
        $object->code = $request->code;
		 $object->save();
		 return redirect()->back()->with('success','تم تعديل العملة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $object = Currencies::find($id);
        $object->delete();
    }
}
