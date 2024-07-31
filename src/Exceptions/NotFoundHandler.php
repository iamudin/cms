<?php
namespace Udiko\Cms\Exceptions;

use Exception;
use Throwable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Session;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
            return redirect()->route('install');
            }
            if (get_option('site_maintenance') == 'Y' && !Auth::check()) {
                return undermaintenance();
                }

            if ($request->expectsJson()) {
                return response()->json(['error' => 'Not Found'], 404);
            } else {
                  $attr['view_type'] = '404';
                  $attr['view_path'] = '404';
                config(['modules.current' => $attr]);
                return View::exists(get_view(get_view())) ? response()->view('cms::layouts.master', [], 404) : response()->view('cms::errors.404', [], 404);
            }
        }

        return parent::render($request, $exception);
    }
}
