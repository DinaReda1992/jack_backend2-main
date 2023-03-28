<?php

namespace App\Http\Controllers\Providers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/provider-panel/index';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest.provider')->except('logout');
    }

    public function login_page(){
        return view('providers.login');
    }

    public function login(Request $request) {
        $this -> validate($request, ['email' => 'required|email', 'password' => 'required']);
        $email = $request -> input('email');
        $password = $request -> input('password');
        if (!Auth::attempt(['email' => $email, 'password' => $password, 'user_type_id' => '3'], $request -> has('remember'))
            && !Auth::attempt(['email' => $email, 'password' => $password, 'user_type_id' => '4'], $request -> has('remember'))) {
            return redirect() -> back() -> with('error', 'لقد أدخلت بيانات غير صحيحة');
        }
        $user=\auth()->user();
        if($user->block || $user->is_archived){
            Auth::logout();
            return redirect('/provider-panel/login')->with('error',"لا يمكنك تسجيل الدخول ربما تم حذف حسابك او حظر دخولك");

        }
        return redirect('/provider-panel/index');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return redirect('/provider-panel/login')->with('success',"تم تسجيل الخروج بنجاح");
    }
}
