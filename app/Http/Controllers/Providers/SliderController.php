<?php

namespace App\Http\Controllers\Providers;

use App\Models\Main_slider;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Sliders;
use Auth;


class SliderController extends Controller
{

    public function __construct()
    {

        $this->middleware(function ($request, $next) {
            $this->check_settings(96);
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
        $main_slider=Main_slider::find($request->slider);
        if(!$main_slider){
            return redirect()->back()->with("error","لا يوجد سلايدر");
        }

        $objects=Sliders::where("main_id",$request->slider)->orderBy('created_at', 'desc')->get();
        return view("providers.sliders.sliders.all")->with("objects",$objects);
    }


    public function store(Request $request)
    {

        $this->validate($request, [
            'text'=>'max:500',
            'text2'=>'max:500',
            'main_slider'=>'required'
        ]);

        $file = $request->file('photo');
        $slider=new Sliders();
        if ($request->hasFile('photo')) {
            $fileName = 'slider-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileName);
            $slider->image=$fileName;
        }
        $slider->slide_title=$request->slide_title ? $request->slide_title : "" ;
        $slider->title=$request->title  ? $request->title : "" ;
        $slider->text=$request->text ? $request->text  :"" ;
        $slider->text2=$request->text2 ? $request->text2 : "";
        $slider->url=$request->url ? $request->url: "" ;
        $slider->main_id=$request->main_slider;
        $slider->save();

        return redirect()->back()->with('success','تم اضافة الشريحة بنجاح');
	}

    public function create()
    {
        return view('/admin/sliders.sliders.add');
    }
    public function edit($id)
    {
        return view('providers.sliders.sliders.add',['object'=> Sliders::find($id)]);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'text'=>'max:500',
            'text2'=>'max:500',
            'main_slider'=>'required'
        ]);
        $file = $request->file('photo');
        $slider= Sliders::find($id);
        if ($request->hasFile('photo')) {
            $fileName = $slider->image?:'slider-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileName);
            $slider->image=$fileName;
        }
        $slider->text=$request->text ? $request->text : "" ;
        $slider->text2=$request->text2 ? $request->text2 : "";
        $slider->url=$request->url ? $request->url : '';
        $slider->slide_title=$request->slide_title ? $request->slide_title :'';
        $slider->title=$request->title  ? $request->title : "" ;
        $slider->main_id=$request->main_slider;
        $slider->save();

        return redirect('admin-panel/slider?slider='.$slider->main_id)->with('success','تم التعديل بنجاح');
    }

    public function destroy($id)
    {
        $object = Sliders::find($id);
        $object->delete();
    }


}
