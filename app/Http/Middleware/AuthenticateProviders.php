<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Factory as Auth;

class AuthenticateProviders
{
    /**
     * The authentication factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */

    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$guards
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $this->authenticate($guards);
        if (auth()->user() && auth()->user()->user_type_id != 3 && auth()->user()->user_type_id != 4) {
            \Illuminate\Support\Facades\Auth::logout();
            return redirect()->to('provider-panel/login')->with('error', 'لابد ان تكون تاجر أو مشرف على متجر  لتتمكن من الدخول .');
        }
        return $next($request);
    }

    /**
     * Determine if the user is logged in to any of the given guards.
     *
     * @param  array  $guards
     * @return void
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    protected function authenticate(array $guards)
    {
        if (empty($guards)) {
            if (auth()->check() && auth()->user()->user_type_id == 3) {
                return $this->auth->authenticate();
            }
            throw new AuthenticationException('Unauthenticated.', $guards, '/provider-panel/login');
        }

        foreach ($guards as $guard) {
            if ($this->auth->guard($guard)->check()) {
                return $this->auth->shouldUse($guard);
            }
        }

        throw new AuthenticationException('Unauthenticated.', $guards, '/provider-panel/login');
    }
}
