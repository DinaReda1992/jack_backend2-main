<?php

namespace App\Http\Controllers\Panel;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Models\Articles;
use App\Models\AdsOrders;
use App\Models\ArticlePhotos;
class ArticlesController extends Controller
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
        return view('admin.articles.all',['objects'=>Articles::all()]);
    }

    public function adv_adss()
    {
        return view('admin.articles.adv',['objects'=>Articles::where('adv',1)->get()]);
    }

    public function normal_ads()
    {
        return view('admin.articles.normal',['objects'=>Articles::where('adv',0)->get()]);
    }




    public function adv_articles($id=0)
    {
      $ads = Articles::find($id);
      if(!$ads){
        return redirect()->back()->with('error','لا يوجد موضوع بهذا العنوان');
      }
      if($ads->adv==0){
        $ads -> adv = 1 ;
        $ads -> save();
        return redirect()->back()->with('success','تم تثبيت الموضوع بنجاح .');
      }else{
        $ads -> adv = 0 ;
        $ads -> save();
        return redirect()->back()->with('success','تم ازالة تثبيت الموضوع بنجاح .');
      }

    }

    public function adv_slider($id=0)
    {
        $ads = Articles::find($id);
        if(!$ads){
            return redirect()->back()->with('error','لا يوجد موضوع بهذا العنوان');
        }
        if($ads->adv_slider==0){
            $ads -> adv_slider = 1 ;
            $ads -> save();
            return redirect()->back()->with('success','تم اضافة الموضوع للاسلايدر بنجاح .');
        }else{
            $ads -> adv_slider = 0 ;
            $ads -> save();
            return redirect()->back()->with('success','تم ازالة الموضوع من الاسلايدر  بنجاح .');
        }

    }

    public function orders_adv()
    {
      return view('admin.articles.ask_orders',['objects'=>AdsOrders::all()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.articles.add');
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
        	'name' => 'required|unique:categories|max:100|min:3',
        ]);
		 $object = new User;
		 $object->name = $request->name;
		 $object->save();
		 return redirect()->back()->with('success','تم اضافة العضو بنجاح .');
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
    	return view('admin.articles.add',['object'=> Articles::find($id)]);
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
        $object= Articles::find($id);
        $this->validate($request, [
        'name' => 'required|max:100|min:3|unique:categories,name,'.$object->id.',id',
         ]);

		 $object->name = $request->name;
		 $object->save();
		 return redirect()->back()->with('success','تم تعديل العضو بنجاح .');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $ads = Articles::find($id);
      if($ads!=false){
        $ads = Articles::find($ads->id);
        foreach (ArticlePhotos::where('article_id',$id)->get() as  $photo) {
          $old_file = 'uploads/'.$photo->photo;
          if(is_file($old_file))	unlink($old_file);
          $photo->delete();
        }
        $ads -> delete();
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
}
