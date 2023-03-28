<?php

namespace App\Http\Controllers\Panel;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Models\ReportTypes;
class ReportTypesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.report-types.all',['objects'=>ReportTypes::all()]);
    }


    public function save_order($cat_id=0,$order=0)
    {
        $cat = ReportTypes::find($cat_id);
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
        return view('admin.report-types.add');
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
        	'name' => 'required|unique:report_types,name|max:100|min:3',
            'name_en' => 'required|unique:report_types,name_en|max:100|min:3',
        ]);
		 $object = new ReportTypes;
		 $object->name = $request->name;
        $object->name_en= $request->name_en;


         $object->save();
		 return redirect()->back()->with('success','تم اضافة قسم التقرير بنجاح');
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
    	return view('admin.report-types.add',['object'=> ReportTypes::find($id)]);
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
        $object= ReportTypes::find($id);
        $this->validate($request, [
        'name' => 'required|max:100|min:3|unique:report_types,name,'.$object->id.',id',
            'name_en' => 'required|max:100|min:3|unique:report_types,name_en,'.$object->id.',id',
         ]);
		
		$object->name = $request->name;
        $object->name_en = $request->name_en;


         $object->save();
		 return redirect()->back()->with('success','تم تعديل القسم بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $object = ReportTypes::find($id);
        $object ->delete();
    }
}
