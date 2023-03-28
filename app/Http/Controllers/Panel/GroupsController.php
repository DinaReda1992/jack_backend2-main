<?php

namespace App\Http\Controllers\Panel;

use App\Models\PrivilegesGroupsDetails;
use Illuminate\Http\Request;

use App\Http\Requests;

use App\Models\Privileges;
use App\Models\Groups;
use App\Http\Controllers\Controller;

class GroupsController extends Controller
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
        return view('admin.groups.all',['objects'=>Groups::where('is_provider',0)->get() ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('admin.groups.add',[ 'privileges'=>Privileges::where('parent_id',0)->where("is_provider",0)->where("hidden","0")->get() ]);
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
            'name' => 'required|max:100|min:3',
        ]);
        $object = new Groups;
        $object->name = $request->name;
        $object->save();
        foreach ($request ->input('privileges') as $prev=>$value){
            $new_prev = new PrivilegesGroupsDetails();
            $new_prev->privilege_id=$value;
            $new_prev->privilege_group_id=$object->id;
            $new_prev->save();
            $privilege=Privileges::where('id',$value)->first();
            if($privilege->parent_id && !PrivilegesGroupsDetails::where('privilege_id',$privilege->parent_id)->where('privilege_group_id',$object->id)->count()){
                $supervisor_group = new PrivilegesGroupsDetails();
                $supervisor_group->privilege_group_id = $object->id;
                $supervisor_group->privilege_id =$privilege->parent_id;
                $supervisor_group->save();
            }

        }
        return redirect()->back()->with('success','تم اضافة المجموعة بنجاح');
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
        $object=Groups::find($id);
        $prArray = \App\Models\PrivilegesGroupsDetails::where('privilege_group_id', $id)->pluck('privilege_id')->toArray();
        $privileges=Privileges::where('parent_id',0)->where("is_provider",0)->where("hidden","0")->get();

        return view('admin.groups.add',['object'=> $object, 'privileges'=>$privileges,'prArray'=>$prArray ]);
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
        $object = Groups::find($id);
        $this->validate($request, [
            'name' => 'required|max:100|min:3',
        ]);
        $object->name = $request->name;
        $object->save();
        PrivilegesGroupsDetails::where('privilege_group_id',$object->id)->delete();
        foreach ($request ->input('privileges') as $prev=>$value){
            $new_prev = new PrivilegesGroupsDetails();
            $new_prev->privilege_id=$value;
            $new_prev->privilege_group_id=$object->id;
            $new_prev->save();
            $privilege=Privileges::where('id',$value)->first();
            if($privilege->parent_id && !PrivilegesGroupsDetails::where('privilege_id',$privilege->parent_id)->where('privilege_group_id',$object->id)->count()){
                $supervisor_group = new PrivilegesGroupsDetails();
                $supervisor_group->privilege_group_id = $object->id;
                $supervisor_group->privilege_id =$privilege->parent_id;
                $supervisor_group->save();
            }

        }
        return redirect()->back()->with('success','تم تعديل المجموعة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $object = Groups::find($id);
        $object->delete();
    }
}
