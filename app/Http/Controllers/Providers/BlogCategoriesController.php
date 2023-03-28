<?php

namespace App\Http\Controllers\Providers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Models\BlogCategories;
class BlogCategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('providers.blog-categories.all',['objects'=>BlogCategories::all()]);
    }


    public function save_order($cat_id=0,$order=0)
    {
        $cat = Categories::find($cat_id);
        $cat ->orderat = $order;
        $cat->save();
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('providers.blog-categories.add');
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
        	'name' => 'required|unique:blog_categories|max:100|min:3',
        ]);
		 $object = new BlogCategories();
		 $object->name = $request->name;
		 $object->save();
		 return redirect()->back()->with('success','تم اضافة القسم الرئيسي للمنتدى بنجاح');
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
    	return view('providers.blog-categories.add',['object'=> BlogCategories::find($id)]);
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
        $object= BlogCategories::find($id);
        $this->validate($request, [
        'name' => 'required|max:100|min:3|unique:blog_categories,name,'.$object->id.',id',
         ]);
		
		 $object->name = $request->name;

        $object->save();
		 return redirect()->back()->with('success','تم تعديل القسم الرئيسي للمنتدى بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $object = BlogCategories::find($id);
        $object->delete();
    }
}
