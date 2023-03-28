<?php

namespace App\Http\Controllers\Panel;

use Illuminate\Http\Request;

use App\Http\Requests;
use Auth;
use App\Models\Menus;
use App\Models\Main_menus;

class MenusController extends Controller
{

  public function __construct()
  {

//      if(Auth::user()->user_type_id != 1){
//
//          if(Auth::user() -> privilege_id!='0' ){
//              $privileges = unserialize(Auth::user() -> privileges());
//      foreach ($privileges as $privilege) {
//        if($privilege == 4){
//          return 1 ;
//        }
//      }
//    }
//
//    die('<h1 align="center">ليس لديك صلاحية للدخول هنا <br> للعودة اضغط <a href="/admin/index"> هنا </a> </h1>');
//
//    }
  }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.menus.all',['objects'=>Main_menus::all()]);
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
          'menu_name' => 'required|unique:main_menus|max:100|min:3',
        ]);
		 $object = new Main_menus();
		 $object->menu_name = $request->menu_name;
		 $object->save();
		 return redirect("/admin/menus/".$object->id."/edit")->with('success','تم اضافة القائمة بنجاح .');
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
          $object->link = url('page/'.$request->page_id) ;
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
        $object = Main_menus::find($id);
        $items= $object->getItems;
        foreach($items as $item){
            $item->delete();
        }

        $object->delete();
    }
}
