<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->check_settings((new \ReflectionClass($this))->getShortName());
            return $next($request);
        });
    }


    public function index()
    {
        $notifications = \App\Models\Notification::orderBy('id', 'desc')
            ->where('reciever_id', auth()->user()->id)
            ->where('message', '!=', '')
            ->where('type', 99)
            ->get();

        return view('admin.activity.all', ['objects' => $notifications]);
    }
}
