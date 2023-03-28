<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\ItemType;
use App\Models\MainSlider;
use App\Models\Slider;
use App\Models\Sliders;
use App\Repositories\Utils\UtilsRepository;
use Illuminate\Http\Request;


class SliderController extends Controller
{

    public function __construct()
    {

        $this->middleware(function ($request, $next) {
            $this->check_settings((new \ReflectionClass($this))->getShortName());
            return $next($request);
        });
    }


    public function showMainSliders(Request $request)
    {
        $objects = MainSlider::all();
        return view("admin.sliders.main.all")->with("objects", $objects);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $main_slider = MainSlider::find($request->main_slider);
        if (!$main_slider) {
            return redirect()->back()->with("error", "لا يوجد سلايدر");
        }

        $objects = Slider::where(['main_slider_id' => $request->main_slider])
            ->orderBy('id', 'DESC')->get();
        return view("admin.sliders.sliders.all")->with([
            "objects" => $objects,
            'mainSlider' => $main_slider
        ]);
    }


    public function store(Request $request)
    {

        $this->validate($request, [
            // 'title' => 'required',
            'description' => 'max:500',
            'photo' => 'required|mimes:jpeg,jpg,png|max:3072',
        ]);


        $slider = new Slider();
        if ($request->hasFile('photo')) {
            // $file = $request->file('photo');
            // $fileName = 'slider-' . time() . '-' . uniqid() . '.webp'; // . $file->getClientOriginalExtension();
            // $destinationPath = 'uploads/';
            // $path = $file->getRealPath();
            // //            $width = 1060;
            // //            $height = 440;
            // UtilsRepository::uploadImage($path, $fileName, $destinationPath, 0, 0);
            // $slider->photo = $fileName;
            //            $fileName = 'slider-mobile-' . time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
            //            $width = 343;
            //            $height = 161;
            //            UtilsRepository::uploadImage($path, $fileName, $destinationPath, $width, $height);
            //            $slider->mobile_photo = $fileName;
            $file = $request->file('photo');
            $fileName = 'file-' . time() . '-' . uniqid() . '.' . 'webp'; // . $file->getClientOriginalExtension();
            $destinationPath = 'uploads/';
            $file->move($destinationPath, $fileName);
            $slider->photo = $fileName;
            $slider->mobile_photo = $fileName;
        }
        $slider->title = $request->title ?: "";
        $slider->title_en = $request->title_en ?: "";
        $slider->description = $request->description ?: "";
        $slider->description_en = $request->description ?: "";
        $slider->locale = $request->_locale;
        $slider->button_title = $request->button_title ?: '';
        $slider->button_title_en = $request->button_title_en ?: '';
        $slider->button_url = $request->button_url ?: '';
        $slider->text_color= $request->text_color?:'#ffffff';
        $slider->main_slider_id = $request->main_slider_id;
        $slider->has_link = $request->has_link ? 1 : 0;
        $slider->item_type = $request->item_type ?: 0;
        $slider->item_id = $request->item_id ?: 0;
        $slider->save();
        return redirect()->back()->with('success', 'تم اضافة الشريحة بنجاح');
    }

    public function create(Request $request)
    {
        $main_slider = MainSlider::find($request->main_slider);
        if (!$main_slider) {
            return redirect()->back()->with("error", "لا يوجد سلايدر");
        }
        $allMainSliders = MainSlider::all();
        if ($main_slider->id == 8) {
            $itemTypes = ItemType::where('id', '>', 3)->get();
        } else {
            $itemTypes = ItemType::where('id', '<=', 3)->get();
        }
        return view('/admin/sliders.sliders.add')->with([
            'mainSlider' => $main_slider,
            'allMainSliders' => $allMainSliders,
            'itemTypes' => $itemTypes
        ]);
    }

    public function edit($id)
    {
        $allMainSliders = MainSlider::all();
        $object = Slider::find($id);
        $main_slider = $object->main_slider;
        if ($main_slider->id == 8) {
            $itemTypes = ItemType::where('id', '>', 3)->get();
        } else {
            $itemTypes = ItemType::where('id', '<=', 3)->get();
        }

        return view('admin.sliders.sliders.add', [
            'mainSlider' => $main_slider,
            'object' => $object,
            'allMainSliders' => $allMainSliders,
            'itemTypes' => $itemTypes
        ]);
    }

    public function update(Request $request, $id)
    {
        $slider = Slider::find($id);
        $this->validate($request, [
            // 'title' => 'required',
            'description' => 'max:500',
            'photo' => (!$slider->photo ? 'required' : '') . 'mimes:jpeg,jpg,png|max:3072'
        ]);
        if ($request->hasFile('photo')) {
            $old_file = 'uploads/' . $slider->photo;
            if (is_file($old_file)) unlink($old_file);


            // $fileName = 'slider-' . time() . '-' . uniqid() . '.webp'; //. $file->getClientOriginalExtension();
            // $destinationPath = 'uploads/';
            // $path = $file->getRealPath();
            //            $width = 1060;
            //            $height = 440;
            $file = $request->file('photo');
            $fileName = 'file-' . time() . '-' . uniqid() . '.' . 'webp'; //. $file->getClientOriginalExtension();
            $destinationPath = 'uploads/';
            $file->move($destinationPath, $fileName);
            $slider->photo = $fileName;
            $slider->mobile_photo = $fileName;
            // $arr_of_images[] = $fileName;
            // UtilsRepository::uploadImage($path, $fileName, $destinationPath, 0, 0);

            // $fileName = 'slider-mobile-' . time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
            // $width = 343;
            // $height = 161;
            // UtilsRepository::uploadImage($path, $fileName, $destinationPath, $width, $height);

        }
        $slider->title = $request->title ?: "";
        $slider->title_en = $request->title_en ?: "";
        $slider->description = $request->description ?: '';
        $slider->description_en = $request->description_en ?: '';
        $slider->locale = $request->_locale;
        $slider->button_title = $request->button_title ?: '';
        $slider->button_title_en = $request->button_title_en ?: '';
        $slider->button_url = $request->button_url ?: '';
        $slider->text_color= $request->text_color?:'#ffffff';
        $slider->main_slider_id = $request->main_slider_id;
        $slider->has_link = $request->has_link ? 1 : 0;
        $slider->item_type = $request->item_type ?: 0;
        $slider->item_id = $request->item_id ?: 0;

        $slider->save();

        return redirect()->back()->with('success', 'تم تعديل الشريحة بنجاح');
    }

    public function destroy($id)
    {
        $object = Slider::find($id);
        if ($object->photo) {
            $old_file = 'uploads/' . $object->photo;
            if (is_file($old_file)) unlink($old_file);
        }
        $object->delete();
    }
}
