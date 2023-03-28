<?php

namespace App\Http\Controllers\Providers;

use App\Http\Resources\Categories;
use App\Models\BankTransfer;
use App\Models\Countries;
use App\Models\DeviceTokens;
use App\Models\Feature;
use App\Models\Hall;
use App\Models\SupplierCategory;
use App\Models\HallFeature;
use App\Models\HallPhoto;
use App\Models\Messages;
use App\Models\Notification;
use App\Models\Orders;
use App\Models\Packages;
use App\Models\Projects;
use App\Models\States;
use App\Models\UserServices;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;
class ProvidersController extends Controller
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
        $this->check_provider_settings(381);
        $provider_id=Auth::user()->user_type_id==3?Auth::id():Auth::user()->main_provider;

        return view('providers.control-providers.all',['objects'=>Hall::where('user_id',$provider_id)->get()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->check_provider_settings(382);
        $provider_id=Auth::user()->user_type_id==3?Auth::id():Auth::user()->main_provider;
        $provider=User::find($provider_id);
$states=States::where('country_id',$provider->country_id)->get();
$features=Feature::all();
        return view('providers.control-providers.add',['states'=>$states,'provider'=>$provider,'features'=>$features]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->check_provider_settings(382);

        $this->validate($request, [
            'title' => 'required',
            'title_en' => 'required',
            'categories' => 'required',
            'price_per_hour' => 'required',
            'currency' => 'required',
            'state_id' =>'required',
            'longitude' =>'required',
            'address_en'=>'required',
            'address'=>'required',
            'latitude'=>'required',
            'chairs'=>'required',
            'capacity'=>'required',
            'photos'=>'required|min:3'

        ]);
		 $object = new Hall();
		 $object->title= $request->title;
         $object->title_en = $request->title_en;
         $object->price_per_hour = $request->price_per_hour;
         $object->currency = $request->currency;
         $object->state_id = $request->state_id;
         $object->longitude = $request->longitude;
        $object->latitude = $request->latitude;
         $object->address = $request->address;
        $object->address_en = $request->address_en;
        $object->chairs = $request->chairs;
        $object->capacity = $request->capacity;
        $object->description = $request->description?:'';
        $object->description_en = $request->description_en?:'';
        $object->terms = $request->terms?:'';
        $object->terms_en = $request->terms_en?:'';
        $object->policy = $request->policy?:'';
        $object->policy_en = $request->policy_en?:'';
        $provider_id=Auth::user()->user_type_id==3?Auth::id():Auth::user()->main_provider;
        $object->user_id=$provider_id;
        $object->save();
        if($request->categories){
            foreach ($request->categories as $key=>$value) {
                $hallCategory=new SupplierCategory();
                $hallCategory->hall_id=$object->id;
                $hallCategory->category_id=$value;
                $hallCategory->save();
            }
        }
        if($request->features){
            foreach ($request->features as $key=>$value) {
                if($value) {

                    $hallFeature = new HallFeature();
                    $hallFeature->hall_id = $object->id;
                    $hallFeature->feature_id = $value;
                    $hallFeature->description = @$request->description_[$key]?:'';
                    $hallFeature->description_en = @$request->description_en_[$key]?:'';
                    $hallFeature->price = @$request->price[$key]?:'';

                    $hallFeature->save();
                }
            }
        }

        if ($request->hasFile('photos')) {
            $files = $request->file('photos');
            foreach($files as $file_){
                $fileName = 'hall-'.time().'-'.uniqid().'.'.$file_->getClientOriginalExtension();
                $destinationPath = 'uploads';
                $file_->move($destinationPath, $fileName);
                $object1 = new HallPhoto();
                $object1->photo=$fileName;
                $object1->hall_id=$object->id;
                $object1->save();
            }
        }

		 return redirect()->back()->with('success','تم اضافة القاعة بنجاح .');
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
        $this->check_provider_settings(383);
        $provider_id=Auth::user()->user_type_id==3?Auth::id():Auth::user()->main_provider;
        $provider=User::find($provider_id);
        $states=States::where('country_id',$provider->country_id)->get();
        $features=Feature::all();
        $hall_types = SupplierCategory::where('hall_id',$id)->pluck('category_id')->toArray();
        $object =Hall::where('id',$id)->where(function ($query)  {
            $query->where('user_id', auth()->id())
                ->orWhere('user_id', auth()->user()->main_provider)  ;
        })->first();
        ;
        if(!$object){
            return  redirect()->back()->with('error','لا توجد قاعة للتعديل');
        }

        return view('providers.control-providers.add',['object'=>$object,'states'=>$states,'provider'=>$provider,'features'=>$features,'hall_types'=>$hall_types]);
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
        $this->check_provider_settings(383);

        $this->validate($request, [
            'title' => 'required',
            'title_en' => 'required',
            'categories' => 'required',
            'price_per_hour' => 'required',
            'currency' => 'required',
            'state_id' =>'required',
            'longitude' =>'required',
            'address_en'=>'required',
            'address'=>'required',
            'latitude'=>'required',
            'chairs'=>'required',
            'capacity'=>'required',

        ]);
        $object = Hall::where('id',$id)->where(function ($query)  {
            $query->where('user_id', auth()->id())
                ->orWhere('user_id', auth()->user()->main_provider)  ;
        })->first();
        if(!$object){
            return redirect()->back()->with('error','لايوجد قاعة للتعديل');
        }
        $object->title= $request->title;
        $object->title_en = $request->title_en;
        $object->price_per_hour = $request->price_per_hour;
        $object->currency = $request->currency;
        $object->state_id = $request->state_id;
        $object->longitude = $request->longitude;
        $object->latitude = $request->latitude;
        $object->address = $request->address;
        $object->address_en = $request->address_en;
        $object->chairs = $request->chairs;
        $object->capacity = $request->capacity;
        $object->description = $request->description;
        $object->description_en = $request->description_en;
        $object->terms = $request->terms;
        $object->terms_en = $request->terms_en;
        $object->policy = $request->policy;
        $object->policy_en = $request->policy_en;
        $object->status=$object->status==3?0:$object->status;
        $provider_id=Auth::user()->user_type_id==3?Auth::id():Auth::user()->main_provider;
//        $object->user_id=$provider_id;
        $object->status=0;
        $object->save();
        SupplierCategory::where('hall_id',$object->id)->delete();
        if($request->categories){
            foreach ($request->categories as $key=>$value) {
                $hallCategory=new SupplierCategory();
                $hallCategory->hall_id=$object->id;
                $hallCategory->category_id=$value;
                $hallCategory->save();
            }
        }
        HallFeature::where('hall_id',$object->id)->delete();

        if($request->features){
            foreach ($request->features as $key=>$value) {
                if($value){
                    $hallFeature=new HallFeature();
                    $hallFeature->hall_id=$object->id;
                    $hallFeature->feature_id=$value;
//                dd(@$request->description[$key]);
                    $hallFeature->description=@$request->description_[$key];
                    $hallFeature->description_en=@$request->description_en_[$key];
                    $hallFeature->price=@$request->price[$key];

                    $hallFeature->save();

                }
            }
        }

        if ($request->hasFile('photos')) {
            $files = $request->file('photos');
            foreach($files as $file_){
                $fileName = 'hall-'.time().'-'.uniqid().'.'.$file_->getClientOriginalExtension();
                $destinationPath = 'uploads';
                $file_->move($destinationPath, $fileName);
                $object1 = new HallPhoto();
                $object1->photo=$fileName;
                $object1->hall_id=$object->id;
                $object1->save();
            }
        }

		 return redirect()->back()->with('success','تم تعديل القاعة بنجاح .');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteHallPhoto($id)
    {
        $this->check_provider_settings(383);
        $photo =HallPhoto::find($id);
        $provider_id=Auth::user()->user_type_id==3?Auth::id():Auth::user()->main_provider;
if($photo && $photo->hall->user_id==$provider_id){
            $path = 'uploads/'.$photo->photo;
            if(is_file($path))	unlink($path);
            $photo->delete();
    //status = 0 جديده
//status = 1 مقبولة
//status = 2 معطله
//status = 3 مرفوضة
    $hall=$photo->hall;
$hall->status=0;
$hall->save();
            return redirect()->back()->with('success','تم حذف الصورة .');
        }
        return  redirect()->back()->with('error','لا توجد قاعة للتعديل');

    }

    public function destroy($id)
    {
        $hall =Hall::where('id',$id)->where(function ($query)  {
            $query->where('user_id', auth()->id())
                ->orWhere('user_id', auth()->user()->main_provider)  ;
        })->first();
        if($hall){
            $hall->stop=$hall->stop?0:1;
            $hall->save();

            return  1;
        }
        return -1;

    }
    public function stopOpenHall($id){
        $this->check_provider_settings(383);

//status = 0 جديده
//status = 1 مقبولة
//status = 2 معطله
//status = 3 مرفوضة
        $hall =Hall::where('id',$id)
            ->where(function ($query)  {
                $query->where('user_id', auth()->id())
                    ->orWhere('user_id', auth()->user()->main_provider)  ;
            })->first();

        if($hall){
            $hall->status=$hall->status==1?2:1;
            $hall->save();
            return redirect()->back()->with('success','تم تغيير حالة القاعة بنجاح .');
        }
        return redirect()->back()->with('success','لا توجد قاعة للتعديل .');

    }
    public function extraFeatures(){

        return view('providers.items.feature',['features'=>Feature::all()]);
    }
}
