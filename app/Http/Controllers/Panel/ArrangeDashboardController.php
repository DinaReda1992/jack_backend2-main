<?php

namespace App\Http\Controllers\Panel;

use App\Models\Content;
use App\Models\Groups;
use App\Models\Privileges;
use Illuminate\Http\Request;

use App\Http\Requests;
use Auth;
use App\Models\Menus;
use App\Models\Main_menus;
use App\Http\Controllers\Controller;

class ArrangeDashboardController extends Controller
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
        return view('admin.arrange_dashboard.edit_menu',['privileges'=>Privileges::where('parent_id',0)->where("is_provider",0)->where("hidden","0")->orderBy('orders')->get() ]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.menus.add');
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
        $object = new Menus;
        $object->name = $request->name;
        $object->page_id = $request->page_id;

        $object->parent_id = $request->parent_id;
        if($request->page_id){
            $page=Content::find($request->page_id);
            $object->link = url($page->link?:'page/'.$page->page_name) ;

            $object->type = 1 ;
        }else{
            $object->type = 0 ;
            $object->link = $request->link;
        }
        $object->menu_id=$request->menu_id;
        $object->save();
        return redirect()->back()->with('success','تم اضافة القائمة بنجاح .');
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
    	return view('admin.menus.edit_menu',['object'=> Main_menus::find($id)]);
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
        $object= Menus::find($id);
        $this->validate($request, [
          'name' => 'required|max:100|min:3|unique:menus,name,'.$object->id.',id',
          'meta_title' => 'required|max:100|min:3',
          'meta_description' => 'required|max:100|min:3',
          'meta_keywords' => 'required|max:100|min:3',
        ]);
        $object->name = $request->name;
        $object->page_id = $request->page_id;

        $object->parent_id = $request->parent_id;
        if($request->page_id){
            $page=Content::find($request->page_id);

          $object->link = url($page->link?:'page/'.$page->page_name) ;
          $object->type = 1 ;
        }else{
          $object->type = 0 ;
          $object->link = $request->link;
        }

		 return redirect()->back()->with('success','تم تعديل القائمة بنجاح ');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $object = menus::find($id);
        $object->delete();
    }
    public function setArrangeDashboard(Request $request){

        $list=json_decode($request->items, true);

        if(count($list)>0){
            $this->saveList($list,0,0);
        }
        return 1;
    }

    function saveList($list,$parent_id=0,$m_order){
        foreach($list as $item){
            $m_order++;
            $item_menu=Privileges::find($item["id"]);
            $item_menu->parent_id=$parent_id;
            $item_menu->orders=$m_order;
            $item_menu->save();
            if (array_key_exists("children", $item)) {
                $this->saveList( $item["children"], $item["id"], $m_order);
            }

        }

    }

}
