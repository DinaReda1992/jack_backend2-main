<?php

namespace App\Http\Controllers\Panel;

use App\Models\Faqs;
use App\Models\MiddleSection;
use Illuminate\Http\Request;

use App\Http\Requests;

use App\Models\States;
class MiddleSectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
//        return view('admin.faqs.all',['objects'=>Faqs::all()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
//        return view('admin.faqs.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
//        $this->validate($request, [
//        	'question' => 'required|min:3',
//            'question_en' => 'required|min:3',
//            'answer' => 'required|min:3',
//            'answer_en' => 'required|min:3',
//        ]);
//		 $object = new Faqs();
//		 $object->question = $request->question;
//         $object->question_en = $request->question_en;
//         $object->answer = $request->answer;
//         $object->answer_en = $request->answer_en;
//		 $object->save();
//		 return redirect()->back()->with('success','تم اضافة السؤال بنجاح');
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
    	return view('admin.middle_section.add',['object'=> MiddleSection::find($id)]);
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
        $object= MiddleSection::find($id);
        $this->validate($request, [
            'title' => 'required|min:3',
            'title_en' => 'required|min:3',
        ]);


        $object->title = $request->title;
        $object->title_en = $request->title_en;
        $object->description = $request->description;
        $object->description_en = $request->description_en;
        $object->btn_text = $request->btn_text;
        $object->btn_text_en = $request->btn_text_en;
        $object->btn_link = $request->btn_link;
        $image=  $request->file('image');

        if ($request->hasFile('image')) {
            $old_file = 'uploads/'.$object->image;
            if(is_file($old_file))	unlink($old_file);
            $fileNameCover = 'middle-'.time().'-'.uniqid().'.'.$image->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('image')->move($destinationPath, $fileNameCover);
            $object->image=$fileNameCover;
        }

        $object->save();
		 return redirect()->back()->with('success','تم تعديل منتصف الموقع بنجاح');
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
