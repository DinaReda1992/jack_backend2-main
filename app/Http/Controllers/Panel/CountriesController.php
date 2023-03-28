<?php

namespace App\Http\Controllers\Panel;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Http\Requests;

use App\Models\Countries;

class CountriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {

        $this->middleware(function ($request, $next) {
            $this->check_settings((new \ReflectionClass($this))->getShortName());
            return $next($request);
        });
    }

    public function index()
    {
        return view('admin.countries.all',['objects'=>Countries::all()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.countries.add');
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
        	'name' => 'required|unique:countries,name|max:100|min:3',
            'name_en'=> 'required|unique:countries,name_en|max:100|min:3',
            'phonecode'=> 'required',
        ]);
		 $object = new Countries;
		 $object->name_en = $request->name_en;
         $object->name= $request->name;
         $object->phonecode = $request->phonecode;
        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $fileName = 'flag-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'flags';
            $request->file('photo')->move($destinationPath, $fileName);
            $object->photo=$fileName;
        }
		 $object->save();


		 return redirect()->back()->with('success','تم اضافة الدولة بنجاح');
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
    	return view('admin.countries.add',['object'=> Countries::find($id)]);
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
        $object = Countries::find($id);
        $this->validate($request, [
        'name' => 'required|max:100|min:3|unique:countries,name,'.$object->id.',id',
        'name_en' => 'required|max:100|min:3|unique:countries,name_en,'.$object->id.',id',
        'phonecode'=> 'required',
        ]);

        $object->name = $request->name;
        $object->name_en = $request->name_en;
        $object->phonecode = $request->phonecode;

        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $old_file = 'flags/'.$object->icon;
            if(is_file($old_file))	unlink($old_file);
            $fileName = 'flag-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileName);
            $object->photo=$fileName;
        }

		 $object->save();
		 return redirect()->back()->with('success','تم تعديل الدولة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $object = Countries::find($id);
        $object->delete();
    }
}
