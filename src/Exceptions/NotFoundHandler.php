<?php
namespace Udiko\Cms\Exceptions;

use Throwable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\RateLimiter;
class NotFoundHandler extends ExceptionHandler
{
    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {

        if ($exception instanceof NotFoundHttpException) {
            if(!config('modules.installed')){
                if(env('DB_CONNECTION')!='mysql'){
                    rewrite_env(['DB_CONNECTION'=>'mysql']);
                }
            return redirect()->route('install');

            }
            if (get_option('site_maintenance') == 'Y') {
                return undermaintenance();
                }
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Not Found'], 404);
            } else {
                if (get_option('time_limit_reload')) {
                    if (RateLimiter::tooManyAttempts('page' . getRateLimiterKey($request), get_option('time_limit_reload') ?? 10)) {
                        abort(429);
                    }
                    $limit = get_option('limit_duration') ?? 60;
                    RateLimiter::hit('page' . getRateLimiterKey($request), (int) $limit);
                }
                if (get_option('forbidden_keyword') && str()->contains(str(url()->full())->lower(), explode(",", str_replace(" ", "", get_option('forbidden_keyword') ?? '')))) {
                    if ($redirect = get_option('forbidden_redirect'))
                        return redirect($redirect);
                    abort(403);
                }

                if (get_option('block_ip') && in_array($request->ip(), explode(",", get_option('block_ip')))) {
                    abort(403);
                }

                  $attr['view_type'] = '404';
                  $attr['view_path'] = '404';
                config(['modules.current' => $attr]);
                return View::exists(get_view(get_view())) ? response()->view('cms::layouts.master', [], 404) : response()->view('cms::errors.404', [], 404);
            }
        }

        return parent::render($request, $exception);
    }
}
