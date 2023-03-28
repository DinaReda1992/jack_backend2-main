<?php

namespace App\Http\Middleware;

use Closure;

class Locale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(in_array($request->segment(1), config('app.locales'))){
           app()->setLocale($request->segment(1));
        }else{
            app()->setLocale(config('app.locale'));
        }
        return $next($request);
    }

//  function for localization
//    protected function mapApiRoutes()
//    {
//
//        if(in_array(request()->segment(1), config('app.locales'))){
//            $locale = request()->segment(1);
//        }else{
//            $locale = "ar";
//        }
//        Route::middleware('api')
//            ->namespace($this->namespaceApi)
//            ->prefix($locale."/api/v1")
//            ->group(base_path('routes/api.php'));
//
//    }

}