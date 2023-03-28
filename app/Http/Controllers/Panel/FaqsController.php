<?php

namespace App\Http\Controllers\Panel;

use App\Models\Faqs;
use Illuminate\Http\Request;

use App\Http\Requests;

use App\Models\States;
class FaqsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.faqs.all',['objects'=>Faqs::all()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.faqs.add');
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
        	'question' => 'required|min:3',
            'question_en' => 'required|min:3',
            'answer' => 'required|min:3',
            'answer_en' => 'required|min:3',
        ]);
		 $object = new Faqs();
		 $object->question = $request->question;
         $object->question_en = $request->question_en;
         $object->answer = $request->answer;
         $object->answer_en = $request->answer_en;
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
    	return view('admin.faqs.add',['object'=> Faqs::find($id)]);
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
        $object= Faqs::find($id);
        $this->validate($request, [
            'question' => 'required|min:3',
            'question_en' => 'required|min:3',
            'answer' => 'required|min:3',
            'answer_en' => 'required|min:3',
        ]);

        $object->question = $request->question;
        $object->question_en = $request->question_en;
        $object->answer = $request->answer;
        $object->answer_en = $request->answer_en;
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
        $object = Faqs::find($id);
        $object->delete();
    }
}
