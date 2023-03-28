<?php

namespace App\Http\Controllers\Panel;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Models\Suggestions;
use App\Models\AdsOrders;
use App\Models\AdsPhotos;
class SuggestionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.suggestions.all',['objects'=>Suggestions::orderBy('id','DESC')->get()]);
    }

    public function adv_adss()
    {
        return view('admin.pay_account.adv',['objects'=>Ads::where('adv',1)->get()]);
    }

    public function normal_ads()
    {
        return view('admin.ads.normal',['objects'=>Ads::where('adv',0)->get()]);
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
        return redirect()->back()->with('success','تم تميز الاعلان بنجاح .');
      }else{
        $ads -> adv = 0 ;
        $ads -> save();
        return redirect()->back()->with('success','تم ازالة تمييز الاعلان بنجاح .');
      }

    }

    public function contacts_new()
    {
      return view('admin.suggestions.ask_orders',['objects'=>Suggestions::where('status',0)->orderBy('id','DESC')->get()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.ads.add');
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
    	return view('admin.ads.add',['object'=> Ads::find($id)]);
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
      $ads = Suggestions::find($id);
      if($ads!=false){
        $ads = Suggestions::find($ads->id);
        $ads -> delete();
      }
      return  redirect()->back('success','تم حذف الرسالة بنجاح');
    }

    public function delete_order($id=0)
    {
      $ads = Suggestions::find($id);
      if($ads!=false){
        $ads = Suggestions::find($ads->id);
        $ads -> delete();
      }
      return  redirect()->back('success','تم حذف الطلب بنجاح');
    }
}
