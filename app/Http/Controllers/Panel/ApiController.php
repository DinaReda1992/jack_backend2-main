<?php

namespace App\Http\Controllers\Panel;
use App\Models\BankAccounts;
use App\Models\ContactTypes;
use App\Models\Faqs;
use App\Models\JoinUs;
use App\Models\MembershipBenefits;
use App\Models\Orders;
use App\Models\Packages;
use App\Models\Years;
use Illuminate\Support\Facades\App;

use App\Http\Requests;
use App\Models\Answers;
use App\Models\ApprovedProjects;
use App\Models\ArticleComments;
use App\Models\ArticlePhotos;
use App\Models\ArticleReports;
use App\Models\Articles;
use App\Models\BlogCategories;
use App\Models\BlogSubcategories;
use App\Models\Countries;
use App\Models\Illustrations;
use App\Models\Notification;
use App\Models\ProjectOffers;
use App\Models\ProjectPhotos;
use App\Models\Projects;
use App\Models\Questions;
use App\Models\Rating;
use App\Models\Services;
use App\Models\ServicesPhotos;
use App\Models\Steps;
use App\Models\Styles;
use App\Models\WhyUs;
use App\Models\WorkPhotos;
use App\Models\Works;
use Illuminate\Http\Request;
use App\Models\Cities;
use App\Models\Categories;
use App\Models\Subcategories;
use App\Models\Cars;
use App\Models\Contacts;
use App\Models\CarsModels;
use App\Models\States;
use App\Models\User;
use App\Models\Ads;
use App\Models\AdsPhotos;
use App\Models\AdsOrders;
use App\Models\AdsNotify;
use App\Models\Settings;
use App\Models\Comments;
use App\Models\CommentsFollows;
use App\Models\CommentsNotify;
use App\Models\Messages;
use App\Models\Marchant;
use App\Models\Companies;
use App\Models\Museums;
use App\Models\Likes;
use App\Models\Content;
use App\Models\Reports;
use App\Models\FollowCar;
use App\Models\PayAccount;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use \Carbon\Carbon;
use Mail;
use Illuminate\Support\Facades\Input;

class ApiController extends Controller
{

    public function __construct(Request $request)
    {
//        parent::__construct();
        $language = $request->headers->get('lang')  ? $request->headers->get('lang') : 'ar' ;
        App::setLocale($language);
    }


    public function getCountries(){
        return response()->json(
        [
         'status'=>200,
         'data'=>Countries::select('id','name','name_ar')->selectRaw('(CONCAT ("'.url('/').'/flags/", photo)) as photo')->selectRaw('(CONCAT ("+", phonecode)) as phonecode')->get()
        ]
        );
    }

    public function getFaqs(){
        if(App::isLocale('ar')){
            $faqs = Faqs::select('id','question','answer')->get();
            return response()->json(
                [
                    'status' => 200,
                    'data' =>$faqs
                ]
            );
        }else{
            $faqs = Faqs::select('id','question_en as question','answer_en as answer')->get();
            return response()->json(
                [
                    'status' => 200,
                    'data' =>$faqs
                ]
            );
        }
    }

    public function getOnlyCountries($id=0){
        if(App::isLocale('ar')){
            $countries = Countries::select('id','name_ar as name')->get();
            return response()->json(
                [
                    'status' => 200,
                    'data' =>$countries
                ]
            );
        }else{
            $countries = Countries::select('id','name')->get();
            return response()->json(
                [
                    'status' => 200,
                    'data' =>$countries
                ]
            );
        }
    }

    public function get_all_states(){
        if(App::isLocale('ar')){
            $countries = States::select('id','name')->get();
            return response()->json(
                [
                    'status' => 200,
                    'data' =>$countries
                ]
            );
        }else{
            $countries = States::select('id','name_en as name')->get();
            return response()->json(
                [
                    'status' => 200,
                    'data' =>$countries
                ]
            );
        }
    }


    public function getStates($id=0){
        if(App::isLocale('ar')){
            $states = States::select('id','name')->where('country_id',$id)->get();
            return response()->json(
                [
                    'status' => 200,
                    'data' =>$states
                ]
            );
        }else{
            $states = States::select('id','name_en as name')->where('country_id',$id)->get();
            return response()->json(
                [
                    'status' => 200,
                    'data' =>$states
                ]
            );
        }
    }

    public function get_contact_categories($id=0){
        if(App::isLocale('ar')){
            $types = ContactTypes::select('id','name')->get();
            return response()->json(
                [
                    'status' => 200,
                    'data' =>$types
                ]
            );
        }else{
            $types = ContactTypes::select('id','name_en as name')->get();
            return response()->json(
                [
                    'status' => 200,
                    'data' =>$types
                ]
            );
        }
    }


    public function getBrands(){
        if(App::isLocale('ar')){
            $cars = Cars::select('id','name')->get();
            return response()->json(
                [
                    'status' => 200,
                    'data' =>$cars
                ]
            );
        }else{
            $cars = Cars::select('id','name_en as name')->get();
            return response()->json(
                [
                    'status' => 200,
                    'data' =>$cars
                ]
            );
        }
    }

    public function getYears(){
            $years = Years::select('id','name')->orderBy('name','DESC')->get();
            return response()->json(
                [
                    'status' => 200,
                    'data' =>$years
                ]
            );

    }

    public function getAdmins(){
        $admins = User::select('id','username','first_name','last_name')->where('user_type_id',1)->orderBy('id','DESC')->get();
        return response()->json(
            [
                'status' => 200,
                'data' =>$admins
            ]
        );

    }


    public function getModels($id=0){
        if(App::isLocale('ar')){
            $models = CarsModels::select('id','name')->where('cars_category_id',$id)->get();
            return response()->json(
                [
                    'status' => 200,
                    'data' =>$models
                ]
            );
        }else{
            $models = CarsModels::select('id','name_en as name')->where('cars_category_id',$id)->get();
            return response()->json(
                [
                    'status' => 200,
                    'data' =>$models
                ]
            );
        }
    }


    public function services(){
        if(App::isLocale('ar')){
            $services = Services::
            select('id','name','brief','vin_required','elegant_service')->selectRaw('(CONCAT ("'.url('/').'/uploads/", photo)) as photo')
                ->with(['advantages'=> function ($query) {
                    $query->select('id','name','service_id');
                    $query->selectRaw('(CASE WHEN photo = "" THEN "'.URL::to('/').'/site/img/technics.png" ELSE (CONCAT ("'.URL::to('/').'/uploads/", photo)) END) AS photo');
                }])
                ->get();

            foreach ($services as $service){
                $report_id = Reports::where('service_id',$service->id)->where('order_id','!=',0)->first() ? Reports::where('service_id',$service->id)->where('order_id','!=',0)->orderBy('id','DESC')->first() : Reports::where('order_id','!=',0)->orderBy('id','DESC')->first();
                $url = url('/')."/ar/single-report/".$report_id->id;
                $service->{'report_url'}= $url;
            }
            return response()->json(
                [
                    'status' => 200,
                    'data' =>$services
                ]
            );
        }else{
            $services = Services::
            select('id','name_en as name','brief_en as brief','vin_required','elegant_service')->selectRaw('(CONCAT ("'.url('/').'/uploads/", photo)) as photo')
                ->with(['advantages'=> function ($query) {
                    $query->select('id','name_en as name','service_id');
                    $query->selectRaw('(CASE WHEN photo = "" THEN "'.URL::to('/').'/site/img/technics.png" ELSE (CONCAT ("'.URL::to('/').'/uploads/", photo)) END) AS photo');
                }])
                ->get();
            foreach ($services as $service){
                $report_id = Reports::where('service_id',$service->id)->where('order_id','!=',0)->first() ? Reports::where('service_id',$service->id)->where('order_id','!=',0)->first() : Reports::where('order_id','!=',0)->first();
                $url = url('/')."/en/single-report/".$report_id->id;
                $service->{'report_url'}= $url;
            }
            return response()->json(
                [
                    'status' => 200,
                    'data' =>$services
                ]
            );
        }
    }


    public function getIllustrations(){
        return response()->json(
            [
                'status'=>200,
                'data'=>Illustrations::select('id','type')->selectRaw('(CASE WHEN type = "photo" THEN (CONCAT ("'.url('/').'/uploads/", photo)) ELSE video_url  END) AS url')->get()
            ]

        );
    }

    public function terms()
    {
        if(App::isLocale('ar')) {
            return response()->json(
                [
                    'status'=>200,
                    'data'=>  Content::find(1)->content
                ]
            );
        }else{
            return response()->json(
                [
                    'status'=>200,
                    'data'=>  Content::find(1)->content_en
                ]
            );
        }


    }


    public function get_phone()
    {
        if(App::isLocale('ar')) {
            return response()->json(
                [
                    'status'=>200,
                    'data'=>  \App\Models\Settings::find(8)->value
                ]
            );
        }else{
            return response()->json(
                [
                    'status'=>200,
                    'data'=> \App\Models\Settings::find(8)->value
                ]
            );
        }


    }



    public function about()
    {
        if(App::isLocale('ar')) {

            $arr= [
                'content'=>Content::find(2)->content,
                'email'=>Settings::find(1)->value,
                'phone'=>Settings::find(9)->value,
                'instagram_url'=>Settings::find(2)->value,
                'twitter_url'=>Settings::find(3)->value,
            ];

            return response()->json(
                [
                    'status'=>200,
                    'data'=>  $arr
                ]
            );
        }else{
            $arr= [
                'content'=>Content::find(2)->content_en,
                'email'=>Settings::find(1)->value,
                 'phone'=>Settings::find(9)->value,
                'instagram_url'=>Settings::find(2)->value,
                'twitter_url'=>Settings::find(3)->value,
            ];
            return response()->json(
                [
                    'status'=>200,
                    'data'=>  $arr
                ]
            );
        }


    }

    public function join_us(Request $request) {

        $validator = Validator::make($request->all(), [
            'username' => 'required|max:60|min:3',
            'email' => 'required|email|unique:join_us,email',
            'phone' => 'required|unique:join_us,phone',
            'phonecode' => 'required',
            'city_id' => 'required',
            'message' => 'required',
        ]);

        if ($validator->fails()) {



            return response()->json(
                [
                    'status' => 400 ,
                    'errors' => $validator->errors()->all(),
                    'message' => trans('messages.some_error_happened') ,
                ]
            );
        }



        $user = new JoinUs();
        $user->username= $request->username;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->message = $request->message;
        $user->city_id = $request->city_id;
        $user->phonecode = $request->phonecode;
        $user -> save();


        return response()->json(
            [
                'status' => 200 ,
                'message' => trans('messages.your_request_sent_successfully') ,
            ]);

    }


    public function contact_us(Request $request) {

        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'email' => 'required|email',
            'phone' => 'required',
            'message_type_id' => 'required',
            'message' => 'required',
        ]);

        if ($validator->fails()) {



            return response()->json(
                [
                    'status' => 400 ,
                    'errors' => $validator->errors()->all(),
                    'message' => trans('messages.some_error_happened') ,
                ]
            );
        }



        $user = new Contacts();
        $user->name= $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->message = $request->message;
        $user->message_type_id = $request->message_type_id;
        $user -> save();


        return response()->json(
            [
                'status' => 200 ,
                'message' => trans('messages.your_request_sent_successfully') ,
            ]);

    }

    public function get_bank_accounts(){
        $bank_accounts = BankAccounts::select('*')->selectRaw('(CONCAT ("'.url('/').'/uploads/", photo)) as photo')->get();
        return response()->json(
            [
                'status' => 200,
                'data' =>$bank_accounts
            ]
        );

    }


    public function packages(){

        if(App::isLocale('ar')){
            $packages = Packages::select('id','name','currency_id','price')
                ->with(['getCurrency'=> function ($query) {
                    $query->select('id' ,'name' );
                }])
                ->get();

            return response()->json(
                [
                    'status' => 200,
                    'data' =>[
                        'packages'=> $packages,

                    ]
                ]
            );
        }else{
            $packages = Packages::select('id','name_en as name','currency_id','price')
                ->with(['getCurrency'=> function ($query) {
                    $query->select('id' ,'name_en as name' );
                }])
                ->get();

            return response()->json(
                [
                    'status' => 200,
                    'data' =>[
                       'packages'=> $packages,
                    ]
                ]
            );
        }
    }

    public function search(Request $request){
        $validator = Validator::make($request->all(), [
            'report_id' => 'required',
        ]);
        if ($validator->fails()) {



            return response()->json(
                [
                    'status' => 400 ,
                    'errors' => $validator->errors()->all(),
                    'message' => trans('messages.some_error_happened') ,
                ]
            );
        }else{

            if(App::getLocale()=="ar") {
                $reports = Reports::where('id', 'LIKE', "%" . $request->report_id . "%")
                    ->where('order_id', "!=", 0)
                    ->with(['getService' => function ($query) {
                        $query->select('id', 'name');
                    }])
                    ->whereIn('order_id', function ($query) {
                        $query->select('id')
                            ->from(with(new Orders())->getTable())
                            ->where('price','!=', "");
                    })
                        ->with(['getOrder.getCurrency' => function ($query) {
                        $query->select('id', 'name');
                    }])
                    ->select('id', 'date_of_report', 'service_id','order_id')
                    ->selectRaw('(CONCAT ("'.url('/').'/'.App::getLocale().'/single-report/", id)) as url')
                    ->paginate(10);
            }else{
                $reports = Reports::where('id', 'LIKE', "%" . $request->report_id . "%")
                    ->where('order_id', "!=", 0)
                    ->whereIn('order_id', function ($query) {
                        $query->select('id')
                            ->from(with(new Orders())->getTable())
                            ->where('price','!=', "");
                    })
                    ->with(['getService' => function ($query) {
                        $query->select('id', 'name_en as name');
                    }])
                    ->with(['getOrder.getCurrency' => function ($query) {
                        $query->select('id', 'name_en as name');
                    }])
                    ->select('id', 'date_of_report', 'service_id','order_id')
                    ->selectRaw('(CONCAT ("'.url('/').'/'.App::getLocale().'/single-report/", id)) as url')
                    ->paginate(10);
                }
            if($reports) {
                return response()->json(
                    [
                        'status' => 200,
                        'data' => $reports,
                    ]);
            }else{

                return response()->json(
                    [
                        'status' => 400,
                        'message' => trans('messages.no_reports_found' ),
                    ]);

            }

        }


    }






}
