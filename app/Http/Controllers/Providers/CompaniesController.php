<?php

namespace App\Http\Controllers\Providers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Models\Companies;
use App\Http\Controllers\Controller;

use App\Models\User;

class CompaniesController extends Controller
{
    public function __construct()
    {

        $this->middleware(function ($request, $next) {
            $this->check_settings(289);
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
        return view('providers.companies.all',['objects'=>Companies::all()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('providers.companies.add',['users'=> User::all()]);
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
            'name' => 'required|unique:companies|max:100|min:3',
           'description' => 'required|min:3',
           'phone' => 'required',
           'state_id' => 'required',
           'email' => 'required',
           'type' => 'required',
           'address' => 'required',
            'photo' => 'required|image',
        ]);
		 $object = new Companies;

		 $file = $request->file('photo');
		 if ($request->hasFile('photo')) {
		 	$fileName = 'companies-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
		 	$destinationPath = 'uploads';
		 	$request->file('photo')->move($destinationPath, $fileName);
		 	$object->photo=$fileName;
		 }

	 $object->name = $request->name;
	 $object->description = $request->description;
     $object->phone = $request->phone;
     $object->email = $request->email;
     $object->address = $request->address;
     $object->type = $request->type;
        $object->state_id = $request->state_id;

        $object->save();
		 return redirect()->back()->with('success','تم اضافة الشركة بنجاح');
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
    	return view('providers.companies.add',['object'=> Companies::find($id) , 'users' => User::all() ]);
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
        $object= Companies::find($id);
        $this->validate($request, [
            'name' => 'required|max:100|min:3|unique:companies,name,'.$object->id.',id',
            'description' => 'required|min:3',
            'phone' => 'required',
            'state_id' => 'required',
            'email' => 'required',
            'type' => 'required',
            'address' => 'required',
//            'photo' => 'required|image',
         ]);

        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
        	$old_file = 'uploads/'.$object->icon;
        	if(is_file($old_file))	unlink($old_file);
        	$fileName = 'companies-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
        	$destinationPath = 'uploads';
        	$request->file('photo')->move($destinationPath, $fileName);
        	$object->photo=$fileName;
        }
        $object->name = $request->name;
        $object->state_id = $request->state_id;
        $object->description = $request->description;
        $object->phone = $request->phone;
        $object->email = $request->email;
        $object->address = $request->address;
        $object->type = $request->type;
		 $object->save();
		 return redirect()->back()->with('success','تم تعديل الشركة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $object = Companies::find($id);
        $old_file = 'uploads/'.$object->photo;
        if(is_file($old_file))	unlink($old_file);
        $object->delete();
    }
}
