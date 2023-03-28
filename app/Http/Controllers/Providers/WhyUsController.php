<?php

namespace App\Http\Controllers\Providers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Models\WhyUs;
class WhyUsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('providers.why_us.all',['objects'=>WhyUs::all()]);
    }


    public function save_order_step($cat_id=0,$order=0)
    {
        $cat = WhyUs::find($cat_id);
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
        return view('providers.why_us.add');
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
        	'name' => 'required|unique:why_us|max:100|min:3',
        ]);
		 $object = new WhyUs();
		 $object->name = $request->name;
        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $fileName = 'samem-why-us-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileName);
            $object->photo=$fileName;
        }

         $object->save();
		 return redirect()->back()->with('success','تم اضافة لماذا تختارنا بنجاح');
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
    	return view('providers.why_us.add',['object'=> WhyUs::find($id)]);
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
        $object= WhyUs::find($id);
        $this->validate($request, [
        'name' => 'required|max:100|min:3|unique:why_us,name,'.$object->id.',id',
         ]);
		
		$object->name = $request->name;

        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $old_file = 'uploads/'.$object->icon;
            if(is_file($old_file))	unlink($old_file);
            $fileName = 'samem-why-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileName);
            $object->photo=$fileName;
        }

         $object->save();
		 return redirect()->back()->with('success','تم تعديل لماذا تختارنا بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $object = WhyUs::find($id);
        $object ->delete();
    }
}
