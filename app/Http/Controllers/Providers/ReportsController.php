<?php

namespace App\Http\Controllers\Providers;
use App\Http\Controllers\Controller;
use App\Models\Answers;
use App\Models\Orders;
use App\Models\ReportPhotos;
use App\Models\ReportPoints;
use App\Models\ServiceAdvantages;
use App\Models\Services;
use App\Models\ServicesPhotos;
use Illuminate\Http\Request;

use App\Http\Requests;

use App\Models\Reports;
use App\Models\ProjectOffers;
class ReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->check_settings(144);
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
        return view('providers.reports.all',['objects'=>Reports::all()]);
    }

    public function projects()
    {
        return view('providers.reports.ads',['objects'=>Reports::where('type',0)->orderBy('id','DESC')->get()]);
    }

    public function comments()
    {
        return view('providers.reports.comments',['objects'=>Reports::where('type',1)->get()]);
    }

    public function normal_ads()
    {
        return view('providers.reports.normal',['objects'=>Reports::where('adv',0)->get()]);
    }




    public function adv_ads($id=0)
    {
        $ads = Reports::find($id);
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('providers.reports.add');
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
        return view('providers.reports.add',['object'=> Reports::find($id)]);
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
        $object= Reports::find($id);
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
        $ads = Reports::find($id);
        if($ads!=false){
            $ads = Reports::find($ads->id);
            $ads -> delete();
        }
    }

    public function delComment($id=0){
        $comments = Comments::find($id);
        if($comments){
            $comments ->delete();
        }


        $ads = Reports::where('comment_id' ,$id)->get();
        foreach ($ads as $hd){
            $hd->delete();
        }
    }
}
