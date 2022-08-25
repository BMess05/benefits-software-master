<?php

namespace App\Http\Middleware;

use Closure;

class checkIsActive
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
        // dd($request->user()->toArray());
        if ($request->user() && $request->user()->status == 1) {
            $user = resolve('users')->getUserById($request->user()->id);
            if ($user->status == 1) {
                return $next($request);
            }
        }
        return redirect('logout');
    }
}
