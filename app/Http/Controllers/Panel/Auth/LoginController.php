<?php

namespace App\Http\Controllers\Panel\Auth;

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
    protected $redirectTo = '/admin-panel/index';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest.admin')->except('logout');
    }

    public function login_page(){
        return view('admin/login');
    }

    public function login(Request $request) {
        $this->validate($request, ['email' => 'required|email', 'password' => 'required']);
        $email = $request -> input('email');
        $password = $request -> input('password');
        if (!Auth::attempt(['email' => $email, 'password' => $password, 'user_type_id' => '1'], $request -> has('remember')) && !Auth::attempt(['email' => $email, 'password' => $password, 'user_type_id' => '2'], $request -> has('remember'))) {
            return redirect() -> back() -> with('error', 'لقد أدخلت بيانات غير صحيحة');
        }
        if(\auth()->user()->block==1 ||\auth()->user()->is_archived==1 ){
            Auth::logout();

            return redirect()->back()->with('error','تم حذف او حظر حسابك من التطبيق ');
        }

        return redirect('/admin-panel/index');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return redirect('/admin-panel/login')->with('success',"تم تسجيل الخروج بنجاح");
    }
}
