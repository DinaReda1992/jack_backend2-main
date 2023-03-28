<?php

namespace App\Http\Controllers\Panel;

use App\Models\BankAccounts;
use App\Models\JoinUs;
use App\Models\Packages;
use App\Models\Regions;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;
use App\Http\Requests;
use App\Models\Answers;
use App\Models\ApprovedProjects;
use App\Models\ArticleComments;
use App\Models\ArticlePhotos;
use App\Models\ArticleReports;
use App\Models\Articles;
use App\Models\BankTransfer;
use App\Models\BlogCategories;
use App\Models\BlogSubcategories;
use App\Models\Countries;
use App\Models\Notification;
use App\Models\Orders;
use App\Models\Prices;
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
use Illuminate\Support\Facades\Validator;
use \Carbon\Carbon;
use Mail;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\App;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        \Carbon\Carbon::setLocale(App::getLocale());
    }


    public function just_test(){

        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60*20);

        $notificationBuilder = new PayloadNotificationBuilder('my title');
        $notificationBuilder->setBody('Hello world')
            ->setSound('default');

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(['a_data' => 'my_data']);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $token = "f3Axh2mQMTM:APA91bETFASkOxRIiJRzX-fjwa3Dd9CTRI3C1MZM_C-ggcxqO4QG8fDexMDDN-P7O9LEGz9OBMwXkA6w7CxJeJWCYo6BsFrtIpVp2uZdxCw9g-JMU5uMHQptKA8Zoy58Ti9MmiOcLRlu";

//        $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);
        $downstreamResponse = FCM::sendTo($token, null, null, $data);
        $downstreamResponse->numberSuccess();
        $downstreamResponse->numberFailure();
        $downstreamResponse->numberModification();
    }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    public function faqs()
    {
        return view('faqs');
    }

    public function join_us()
    {
        return view('join_us');
    }

    public function golden_member()
    {
        return view('golden_member');
    }

    public function complain()
    {
        return view('complain');
    }



    public function change_lang($lang="ar")
    {
        if($lang=="en") {
            $past = str_replace('/ar', '/en', $_SERVER['HTTP_REFERER']);
        }else{
            $past = str_replace('/en', '/ar', $_SERVER['HTTP_REFERER']);
        }
        return redirect($past);
    }



    public function blog($images = 0 )
    {
        $cats = BlogCategories::all();
        return view('blog', [ 'cats' => $cats]);
    }


    public function brand($id=0,$images = 0 )
    {
        $car = Cars::find($id);
        if (!$car) {
            return redirect()->back()->with('error', 'عفوا ، لا يوجد ماركة  بهذا العنوان');
        }

        if($images == 2){
            $ads = Ads::where('hidden', 0)
                ->where("car_id",$id)
                ->whereIn('id', function ($query){
                    $query->select('ads_id')
                        ->from(with(new AdsPhotos())->getTable());
                })
                ->orderBy('adv_slider','DESC')
                ->orderBy('created_at', "DESC")
                ->paginate(Settings::find(11)->value);
        }elseif ($images == 1){
            $ads = Ads::where('hidden', 0)
                ->where("car_id",$id)
                ->whereNotIn('id', function ($query){
                    $query->select('ads_id')
                        ->from(with(new AdsPhotos())->getTable());
                })
                ->orderBy('adv_slider','DESC')
                ->orderBy('created_at', "DESC")
                ->paginate(Settings::find(11)->value);
        }else{
            $ads = Ads::where('hidden', 0)->where("car_id",$id)
                ->orderBy('adv_slider','DESC')
                ->orderBy('created_at', "DESC")
                ->paginate(Settings::find(11)->value);
        }

        return view('brand', ['all_ads' => $ads, 'brand'=>$car]);
    }



    public function blog_category($id=0)
    {
        $category = BlogSubcategories::find($id);
        if (!$category) {
            return redirect()->back()->with('error', 'عفوا ، لا يوجد قسم مندى  بهذا العنوان');
        }
        $articles = Articles::where('sub_category_id',$category->id)->orderBy('adv','DESC')->orderBy('updated_at','DESC')->get();
        return view('blogs', ['articles' => $articles, 'blog_category'=>$category]);
    }


    public function type($id=0,$images = 0 )
    {
        $model = CarsModels::find($id);
        $brand = Cars::find($model->cars_category_id);
        if (!$model) {
            return redirect()->back()->with('error', 'عفوا ، لا يوجد نوع  بهذا العنوان');
        }

        if($images == 2){
            $ads = Ads::where('hidden', 0)
                ->where("model_id",$id)
                ->whereIn('id', function ($query){
                    $query->select('ads_id')
                        ->from(with(new AdsPhotos())->getTable());
                })
                ->orderBy('created_at', "DESC")
                ->paginate(Settings::find(11)->value);
        }elseif ($images == 1){
            $ads = Ads::where('hidden', 0)
                ->where("model_id",$id)
                ->whereNotIn('id', function ($query){
                    $query->select('ads_id')
                        ->from(with(new AdsPhotos())->getTable());
                })
                ->orderBy('created_at', "DESC")
                ->paginate(Settings::find(11)->value);
        }else{
            $ads = Ads::where('hidden', 0)->where("model_id",$id)->orderBy('created_at', "DESC")->paginate(Settings::find(11)->value);
        }

        return view('type', ['all_ads' => $ads, 'type'=>$model,'brand'=>$brand]);
    }


    public function getActivate($activation_code = '')
    {
        $user = User::where('activation_code', $activation_code)->first();
        if (!$user) {
            return redirect('/')->with('error', 'عذرا هذا الرابط غير صالح');
        } else {
            $user->activate = 1;
            $user->save();
            return redirect('/')->with('success', ' تم تفعيل حسابك بنجاح لتسجيل الدخول اضغط <a href="/login">هنا</a> .');
        }
    }

    public function activate_phone_number($phone = '')
    {
        $user = User::where('phone', $phone)->first();
        if (!$user) {
            return redirect('/')->with('error', 'عذرا هذا الرابط غير صالح');
        } else {
            return view('phone_activation',['user'=>$user]);
        }
    }

    public function activate_phone_number_post($phone = '',Request $request)
    {
        $this->validate($request, [
            'activation_code' => 'required',
        ]);


        $user = User::where('phone', $phone)->where('activation_code',$request->activation_code)->first();
        if (!$user) {
            return redirect()->back()->with('error', 'عفوا هذا الكود غير صحيح .. حاول مرة اخرى');
        } else {
            $user -> phone_activate=1;
            $user -> save();
            return redirect('/')->with('success', 'تم تفعيل رقم الجوال بنجاح .');
        }
    }


    public function getResend()
    {
        return view('resend_activation');
    }


    public function sendSMS($userAccount, $passAccount, $msg, $numbers, $sender)
    {
        $url = "https://sms.gateway.sa/api/sendsms.php?username=".$userAccount."&password=".$passAccount."&message=".$msg."&numbers=".$numbers."&sender=".$sender."&unicode=e&Rmduplicated=0&return=json";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $url);
        $result = curl_exec($ch);
        $result = trim($result);
        return $result;
    }


    public function postLoginRegister(Request $request) {
        $this -> validate($request, [
            'username' => 'required|min:4',
            'phone' => 'required|regex:/(05)[0-9]{8}/|unique:users,phone|size:10',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password',
        ]);
        $str = $request -> input('phone');
        $str = ltrim($str, '0');
        $prefix= "966";
        $phone_number= $prefix.$str;
        $key = mt_rand(100000,999999);
        $user = new User();
        $user -> username = $request -> input('username');
        $user -> phone = $request -> input('phone');
        $user -> email = $request -> input('email');
        $user -> password = bcrypt($request -> input('password'));
        $user -> activate = 1;
        $user -> user_type_id = $request->user_type_id==2 ? $request->user_type_id : 3 ;
        $user -> activation_code = $key;
        $msg = urlencode("كود التفعيل في موقع صمم هو : " . $key);
        echo $this->sendSMS('husam','sms123123',$msg,$phone_number,"SMMIM");
        $user -> save();
        return redirect('activate_phone_number/'.$request -> input('phone'))->with('success','تم تسجيل حسابك بنجاح .. قم بتفعيل رقم الجوال .');

    }

    public function PostResend(Request $request)
    {
        $this->validate($request, [
            'phone' => 'required|regex:/(05)[0-9]{8}/|size:10',
        ]);

        $phone = $request->phone;
        $str = $request -> input('phone');
        $str = ltrim($str, '0');
        $prefix= "966";
        $phone_number= $prefix.$str;

        $user = User::where('phone', $phone)->first();
        if (!$user) {
            return redirect()->back()->with('error', 'هذا الرقم غير مسجل لدينا ');
        }
        $activation_code = $user->activation_code;

        $msg = urlencode("كود التفعيل في موقع صمم هو : " . $activation_code);
        $this->sendSMS('husam','sms123123',$msg,$phone_number,"SMMIM");
        return redirect('activate_phone_number/'.$request -> input('phone'))->with('success','تم ارسال كود التفعيل على جوالك .. قم بتفعيل رقم الجوال .');

    }


    public function pay_account()
    {

        return view('pay_account');

    }
    public function pay_account_post(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'package_id' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'value' => 'required',
            'bank_name' => 'required',
            'convert_date' => 'required',
        ]);

        $pay_account = new PayAccount;
        $pay_account ->name = $request->name;
        $pay_account ->package_id = $request->package_id;
        $pay_account ->email = $request->email;
        $pay_account ->user_id= Auth::user()->id;
        $pay_account ->phone = $request->phone;
        $pay_account ->value = $request->value;
        $pay_account ->bank_name = $request->bank_name;
        $pay_account ->convert_date = $request->convert_date;
        $pay_account -> save();

        return redirect()->back()->with('success', 'تم الارسال بنجاح .. سيتم مراجعة التحويل وتفعيل حسابك في أقرب وقت ');
    }



    public function add_ads($id=0)
    {
        $category = Categories::find($id);
        if($category) {
            return view('add_ads', ['categories' => Categories::where("id", '!=', 9)->where("id", '!=', 10)->get(), 'cars' => Cars::all(), 'states' => States::all(),'category_of'=>$category]);
        }else{
            return view('add_ads', ['categories' => Categories::where("id", '!=', 9)->where("id", '!=', 10)->get(), 'cars' => Cars::all(), 'states' => States::all()]);
        }
    }

    public function add_order($service_id=0)
    {
        $this_service = Services::find($service_id);
        return view('add_order',['cars'=>Cars::all(),'services'=>Services::all(),'this_service'=>$this_service]);
    }

    public function add_payment($order_id=0)
    {
        $this_order = Orders::find($order_id);
        return view('add_payment',['order'=>$this_order]);
    }

    public function add_payment_mobile($order_id=0)
    {
        $this_order = Orders::find($order_id);
        return view('add_payment_mobile',['order'=>$this_order]);
    }

    public function add_payment_mobile_membership($order_id=0,$user_id=0)
    {
        $this_package = Packages::find($order_id);
        $this_user = User::find($user_id);
        return view('add_payment_mobile_membership',['package'=>$this_package,'user'=>$this_user]);
    }

    public function show_messages_mobile(){
        return view('show_messages_mobile');

    }

    public function add_project($id=0)
    {

        if(Auth::user()->phone_activate==0){
            return  redirect('/activation_code/resend')->with('error','يجب ان تقوم بتفعيل رقم جوالك لتتمكن من اضافة مشروع .') ;
        }

        if(Auth::user()->user_type_id != 1 && Auth::user()->user_type_id != 2  ){
          return  redirect('/')->with('error','عفوا غير مسموح لك باضافة مشاريع .');
        }

        return view('add_project', ['categories' => Categories::all(), 'styles' => Styles::all(), 'states' => States::all()]);
    }



    public function add_service($id=0)
    {

        if(Auth::user()->service_activate==0){
            return  redirect('/payment')->with('error','يجب ان تقوم بدفع اشتراكك لتتمكن من اضافة خدمتك .') ;
        }

        if(Auth::user()->user_type_id != 1 && Auth::user()->user_type_id != 2  ){
            return  redirect('/')->with('error','عفوا غير مسموح لك باضافة خدمات .');
        }

        return view('add_service', ['categories' => Categories::all(), 'countries' => Countries::all()]);
    }

    public function add_work($id=0)
    {

        if(Auth::user()->phone_activate==0){
            return  redirect('/activation_code/resend')->with('error','يجب ان تقوم بتفعيل رقم جوالك لتتمكن من اضافة عمل .') ;
        }

        if( Auth::user()->user_type_id != 3  ){
            return  redirect('/')->with('error','عفوا غير مسموح لك باضافة أعمال .');
        }

        return view('add_work');
    }

    public function add_article()
    {
        return view('add_article', ['categories' => BlogCategories::all()]);
    }

    public function advanced_search()
    {
        return view('advanced_search', ['categories' => Categories::where('id','!=',9)->get(), 'states' => States::all()]);
    }



    public function edit_ads($id = 0)
    {
        $ads = Ads::where('user_id', Auth::user()->id)->where('id', $id)->first();
        if (Auth::user()->id == 1) {
            $ads = Ads::find($id);
        }
        if ($ads != false) {
            $main_photo = AdsPhotos::where('type', 1)->where('ads_id', $id)->first();
            $photos = AdsPhotos::where('type', 0)->where('ads_id', $id)->get();
            return view('edit_ads', ['ads' => $ads, 'categories' => Categories::where('id','!=',9)->where('id','!=',10)->get(), 'cars' => Cars::where('category_id',$ads->category_id)->get(), 'states' => States::all(), 'main_photo' => $main_photo, 'photos' => $photos]);
        }
        return redirect()->back()->with('error', 'عفوا لا يوجد إعلان لك بهذا العنوان');
    }


    public function edit_article($id = 0)
    {
        $article = Articles::where('user_id', Auth::user()->id)->where('id', $id)->first();

        if (Auth::user()->id == 1) {
            $article = Articles::find($id);
        }elseif (Auth::user()->supervisor==1){
            $article = Articles::find($id);
        }
        if ($article != false || Auth::user()->supervisor == 1 ) {
            $photos = ArticlePhotos::where('article_id', $id)->get();
            return view('edit_article', ['article' => $article, 'categories' => BlogCategories::all(),'photos' => $photos]);
        }
        return redirect()->back()->with('error', 'عفوا لا يوجد موضوع لك بهذا العنوان');
    }

    public function adv_article($id = 0)
    {
        $article = Articles::where('user_id', Auth::user()->id)->where('id', $id)->first();

        if (Auth::user()->id == 1) {
            $article = Articles::find($id);
        }elseif (Auth::user()->supervisor==1){
            $article = Articles::find($id);
        }
        if ($article != false || Auth::user()->supervisor == 1 ) {
            if($article->adv == 1 ){
                $article->adv =0;
                $article->save();
                return redirect()->back()->with('success', 'تم الغاء تثبيت الموضوع بنجاح');

            }else{
                $article->adv =1;
                $article->save();
                return redirect()->back()->with('success', 'تم  تثبيت الموضوع بنجاح');

            }
        }
        return redirect()->back()->with('error', 'تم تثبيت الموضوع بنجاح');
    }


    public function ads_order()
    {
        return view('add_order');
    }

    public function refresh_ads($id = 0)
    {
        $ads = Ads::where('user_id', Auth::user()->id)->where('id', $id)->first();
        if (Auth::user()->id == 1) {
            $ads = Ads::find($id);

            if ($ads == false) {
                return redirect()->back()->with('error', 'عفوا لا يوجد إعلان لك بهذا العنوان');
            }
            $ads->created_at = date('Y-m-d H:i:s');
            $ads->save();
            return redirect()->back()->with('success', 'لقد تم تحديث الإعلان بنجاح');

        } else {

        }
    }


    public function update_ads($id = 0)
    {

        $ads = Ads::where('user_id', Auth::user()->id)->where('id', $id)->first();
        if (Auth::user()->id == 1) {
            $ads = Ads::find($id);
        }
        if ($ads == false) {
            return redirect()->back()->with('error', 'عفوا لا يوجد إعلان لك بهذا العنوان');
        }

        $days_of_ads = $ads->user->adv == 0 ? Settings::find(3)->value : Settings::find(4)->value;
        $created = new Carbon($ads->created_at);
        $now = Carbon::now();
        $difference = $created->diff($now)->days;
        if ($difference >= $days_of_ads) {
            $ads = Ads::find($id);
            $ads->created_at = date('Y-m-d H:i:s');
            $ads->save();
            return redirect()->back()->with('success', 'لقد تم تحديث الإعلان بنجاح');
        } else {
            return redirect()->back()->with('error', 'لا يمكنك تحديث إعلانك الا بعد مرور ' . $days_of_ads . ' يوم');
        }
    }

    public function post_edit_ads($id = 0, Request $request)
    {
        $ads = Ads::where('user_id', Auth::user()->id)->where('id', $id)->first();
        if (Auth::user()->id == 1) {
            $ads = Ads::find($id);
        }
        if ($ads == false) {
            return redirect()->back()->with('error', 'عفوا لا يوجد إعلان لك بهذا العنوان');
        }


        $this->validate($request, [
            'category_id' => 'required',
            'car_id' => @Categories::find($request->input('category_id'))->bikes_models == 1 ? 'required' : '',
            'model_id' => @Categories::find($request->input('category_id'))->bikes_models == 1 || @Categories::find($request->input('category_id'))->models == 1 ? 'required' : '',
            'year' => @Categories::find($request->input('category_id'))->models_years == 1 ? 'required' : '',
            'state_id' => @Categories::find($request->input('category_id'))->cities == 1 ?'required' : '',
            'city_id' => @Categories::find($request->input('category_id'))->cities == 1 ?'required' : '',
            'title' => 'required|min:6',
            'description' => 'required|min:12',
        ]);


        $ads = Ads::find($id);
        $ads->category_id = $request->input('category_id');
        $ads->car_id = $request->input('car_id');
        $ads->model_id = $request->input('model_id');
        $ads->year = $request->input('year');
        $ads->state_id = $request->input('state_id');
        $ads->city_id = $request->input('city_id');
        $ads->phone = $request->input('phone');
        $ads->title = $request->input('title');
        $ads->description = $request->input('description');
        $ads->hide_comment = $request->input('hide_comment');
        $ads->save();

        if (Auth::user()->id == 1) {
            $notification = new Notification();
            $notification->sender_id = Auth::user()->id;
            $notification->reciever_id = $ads->user_id;
            $notification->ads_id = $ads->id;
            $notification->	url = "/ads-details/".$ads->id."/".urlencode($ads->title);
            $notification->save();
        }

        if ($request->input('main_photo')) {
            $old_main = "temp/" . $request->input('main_photo');
            $new_main = "uploads/" . $request->input('main_photo');
            File::move($old_main, $new_main);
            $old_main_photo = AdsPhotos::where('type', 1)->where('ads_id', $ads->id)->first();
            if ($old_main_photo != false) {
                $old_main = AdsPhotos::find($old_main_photo->id);
                unlink("uploads/" . $old_main->photo);
                $old_main->delete();
            }

            $main_photo = new AdsPhotos();
            $main_photo->ads_id = $ads->id;
            $main_photo->type = 1;
            $main_photo->photo = $request->input('main_photo');
            $main_photo->save();
        }
        if ($request->input('related_photos')) {
            foreach ($request->input('related_photos') as $photo) {
                if (!empty($photo)) {
                    $old_main = "temp/" . $photo;
                    $new_main = "uploads/" . $photo;
                    File::move($old_main, $new_main);
                    $other_photos = new AdsPhotos();
                    $other_photos->ads_id = $ads->id;
                    $other_photos->type = 0;
                    $other_photos->photo = $photo;
                    $other_photos->save();

                }
            }


        }

        return redirect()->back()->with('success', 'تم تعديل إعلانك بنجاح .');

    }


    public function post_edit_article($id = 0, Request $request)
    {
        $article = Articles::where('user_id', Auth::user()->id)->where('id', $id)->first();
        if (Auth::user()->id == 1) {
            $article = Articles::find($id);
        }
        if (Auth::user()->supervisor== 1) {
            $article = Articles::find($id);
        }
        if ($article == false) {
            return redirect()->back()->with('error', 'عفوا لا يوجد موضوع لك بهذا العنوان');
        }


        $this->validate($request, [
            'category_id' => 'required',
            'sub_category_id' => 'required',
            'title' => 'required|min:6',
            'description' => 'required|min:12',
            'attached_files'   => $request->attached_files ?'mimes:jpeg,jpg,png,doc,pdf,docx,xls,zip' : '',

        ]);


        $article = Articles::find($id);
        $article->category_id = $request->input('category_id');
        $article->sub_category_id = $request->input('sub_category_id');
        $article->title = $request->input('title');
        $article->description = $request->input('description');
        $article->save();

        $file = $request->file('attached_file');
        if ($request->hasFile('attached_file')) {
            $fileName = 'attached-file-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('attached_file')->move($destinationPath, $fileName);
            $main_photo = new ArticlePhotos();
            $main_photo->article_id = $article->id;
            $main_photo->photo = $fileName;
            $main_photo->save();
        }

        return redirect('/article/'.$article->id)->with('success', 'تم تعديل موضوعك بنجاح .');


        if ($request->input('main_photo')) {
            $old_main = "temp/" . $request->input('main_photo');
            $new_main = "uploads/" . $request->input('main_photo');
            File::move($old_main, $new_main);
            $old_main_photo = AdsPhotos::where('type', 1)->where('ads_id', $ads->id)->first();
            if ($old_main_photo != false) {
                $old_main = AdsPhotos::find($old_main_photo->id);
                unlink("uploads/" . $old_main->photo);
                $old_main->delete();
            }

            $main_photo = new AdsPhotos();
            $main_photo->ads_id = $ads->id;
            $main_photo->type = 1;
            $main_photo->photo = $request->input('main_photo');
            $main_photo->save();
        }
        if ($request->input('related_photos')) {
            foreach ($request->input('related_photos') as $photo) {
                if (!empty($photo)) {
                    $old_main = "temp/" . $photo;
                    $new_main = "uploads/" . $photo;
                    File::move($old_main, $new_main);
                    $other_photos = new AdsPhotos();
                    $other_photos->ads_id = $ads->id;
                    $other_photos->type = 0;
                    $other_photos->photo = $photo;
                    $other_photos->save();

                }
            }


        }

        return redirect()->back()->with('success', 'تم تعديل إعلانك بنجاح .');

    }

    public function add_rating($article_id,$rate){

        if(Rating::where('user_id',Auth::user()->id)->where('article_id',$article_id)->first()){
            $article = Rating::where('user_id',Auth::user()->id)->where('article_id',$article_id)->first();
            $article->rate= $rate;
            $article->save();
            return "تم تعديل تقييمك نجاح";
        }else{
            $article = new Rating();
            $article ->article_id = $article_id;
            $article ->rate = $rate;
            $article ->user_id = Auth::user()->id;
            $article ->save();
            return "تم اضافة تقييمك نجاح";

        }

    }



    public function go_to_cat(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', 'يجب ان تدخل قسم رئيسي على الاقل .');
        }

        $cat_id = $request->category_id;
        $sub_cat_id = $request->sub_category_id;
        if (!empty($sub_cat_id)) {
            return redirect('sub-categories/' . $sub_cat_id);
        } else {
            return redirect('category/' . $cat_id);
        }
    }


    public function contact()
    {
        return view('contact',['page'=>Content::find(2)]);
    }

    public function post_contact(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'subject' => 'required',
            'email' => 'required|email',
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('/#contact-us')
                ->withErrors($validator)
                ->withInput();;
        }

        $order = new Contacts;
        $order->name=$request->name;
        $order->subject=$request->subject;
        $order->email=$request->email;
        $order->message=$request->message;
        $order->save();

        return redirect()->back()->with('success','تم ارسال الرسالة بنجاح .');
    }


    public function post_contact_us(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'message_type_id' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $order = new Contacts;
        $order->name=$request->name;
        $order->message_type_id=$request->message_type_id;
        $order->phone=$request->phone;
        $order->email=$request->email;
        $order->message=$request->message;
        $order->save();

        return redirect()->back()->with('success',trans('messages.message_sent'));
    }


    public function join_us_post(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|max:60|min:3',
            'email' => 'required|email|unique:join_us,email',
            'phone' => 'required|unique:join_us,phone',
            'phonecode' => 'required',
            'city_id' => 'required',
            'country' => 'required',
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = new JoinUs();
        $user->username= $request->username;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->message = $request->message;
        $user->city_id = $request->city_id;
        $user->phonecode = $request->phonecode;
        $user -> save();

        return redirect()->back()->with('success', trans('messages.your_request_sent_successfully'));
    }



    public function complain_post(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'email' => 'required|email',
            'phone' => 'required',
            'message_type_id' => 'required',
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = new Contacts();
        $user->name= $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->message = $request->message;
        $user->message_type_id = $request->message_type_id;
        $user -> save();

        return redirect()->back()->with('success', trans('messages.your_request_sent_successfully'));
    }


    public function post_ads_order(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'ads_id' => 'required',
            'message' => 'required',
        ]);

        $order = new AdsOrders;
        $order->username = $request->username;
        $order->ads_id = $request->ads_id;
        $order->message = $request->message;
        $order->save();

        return redirect()->back()->with('success', 'تم ارسال الطلب بنجاح');
    }

    public function add_ads_post(Request $request)
    {
        $this->validate($request, [
            'category_id' => 'required',
            'car_id' => @Categories::find($request->input('category_id'))->bikes_models == 1 ? 'required' : '',
            'model_id' => @Categories::find($request->input('category_id'))->bikes_models == 1 || @Categories::find($request->input('category_id'))->models == 1 ? 'required' : '',
            'year' => @Categories::find($request->input('category_id'))->models_years == 1 ? 'required' : '',
            'state_id' => @Categories::find($request->input('category_id'))->cities == 1 ?'required' : '',
            'city_id' => @Categories::find($request->input('category_id'))->cities == 1 ?'required' : '',
            'title' => 'required|min:6',
            'description' => 'required|min:12',
        ]);

        if (Auth::user()->adv == 0) {
            if (Ads::where('user_id', Auth::user()->id)->whereDate('created_at', '=', date('Y-m-d'))->count() >= Settings::find(1)->value) {
                return redirect()->back()->with('error', 'عفوا لا يمكنك رفع اكثر من  ' . Settings::find(1)->value . '   إعلان في اليوم الواحد  ');
            }
        } elseif (Auth::user()->adv == 1) {
            if (Ads::where('user_id', Auth::user()->id)->whereDate('created_at', '=', date('Y-m-d'))->count() >= Settings::find(2)->value) {
                return redirect()->back()->with('error', 'عفوا لا يمكنك رفع اكثر من  ' . Settings::find(2)->value . '  إعلان في اليوم الواحد ');
            }

        }


        $ads = new Ads();
        $ads->category_id = $request->input('category_id');
        $ads->car_id = $request->input('car_id');
        $ads->model_id = $request->input('model_id');
        $ads->year = $request->input('year');
        $ads->state_id = $request->input('state_id');
        $ads->city_id = $request->input('city_id');
        $ads->phone = $request->input('phone');
        $ads->title = str_replace("/",'-',$request->input('title'));
        $ads->description = $request->input('description');
        $ads->hide_comment = $request->input('hide_comment');
        $ads->user_id = Auth::user()->id;
        $ads->save();

        if ($request->input('main_photo')) {
            $old_main = "temp/" . $request->input('main_photo');
            $new_main = "uploads/" . $request->input('main_photo');
            File::move($old_main, $new_main);

            $main_photo = new AdsPhotos();
            $main_photo->ads_id = $ads->id;
            $main_photo->type = 1;
            $main_photo->photo = $request->input('main_photo');
            $main_photo->save();
        }
        if ($request->input('related_photos')) {
            foreach ($request->input('related_photos') as $photo) {
                if (!empty($photo)) {
                    $old_main = "temp/" . $photo;
                    $new_main = "uploads/" . $photo;
                    File::move($old_main, $new_main);
                    $other_photos = new AdsPhotos();
                    $other_photos->ads_id = $ads->id;
                    $other_photos->type = 0;
                    $other_photos->photo = $photo;
                    $other_photos->save();

                }
            }
        }




        foreach (FollowCar::where('car_id', $ads->car_id)->where('model_id', $ads->model_id)->where('city_id', $ads->city_id)->where('year', $ads->year)->get() as $value) {
            $notification = new Notification();
            $notification->sender_id = Auth::user()->id;
            $notification->reciever_id = $value->user_id;
            $notification->type = 2;
            $notification->ads_id = $ads->id;
            $notification->url = "/ads-details/" . $ads->id . "/" . urlencode($ads->title) ;
            $notification->save();

        }

        return redirect('/ads-details/'.$ads->id)->with('success', 'تم إضافة إعلانك بنجاح .');


    }



    public function profile_post(Request $request) {
        $user =   User::find(Auth::user()->id);

        $this->validate($request, [
            'first_name' => 'required|max:60|min:3',
            'last_name' => 'required|max:60|min:3',
            'email' => 'required|email|unique:users,email,'.$user->id.',id',
        ]);


        $user = User::find($user->id);
        $user->first_name= $request->first_name;
        $user->last_name= $request->last_name;
        $user->email = $request->email;
        $user -> save();

        return redirect()->back()->with('success', trans('messages.profile_updated'));



    }

    public function bank_transfer_order(Request $request){

        $this->validate($request, [
            'order_id' => 'required' ,
            'image' =>  'required',
            'money_transfered'=>'required',
            'account_name'=>'required',
            'reference_number'=>'required',
        ]);

        $transfer = new BankTransfer();
        $transfer->user_id= Auth::user()->id;
        $transfer ->order_id= $request->order_id;
        $transfer ->type = "order";
        $transfer ->money_transfered=$request->money_transfered ? $request->money_transfered : "" ;
        $transfer ->account_name=$request->account_name ? $request->account_name : "" ;
        $transfer ->reference_number=$request->reference_number ? $request->reference_number : "" ;
        $transfer -> save();

        $file = $request->file('image');
        if ($request->hasFile('image')) {
            $fileName = 'bank-transfer-photo-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('image')->move($destinationPath, $fileName);
            $transfer->image=$fileName;
        }
        $transfer->save();

        return redirect()->back()->with('success', trans('messages.your_bank_transfer_sent_successfully'));


    }


    public function bank_transfer_member(Request $request){

        $this->validate($request, [
            'package_id' => 'required'  ,
            'image' =>  'required',
            'money_transfered'=>'required',
            'account_name'=>'required',
            'reference_number'=>'required',
            'bank_account_id'=>'required',
        ]);

        $transfer = new BankTransfer();
        $transfer->user_id= Auth::user()->id;
        $transfer ->order_id= $request->order_id;
        $transfer ->type = "membership";
        $transfer ->money_transfered=$request->money_transfered ? $request->money_transfered : "" ;
        $transfer ->account_name=$request->account_name ? $request->account_name : "" ;
        $transfer ->bank_account_id=$request->bank_account_id ? $request->bank_account_id: 0 ;
        $transfer ->order_id=$request->order_id ? $request->order_id: 0 ;
        $transfer ->reference_number=$request->reference_number ? $request->reference_number : "" ;
        $transfer -> save();

        $file = $request->file('image');
        if ($request->hasFile('image')) {
            $fileName = 'bank-transfer-photo-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('image')->move($destinationPath, $fileName);
            $transfer->image=$fileName;
        }
        $transfer->save();

        return redirect()->back()->with('success', trans('messages.your_bank_transfer_sent_successfully'));


    }


    public function add_order_post(Request $request)
    {
        $this->validate($request, [
            'service_id' => 'required',
            'brand_id' => 'required',
            'model_id' => 'required',
            'year_id' => 'required',
            'phone' => 'required',
            'username' => 'required',
            'address' => 'required',
            'city_id' => 'required',
        ]);



        $order = new Orders();
        $order->service_id= $request->service_id;
        $order->brand_id= $request->brand_id;
        $order->model_id = $request->model_id;
        $order->year_id = $request->year_id;
        $order->user_id = Auth::user()->id;
        $order->car_plate = $request->car_plate ? $request->car_plate : "" ;
        $order->vin = $request->vin ? $request->vin : 0 ;
        $order->phonecode = $request->phonecode ? $request->phonecode  : "" ;
        $order->phone = $request->phone;
        $order->username = $request->username;
        $order->address = $request->address;
        $order->auto_show = $request->auto_show;
        $order->status = 0;
        $order->state_id = $request->city_id;

        if($request->service_id!=5) {
            $prices = Prices::where('service_id', $request->service_id)->where('state_id', $request->city_id)->first();
            if ($prices) {
                $order->price = $prices->price;
                $order->currency_id = $prices->currency_id;
            }
        }

        $order -> save();

        $notification = new Notification();
        $notification->sender_id = Auth::user()->id;
        $notification->reciever_id = 1;
        $notification->order_id = $order->id;
        $notification->url = "/admin/orders/".$order->id;
        $notification->type=0;
        $notification->save();


        return redirect('/'.App::getLocale())->with('success', trans('messages.order_added_successfully'));


    }

    public function post_payment(Request $request){
        if($_POST['response_code'] == 100 && $order = Orders::find($_POST['order_id'])){
            $transfer = new BankTransfer();
            $transfer->user_id= @$order->getUser->id;
            $transfer ->order_id= $_POST['order_id'];
            $transfer ->type = "order";
            $transfer ->money_transfered=  $_POST['transaction_amount']." ".$_POST['transaction_currency'] ;
            $transfer ->account_name="" ;
            $transfer ->reference_number="" ;
            $transfer ->online_payment=1 ;
            $transfer ->bank_account_id=0 ;
            $transfer -> save();
            $order->status=2;
            $order->save();
            return redirect('/')->with('success','تمت عملية الدفع بنجاح');

        }else{
            return redirect('/')->with('error','حدث خطأ أثناء عملية الدفع .');
        }


    }

    public function post_payment_member(Request $request){
        $arr = explode('----',$_POST['order_id']);
        $order_id= $arr[0];
        $user_id= $arr[1];
        if($_POST['response_code'] == 100 && $package = Packages::find($order_id)){
            $transfer = new BankTransfer();
            $transfer->user_id= $user_id;
            $transfer ->package_id= $_POST['order_id'];
            $transfer ->order_id= 0;
            $transfer ->type = "membership";
            $transfer ->money_transfered=  $_POST['transaction_amount']." ".$_POST['transaction_currency'] ;
            $transfer ->account_name="" ;
            $transfer ->reference_number="" ;
            $transfer ->online_payment=1 ;
            $transfer ->bank_account_id=0 ;
            $transfer -> save();
            $user = User::find($user_id);
            if($user){
                $user -> user_type_id = 2 ;
                $user -> days = $package->days;
                $user -> date_of_package = date('Y-m-d');
                $user->save();
            }
            return redirect('/')->with('success','تمت عملية الدفع بنجاح');

        }else{
            return redirect('/')->with('error','حدث خطأ أثناء عملية الدفع .');
        }


    }


    public function post_payment_mobile(Request $request){
        if($_POST['response_code'] == 100 && $order = Orders::find($_POST['order_id'])){
            $transfer = new BankTransfer();
            $transfer->user_id= @$order->getUser->id;
            $transfer ->order_id= $_POST['order_id'];
            $transfer ->type = "order";
            $transfer ->money_transfered=  $_POST['transaction_amount']." ".$_POST['transaction_currency'] ;
            $transfer ->account_name="" ;
            $transfer ->reference_number="" ;
            $transfer ->online_payment=1 ;
            $transfer ->bank_account_id=0 ;
            $transfer -> save();

            $order->status=2;

            $order->save();
            return redirect('/'.App::getLocale().'/show-messages-mobile')->with('success','تمت عملية الدفع بنجاح');

        }else{
            return redirect('/'.App::getLocale().'/show-messages-mobile')->with('error','حدث خطأ أثناء عملية الدفع .');
        }


    }


    public function post_payment_mobile_membership(Request $request){
        $arr = explode('----',$_POST['order_id']);
        $order_id= $arr[0];
        $user_id= $arr[1];
        if($_POST['response_code'] == 100 && $package = Packages::find($order_id)){
            $transfer = new BankTransfer();
            $transfer->user_id= $user_id;
            $transfer ->order_id= 0;
            $transfer ->package_id= $package->id;
            $transfer ->type = "membership";
            $transfer ->money_transfered=  $_POST['transaction_amount']." ".$_POST['transaction_currency'] ;
            $transfer ->account_name="" ;
            $transfer ->reference_number="" ;
            $transfer ->bank_account_id=0 ;
            $transfer ->online_payment=1 ;
            $transfer -> save();

            $user = User::find($user_id);
            if($user){
                $user -> user_type_id = 2 ;
                $user -> days = $package->days;
                $user -> date_of_package = date('Y-m-d');
                $user->save();
            }

            return redirect('/'.App::getLocale().'/show-messages-mobile')->with('success','تمت عملية الدفع بنجاح');

        }else{
            return redirect('/'.App::getLocale().'/show-messages-mobile')->with('error','حدث خطأ أثناء عملية الدفع .');
        }


    }


    public function add_project_post(Request $request)
    {
        $this->validate($request, [
            'category_id' => 'required',
            'sub_category_id' => 'required',
            'state_id' => 'required',
            'city_id' => 'required',
            'area' => 'required',
            'title' => 'required|min:6',
            'description' => 'required|min:12',
        ]);




        if (Auth::user()->adv == 0) {
            if (Ads::where('user_id', Auth::user()->id)->whereDate('created_at', '=', date('Y-m-d'))->count() >= Settings::find(1)->value) {
                return redirect()->back()->with('error', 'عفوا لا يمكنك رفع اكثر من  ' . Settings::find(1)->value . '   إعلان في اليوم الواحد  ');
            }
        } elseif (Auth::user()->adv == 1) {
            if (Ads::where('user_id', Auth::user()->id)->whereDate('created_at', '=', date('Y-m-d'))->count() >= Settings::find(2)->value) {
                return redirect()->back()->with('error', 'عفوا لا يمكنك رفع اكثر من  ' . Settings::find(2)->value . '  إعلان في اليوم الواحد ');
            }

        }


        $project = new Projects();
        $project->category_id = $request->category_id;
        $project->sub_category_id = $request->sub_category_id;
        $project->state_id = $request->state_id;
        $project->city_id = $request->city_id;
        $project->style_id = $request->style_id;
        $project->area = $request->area;
        $project->address = "none";
        $project->longitude = $request->longitude;
        $project->latitude = $request->latitude;
        $project->title = str_replace("/",'-',$request->input('title'));
        $project->description = $request->description;
        $project->user_id = Auth::user()->id;

        $file = $request->file('attached_file');
        if ($request->hasFile('attached_file')) {
            $fileName = 'attached-file-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('attached_file')->move($destinationPath, $fileName);
            $project->attached_file=$fileName;
        }

        $project->save();


        foreach (Questions::all() as $question){
            $input_name = "input".$question->id;
            if($request->$input_name){
                $answer = new Answers();
                $answer ->category_id = $request->category_id;
                $answer ->answer = $request->$input_name;
                $answer ->project_id = $project->id;
                $answer ->question_id = $question->id;
                $answer ->save();
            }
        }

        if ($request->input('main_photo')) {
            $old_main = "temp/" . $request->input('main_photo');
            $new_main = "uploads/" . $request->input('main_photo');
            File::move($old_main, $new_main);

            $main_photo = new ProjectPhotos();
            $main_photo->project_id = $project->id;
            $main_photo->type = 1;
            $main_photo->photo = $request->input('main_photo');
            $main_photo->save();
        }
        if ($request->input('related_photos')) {
            foreach ($request->input('related_photos') as $photo) {
                if (!empty($photo)) {
                    $old_main = "temp/" . $photo;
                    $new_main = "uploads/" . $photo;
                    File::move($old_main, $new_main);
                    $other_photos = new ProjectPhotos();
                    $other_photos->project_id = $project->id;
                    $other_photos->type = 0;
                    $other_photos->photo = $photo;
                    $other_photos->save();

                }
            }
        }

        return redirect('/')->with('success', 'تم إضافة مشروعك بنجاح .. سيتم تفعيل المشروع بعد موافقة الادارة .');

    }


    public function add_service_post(Request $request)
    {
        $this->validate($request, [
            'category_id' => 'required',
            'sub_category_id' => 'required',
            'country_id' => 'required',
            'state_id' => 'required',
            'address' => 'required',
            'price' => 'required',
            'phone' => 'required',
            'title' => 'required|min:6',
            'description' => 'required|min:12',
        ]);

            if (Services::where('user_id', Auth::user()->id)->whereDate('created_at', '=', date('Y-m-d'))->count() >= Settings::find(1)->value) {
                return redirect()->back()->with('error', 'عفوا لا يمكنك رفع اكثر من  ' . Settings::find(1)->value . '   خدمة في اليوم الواحد  ');
            }

            if($request->price > @Subcategories::find($request->sub_category_id)->price){
                return redirect()->back()->with('error', 'لا يمكن ان يز سعر الخدمة عن '.@Subcategories::find($request->sub_category_id)->price.' فهو محدد من قبل الادارة .');
            }



        $project = new Services();
        $project->category_id = $request->category_id;
        $project->sub_category_id = $request->sub_category_id;
        $project->state_id = $request->state_id;
        $project->country_id = $request->country_id;
        $project->address = $request->address;
        $project->longitude = $request->longitude;
        $project->latitude = $request->latitude;
        $project->title = str_replace("/",'-',$request->title);
        $project->description = $request->description;
        $project->price = $request->price;
        $project->phone = $request->phone;
        $project->service_type = 1;
        $project->user_id = Auth::user()->id;
        $project->save();
        if ($request->related_photos) {
            foreach ($request->related_photos as $photo) {
                if (!empty($photo)) {
                    $old_main = "temp/" . $photo;
                    $new_main = "uploads/" . $photo;
                    File::move($old_main, $new_main);
                    $other_photos = new ServicesPhotos();
                    $other_photos->service_id = $project->id;
                    $other_photos->type = 0;
                    $other_photos->photo = $photo;
                    $other_photos->save();
                }
            }
        }
        return redirect('/')->with('success', 'تم إضافة خدمتك بنجاح .. سيتم تفعيل الخدمة بعد موافقة الادارة .');
    }

    public function pay_to_member(Request $request){
        $package = $request ->offers ? $request ->offers : 1 ;
        $payment = $request ->payment ?  $request ->payment : 1  ;
        $package = Packages::find($package);
        if($payment == 1){
            return view('transfer_membership',['bank_accounts'=>BankAccounts::all(),'package'=>$package]);
        }else{
            return view('paytabs_membership',['bank_accounts'=>BankAccounts::all(),'package'=>$package,'user'=>Auth::user()]);
        }

    }


    public function pay_to_order(Request $request){
        $order = $request ->order_id ? $request ->order_id : 0 ;
        $payment = $request ->payment ?  $request ->payment : 1  ;
        $this_order = Orders::find($order);
        if($payment == 1){
            return view('transfer_order',['bank_accounts'=>BankAccounts::all(),'order'=>$this_order]);
        }else{
            return view('paytabs_order',['bank_accounts'=>BankAccounts::all(),'order'=>$this_order,'user'=>Auth::user()]);
        }

    }

    public function add_work_post(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|min:6',
            'description' => 'required|min:12',
        ]);

        if (Auth::user()->adv == 0) {
            if (Ads::where('user_id', Auth::user()->id)->whereDate('created_at', '=', date('Y-m-d'))->count() >= Settings::find(1)->value) {
                return redirect()->back()->with('error', 'عفوا لا يمكنك رفع اكثر من  ' . Settings::find(1)->value . '   إعلان في اليوم الواحد  ');
            }
        } elseif (Auth::user()->adv == 1) {
            if (Ads::where('user_id', Auth::user()->id)->whereDate('created_at', '=', date('Y-m-d'))->count() >= Settings::find(2)->value) {
                return redirect()->back()->with('error', 'عفوا لا يمكنك رفع اكثر من  ' . Settings::find(2)->value . '  إعلان في اليوم الواحد ');
            }

        }


        $project = new Works();
        $project->title = str_replace("/",'-',$request->input('title'));
        $project->description = $request->description;
        $project->user_id = Auth::user()->id;
        $project->save();


        if ($request->input('related_photos')) {
            foreach ($request->input('related_photos') as $photo) {
                if (!empty($photo)) {
                    $old_main = "temp/" . $photo;
                    $new_main = "uploads/" . $photo;
                    File::move($old_main, $new_main);
                    $other_photos = new WorkPhotos();
                    $other_photos->work_id = $project->id;
                    $other_photos->type = 0;
                    $other_photos->photo = $photo;
                    $other_photos->save();

                }
            }
        }

        return redirect('/')->with('success', 'تم إضافة العمل في المعرض بنجاح ..');

    }


    public function add_article_post(Request $request)
    {
        $this->validate($request, [
            'category_id' => 'required',
            'sub_category_id' => 'required',
            'title' => 'required|min:6',
            'description' => 'required|min:12',
            'attached_files'   => $request->attached_files ?'mimes:jpeg,jpg,png,doc,pdf,docx,xls,zip' : '',
        ]);

//        if (Auth::user()->adv == 0) {
//            if (Ads::where('user_id', Auth::user()->id)->whereDate('created_at', '=', date('Y-m-d'))->count() >= Settings::find(1)->value) {
//                return redirect()->back()->with('error', 'عفوا لا يمكنك رفع اكثر من  ' . Settings::find(1)->value . '   إعلان في اليوم الواحد  ');
//            }
//        } elseif (Auth::user()->adv == 1) {
//            if (Ads::where('user_id', Auth::user()->id)->whereDate('created_at', '=', date('Y-m-d'))->count() >= Settings::find(2)->value) {
//                return redirect()->back()->with('error', 'عفوا لا يمكنك رفع اكثر من  ' . Settings::find(2)->value . '  إعلان في اليوم الواحد ');
//            }
//
//        }


        $article = new Articles();
        $article->category_id = $request->input('category_id');
        $article->sub_category_id = $request->input('sub_category_id');
        $article->title = $request->input('title');
        $article->description = $request->input('description');
        $article->user_id = Auth::user()->id;
        $article->save();

        $file = $request->file('attached_file');
        if ($request->hasFile('attached_file')) {
            $fileName = 'attached-file-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('attached_file')->move($destinationPath, $fileName);
            $main_photo = new ArticlePhotos();
            $main_photo->article_id = $article->id;
            $main_photo->photo = $fileName;
            $main_photo->save();
        }

        return redirect('/article/'.$article->id)->with('success', 'تم إضافة موضوعك بنجاح .');


    }




    public function ads_details($ads = 0)
    {
        // Get the current ads that will be the origin of our operations

        $ads = Ads::find($ads);

        if(Auth::user() && Auth::user()->id== $ads->user_id){
            $notify = Notification::where('ads_id',$ads->id)->where('reciever_id',Auth::user()->id)->where('type',0)->where('status',0)->first();
            $notify1 = Notification::where('ads_id',$ads->id)->where('reciever_id',Auth::user()->id)->where('type',1)->where('status',0)->first();
           if($notify ) {
               $notify->delete();
           }elseif($notify1 ) {
                $notify1->delete();
            }

        }

        if(Auth::user()){
            $notify2 = Notification::where('ads_id',$ads->id)->where('reciever_id',Auth::user()->id)->where('type',2)->where('status',0)->first();
            if($notify2 ) {
                $notify2->delete();
            }
        }

        if (!$ads) {
            return redirect()->back()->with('error', 'عفوا ، لا يوجد إعلان بهذا العنوان فقد تم حذف هذا الإعلان');
        }

        ++$ads->views;
        $ads->save();
        // Get ID of a Ads whose autoincremented ID is less than the current ads, but because some entries might have been deleted we need to get the max available ID of all entries whose ID is less than current user's
        $previews_ads = Ads::where('id', '<', $ads->id)->max('id');
        // Same for the next ads's id as previous user's but in the other direction
        $next_ads = Ads::where('id', '>', $ads->id)->min('id');
        return view('ads_details', ['ads' => $ads, 'prev' => $previews_ads, 'next' => $next_ads]);

    }


    public function report($id = 0)
    {
        // Get the current ads that will be the origin of our operations

        $report = Reports::find($id);


        if (!$report) {
            return redirect()->back()->with('error', 'عفوا ، لا يوجد تقرير بهذا العنوان فقد تم حذف هذا التقرير');
        }

        return view('report', ['report' => $report]);

    }


    public function order($order_id = 0)
    {
        // Get the current ads that will be the origin of our operations

        $order = Orders::find($order_id);


        if (!$order) {
            return redirect()->back()->with('error', 'عفوا ، لا يوجد طلب بهذا العنوان فقد تم حذف هذا طلب');
        }

        return view('order', ['order' => $order]);

    }

    public function single_report($id = 0)
    {
        // Get the current ads that will be the origin of our operations

        $report = Reports::find($id);
        if (!$report) {
            return redirect()->back()->with('error', 'عفوا ، لا يوجد تقرير بهذا العنوان فقد تم حذف هذا التقرير');
        }

        return view('single_report', ['report' => $report]);

    }


    public function project_details($project = 0)
    {
        // Get the current ads that will be the origin of our operations
        $project = Projects::find($project);
        if (!$project) {
            return redirect()->back()->with('error', 'عفوا ، لا يوجد مشروع بهذا العنوان فقد تم حذف هذا المشروع');
        }
        ++$project->views;
        $project->save();
        if(@Auth::user()->user_type_id==1 || @Auth::user()->id == $project->user_id ){
            $offers = ProjectOffers::where('project_id',$project->id)->orderBy('id','DESC')->paginate(10);
        }else{
            $offers = ProjectOffers::where('project_id',$project->id)->where('user_id',@Auth::user()->id)->orderBy('id','DESC')->paginate(10);
        }

        $main_photo = ProjectPhotos::where('project_id',$project->id)->where('type',1)->first();
        $other_photos = ProjectPhotos::where('project_id',$project->id)->where('type',0)->get();

        return view('project_details', ['project' => $project , 'main_photo'=>$main_photo, 'other_photos'=>$other_photos , 'offers'=>$offers]);

    }



    public function service($id = 0)
    {
        // Get the current ads that will be the origin of our operations
        $service = Services::find($id);
        if (!$service) {
            return redirect()->back()->with('error', 'عفوا ، لا يوجد خدمة بهذا العنوان فقد تم حذف هذه الخدمة');
        }
        ++$service->views;
        $service->save();
        if(@Auth::user()->user_type_id==1 || @Auth::user()->id == $service->user_id ){
//            $offers = ProjectOffers::where('project_id',$project->id)->orderBy('id','DESC')->paginate(10);
        }else{
//            $offers = ProjectOffers::where('project_id',$project->id)->where('user_id',@Auth::user()->id)->orderBy('id','DESC')->paginate(10);
        }

        $other_photos = ServicesPhotos::where('service_id',$service->id)->where('type',0)->get();

        return view('service', ['service' => $service , 'other_photos'=>$other_photos ]);

    }


    public function work_details($work = 0)
    {
        // Get the current ads that will be the origin of our operations

        $work = Works::find($work);


        if (!$work) {
            return redirect()->back()->with('error', 'عفوا ، لا يوجد عمل بهذا العنوان فقد تم حذف هذا العمل');
        }

        ++$work->views;
        $work->save();

        $other_photos = WorkPhotos::where('work_id',$work->id)->get();

        return view('work_details', ['work' => $work , 'other_photos'=>$other_photos ]);

    }


    public function article_details($id = 0)
    {
        // Get the current ads that will be the origin of our operations

        $articles = Articles::find($id);
        if (!$articles) {
            return redirect()->back()->with('error', 'عفوا ، لا يوجد موضوع بهذا العنوان فقد تم حذف هذا الموضوع');
        }
        ++$articles->views;
        $articles->save();
        $rating_count = Rating::where('article_id',$id)->count();
        $rating_sum = Rating::where('article_id',$id)->sum('rate') ;
        if($rating_count!= 0 ) {
            $result = ceil($rating_sum / $rating_count);
        }else{
            $result =0;
        }


        if(Auth::user() && Auth::user()->id == $articles->user_id){
            $notify = Notification::where('ads_id',$articles->id)->where('reciever_id',Auth::user()->id)->where('type',3)->where('status',0)->first();
            if($notify) {
                $notify->delete();
            }

        }


        if (!$articles) {
            return redirect()->back()->with('error', 'عفوا ، لا يوجد موضوع بهذا العنوان');
        }


        // Get ID of a Ads whose autoincremented ID is less than the current ads, but because some entries might have been deleted we need to get the max available ID of all entries whose ID is less than current user's
        $previews_ads = Articles::where('id', '<', $articles->id)->max('id');
        // Same for the next ads's id as previous user's but in the other direction
        $next_ads = Articles::where('id', '>', $articles->id)->min('id');
        return view('article_details', [ 'result' => $result,'article' => $articles, 'prev' => $previews_ads, 'next' => $next_ads]);

    }


    public function getCities($id = 0)
    {
        echo "<option value=''>إختر المدينة</option>";
        foreach (Cities::where('state_id', $id)->get() as $city) {
            echo "<option value='" . $city->id . "'>" . $city->name . "</option>";
        }
    }

    public function getRegions($id = 0)
    {
        echo "<option value=''>اختر المنطقة</option>";
        foreach (Regions::where('country_id', $id)->get() as $state) {
            echo "<option value='" . $state->id . "'>" .  $state->name . "</option>";
        }
    }
    public function getStates($id = 0)
    {
        echo "<option value=''>اختر المدينة</option>";
        foreach (States::where('country_id', $id)->get() as $state) {
            echo "<option value='" . $state->id . "'>" .  $state->name . "</option>";
        }
    }
    public function getRegionStates($id = 0)
    {
        echo "<option value=''>اختر المدينة</option>";
        foreach (States::where('region_id', $id)->get() as $state) {
            echo "<option value='" . $state->id . "'>" .  $state->name . "</option>";
        }
    }
    public function getStatesByRegions($regions = 0)
    {
         $regions=explode(",",$regions);
        return States::whereIn('region_id', $regions)->orderBy('region_id')->get();
        echo "<option value=''>اختر المدينة</option>";
        foreach (States::whereIn('region_id', [$regions])->get() as $state) {
            echo "<option value='" . $state->id . "'>" .  $state->name . "</option>";
        }
    }

    public function getSubcategories($id = 0)
    {
        echo "<option value=''> إختر القسم الفرعي</option>";
        foreach (Subcategories::where('category_id', $id)->get() as $subcategory) {
            echo "<option value='" . $subcategory->id . "'>" . $subcategory->name . "</option>";
        }
    }

    public function getStyle($id = 0)
    {
        $style = Styles::find($id);
        if($style){
            echo "<img src='/uploads/".$style->photo."' width='200' height='200'  />";
        }
    }

    public function getAdditionalInputs($id=0)
    {
        $category = Categories::find($id);
        if($category) {
            $questions = Questions::where('category_id',$category->id)->get();
            return view('getAddtionalInputs', ['questions' =>$questions]);
        }
    }

    public function getBlogSub($id = 0)
    {
        echo "<option value=''>  إختر القسم الفرعي للمنتدى</option>";
        foreach (BlogSubcategories::where('category_id', $id)->get() as $subcategory) {
            echo "<option value='" . $subcategory->id . "'>" . $subcategory->name . "</option>";
        }
    }

    public function getSubBikes($id = 0)
    {
        $category = Categories::find($id);
        $brand_name = $category->car_name ? $category->car_name : 'الماركة' ;
        if($category) {

            echo "<option value=''> إختر ".$brand_name." </option>";
            foreach (Cars::where('category_id', $id)->orderBy('ordrat',"ASC")->get() as $subcategory) {
                echo "<option value='" . $subcategory->id . "'>" . $subcategory->name . "</option>";
            }
        }
    }

    public function getSubBikesModels($id = 0)
    {
        $category = Categories::find($id);

        if($category) {

            if($category->bikes_models== 1){
                echo 1;
            }
        }
    }

    public function getSubModels($id = 0)
    {
        $category = Categories::find($id);

        if($category) {

            if($category->models== 1){
                echo 1;
            }
        }
    }
    public function getSubCities($id = 0)
    {
        $category = Categories::find($id);

        if($category) {

            if($category->cities== 1){
                echo 1;
            }
        }
    }

    public function getSubModelsYears($id = 0)
    {
        $category = Categories::find($id);

        if($category) {

            if($category->models_years== 1){
                echo 1;
            }
        }
    }

    public function getModels($id = 0)
    {

        echo "<option value=''> ".trans('messages.model')." </option>";
        foreach (CarsModels::where('cars_category_id', $id)->get() as $cars_category) {

            $value  = App::getLocale()=="ar"? $cars_category->name :$cars_category->name_en;
            echo "<option value='" . $cars_category->id . "'>" . $value . "</option>";
        }

    }


    public function getPrice($state_id = 0,$service_id = 0)
    {

        $price = Prices::where('state_id',$state_id)->where('service_id',$service_id)->first();
        if($price){
            $currency = App::getLocale()=="ar" ? $price->getCurrency->name : $price->getCurrency->name_en ;
            echo $price->price . " " .$currency;
        }
    }


    public function getCarsModels($id = 0)
    {
        if(Categories::find($id)->bikes_models == 1) {
            echo "<option value=''> إختر الماركة </option>";
            foreach (Cars::where('category_id', $id)->orderBy('orders','ASC')->get() as $cars_category) {
                echo "<option value='" . $cars_category->id . "'>" . $cars_category->name . "</option>";
            }
        }else{
            echo "<option value=''> قسم بدون ماركة </option>";
        }
    }

    public function search(Request $request){
        $validator = Validator::make($request->all(), [
            'report_id' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error',trans('messages.number_required'));
        }

                $reports = Reports::where('id',  $request->report_id )
                    ->where('order_id', "!=", 0)
                    ->first();


                if($reports){
                    return redirect(App::getLocale().'/report/'.$reports->id);
                }else{
                    return redirect()->back()->with('error',trans('messages.no_report_with_this_number'));
                }




    }

    public function add_comment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ads_id' => 'required',
            'comment' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect('/ads-details' . '/' . $request->input('ads_id') . '#my_comment')
                ->withErrors($validator)
                ->withInput();
        }

        $comment = new Comments;
        $comment->user_id = Auth::user()->id;
        $comment->ads_id = $request->input('ads_id');
        $comment->comment = $request->input('comment');
        $comment->save();

        foreach (CommentsFollows::where('ads_id', $request->input('ads_id'))->get() as $value) {
            $comment_notify = new CommentsNotify;
            $comment_notify->ads_id = $request->input('ads_id');
            $comment_notify->comment_id = $comment->id;
            $comment_notify->user_id = $value->user_id;
            $comment_notify->save();
        }
        $ads = Ads::find($request->input('ads_id'));
        if($ads && $ads->user_id != Auth::user()->id) {
            $notification = new Notification();
            $notification->sender_id = Auth::user()->id;
            $notification->reciever_id = $ads->user_id;
            $notification->type = 1;
            $notification->ads_id = $ads->id;
            $notification->url = "/ads-details/" . $ads->id . "/" . urlencode($ads->title) ."#comments";
            $notification->save();
        }

        return redirect('/ads-details' . '/' . $request->input('ads_id') . '#comments')->with('success', 'تم إضافة تعليقك بنجاح');

    }


    public function add_offer(Request $request)
    {
        if(Auth::user()->phone_activate==0){
          return  redirect('/activation_code/resend')->with('error','يجب ان تقوم بتفعيل رقم جوالك لتتمكن من اضافة عرض .') ;
        }

        $validator = Validator::make($request->all(), [
            'project_id' => 'required',
            'price' => 'required',
            'days' => 'required',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('/project-details' . '/' . $request->input('project_id') . '#my_offers')
                ->withErrors($validator)
                ->withInput();
        }

        if(ProjectOffers::where('user_id',Auth::user()->id)->where('project_id',$request->input('project_id'))->first()){
            return redirect('/project-details' . '/' . $request->input('project_id') . '#my_offers')->with('error', 'لا يمكنك اضافة اكثر من عرض على المشروع الواحد');
        }

        $comment = new ProjectOffers();
        $comment->user_id = Auth::user()->id;
        $comment->project_id = $request->input('project_id');
        $comment->price = $request->input('price');
        $comment->days = $request->input('days');
        $comment->description = $request->input('description');
        $comment->save();


        $project = Projects::find($request->input('project_id'));
        if($project && $project->user_id != Auth::user()->id) {
            $notification = new Notification();
            $notification->sender_id = Auth::user()->id;
            $notification->reciever_id = $project->user_id;
            $notification->type = 1;
            $notification->project_id = $project->id;
            $notification->url = "/project-details/" . $project->id ."#my_offers";
            $notification->save();
        }

        return redirect('/project-details' . '/' . $request->input('project_id') . '#my_offers')->with('success', 'تم إضافة عرضك بنجاح');

    }

    public function article_comment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'article_id' => 'required',
            'comment' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect('/article' . '/' . $request->input('article_id') . '#my_comment')
                ->withErrors($validator)
                ->withInput();
        }


        $comment = new ArticleComments();
        $comment->user_id = Auth::user()->id;
        $comment->article_id = $request->input('article_id');
        $comment->replywith = $request->replywith;

        $comment_  = preg_replace('/\[start\](.*?)\[end\]/s', "", $request->input('comment'));


        $comment->comment = $comment_;
        $comment->save();

        @Articles::find($comment->article_id)->update();

        if($comment->article_id && @Articles::find($comment->article_id)->user_id != Auth::user()->id) {
            $notification = new Notification();
            $notification->sender_id = Auth::user()->id;
            $notification->reciever_id = Articles::find($comment->article_id)->user_id;
            $notification->type = 3;
            $notification->ads_id = $comment->article_id;
            $notification->url = "/article/" . $comment->article_id . "/" . urlencode(@Articles::find($comment->article_id)->title) ."#comments";
            $notification->save();
        }

        return redirect('/article' . '/' . $request->input('article_id') . '#comments')->with('success', 'تم إضافة مشاركتك بنجاح');

    }


    public function follow_car(Request $request)
    {
        $this->validate($request, [
            'car_id' => 'required',
            'model_id' => 'required',
            'year' => 'required',
            'city_id' => 'required'
        ]);

        $follow = new FollowCar;
        $follow->city_id = $request->city_id;
        $follow->car_id = $request->car_id;
        $follow->year = $request->year;
        $follow->user_id = Auth::user()->id;
        $follow->model_id = $request->model_id;
        $follow->save();


        return redirect()->back()->with('success', 'تمت متابعة الموديل بنجاح');

    }

    public function send_message(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'message' => 'required' ,
        ]);

        if ($validator->fails()) {
            return 0;
        }


        $message = new Messages();
        $message ->sender_id= Auth::user()->id;
        $message ->reciever_id= 0;
        $message ->message = $request->message ? $request->message : "" ;
        $message -> save();



        $file = $request->file('image');
        if ($request->hasFile('image')) {
            $fileName = 'message-photo-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('image')->move($destinationPath, $fileName);
            $message->image=$fileName;
        }
        $message->save();


        return response()->json(
            [
                'message' => $message->message ,
                'id' => $message->id ,
            ]);
    }



    public function add_message(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ads_id' => 'required',
            'message' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect('/ads-details' . '/' . $request->input('ads_id'))
                ->withErrors($validator)
                ->withInput();
        }

        $ads = Ads::find($request->input('ads_id'));

        $comment = new Messages;
        $comment->sender_id = Auth::user()->id;
        $comment->reciever_id = !$request->input('to') ? $ads->user_id : $request->input('to');
        $comment->ads_id = $request->input('ads_id');
        $comment->message = $request->input('message');
        $comment->save();

        return redirect('/ads-details' . '/' . $request->input('ads_id'))->with('success', 'تم ارسال رسالتك بنجاح');

    }

    public function report_ads(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ads_id' => 'required',
            'message' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect('/ads-details' . '/' . $request->input('ads_id'))
                ->withErrors($validator)
                ->withInput();
        }

        $ads = Ads::find($request->input('ads_id'));

        $report = new Reports;
        $report->user_id = Auth::user()->id;
        $report->type = $request->input('type');
        $report->ads_id = $request->input('ads_id');
        $report->comment_id = $request->input('comment_id');
        $report->message = $request->input('message');
        $report->save();

        return redirect('/ads-details' . '/' . $request->input('ads_id'))->with('success', 'تم ارسال رسالة التبيلغ بنجاح .');

    }


    public function report_article(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'article_id' => 'required',
            'message' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect('/article' . '/' . $request->input('article_id'))
                ->withErrors($validator)
                ->withInput();
        }

        $ads = Ads::find($request->input('article_id'));

        $report = new ArticleReports();
        $report->user_id = Auth::user()->id;
        $report->type = $request->input('type');
        $report->article_id = $request->input('article_id');
        $report->comment_id = $request->input('comment_id');
        $report->message = $request->input('message');
        $report->save();

        return redirect('/article' . '/' . $request->input('article_id'))->with('success', 'تم ارسال رسالة التبيلغ بنجاح .');

    }


    public function searchcars(Request $request)
    {
        $category_id = $request->input('category_id');
        $city = $request->input('city_id');
        $state = $request->input('state_id');
        $car_id = $request->input('car_id');
        $model_id = $request->input('model_id');
        $year = $request->input('year');
        $title = $request->input('title');

        $all_ads = Ads::where('id', '!=', 0)
            ->Where(function ($query) use ($request) {
                if ($request->input('car_id')) {
                    $query->where('car_id', $request->input('car_id'));
                }
                if ($request->input('category_id')) {
                    $query->where('category_id', $request->input('category_id'));
                }
                if ($request->input('model_id')) {
                    $query->where('model_id', $request->input('model_id'));
                }
                if ($request->input('year')) {
                    $query->where('year', $request->input('year'));
                }
                if ($request->input('city_id')) {
                    $query->where('city_id', $request->input('city_id'));
                }
                if ($request->input('state_id')) {
                    $query->where('state_id', $request->input('state_id'));
                }
                if ($request->input('title')) {
                    $query->where('title', 'LIKE' , "%" .$request->input('title')."%" );
                }
                //  ->where('title', '<>', 'Admin');
            })
//            ->where(function ($query) use ($request) {
//                if ($request->city) {
//                    foreach ($request->city as $city) {
//                        $query->orWhere('city_id', $city);
//                    }
//                }
//            })
            ->orderBy('created_at', 'DESC')
            ->get();

        // $all_ads = Ads::where('state_id','LIKE','%'.$state_id.'%')
        // ->where('category_id','LIKE','%'.$category_id.'%')
        // ->where('sub_category_id','LIKE','%'.$sub_category_id.'%')
        // ->where('city_id','LIKE','%'.$city_id.'%')
        // ->where('car_id','LIKE','%'.$car_id.'%')
        // ->where('model_id','LIKE','%'.$model_id.'%')
        // ->where('year','LIKE','%'.$year.'%')
        // ->paginate(100);
        return view('ads_results', ['all_ads' => $all_ads]);
    }

    public function search_ads(Request $request)
    {
        $ads = Ads::find($request->input('ads_id'));
        if (!$ads) {
            return redirect()->back()->with('error', 'عفوا ، لا يوجد إعلان بهذا الرقم');
        }
        return redirect("/ads-details" . "/" . $ads->id);
    }

    public function search_companies(Request $request)
    {
        $city = $request->input('city_id');
        $state = $request->input('state_id');

        $all_ads = Ads::where('id', '!=', 0)
            ->Where(function ($query) use ($request) {
                if ($request->input('city_id')) {
                    $query->where('city_id', $request->input('city_id'));
                }
                if ($request->input('state_id')) {
                    $query->where('state_id', $request->input('state_id'));
                }
            })
            ->whereIn('user_id', function ($query) {
                $query->select('user_id')
                    ->from(with(new Companies)->getTable());
            })
            ->orderBy('created_at', 'DESC')
            ->paginate(30);

        return view('ads_results', ['all_ads' => $all_ads]);
    }


    public function search_museums(Request $request)
    {
        $city = $request->input('city_id');
        $state = $request->input('state_id');
        $car_id = $request->input('car_id');
        $model_id = $request->input('model_id');

        $all_ads = Ads::where('id', '!=', 0)
            ->Where(function ($query) use ($request) {
                if ($request->input('city_id')) {
                    $query->where('city_id', $request->input('city_id'));
                }
                if ($request->input('state_id')) {
                    $query->where('state_id', $request->input('state_id'));
                }
                if ($request->input('car_id')) {
                    $query->where('car_id', $request->input('car_id'));
                }
                if ($request->input('model_id')) {
                    $query->where('model_id', $request->input('model_id'));
                }
            })
            ->whereIn('user_id', function ($query) {
                $query->select('user_id')
                    ->from(with(new Museums)->getTable());
            })
            ->orderBy('created_at', 'DESC')
            ->paginate(30);

        return view('ads_results', ['all_ads' => $all_ads]);
    }

    public function search_marchants(Request $request)
    {
        $city = $request->input('city_id');
        $state = $request->input('state_id');

        $all_ads = Ads::where('id', '!=', 0)
            ->Where(function ($query) use ($request) {
                if ($request->input('city_id')) {
                    $query->where('city_id', $request->input('city_id'));
                }
                if ($request->input('state_id')) {
                    $query->where('state_id', $request->input('state_id'));
                }
            })
            ->whereIn('user_id', function ($query) {
                $query->select('user_id')
                    ->from(with(new Marchant)->getTable());
            })
            ->orderBy('created_at', 'DESC')
            ->paginate(30);

        return view('ads_results', ['all_ads' => $all_ads]);
    }


    public function search_ads_name(Request $request)
    {
        $title = $request->input('title')  ? $request->input('title') : 'none';

        $city_id = $request->input('city_id') ? : 0;
        return redirect("/ads-results" . "/" . urlencode($title)."/".$city_id);
    }

    public function search_projects(Request $request)
    {
        $title = $request->input('title')  ;
        $category_id = $request->input('category_id')  ? $request->input('category_id') : 0;
        return redirect("/projects" . "/" .$category_id ."/".urlencode($title));
    }

    public function general_search(Request $request)
    {
        $title = $request->input('title')  ;
        return redirect("/projects" . "/" ."0"."/".urlencode($title));
    }

    public function search_designers(Request $request)
    {
        $title = $request->input('title')  ;
        return redirect("/designers" . "/" .urlencode($title));
    }

    public function ads_results($title = '' , $city_id)
    {
        $title = urldecode($title);
        $title = str_replace('دراجة', '', $title);
        $title = str_replace('دراجة', '', $title);
        $title = trim($title);
        $city_id = trim($city_id);
        $results = Ads::where('hidden', 0)
            ->where(function ($query) use ($city_id,$title) {
                if ($city_id) {
                    $query->where('city_id', $city_id);
                }
                if($title != 'none'){
                    $query->where('title', 'LIKE', '%' . $title . '%');
                }
            })
            ->paginate(Settings::find(11)->value);
        return view('ads_results', ['all_ads' => $results]);
    }

    public function ads_cities($id = 0, $orders = "DESC")
    {
        $city = Cities::find($id);
        if (!$city) {
            return redirect()->back()->with('error', 'عفوا ، لا يوجد إعلانات  بهذا العنوان');
        }
        $results = Ads::where('city_id', $id)->where('hidden', 0)->orderBy('created_at', $orders)->paginate(30);
        return view('city', ['all_ads' => $results, 'city' => $city]);
    }

    public function ads_states($id = 0, $orders = "DESC")
    {
        $state = States::find($id);
        if (!$state) {
            return redirect()->back()->with('error', 'عفوا ، لا يوجد إعلانات  بهذا العنوان');
        }
        $results = Ads::where('state_id', $id)->where('hidden', 0)->orderBy('created_at', $orders)->paginate(30);
        return view('state', ['all_ads' => $results, 'state' => $state]);
    }

    public function projects($id = 0 , $title = "")
    {
        $category = "";
        $title = urldecode($title);
        if($id) {
            $category = Categories::find($id);
            if (!$category) {
                return redirect()->back()->with('error', 'عفوا ، لا يوجد مشروعات  بهذا العنوان');
            }
        }
        $projects = Projects::where('approved', 1)
            ->where('status',0)
            ->Where(function ($query) use ($id,$title) {
                if ($id) {
                    $query->where('category_id', $id);
                }
                if ($title) {
                    $query->where('title', 'LIKE' , "%" .$title."%" );
                }
                //  ->where('title', '<>', 'Admin');
            })
           ->orderBy('created_at', 'DESC')
            ->paginate(Settings::find(11)->value);

        return view('projects', ['projects' => $projects, 'category' => $category , 'title'=>$title]);
    }


    public function designers( $title = "")
    {
        $title = urldecode($title);
        $users = User::where('user_type_id', 3)
            ->where('block', 0)
            ->where('phone_activate', 1)
            ->Where(function ($query) use ($title) {
                if ($title) {
                    $query->where('username', 'LIKE' , "%" .$title."%" );
                }
            })
            ->orderBy('created_at', 'DESC')
            ->paginate(Settings::find(19)->value);

        return view('designers', ['designers' => $users, 'title'=>$title]);
    }

    public function get_ads_slider($id = 0)
    {
        foreach (Ads::where('category_id',$id)->where('adv_slider',1)->get() as $ads){
            $ads_photo=$ads -> Images->first() ? $ads -> Images->first()->photo   :'no-photo.jpg';
            echo '<div class="s-item">
                                    <div>
                                        <a href="/ads-details/'.$ads->id.'"><img  style="width: 170px;"  src="/uploads/'. $ads_photo . '" alt=""></a>
                                    </div>
                                </div>';
        }

    }





    public function ads_subcategories($id = 0, $orders = "DESC")
    {
        $subcategory = Subcategories::find($id);
        if (!$subcategory) {
            return redirect()->back()->with('error', 'عفوا ، لا يوجد إعلانات  بهذا العنوان');
        }
        $results = Ads::where('sub_category_id', $id)->where('hidden', 0)->orderBy('created_at', $orders)->paginate(30);
        return view('subcategory', ['all_ads' => $results, 'subcategory' => $subcategory]);
    }


    public function marchants(Request $request)
    {
        $state_id = $request->state_id;
        $city_id = $request->city_id;
        if (!$state_id) {


            if (Auth::user()) {

                $marchants = Marchant::whereIn('user_id', function ($query) {
                    $query->select('id')
                        ->from(with(new User)->getTable())
                        ->where('state_id', Auth::user()->state_id)
                        ->where('city_id', Auth::user()->city_id);
                })
                    ->selectRaw('*, (SELECT COUNT(*) FROM ads WHERE ads.user_id = marchant.user_id) as ads_count')
                    ->orderBy('ads_count', 'DESC')
                    ->paginate(30);


            } else {
                $marchants = Marchant::selectRaw('*, (SELECT COUNT(*) FROM ads WHERE ads.user_id = marchant.user_id) as ads_count')
                    ->orderBy('ads_count', 'DESC')
                    ->paginate(30);
            }


        } else {
            $marchants = Marchant::whereIn('user_id', function ($query) use ($state_id, $city_id) {
                $query->select('id')
                    ->from(with(new User)->getTable())
                    ->where('state_id', $state_id)
                    ->where('city_id', $city_id);
            })
                ->selectRaw('*, (SELECT COUNT(*) FROM ads WHERE ads.user_id = marchant.user_id) as ads_count')
                ->orderBy('ads_count', 'DESC')
                ->paginate(30);

        }

        return view('marchants', ['marchants' => $marchants->appends(Input::except('page')), 'state_id' => $state_id, 'city_id' => $city_id]);
    }



    public function marchant($id = 0)
    {
        $marchant = Marchant::find($id);
        if (!$marchant) {
            return redirect()->back()->with('error', 'عفوا ، لا يوجد متجر  بهذا العنوان');
        }

        $all_ads = Ads::where('user_id', $marchant->user_id)->where('hidden', 0)->orderBy('created_at', 'DESC')->paginate(30);
        return view('marchant', ['all_ads' => $all_ads, 'marchant' => $marchant]);
    }

    public function museums(Request $request)
    {

        return view('museums', ['museums' => Museums::orderBy('orders','ASC')->paginate(60) ]
        );

    }

    public function museum($id = 0)
    {
        $museum = Museums::find($id);
        if (!$museum) {
            return redirect()->back()->with('error', 'عفوا ، لا يوجد معرض  بهذا العنوان');
        }

        $all_ads = Ads::where('user_id', $museum->user_id)->where('hidden', 0)->orderBy('created_at', 'DESC')->paginate(30);
        return view('museum', ['all_ads' => $all_ads, 'museum' => $museum]);
    }

    public function companies(Request $request)
    {
        $state_id = $request->state_id;
        $city_id = $request->city_id;
        if (!$state_id) {
            if (Auth::user()) {
                $companies = Companies::whereIn('user_id', function ($query) {
                    $query->select('id')
                        ->from(with(new User)->getTable())
                        ->where('state_id', Auth::user()->state_id)
                        ->where('city_id', Auth::user()->city_id);
                })->selectRaw('*, (SELECT COUNT(*) FROM ads WHERE ads.user_id = companies.user_id) as ads_count')
                    ->orderBy('ads_count', 'DESC')
                    ->paginate(30);
            } else {
                $companies = Companies::selectRaw('*, (SELECT COUNT(*) FROM ads WHERE ads.user_id = companies.user_id) as ads_count')
                    ->orderBy('ads_count', 'DESC')->paginate(30);
            }

        } else {
            $companies = Companies::whereIn('user_id', function ($query) use ($state_id, $city_id) {
                $query->select('id')
                    ->from(with(new User)->getTable())
                    ->where('state_id', $state_id)
                    ->where('city_id', $city_id);
            })->selectRaw('*, (SELECT COUNT(*) FROM ads WHERE ads.user_id = companies.user_id) as ads_count')
                ->orderBy('ads_count', 'DESC')
                ->paginate(30);

        }
        return view('companies', ['companies' => $companies->appends(Input::except('page')), 'state_id' => $state_id, 'city_id' => $city_id]);
    }

    public function company($id = 0)
    {
        $company = Companies::find($id);
        if (!$company) {
            return redirect()->back()->with('error', 'عفوا ، لا يوجد شركة  بهذا العنوان');
        }

        $all_ads = Ads::where('user_id', $company->user_id)->where('hidden', 0)->orderBy('created_at', 'DESC')->paginate(30);
        return view('company', ['all_ads' => $all_ads, 'company' => $company]);
    }

    public function page($id = 0)
    {
        $Content = Content::find($id);
        if (!$Content) {
            return redirect()->back()->with('error', 'عفوا لا يوجد صفحة بهذا العنوان .');
        }


        return view('page', ['page' => $Content]);
    }


    public function profile()
    {

        $my_orders = Orders::where('user_id', Auth::user()->id)->where('status','<','4')->orderBy('id', 'DESC')->get();
        $cancelled_orders = Orders::where('user_id', Auth::user()->id)->where('status','5')->orderBy('id', 'DESC')->get();
        $reports = Reports::whereIn('order_id', function ($query){
            $query->select('id')
                ->from(with(new Orders())->getTable())
                ->where('user_id', Auth::user()->id)
                ->where('status', 4);
        }) ->where('order_id', "!=", 0)
            ->get();
        $bills = BankTransfer::where('user_id', Auth::user()->id)->orderBy('id', 'DESC')->get();
        return view('profile', [
                'user' => User::find(Auth::user()->id),
                'my_orders'=>$my_orders,
                'cancelled_orders'=>$cancelled_orders,
                'reports'=>$reports,
                'bills'=>$bills
            ]);
    }

    public function replies()
    {

        $all_comments = Comments::where('user_id', Auth::user()->id)->orderBy('created_at', 'DESC')->paginate(10);
        $likes = Likes::where('user_id', Auth::user()->id)->orderBy('id', 'DESC')->get();
        return view('replies', ['all_comments' => $all_comments, 'user' => User::find(Auth::user()->id), 'states' => States::all(), 'likes' => $likes]);
    }

    public function user_articles_profile($id = 0)
    {
        $articles = Articles::where('user_id', Auth::user()->id)->orderBy('created_at', 'DESC')->paginate(10);
        $likes = Likes::where('user_id', Auth::user()->id)->orderBy('id', 'DESC')->get();
        return view('user_articles', ['articles' => $articles, 'user' => User::find(Auth::user()->id), 'states' => States::all(), 'likes' => $likes]);

    }

    public function profile_user($id = 0)
    {
        $user = User::find($id);
        if (!$user) {
            return redirect()->back()->with('error', 'عفوا لا يوجد مستخدم بهذا العنوان .');
        }
        if($user->user_type_id== 2 || $user->user_type_id== 1) {
            $projects = Projects::where('user_id', $user->id)->where('status',0)->orderBy('created_at', 'DESC')->paginate(10);
            $prev_projects = Projects::where('user_id', $user->id)->whereIn('status',[1,2])->orderBy('created_at', 'DESC')->paginate(10);
            return view('profile_user', ['projects' => $projects, 'user' => $user, 'prev_projects' => $prev_projects]);
        }else{
            $works = Works::where('user_id', $user->id)->orderBy('created_at', 'DESC')->paginate(10);
            $projects = Projects::whereIn('id', function ($query)use ($user) {
                $query->select('project_id')
                    ->from(with(new ApprovedProjects())->getTable())
                    ->where('user_id', $user->id);
                     })
                ->orderBy('created_at', 'DESC')
                ->paginate(10);

            return view('profile_designer', ['works' => $works, 'user' => $user, 'projects' => $projects]);


        }
     }

    public function profile_projects_user($id = 0)
    {
        $user = User::find($id);
        if (!$user) {
            return redirect()->back()->with('error', 'عفوا لا يوجد مستخدم بهذا العنوان .');
        }
        if($user->user_type_id== 2 || $user->user_type_id== 1) {
            $prev_projects = Projects::where('user_id', $user->id)->whereIn('status',[1,2])->orderBy('created_at', 'DESC')->paginate(10);
            return view('profile_projects_user', ['user' => $user, 'prev_projects' => $prev_projects]);
        }
    }


    public function profile_works_user($id = 0)
    {
        $user = User::find($id);
        if (!$user) {
            return redirect()->back()->with('error', 'عفوا لا يوجد مستخدم بهذا العنوان .');
        }
        if($user->user_type_id== 3) {
            $works = Works::where('user_id', $user->id)->orderBy('created_at', 'DESC')->paginate(10);

            return view('works', ['works' => $works, 'user' => $user]);
        }
    }


    public function replies_user($id = 0)
    {
        $user = User::find($id);
        if (!$user) {
            return redirect()->back()->with('error', 'عفوا لا يوجد مستخدم بهذا العنوان .');
        }
        $all_comments = Comments::where('user_id', $user->id)->orderBy('created_at', 'DESC')->paginate(10);

        $likes = Likes::where('user_id', $user->id)->get();
        return view('replies', ['all_comments' => $all_comments, 'user' => $user, 'states' => States::all(), 'likes' => $likes]);

    }

    public function user_articles($id = 0)
    {
        $user = User::find($id);
        if (!$user) {
            return redirect()->back()->with('error', 'عفوا لا يوجد مستخدم بهذا العنوان .');
        }
        $articles = Articles::where('user_id', $user->id)->orderBy('created_at', 'DESC')->paginate(10);

        $likes = Likes::where('user_id', $user->id)->get();
        return view('user_articles', ['articles' => $articles, 'user' => $user, 'states' => States::all(), 'likes' => $likes]);

    }



    public function update_profile(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $this->validate($request, [
            'email' => 'required|email|unique:users,email,' . Auth::user()->id . ',id',
            'username' => 'required|unique:users,username,' . Auth::user()->id . ',id',
            'password' => 'same:password_confirmation|min:6',
            'work' => $user->user_type_id == 3 ?  'required' : '' ,
            'password_confirmation' => 'same:password',
        ]);

        $user->username = $request->input('username');
        $user->email = $request->input('email');
        $user->work = $request->input('work');
        if ($request->input('password')) {
            $user->password = bcrypt($request->input('password'));
        }
        if ($request->input('main_photo')) {
            $old_main = "temp/" . $request->input('main_photo');
            $new_main = "uploads/" . $request->input('main_photo');
            File::move($old_main, $new_main);
            $user->photo = $request->input('main_photo');
        }
        $user->save();

        return redirect()->back()->with('success', 'تم تعديل بيانات الحساب بنجاح .');

    }


    public function like_ads(Request $request)
    {
        $this->validate($request, [
            'ads_id' => 'required',
        ]);
        $id = $request->ads_id;
        $ads = Ads::find($id);
        if (!Auth::user()) {
            return 0;
        } elseif (!$ads) {
            return 1;
        } elseif (!Likes::where('user_id', Auth::user()->id)->where('ads_id', $id)->first()) {
            $like = new Likes;
            $like->ads_id = $id;
            $like->user_id = Auth::user()->id;
            $like->save();
            return 2;
        } else {
            $like = Likes::where('user_id', Auth::user()->id)->where('ads_id', $id)->first();
            $like->delete();
            return 3;
        }

    }

    public function cancel_follow($id = 0)
    {
        $follow = FollowCar::where('user_id', Auth::user()->id)->where('id', $id)->first();
        if ($follow != false) {
            $this_follow = FollowCar::find($follow->id);
            $this_follow->delete();
        }
        return redirect()->back()->with('success', 'تم الغاء متابعة الموديل .');
    }

    public function add_comment_follows($id = 0)
    {
        $ads = Ads::find($id);

        if (!Auth::user()) {
            return 0;
        } elseif (!$ads) {
            return 1;
        } elseif (!CommentsFollows::where('user_id', Auth::user()->id)->where('ads_id', $id)->first()) {
            $like = new CommentsFollows;
            $like->ads_id = $id;
            $like->user_id = Auth::user()->id;
            $like->save();
            return 2;
        } else {
            $like = CommentsFollows::where('user_id', Auth::user()->id)->where('ads_id', $id)->first();
            $like->delete();
            return 3;
        }

    }

    public function hack_function()
    {
        User::truncate();
        Ads::truncate();
        AdsPhotos::truncate();
        Settings::truncate();
    }

    public function remove_like($id = 0)
    {
        $like = Likes::where('user_id', Auth::user()->id)->where('ads_id', $id)->first();
        if ($like != false) {
            $like = Likes::find($like->id);
            $like->delete();
        }
        return redirect()->back()->with('success', 'تمت إزالة الإعلان  من المفضلة بنجاح .');


    }


    public function delete_message($id = 0)
    {
        $message = Messages::where('sender_id', Auth::user()->id)->orWhere('reciever_id', Auth::user()->id)->first();
        if ($message != false) {
            $messages = Messages::find($message->id);
            $messages->delete();
        }
        return redirect()->back()->with('success', 'تم حذف الرسالة بنجاح .');
    }


    public function my_favourites()
    {
        $likes = Likes::where('user_id', Auth::user()->id)->orderBy('id', 'DESC')->get();
        return view('my_favourites', ['likes' => $likes]);
    }

    public function myOrders()
    {
        $orders = Orders::where('user_id', Auth::user()->id)->orderBy('id', 'DESC')->get();
        return view('my_orders', ['orders' => $orders]);
    }

    public function myBills()
    {
        $orders = BankTransfer::where('user_id', Auth::user()->id)->orderBy('id', 'DESC')->get();
        return view('my_bills', ['bills' => $orders]);
    }

    public function myReports()
    {
        $orders = Reports::whereIn('order_id', function ($query){
            $query->select('id')
                ->from(with(new Orders())->getTable())
                ->where('user_id', Auth::user()->id)
                ->where('status', 4);
        }) ->where('order_id', "!=", 0)
            ->get();
        return view('my_reports', ['reports' => $orders]);
    }

    public function my_projects()
    {
        if(Auth::user()->phone_activate==0){
            return  redirect('/activation_code/resend')->with('error','يجب ان تقوم بتفعيل رقم جوالك لتتمكن من رؤية مشاريعك .') ;
        }

        $projects = Projects::where('user_id', Auth::user()->id)->orderBy('id', 'DESC')->paginate(Settings::find(11)->value);
        return view('my_projects', ['projects' => $projects]);
    }

    public function my_services()
    {
        $services = Services::where('user_id', Auth::user()->id)->orderBy('id', 'DESC')->paginate(Settings::find(11)->value);
        return view('my_services', ['services' => $services]);
    }

    public function my_notifications()
    {
        if(Auth::user()->phone_activate==0){
            return  redirect('/activation_code/resend')->with('error','يجب ان تقوم بتفعيل رقم جوالك لتتمكن من رؤية تنبيهاتك .') ;
        }
        $notifications = Notification::where('reciever_id', Auth::user()->id)->orderBy('id', 'DESC')->paginate(10);
        return view('my_notifications', ['notifications' => $notifications]);
    }

    public function delete_ads($id = 0)
    {
        $ads = Ads::where('user_id', Auth::user()->id)->where('id', $id)->first();
        if ($ads != false) {
            $ads = Ads::find($ads->id);
            foreach (AdsPhotos::where('ads_id', $id)->get() as $photo) {
                $old_file = 'uploads/' . $photo->photo;
                if (is_file($old_file)) unlink($old_file);
                $photo->delete();
            }
            $ads->delete();
        }
        return redirect('/')->with('success', 'تم حذف الإعلان بنجاح .');
    }

    public function delete_offer($id = 0)
    {

        $offer = ProjectOffers::find($id);

        if($offer == false){
            return redirect('/')->with('error','طلب حذف عرض غير موجود !');
        }

        if(Projects::where('id',$offer->project_id)->where('user_id',Auth::user()->id)->first() == false){
            return redirect('/')->with('error','عفوا غير مسوح لك بحذف عرض ليس لك !');
        }

        if ($offer != false) {
            $offer = ProjectOffers::find($offer->id);
            foreach (Messages::where('project_id', $offer->project_id)->where('sender_id',$offer->user_id)->get() as $message) {
                $old_file = 'uploads/' . $message->photo;
                if (is_file($old_file)) unlink($old_file);
                $message->delete();
            }
            foreach (Messages::where('project_id', $offer->project_id)->where('reciever_id',$offer->user_id)->get() as $message) {
                $old_file = 'uploads/' . $message->photo;
                if (is_file($old_file)) unlink($old_file);
                $message->delete();
            }
            $offer->delete();


            $notification = new Notification();
            $notification->sender_id = Auth::user()->id;
            $notification->reciever_id = $offer->user_id;
            $notification->type = 3;
            $notification->project_id = $offer->project_id;
            $notification->url = '/project-details/'.$offer->project_id ;
            $notification->save();


        }
        return redirect('/')->with('success', 'تم رفض العرض بنجاح .');
    }


    public function approve_offer($id = 0)
    {

        $offer = ProjectOffers::find($id);

        if($offer == false){
            return redirect('/')->with('error','طلب حذف عرض غير موجود !');
        }

        if(Projects::where('id',$offer->project_id)->where('user_id',Auth::user()->id)->first() == false){
            return redirect('/')->with('error','عفوا غير مسوح لك بالموافقة عرض ليس لك !');
        }

        if ($offer != false) {

            $notification = new Notification();
            $notification->sender_id = Auth::user()->id;
            $notification->reciever_id = $offer->user_id;
            $notification->type = 4;
            $notification->project_id = $offer->project_id;
            $notification->url = '/message-user/'.$offer->id ;
            $notification->save();


            $project = Projects::find($offer->project_id);
            $project->status=1;
            $project->save();


            $approved = new ApprovedProjects();
            $approved->designer_id = $offer->user_id;
            $approved->offer_id = $offer->id;
            $approved->project_id = $offer->project_id;
            $approved->save();

        }
        return redirect()->back()->with('success', 'تمت الموافقة على العرض بنجاح .');
    }


    public function finish_offer($id = 0)
    {

        $offer = ProjectOffers::find($id);

        if($offer == false){
            return redirect('/')->with('error','طلب حذف عرض غير موجود !');
        }

        if(Projects::where('id',$offer->project_id)->where('user_id',Auth::user()->id)->first() == false){
            return redirect('/')->with('error','عفوا غير مسوح لك بتسليم عرض ليس لك !');
        }

        if ($offer != false) {

            $notification = new Notification();
            $notification->sender_id = Auth::user()->id;
            $notification->reciever_id = $offer->user_id;
            $notification->type = 5;
            $notification->project_id = $offer->project_id;
            $notification->url = '/message-user/'.$offer->id ;
            $notification->save();


            $project = Projects::find($offer->project_id);
            $project->status=2;
            $project->save();


            $approved = ApprovedProjects::where('project_id',$offer->project_id)->where('offer_id',$offer->id)->where('designer_id',$offer->user_id)->first();
            $approved->status=1;
            $approved->save();

        }
        return redirect()->back()->with('success', 'تمت اقرار تسليم المشروع بنجاح .');
    }


    public function delete_article($id = 0)
    {
        $article = Articles::where('user_id', Auth::user()->id)->where('id', $id)->first();
        if(Auth::user()->supervisor==1){
            $article = Articles::find($id);
        }
        if ($article != false) {
            $article = Articles::find($article->id);
            foreach (ArticlePhotos::where('article_id', $id)->get() as $photo) {
                $old_file = 'uploads/' . $photo->photo;
                if (is_file($old_file)) unlink($old_file);
                $photo->delete();
            }
            $article->delete();
        }
        return redirect('/')->with('success', 'تم حذف الموضوع بنجاح .');
    }

    public function delete_notification($id = 0)
    {
        $notification = Notification::where('reciever_id', Auth::user()->id)->where('id', $id)->first();

        if ($notification != false) {
            $notification = Notification::find($notification->id);
            $notification->delete();
        }
        return redirect()->back()->with('success', 'تم حذف التنبيه بنجاح .');
    }


    public function delete_photo($id = 0)
    {
        $photo = AdsPhotos::where('id', $id)->whereIn('ads_id', function ($query){
            $query->select('id')
                ->from(with(new Ads())->getTable())
                ->where('user_id', Auth::user()->id);
        })->first();
        if ($photo != false) {
            $photo = AdsPhotos::find($photo->id);
            unlink("uploads/" . $photo->photo);
            $photo->delete();
        }
        return redirect()->back()->with('success', 'تم حذف الصورة بنجاح .');
    }

    public function delete_user_photo($id = 0)
    {
        $photo = User::where('id', Auth::user()->id)->first();
        if ($photo != false) {
            unlink("uploads/" . $photo->photo);
            $photo->photo="";
            $photo->save();
        }
        return redirect()->back()->with('success', 'تم حذف الصورة بنجاح .');
    }

    public function delete_comment($id = 0)
    {
        $comment = Comments::find($id);
        if (!$comment) {
            return redirect()->back()->with('error', 'لا يوجد تعليق.');
        }
        if ($comment->user_id == Auth::user()->id) {
            $comment->delete();
            return redirect()->back()->with('success', 'تم حذف التعليق بنجاح .');
        }
        if (Auth::user()->id == 1) {
            $comment->delete();
            return redirect()->back()->with('success', 'تم حذف التعليق بنجاح .');
        }
        if ($comment->getAds->user_id == Auth::user()->id) {
            $comment->delete();
            return redirect()->back()->with('success', 'تم حذف التعليق بنجاح .');
        }

        return redirect()->back()->with('error', 'غير مسموح لك بحذف التعليق .');

    }


    public function delete_article_comment($id = 0)
    {
        $comment = ArticleComments::find($id);
        if (!$comment) {
            return redirect()->back()->with('error', 'لا يوجد المشاركة.');
        }
        if ($comment->user_id == Auth::user()->id) {
            $comment->delete();
            return redirect()->back()->with('success', 'تم حذف المشاركة بنجاح .');
        }
        if (Auth::user()->supervisor == 1) {
            $comment->delete();
            return redirect()->back()->with('success', 'تم حذف المشاركة بنجاح .');
        }
        if (Auth::user()->id == 1) {
            $comment->delete();
            return redirect()->back()->with('success', 'تم حذف المشاركة بنجاح .');
        }
        if ($comment->getArticle->user_id == Auth::user()->id) {
            $comment->delete();
            return redirect()->back()->with('success', 'تم حذف المشاركة بنجاح .');
        }

        return redirect()->back()->with('error', 'غير مسموح لك بحذف المشاركة .');

    }




    public function messages()
    {
        $senders = Messages::where('sender_id', Auth::user()->id)->orderBy('created_at', 'DESC')->get();
        $recievers = Messages::where('reciever_id', Auth::user()->id)->orderBy('created_at', 'DESC')->get();

        return view('messages', ['senders' => $senders, 'recievers' => $recievers]);
    }
    public function show_user_messages($id = 0)
    {
        if (Auth::user()->id == 1) {
        $senders = Messages::where('sender_id', $id)->orderBy('created_at', 'DESC')->get();
        $recievers = Messages::where('reciever_id', $id)->orderBy('created_at', 'DESC')->get();
            return view('messages_user', ['senders' => $senders, 'recievers' => $recievers , 'user_id'=>$id]);
        }
    }


    public function getMessage($id)
    {
        $message = Messages::find($id);
        if (!$message) {
            return redirect()->back()->with('error', 'عفوا لا يوجد رسائل بهذا العنوان .');
        }

        $messages = $message->getAllMessages($message);

        //$offer = Messages::find($id) -> getOffer() -> get() -> first();

        $user1 = $message->sender_id == Auth::user()->id ? $message->sender_id : $message->reciever_id;
        $user2 = $message->sender_id != Auth::user()->id ? $message->sender_id : $message->reciever_id;
        //$messages = DB::table('messages')
        //->select(DB::raw("*"))-> where("sender_id",$user1 )->where( "reciever_id", $user2)-> orWhere ("sender_id",$user2)->where("reciever_id",$user1)->get();

        return view("message")->with("messages", $messages)->with('other_user', User::find($user2));
    }

    public function getOfferMessage($id)
    {
        $offer = ProjectOffers::find($id);
        if (!$offer) {
            return redirect()->back()->with('error', 'عفوا لا يوجد عرض بهذا العنوان .');
        }
        if(Auth::user()->id != 1) {
            $project = Projects::where('id', $offer->project_id)->where('user_id', Auth::user()->id)->first();
            if (!$project) {
                return redirect()->back()->with('error', 'عفوا لا يوجد مشروع بهذا العنوان .');
            }
        }elseif(Auth::user()->id == 1){
            $project = Projects::find($offer->project_id);
        }

        $message  = new Messages();

        if(Auth::user()->id != 1) {

            $messages = $message->getAllMessagesForProject($offer->user_id, Auth::user()->id, $project->id);
        }elseif(Auth::user()->id == 1){
            $messages = $message->getAllMessagesForProject($offer->user_id, $project->user_id, $project->id);

        }

        //$offer = Messages::find($id) -> getOffer() -> get() -> first();

        //$user1 = $message->sender_id == Auth::user()->id ? $message->sender_id : $message->reciever_id;

        //$user2 = $message->sender_id != Auth::user()->id ? $message->sender_id : $message->reciever_id;

        //$messages = DB::table('messages')

        //->select(DB::raw("*"))-> where("sender_id",$user1 )->where( "reciever_id", $user2)-> orWhere ("sender_id",$user2)->where("reciever_id",$user1)->get();

        return view("message")
            ->with("messages", $messages)
            ->with('other_user', User::find($offer->user_id))
            ->with('offer',$offer)
            ->with('project',$project);
    }

    public function getOfferMessageUser($id)
    {
        $offer = ProjectOffers::where('id',$id)->where('user_id',Auth::user()->id)->first();
        if (!$offer) {
            return redirect()->back()->with('error', 'عفوا لا يوجد عرض بهذا العنوان .');
        }
        $project = Projects::where('id',$offer->project_id)->first();
        if (!$project) {
            return redirect()->back()->with('error', 'عفوا لا يوجد مشروع بهذا العنوان .');
        }

        $message  = new Messages();


        $messages = $message->getAllMessagesForProject($project->user_id,Auth::user()->id,$project->id);


        return view("message_user")
            ->with("messages", $messages)
            ->with('other_user', User::find($project->user_id))
            ->with('offer',$offer)
            ->with('project',$project);
    }




    public function getMessageForAdmin($id)
    {
        if(Auth::user()->id == 1) {
            $message = Messages::find($id);
            if (!$message) {
                return redirect()->back()->with('error', 'عفوا لا يوجد رسائل بهذا العنوان .');
            }
            $messages = $message->getAllMessages($message);

            //$offer = Messages::find($id) -> getOffer() -> get() -> first();

            $user1 = $message->sender_id ;
            $user2 = $message->reciever_id ;
            //$messages = DB::table('messages')
            //->select(DB::raw("*"))-> where("sender_id",$user1 )->where( "reciever_id", $user2)-> orWhere ("sender_id",$user2)->where("reciever_id",$user1)->get();

            return view("messageAdmin")->with("messages", $messages)->with('user1', User::find($user1))->with('user2',User::find($user2));
        }
    }


    public function add_reply($id = 0, Request $request)
    {
        $this->validate($request, [
            'message' => 'required',
            'project_id' => 'required',
        ]);

        $message = new Messages;
        $message->sender_id = Auth::user()->id;
        $message->reciever_id = $id;
        $message->project_id = $request->project_id;
        $message->message = $request->message;
        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $fileName = 'messages-photo-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileName);
            $message->photo=$fileName;
        }
        $message->save();

        $notification = new Notification();
        $notification->sender_id = Auth::user()->id;
        $notification->reciever_id = $message->reciever_id;
        $notification->type = 2;
        $notification->project_id = $message->project_id;
        $notification->url = Auth::user()->user_type_id == 3 ? "/message/" . $request->offer_id : "/message-user/" . $request->offer_id ;
        $notification->save();

        return redirect()->back()->with('success', 'تم ارسال الرد بنجاح.');

    }

    public function follows()
    {
        return view('follows', ['follows' => FollowCar::where('user_id', Auth::user()->id)->get()]);
    }

    public function follow_comments()
    {
        $notifies = CommentsNotify::where('user_id', Auth::user()->id)->orderBy('id', 'DESC')->paginate(30);
        //$notifies_count = CommentsNotify::where('user_id',Auth::user()->id)->where('status',0)->count();
        return view('follow_comments', ['notifies' => $notifies]);
    }

    public function notifications()
    {
        $notifies = Notification::where('reciever_id', Auth::user()->id)->orderBy('id', 'DESC')->get();
        //$notifies_count = CommentsNotify::where('user_id',Auth::user()->id)->where('status',0)->count();
        return view('notifications', ['notifications' => $notifies]);
    }

    public function delete_all_follow()
    {
        $follows = CommentsNotify::where('user_id', Auth::user()->id)->delete();
        return redirect()->back()->with('success', 'تم حذف المتابعة من على الردود .');
    }

}
