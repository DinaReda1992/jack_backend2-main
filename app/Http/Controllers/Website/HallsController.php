<?php

namespace App\Http\Controllers\Website;

use App\Http\Requests;
use App\Http\Resources\HallsResource;
use App\Models\Content;
use App\Models\Faqs;
use App\Models\Hall;
use App\Models\SupplierCategory;
use App\Models\Likes;
use App\Models\MiddleSection;
use App\Models\Notification;
use App\Models\RequestRepresentative;
use App\Models\RequestUserService;
use App\Models\Reservations;
use App\Models\Slider;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use \Carbon\Carbon;
use Mail;
use Illuminate\Support\Facades\Input;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;
use PhpParser\Node\Expr\New_;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class HallsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        //  $this->middleware('auth');
        }


    public function gethall($id)
    {
        $hall = Hall::find($id);
        if (!$hall) {
            return abort(404);
        }
        $is_like = false;
        if (Auth::User()) {
            $like = Likes::where('hall_id', $hall->id)->where('user_id', Auth::id())->get();
            $is_like = $like->count() ? true : false;
        }
        return view('hallDetails', ['hall' => $hall, 'is_like' => $is_like]);
    }

    public function addToFavorite($id)
    {
        $hall = Hall::find($id);
        if (!$hall || !Auth::User()) {
            return false;
        }
        $like = Likes::where('hall_id', $id)->where('user_id', Auth::id())->first();
        if ($like) {
            $like->delete();
            return '<i class="lni-heart"></i>';
        } else {
            $newLike = new Likes();
            $newLike->hall_id = $id;
            $newLike->user_id = Auth::id();
            $newLike->save();
            return '<i class="lni-heart-filled"></i>';

        }
    }
    public function removeFromFavorites($id)
    {
        $hall = Hall::find($id);
        if (!$hall || !Auth::User()) {
            return redirect()->back()->with('error','لا يوجد لديك صلاحية .');
        }
        $like = Likes::where('hall_id', $id)->where('user_id', Auth::id())->first();
        if ($like->count()) {

            $like->delete();
            return redirect()->back()->with('success','تم الحذف من المفضلة .');
        }
        return redirect()->back()->with('error','لا يوجد لديك صلاحية .');

    }

    public function my_favourite_halls()
    {
        $user_like = Auth::User()?Auth::Id():0;


            $halls = Hall::select('*')->whereIn('id', function ($query) use ($user_like) {
                $query->select('hall_id')
                    ->from(with(new Likes())->getTable())
                    ->where('user_id',$user_like);
            })
                ->selectRaw('(SELECT count(*) FROM likes WHERE likes.hall_id=halls.id) as likes_count')
                ->selectRaw('(SELECT count(*) FROM likes WHERE likes.user_id =' . $user_like . ' AND likes.hall_id=halls.id) as is_liked')
                ->selectRaw('(SELECT IFNULL(ROUND(AVG(rate) ,0),0) FROM ratings WHERE ratings.hall_id=halls.id ) as hall_rate')

                ->paginate(10);

        return view('favorites',['halls'=>$halls]);



    }

    public function my_reserved_halls()
    {
        $user_like = Auth::User()?Auth::Id():0;


        $halls = Hall::whereIn('id', function ($query) use ($user_like) {
            $query->select('hall_id')
                ->from(with(new Reservations())->getTable())
                ->where('user_id',$user_like)
                ->where('status',1)
                ->where('date','>=',date('Y-m-d'));
        })
            ->select("id","title",'address','longitude','latitude','chairs','currency','price_per_hour')
            ->selectRaw('(SELECT count(*) FROM likes WHERE likes.hall_id=halls.id) as likes_count')
            ->selectRaw('(SELECT count(*) FROM likes WHERE likes.user_id =' . $user_like . ' AND likes.hall_id=halls.id) as is_liked')
            ->selectRaw('(SELECT IFNULL(ROUND(AVG(rate) ,0),0) FROM ratings WHERE ratings.hall_id=halls.id ) as hall_rate')
            ->paginate(10);

        return view('reserved',['halls'=>$halls]);

    }

}
