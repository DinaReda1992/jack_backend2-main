<?php

namespace App\Http\Controllers\Providers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Models\Ads;
use App\Models\AdsOrders;
use App\Models\AdsPhotos;
class AdsController extends Controller
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
        return view('providers.ads.all',['objects'=>Ads::all()]);
    }

    public function adv_adss()
    {
        return view('providers.ads.adv',['objects'=>Ads::where('adv',1)->get()]);
    }

    public function normal_ads()
    {
        return view('providers.ads.normal',['objects'=>Ads::where('adv',0)->get()]);
    }




    public function adv_ads($id=0)
    {
      $ads = Ads::find($id);
      if(!$ads){
        return redirect()->back()->with('error','لا يوجد اعلان بهذا العنوان');
      }
      if($ads->adv==0){
        $ads -> adv = 1 ;
        $ads -> save();
        return redirect()->back()->with('success','تم تثبيت الاعلان في الرئيسية بنجاح .');
      }else{
        $ads -> adv = 0 ;
        $ads -> save();
        return redirect()->back()->with('success','تم ازالة التثبيت من الرئيسية بنجاح .');
      }

    }

    public function adv_slider($id=0)
    {
        $ads = Ads::find($id);
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
      return view('providers.ads.ask_orders',['objects'=>AdsOrders::all()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('providers.ads.add');
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
    	return view('providers.ads.add',['object'=> Ads::find($id)]);
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
        $object= Ads::find($id);
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
      $ads = Ads::find($id);
      if($ads!=false){
        $ads = Ads::find($ads->id);
        foreach (AdsPhotos::where('ads_id',$id)->get() as  $photo) {
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
