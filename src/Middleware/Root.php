<?php
namespace Udiko\Cms\Middleware;

use Illuminate\Http\Request;
use Closure;
class Root
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if(!session()->has('root')){
            return to_route('login')->send()->with('error','Silahkan login!');
        }else{
            if(!$request->segment(1))
            return redirect('dashboard')->send();


        }
        return $next($request);

    }
}
