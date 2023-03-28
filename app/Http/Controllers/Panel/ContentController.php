<?php

namespace App\Http\Controllers\Panel;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Content;
class ContentController extends Controller
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

    public function index()
    {
        return view('admin.content.all',['objects'=>Content::all()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.content.add');
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
            'page_name_en'=>'required',
            'content_en'=>'required',
//            'meta_title' => 'max:250',
//            'meta_description' => 'max:250',
//            'meta_keywords' => 'max:250',
//                        'meta_title_en' => 'max:250',
//            'meta_description_en' => 'max:250',
//            'meta_keywords_en' => 'max:250'

        ]);
        $object = new Content;
        $object->content = $request->input('content');
        $object->content_en = $request->input('content_en');
          $object->page_name = $request->page_name;
        $object->page_name_en = $request->page_name_en;
//        $object->slug= str_slug($request->page_name_en);
//        $object->meta_title = $request->meta_title?:'';
//        $object->meta_description = $request->meta_description?:'';
//        $object->meta_keywords = $request->meta_keywords?:'';
//
//        $object->meta_title_en = $request->meta_keywords_en;
//        $object->meta_keywords_en = $request->meta_keywords_en;
//        $object->meta_description_en = $request->meta_description_en;
//
//        $cover = $request->file('cover');
//        $photo = $request->file('photo');
//        $photo2 = $request->file('photo2');
//
//        if ($request->hasFile('cover')) {
//            $fileNameCover = 'cover-'.time().'-'.uniqid().'.'.$cover->getClientOriginalExtension();
//            $destinationPath = 'uploads';
//            $request->file('cover')->move($destinationPath, $fileNameCover);
//            $object->cover=$fileNameCover;
//        }
//
//        if ($request->hasFile('photo')) {
//            $fileNameFirst = 'photo-'.time().'-'.uniqid().'.'.$photo->getClientOriginalExtension();
//            $destinationPath = 'uploads';
//            $request->file('photo')->move($destinationPath, $fileNameFirst);
//            $object->photo=$fileNameFirst;
//        }
//
//        if ($request->hasFile('photo2')) {
//            $fileNameSecond = 'photo-'.time().'-'.uniqid().'.'.$photo2->getClientOriginalExtension();
//            $destinationPath = 'uploads';
//            $request->file('photo2')->move($destinationPath, $fileNameSecond);
//            $object->photo2=$fileNameSecond;
//        }

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
        return view('admin.content.add',['object'=> Content::find($id)]);
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
            'page_name_en'=>'required',
            'content_en'=>'required',


        ]);
        $object->content = $request->input('content');
//        $object->slug= str_slug($request->page_name_en);

        $object->content_en = $request->input('content_en');
        $object->page_name = $request->page_name;
        $object->page_name_en = $request->page_name_en;
//        $object->meta_title = $request->meta_title?:'';
//        $object->meta_description = $request->meta_description?:'';
//        $object->meta_keywords = $request->meta_keywords?:'';
//
//        $object->meta_title_en = $request->meta_keywords_en;
//        $object->meta_keywords_en = $request->meta_keywords_en;
//        $object->meta_description_en = $request->meta_description_en;
//
//        $cover = $request->file('cover');
//        $photo = $request->file('photo');
//        $photo2 = $request->file('photo2');
//
//
//        if ($request->hasFile('cover')) {
//        	$old_file = 'uploads/'.$object->cover;
//        	if(is_file($old_file))	unlink($old_file);
//        	$fileNameCover = 'cover-'.time().'-'.uniqid().'.'.$cover->getClientOriginalExtension();
//        	$destinationPath = 'uploads';
//        	$request->file('cover')->move($destinationPath, $fileNameCover);
//        	$object->cover=$fileNameCover;
//        }
//        if ($request->hasFile('photo')) {
//            $old_file = 'uploads/'.$object->photo;
//            if(is_file($old_file))	unlink($old_file);
//            $fileNameFirst = 'photo-'.time().'-'.uniqid().'.'.$photo->getClientOriginalExtension();
//            $destinationPath = 'uploads';
//            $request->file('photo')->move($destinationPath, $fileNameFirst);
//            $object->photo=$fileNameFirst;
//        }
//        if ($request->hasFile('photo2')) {
//            $old_file = 'uploads/'.$object->photo2;
//            if(is_file($old_file))	unlink($old_file);
//            $fileNameSecond = 'photo-'.time().'-'.uniqid().'.'.$photo2->getClientOriginalExtension();
//            $destinationPath = 'uploads';
//            $request->file('photo2')->move($destinationPath, $fileNameSecond);
//            $object->photo2=$fileNameSecond;
//        }

//        }
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
