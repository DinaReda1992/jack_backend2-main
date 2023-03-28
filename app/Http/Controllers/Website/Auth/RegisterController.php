<?php

namespace App\Http\Controllers\Website\Auth;

use App\Facades\AuthServiceFacade;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\ActivationCodes;
use App\Models\Countries;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/';

    public function __construct()
    {
        $this->middleware('guest:client');
    }

    public function register_page()
    {
        return redirect('/');
    }

    public function postRegister(RegisterRequest $request)
    {
        return AuthServiceFacade::register($request, 'client');
    }
}
