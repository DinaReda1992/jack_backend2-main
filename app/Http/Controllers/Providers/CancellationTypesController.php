<?php

namespace App\Http\Controllers\Providers;

use App\Models\Countries;
use App\Models\Prices;

use App\Http\Controllers\Controller;


use Illuminate\Http\Request;

use App\Http\Requests;

use App\Models\CancellationTypes;
class CancellationTypesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {

//        $this->middleware(function ($request, $next) {
//            $this->check_settings(428);
//            return $next($request);
//        });
    }
    public function index()
    {
        $this->check_provider_settings(429);

        return view('providers.cancellation_types.all',['objects'=>CancellationTypes::all()]);
    }

}
