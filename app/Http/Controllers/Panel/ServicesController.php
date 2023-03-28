<?php

namespace App\Http\Controllers\Panel;

use App\Models\Answers;
use App\Models\Orders;
use App\Models\Reports;
use App\Models\ServiceAdvantages;
use App\Models\ServicesPhotos;
use Illuminate\Http\Request;

use App\Http\Requests;

use App\Models\Services;
use App\Models\ProjectPhotos;
use App\Models\ProjectOffers;
class ServicesController  extends Controller
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
        return view('admin.services.all',['objects'=>Services::all()]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.services.add');
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
        	'name' => 'required|unique:services,name|max:100|min:3',
            'name_en' => 'required|unique:services,name_en|max:100|min:3',
            'brief'=>'required',
            'brief_en'=>'required',
//            'photo'=>'required',
        ]);
		 $object = new Services();
		 $object->name = $request->name;
         $object->name_en = $request->name_en;
         $object->brief = $request->brief;
         $object->brief_en = $request->brief_en;
//         $object->description = $request->description;
//         $object->description_en = $request->description_en;
         $object->price = $request->price;
         $object->currency_id = $request->currency_id;
        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $fileName = 'service-photo-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileName);
            $object->photo=$fileName;
        }
        $object->save();



		 return redirect()->back()->with('success','تم اضافة الخدمة بنجاح .');
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
    	return view('admin.services.add',['object'=> Services::find($id)]);
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
        $object= Services::find($id);
        $this->validate($request, [
        'name' => 'required|max:100|min:3|unique:services,name,'.$object->id.',id',
            'name_en' => 'required|max:100|min:3|unique:services,name_en,'.$object->id.',id',
            'brief'=>'required',
            'brief_en'=>'required',
//            'description'=>'required',
//            'description_en'=>'required',
//            'price'=>'required',
//            'currency_id'=>'required',
         ]);

        $object->name = $request->name;
        $object->name_en = $request->name_en;
        $object->brief = $request->brief;
        $object->brief_en = $request->brief_en;
//        $object->description = $request->description;
//        $object->description_en = $request->description_en;
        $object->price = $request->price ? $request->price : 0 ;
        $object->min_shipments = $request->min_shipments ? $request->min_shipments :0 ;
        $object->min_plane_price = $request->min_plane_price ? $request->min_plane_price :0 ;
        $object->max_plane_price  = $request->max_plane_price  ? $request->max_plane_price :0 ;
        $object->currency_id = $request->currency_id;
        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $old_file = 'uploads/'.$object->photo;
            if(is_file($old_file))	unlink($old_file);

            $fileName = 'service-photo-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileName);
            $object->photo=$fileName;
        }
        $object->save();

        return redirect()->back()->with('success','تم تعديل الخدمة بنجاح .');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $project = Services::find($id);
      if($project!=false){
          $project = Services::find($project->id);

          ServiceAdvantages::where('service_id',$project->id)->delete();
          Orders::where('service_id',$project->id)->delete();
          Reports::where('service_id',$project->id)->delete();

          foreach (ServicesPhotos::where('service_id',$id)->get() as  $photo) {
          $old_file = 'uploads/'.$photo->photo;
          if(is_file($old_file))	unlink($old_file);
          $photo->delete();
        }

          $project -> delete();
      }
    }

    public function delete_photo($id=0)
    {
      $photo = ServicesPhotos::find($id);
      if($photo!=false){
          $old_file = 'uploads/'.$photo->photo;
          if(is_file($old_file))	unlink($old_file);



          $photo -> delete();
      }
     return  redirect()->back()->with('success','تم حذف الصورة بنجاح');
    }
}
