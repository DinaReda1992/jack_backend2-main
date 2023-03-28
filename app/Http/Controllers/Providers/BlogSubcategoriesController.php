<?php

namespace App\Http\Controllers\Providers;

use App\Models\BlogSubcategories;
use Illuminate\Http\Request;

use App\Http\Requests;

use App\Models\BlogCategories;

class BlogSubcategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('providers.blog-subcategories.all',['objects'=>BlogSubcategories::all() ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('providers.blog-subcategories.add',[ 'categories'=>BlogCategories::all() ]);
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
        	'name' => 'required|unique:blog_subcategories|max:100|min:3',
        	'category_id' => 'required',
        ]);
		 $object = new BlogSubcategories();
		 $object->name = $request->name;
		 $object->category_id = $request->category_id;
		 $object->save();
		 return redirect()->back()->with('success','تم اضافة القسم الفرعي للمنتدى بنجاح');
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
    	return view('providers.blog-subcategories.add',['object'=> BlogSubcategories::find($id), 'categories'=>BlogCategories::all() ]);
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
        $object = BlogSubcategories::find($id);
        $this->validate($request, [
        'name' => 'required|max:100|min:3|unique:blog_subcategories,name,'.$object->id.',id',
        'category_id' => 'required' 
        ]);
		
		 $object->name = $request->name;
		 $object->category_id = $request->category_id;
		 $object->save();
		 return redirect()->back()->with('success','تم تعديل القسم الفرعي للمنتدى بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $object = BlogSubcategories::find($id);
        $object->delete();
    }
}
