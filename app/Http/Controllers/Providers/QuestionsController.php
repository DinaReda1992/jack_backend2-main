<?php

namespace App\Http\Controllers\Providers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Models\Categories;
use App\Models\Questions;
class QuestionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('providers.questions.all',['objects'=>Questions::all() ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('providers.questions.add',[ 'categories'=>Categories::all() ]);
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
        	'name' => 'required|unique:questions|max:100|min:3',
        	'category_id' => 'required',
        ]);
		 $object = new Questions();
		 $object->name = $request->name;
		 $object->category_id = $request->category_id;
         $object->required = $request->required;
		 $object->save();
		 return redirect()->back()->with('success','تم اضافة السؤال بنجاح');
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
    	return view('providers.questions.add',['object'=> Questions::find($id), 'categories'=>Categories::all() ]);
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
        $object = Questions::find($id);
        $this->validate($request, [
        'name' => 'required|max:100|min:3|unique:categories,name,'.$object->id.',id',
        'category_id' => 'required'
        ]);
		
		 $object->name = $request->name;
		 $object->category_id = $request->category_id;
        $object->required = $request->required;

        $object->save();
		 return redirect()->back()->with('success','تم تعديل السؤال بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $object = Questions::find($id);
        $object->delete();
    }
}
