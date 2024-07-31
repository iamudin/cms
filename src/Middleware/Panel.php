<?php

namespace Udiko\Cms\Middleware;

use Closure;
use Illuminate\Http\Request;
use Udiko\Cms\Models\Visitor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\RateLimiter;
use Udiko\Cms\Http\Controllers\VisitorController;

class Panel
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
        $admin_path = admin_path();
        foreach (get_module() as $modul) {

            if ($request->is($admin_path . '/' . $modul->name)) {
                config([
                    'modules.current' => [
                        'post_type' => $modul->name,
                        'title_crud' => $modul->title,
                    ]
                ]);
            }

            if ($request->is($admin_path . '/' . $modul->name . '/*/edit')) {
                config([
                    'modules.current' => [
                        'post_type' => $modul->name,
                        'title_crud' => 'Edit ' . $modul->title,
                    ]
                ]);
            }
            if ($request->is($admin_path . '/' . $modul->name . '/*/show')) {

                config([
                    'modules.current' => [
                        'post_type' => $modul->name,
                        'title_crud' => 'Lihat ' . $modul->title,
                    ]
                ]);
            }


            if ($request->is($admin_path . '/' . $modul->name . '/category/*/edit')) {
                $title = 'Edit Kategori ' . $modul->title;
                config([
                    'modules.current' => [
                        'post_type' => $modul->name,
                        'title_crud' => $title,
                    ]
                ]);
            }
            if ($request->is($admin_path . '/' . $modul->name . '/category/create')) {
                $title = 'Tambah Kategori ' . $modul->title;
                config([
                    'modules.current' => [
                        'post_type' => $modul->name,
                        'title_crud' => $title,
                    ]
                ]);
            }
            if ($request->is($admin_path . '/' . $modul->name . '/category')) {
                $title = 'Kategori ' . $modul->title;
                config([
                    'modules.current' => [
                        'post_type' => $modul->name,
                        'title_crud' =>  $title,
                    ]
                ]);
            }

            if ($request->is($admin_path . '/' . $modul->name . '/create')) {

                config([
                    'modules.current' => [
                        'post_type' => $modul->name,
                        'title_crud' => 'Tambah ' . $modul->title,
                    ]
                ]);
            }
        }
        $response = $next($request);
        if ($response->headers->get('Content-Type') == 'text/html; charset=UTF-8') {
            $content = $response->getContent();
            $content = preg_replace_callback('/<img\s+([^>]*?)src=["\']([^"\']*?)["\']([^>]*?)>/', function ($matches) {
                $attributes = $matches[1] . 'data-src="' . $matches[2] . '" ' . $matches[3];
                if (strpos($attributes, 'class="') !== false) {
                    $attributes = preg_replace('/class=["\']([^"\']*?)["\']/', 'class="$1 lazyload" ', $attributes);
                } else {
                    $attributes .= ' class="lazyload"';
                }
                return '<img ' . $attributes . ' src="/shimmer.gif">';
            }, $content);
            $content = preg_replace('/\s+/', ' ', $content);
            $response->setContent($content);
        }
        return $response;
    }
}
