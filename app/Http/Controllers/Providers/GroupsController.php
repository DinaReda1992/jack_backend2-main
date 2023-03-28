<?php

namespace App\Http\Controllers\Providers;

use App\Models\PrivilegesGroupsDetails;
use App\Models\SupervisorGroup;
use App\Models\SupervisorGroupsPrivileges;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Privileges;
use App\Models\Groups;
use Illuminate\Support\Facades\Auth;

class GroupsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {

        $this->middleware(function ($request, $next) {
            $this->check_settings((new \ReflectionClass($this))->getShortName());
            return $next($request);
        });
    }

    public function index()
    {

        $objects=Groups::where('is_provider',1)->where(function ($query){
           $query->where('provider_id',\auth()->user()->main_provider)->orWhere('provider_id',\auth()->id());})->get();

        return view('providers.groups.all',['objects'=>$objects ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('providers.groups.add',[ 'privileges'=>Privileges::where('parent_id',0)->where("is_provider",1)->where("hidden","0")->get() ]);
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
        	'name' => 'required|unique:privileges_groups|max:100|min:3',
        ]);
        $provider_id=Auth::user()->main_provider?Auth::user()->main_provider:Auth::id();

        $object = new Groups();
		 $object->name = $request->name;
        $object->provider_id=$provider_id;
        $object->is_provider=1;
	        $object->save();
        foreach ($request->privileges as $key=>$value) {
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
        $object=Groups::where('id',$id)->where(function ($query){
            $query->where('provider_id', auth()->id())
                ->orWhere('provider_id', auth()->user()->main_provider);
        })->first();
        $prArray = \App\Models\PrivilegesGroupsDetails::where('privilege_group_id', $id)->pluck('privilege_id')->toArray();
        return view('providers.groups.add',['object'=> $object, 'privileges'=>Privileges::where('parent_id',0)->where("is_provider",1)->where("hidden","0")->get(),'prArray'=>$prArray ]);

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
        $object = Groups::where('id',$id)->where(function ($query){
            $query->where('provider_id', auth()->id())
                ->orWhere('provider_id', auth()->user()->main_provider);
        })->first();
        $this->validate($request, [
        'name' => 'required|max:100|min:3',
        ]);
        $provider_id=Auth::user()->main_provider?Auth::user()->main_provider:Auth::id();

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

        $object = Groups::where('id',$id)->where(function ($query){
            $query->where('provider_id', auth()->id())
                ->orWhere('provider_id', auth()->user()->main_provider);
        })->first();
        $object->delete();
    }
}
