<?php

namespace App\Http\Controllers\Providers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Content;
class ContentController extends Controller
{
    public function __construct()
    {
            $this->middleware(function ($request, $next) {
            $this->check_settings(26);
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
        return view('providers.content.all',['objects'=>Content::all()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('providers.content.add');
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
            'content'=>'required',
            'page_name'=>'required',
//            'page_name_en'=>'required',
//            'content_en'=>'required',
//            'meta_title' => 'required|max:250|min:3',
//            'meta_description' => 'required|max:250|min:3',
//            'meta_keywords' => 'required|max:250|min:3'
        ]);
        $object = new Content;
        $object->content = $request->input('content');
//        $object->content_en = $request->input('content_en');
          $object->page_name = $request->page_name;
//        $object->page_name_en = $request->page_name_en;
        $file = $request->file('icon');
        if ($request->hasFile('icon')) {
            $fileName = 'icon-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('icon')->move($destinationPath, $fileName);
            $object->icon=$fileName;
        }
        //        $object->meta_description = $request->meta_description;
//        $object->meta_description_en = $request->meta_description_en;
//        $object->meta_keywords_en = $request->meta_keywords_en;
//        $object->type = 1;
        $object->save();
        return redirect()->back()->with('success','تم اضافة الصفحة بنجاح .');
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
        return view('providers.content.add',['object'=> Content::find($id)]);
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
        $object= Content::find($id);
        $this->validate($request, [
            'content'=>'required',
            'page_name'=>'required',
//            'content_en'=>'required',
//            'page_name_en'=>'required',
        ]);
            $object->content = $request->input('content');
//        $object->content_en = $request->input('content_en');
//        $object->content_en = $request->content_en;
//        $object->meta_title = $request->meta_title;
//        $object->meta_title_en = $request->meta_title_en;
//        $object->page_name_en = $request->page_name_en;
        $object->page_name= $request->page_name;
//        if($request->page_name) {


                $file = $request->file('icon');
        if ($request->hasFile('icon')) {
        	$old_file = 'uploads/'.$object->icon;
        	if(is_file($old_file))	unlink($old_file);
        	$fileName = 'icon-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
        	$destinationPath = 'uploads';
        	$request->file('icon')->move($destinationPath, $fileName);
        	$object->icon=$fileName;
        }

//        }
//        $object->meta_description_en = $request->meta_description_en;
//        $object->meta_keywords_en = $request->meta_keywords_en;
//        $object->meta_description = $request->meta_description;
//        $object->meta_keywords = $request->meta_keywords;
        $object->save();
        return redirect()->back()->with('success','تم التعديل بنجاح ');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $object = Content::find($id);
        $object->delete();
    }
}
