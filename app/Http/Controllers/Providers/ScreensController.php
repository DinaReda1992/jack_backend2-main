<?php

namespace App\Http\Controllers\Providers;

use App\Models\Answers;
use App\Models\Orders;
use App\Models\Reports;
use App\Models\ScreenDetails;
use App\Models\Screens;
use App\Models\ServiceAdvantages;
use App\Models\ServicesPhotos;
use Illuminate\Http\Request;

use App\Http\Requests;

use App\Models\Services;
use App\Models\ProjectPhotos;
use App\Models\ProjectOffers;
class ScreensController  extends Controller
{
    public function __construct()
    {
        }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('providers.screens.all',['objects'=>Screens::all()]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('providers.screens.add');
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
        	'name' => 'required|unique:screens,name',
            'photo'=>'required',
        ]);
		 $object = new Screens();
		 $object->name = $request->name;
        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $fileName = 'screen-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileName);
            $object->photo=$fileName;
        }
        $object->save();


        if($request->details){
            foreach ($request->details as $key=>$value){
                if($value) {
                    $adv = new ScreenDetails();
                    $adv->screen_id = $object->id;
                    $adv->detail = $value;
                    $adv->save();
                }
            }
        }

		 return redirect()->back()->with('success','تم اضافة الشاشة بنجاح .');
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
    	return view('providers.screens.add',['object'=> Screens::find($id)]);
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
        $object= Screens::find($id);
        $this->validate($request, [
        'name' => 'required|unique:screens,name,'.$object->id.',id',

         ]);

        $object->name = $request->name;
        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $old_file = 'uploads/'.$object->photo;
            if(is_file($old_file))	unlink($old_file);

            $fileName = 'screen-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileName);
            $object->photo=$fileName;
        }
        $object->save();

        ScreenDetails::where('screen_id',$object->id)->delete();
        if($request->details){
        foreach ($request->details as $key=>$value){
                if($value) {
                    $adv = new ScreenDetails();
                    $adv->screen_id = $object->id;
                    $adv->detail = $value;
                    $adv->save();
                }
            }
    }

        return redirect()->back()->with('success','تم تعديل الشاشة بنجاح .');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $project = Screens::find($id);
      if($project!=false){
          $project = Screens::find($project->id);

          ScreenDetails::where('screen_id',$project->id)->delete();
          $project -> delete();
      }
    }
}
