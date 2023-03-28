<?php

namespace App\Http\Controllers\Website\Auth;

use App\Facades\AuthServiceFacade;
use App\Models\User;
use App\Models\CartItem;
use App\Models\Products;
use App\Models\Settings;
use App\Models\Countries;
use App\Models\ClientTypes;
use App\Models\DeviceTokens;
use Illuminate\Http\Request;
use App\Models\ActivationCodes;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UsersResource;
use Illuminate\Support\Facades\Validator;
use MFrouh\Sms4jawaly\Facades\Sms4jawaly;
use telesign\sdk\messaging\MessagingClient;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

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
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:client')->except('logout');
    }

    public function get_login()
    {
        return redirect('/');
    }

    public function login(Request $request)
    {
        return  AuthServiceFacade::Login($request);
    }

    public function activate(Request $request)
    {
        return  AuthServiceFacade::activate($request, 'client');
    }

    public function logout(Request $request)
    {
        Auth::guard('client')->logout();
        return redirect('/')->with('success', "تم تسجيل الخروج بنجاح");
    }
}
