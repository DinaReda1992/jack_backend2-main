<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\ClientTypes;
use Illuminate\Http\Request;

class ClientTypesController extends Controller
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
        $objects = ClientTypes::get();
        return view('admin.client_types.all', ['objects' => $objects]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.client_types.add');
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
            'name_en' => 'required',
        ]);
        $object = new ClientTypes;
        $object->name = $request->name;
        $object->name_en = $request->name_en;
        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $fileName = 'category-' . time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileName);
            $object->photo = $fileName;
        }

        $object->save();
        return redirect()->back()->with('success', 'تم اضافة نوع النشاط  بنجاح');
    }

    public function show($id)
    {
    }

    public function edit($id)
    {
        return view('admin.client_types.add', ['object' => ClientTypes::find($id)]);
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
        $object = ClientTypes::find($id);
        $this->validate($request, [
            'name' => 'required',
            'name_en' => 'required',
        ]);

        $object->name = $request->name;
        $object->name_en = $request->name_en;
        $object->save();

        return redirect()->back()->with('success', 'تم تعديل نوع النشاط  بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $object = ClientTypes::find($id);
        $object->delete();
    }
}
