<?php

namespace App\Http\Controllers\Panel;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Models\Steps;
class StepsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.steps.all',['objects'=>Steps::all()]);
    }


    public function save_order_step($cat_id=0,$order=0)
    {
        $cat = Steps::find($cat_id);
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
        return view('admin.steps.add');
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
        	'title' => 'required|unique:steps,title|max:100|min:3',
            'title_en' => 'required|unique:steps,title_en|max:100|min:3',
            'description' => 'required',
            'description_en' => 'required',
        ]);
		 $object = new Steps;
		 $object->title = $request->title;
         $object->title_en = $request->title_en;
         $object->description = $request->description;
        $object->description_en = $request->description_en;

//        $file = $request->file('photo');
//        if ($request->hasFile('photo')) {
//            $fileName = 'samem-category-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
//            $destinationPath = 'uploads';
//            $request->file('photo')->move($destinationPath, $fileName);
//            $object->photo=$fileName;
//        }

         $object->save();
		 return redirect()->back()->with('success','تم اضافة الخطوة بنجاح');
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
    	return view('admin.steps.add',['object'=> Steps::find($id)]);
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
        $object= Steps::find($id);
        $this->validate($request, [
        'title' => 'required|max:100|min:3|unique:steps,title,'.$object->id.',id',
            'title_en' => 'required|max:100|min:3|unique:steps,title_en,'.$object->id.',id',
            'description' => 'required',
            'description_en' => 'required',

        ]);

        $object->title = $request->title;
        $object->title_en = $request->title_en;
        $object->description = $request->description;
        $object->description_en = $request->description_en;

//        $file = $request->file('photo');
//        if ($request->hasFile('photo')) {
//            $old_file = 'uploads/'.$object->icon;
//            if(is_file($old_file))	unlink($old_file);
//            $fileName = 'samem-category-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
//            $destinationPath = 'uploads';
//            $request->file('photo')->move($destinationPath, $fileName);
//            $object->photo=$fileName;
//        }

         $object->save();
		 return redirect()->back()->with('success','تم تعديل الخطوة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $object = Steps::find($id);
        $object ->delete();
    }
}
