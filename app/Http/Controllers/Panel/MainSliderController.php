<?php

namespace App\Http\Controllers\Panel;

use App\Models\Main_slider;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Sliders;
use Auth;


class MainSliderController extends Controller
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
    public function index()
    {
        $categories=Main_slider::orderBy('created_at', 'desc')->get();
        return view("admin.sliders.main.all")->with("objects",$categories);
    }


    public function store(Request $request)
    {

        $this->validate($request, [
            'name' => 'required','description'=>'max:1000'
        ]);

        $slider=new Main_slider();
        $slider->name=$request->name;
        $slider->description=$request->description;
        $slider->save();

        return redirect()->back()->with('success','تم اضافة السلايدر بنجاح');
	}

    public function create()
    {
        return view('admin.sliders.main.add');
    }
    public function edit($id)
    {
        return view('admin.sliders.main.add',['object'=> Main_slider::find($id)]);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required','description'=>'max:1000'
        ]);

        $slider= Main_slider::find($id);
        $slider->name=$request->name;
        $slider->description=$request->description;
        $slider->save();

        return redirect()->back()->with('success','تم التعديل بنجاح');
    }

    public function destroy($id)
    {
        $object = Main_slider::find($id);
        $object->delete();
    }


}
