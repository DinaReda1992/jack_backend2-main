<?php

namespace App\Http\Controllers\Panel;

use App\Models\Answers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Projects;
use App\Models\ProjectPhotos;
use App\Models\ProjectOffers;
use SebastianBergmann\CodeCoverage\Report\Xml\Project;

class ProjectsController  extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->check_settings(127);
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
        return view('admin.projects.all',['objects'=>Projects::orderBy('id','DESC')->get()]);
    }

    public function new_projects()
    {
        return view('admin.projects.new_projects',['objects'=>Projects::where('status',0)->orderBy('id','DESC')->get()]);
    }

    public function approved_projects()
    {
        return view('admin.projects.approved_projects',['objects'=>Projects::where('status',1)->orderBy('id','DESC')->get()]);
    }

    public function cancelled_projects()
    {
        return view('admin.projects.cancelled_projects',['objects'=>Projects::where('status',2)->orderBy('id','DESC')->get()]);
    }

    public function normal_ads()
    {
        return view('admin.projects.normal',['objects'=>Projects::where('adv',0)->get()]);
    }




    public function approve_project($id=0)
    {
        $project = Projects::find($id);
        $project->status=1;
        $project->save();
        return redirect()->back()->with('success','تمت الموافقة على المشروع بنجاح .');
    }

    public function cancel_project($id=0)
    {
        $project = Projects::find($id);
        $project->status=2;
        $project->save();
        return redirect()->back()->with('success','تم رفض المشروع بنجاح .');
    }

    public function adv_slider($id=0)
    {
        $ads = Projects::find($id);
        if(!$ads){
            return redirect()->back()->with('error','لا يوجد اعلان بهذا العنوان');
        }
        if($ads->adv_slider==0){
            $ads -> adv_slider = 1 ;
            $ads -> save();
            return redirect()->back()->with('success','تم تثبيت الاعلان في القسم بنجاح .');
        }else{
            $ads -> adv_slider = 0 ;
            $ads -> save();
            return redirect()->back()->with('success','تم ازالة التثبيت من القسم  بنجاح .');
        }

    }



    public function orders_adv()
    {
      return view('admin.projects.ask_orders',['objects'=>AdsOrders::all()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.projects.add');
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
            'category_id' => 'required',
            'title' => 'required|min:3',
            'description' => 'required|min:12',
            'country_id' => 'required',
            'state_id' => 'required',
            'sub_category_id' => 'required',
        ]);
        $object = new Projects();
        $object->title = $request->title;
        $object->category_id = $request->category_id;
        $object->sub_category_id = $request->sub_category_id;
        $object->description = $request->description;
        $object->country_id = $request->country_id;
        $object->state_id = $request->state_id;
        $object->project_status = $request->project_status;
        $object->user_id = auth()->id();

        $object->save();

        if ($request->hasFile('photos')) {
            $files = $request->file('photos');
            foreach($files as $file_){
                $fileName = 'ads-'.time().'-'.uniqid().'.'.$file_->getClientOriginalExtension();
                $destinationPath = 'uploads';
                $file_->move($destinationPath, $fileName);
                $object1 = new ProjectPhotos();
                $object1->photo=$fileName;
                $object1->project_id=$object->id;
                $object1->save();
            }
        }



        return redirect()->back()->with('success','تم إضافة المشروع بنجاح .');
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
    	return view('admin.projects.add',['object'=> Projects::find($id)]);
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
        $object= Projects::find($id);
        $this->validate($request, [
            'category_id' => 'required',
            'title' => 'required|min:3',
            'description' => 'required|min:12',
            'country_id' => 'required',
            'state_id' => 'required',
            'sub_category_id' => 'required',
        ]);

        $object->title = $request->title;
        $object->category_id = $request->category_id;
        $object->description = $request->description;
        $object->country_id = $request->country_id;
        $object->state_id = $request->state_id;
        $object->sub_category_id = $request->sub_category_id;
        $object->project_status = $request->project_status;
        $object->save();

        if ($request->hasFile('photos')) {
            $files = $request->file('photos');
            foreach($files as $file_){
                $fileName = 'ads-'.time().'-'.uniqid().'.'.$file_->getClientOriginalExtension();
                $destinationPath = 'uploads';
                $file_->move($destinationPath, $fileName);
                $object1 = new ProjectPhotos();
                $object1->photo=$fileName;
                $object1->project_id=$object->id;
                $object1->save();
            }
        }

        return redirect()->back()->with('success','تم تعديل المشروع بنجاح .');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $project = Projects::find($id);
      if($project!=false){
          $project = Projects::find($project->id);
        foreach (ProjectPhotos::where('project_id',$id)->get() as  $photo) {
          $old_file = 'uploads/'.$photo->photo;
          if(is_file($old_file))	unlink($old_file);
          $photo->delete();
        }

          $project -> delete();
      }
    }

    public function delete_order($id=0)
    {
      $ads = AdsOrders::find($id);
      if($ads!=false){
        $ads = AdsOrders::find($ads->id);
        $ads -> delete();
      }
     return  redirect()->back('success','تم حذف الطلب بنجاح');
    }


    public function delete_photo($id=0)
    {
        $photo = ProjectPhotos::find($id);
        if($photo!=false){
            $old_file = 'uploads/'.$photo->photo;
            if(is_file($old_file))	unlink($old_file);
            $photo -> delete();
        }
        return  redirect()->back()->with('success','تم حذف الصورة بنجاح');
    }

}
