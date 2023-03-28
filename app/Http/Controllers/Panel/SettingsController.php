<?php

namespace App\Http\Controllers\Panel;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Settings;
use Auth;


class SettingsController extends Controller
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
        return view('admin.settings.add');
    }


    public function store(Request $request)
    {

        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $fileName = 'logo.png';
            $destinationPath = 'images';
            $request->file('photo')->move($destinationPath, $fileName);
            $photo = Settings::find(1);
            $photo->value = $fileName;
            $photo->save();
        }

        $logoicon = $request->file('logoicon');
        if ($request->hasFile('logoicon')) {
            $fileName = 'logoicon.png';
            $destinationPath = 'images';
            $request->file('logoicon')->move($destinationPath, $fileName);
            $photo = Settings::where('option_name', 'logo_icon')->first();
            $photo->value = $fileName;
            $photo->save();
        }
        $header = $request->file('header');
        if ($request->hasFile('header')) {
            $fileName = 'header1' . str_random(4) . '.png';
            $destinationPath = 'images';
            $request->file('header')->move($destinationPath, $fileName);
            $photo = Settings::where('option_name', 'header')->first();
            $photo->value = $fileName;
            $photo->save();
        }
        $footer = $request->file('footer');
        if ($request->hasFile('footer')) {
            $fileName = 'footer1' . str_random(4) . '.png';
            $destinationPath = 'images';
            $request->file('footer')->move($destinationPath, $fileName);
            $photo = Settings::where('option_name', 'footer')->first();
            $photo->value = $fileName;
            $photo->save();
        }

        $middle_photo = $request->file('middle_photo');
        if ($request->hasFile('middle_photo')) {
            $photo = Settings::where('option_name', 'middle_photo')->first();
            $old_file = 'uploads/' . $photo->value;
            if (is_file($old_file))    unlink($old_file);

            $fileName = 'middle-' . time() . '-' . uniqid() . '.' . $middle_photo->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('middle_photo')->move($destinationPath, $fileName);
            $photo->value = $fileName;
            $photo->save();
        }

        if ($request->settings) {


            foreach ($request->settings as $key => $value) {
                $settings = Settings::find($key);

                if ($settings) {
                    $settings->value = $value;
                    //echo $settings -> input_type ;
                    if ($settings->input_type == "checkbox") {

                        if ($value == "1") {
                            $settings->value = "1";
                        } else {
                            $settings->value = "0";
                        }
                    }
                    if ($settings->input_type == "switch") {
                        $settings->value = $value ? 1 : 0;
                    }

                    $settings->save();
                }
            }
        }
        return redirect()->back()->with('success', 'تم تعديل الاعدادات بنجاح');
    }
}
