<?php

namespace Udiko\Cms\Middleware;
use Closure;
use Illuminate\Http\Request;
use Udiko\Cms\Models\Visitor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\RateLimiter;

class Web
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
        if(!config('modules.installed')){
            if(env('DB_CONNECTION')!='mysql'){
                rewrite_env(['DB_CONNECTION'=>'mysql']);
            }

                return redirect()->route('install');
        }
        if (strpos($request->getRequestUri(), 'index.php') !== false || $request->getHost()!=str_replace('http://','',config('app.url'))) {
            return redirect( config('app.url') . str_replace('/index.php', '', $request->getRequestUri()));
        }

        $modules = collect(get_module())->where('name', '!=', 'halaman')->where('public', true);
        foreach ($modules as $modul) {
            $attr['post_type'] = $modul->name;

            if ($modul->web->index && $request->is($modul->name)) {
                $attr['detail_visited'] = false;
                $attr['view_type'] = 'index';
                $attr['view_path'] = $modul->name . '.index';
                config([
                    'modules.current' => $attr
                ]);
            }
            if ($modul->web->detail && request()->is($modul->name . '/*')) {
                $attr['detail_visited'] = true;
                $attr['view_type'] = 'detail';
                $attr['view_path'] = $modul->name . '.detail';
                config([
                    'modules.current' => $attr
                ]);
            }
            if ($modul->form->category && request()->is($modul->name . '/category/*')) {
                $attr['detail_visited'] = false;
                $attr['view_type'] = 'category';
                $attr['view_path'] = $modul->name . '.category';
                config([
                    'modules.current' => $attr
                ]);
            }

            if ($modul->web->archive && (request()->is($modul->name . '/archive') || request()->is($modul->name . '/archive/*') || request()->is($modul->name . '/archive/*/*') || request()->is($modul->name . '/archive/*/*/*'))) {
                $attr['detail_visited'] = false;
                $attr['view_type'] = 'archive';
                $attr['view_path'] = $modul->name . '.archive';
                config([
                    'modules.current' => $attr
                ]);
            }
            if ($modul->form->post_parent && (request()->is($modul->name . '/' . $modul->form->post_parent[1]) || request()->is($modul->name . '/' . $modul->form->post_parent[1] . '/*'))) {
                $attr['detail_visited'] = false;
                $attr['view_type'] = 'post_parent';
                $attr['view_path'] = $modul->name . '.post_parent';
                config([
                    'modules.current' => $attr
                ]);
            }
        }


        if (request()->is('*') && !in_array($request->segment(1), $modules->pluck('name')->toArray())) {
            $attr['post_type'] = 'halaman';
            $attr['detail_visited'] = true;
            $attr['view_type'] = 'detail';
            $attr['view_path'] = 'halaman.detail';
            config([
                'modules.current' => $attr
            ]);
        }
        if (request()->is('search/*')) {
            $attr['post_type'] = null;
            $attr['detail_visited'] = false;
            $attr['view_type'] = 'search';
            $attr['view_path'] = 'search';
            config([
                'modules.current' => $attr
            ]);
        }
        if (request()->is('tags/*')) {
            $attr['post_type'] = null;
            $attr['detail_visited'] = false;
            $attr['view_type'] = 'tags';
            $attr['view_path'] = 'tags';
            config([
                'modules.current' => $attr
            ]);
        }
        if (request()->is('/')) {
            $attr['post_type'] = null;
            $attr['detail_visited'] = false;
            $attr['view_type'] = 'home';
            $attr['view_path'] = 'home';
            config([
                'modules.current' => $attr
            ]);
        }
        $response = $next($request);
        $this->processVisitorData();
        if (get_option('site_maintenance') == 'Y' && !Auth::check()) {
            return undermaintenance();
        }
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
        if ($response->headers->get('Content-Type') == 'text/html; charset=UTF-8') {
            $content = $response->getContent();
            // Tambahkan atribut loading="lazy" ke semua tag <img>

            // Gantikan semua src="" dengan data-src="" dan tambahkan atribut loading="lazy" dan class lazyload
            $content = preg_replace_callback('/<img\s+([^>]*?)src=["\']([^"\']*?)["\']([^>]*?)>/', function ($matches) {
                $attributes = $matches[1] . 'data-src="' . $matches[2] . '" ' . $matches[3];
                if (strpos($attributes, 'class="') !== false) {
                    $attributes = preg_replace('/class=["\']([^"\']*?)["\']/', 'class="$1 lazyload"', $attributes);
                } else {
                    $attributes .= ' class="lazyload"';
                }
                return '<img ' . $attributes . ' src="/shimmer.gif">';
            }, $content);
            $content = preg_replace('/\s+/', ' ', $content);
            $response->setContent($content);
        }
        $this->securityHeaders($response);
        return $response;
    }
    public function processVisitorData()
    {

        if (!Cache::has('visit_to_db')) {
            $cacheKey = 'visitor_sorted';
            $visitorDataList = Cache::pull($cacheKey, []);
            foreach ($visitorDataList as $data) {
                $visitorData = $data;
                if (is_array($data)) {
                    Visitor::create($visitorData);
                }
            }

            Cache::put('visit_to_db', true, now()->addMinutes(1));
        }
    }
    function securityHeaders($response){
            $response->headers->set('X-Content-Type-Options', 'nosniff');

        if(get_option('frame_embed') && get_option('frame_embed')=='Y'){
        // Set X-Frame-Options Header
        $response->headers->set('X-Frame-Options', 'DENY');
         }

        // Set X-XSS-Protection Header
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Set Content-Security-Policy Header
        $response->headers->set('Content-Security-Policy', "'unsafe-eval';");


    }
}
