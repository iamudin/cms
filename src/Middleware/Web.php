<?php
namespace Udiko\Cms\Middleware;
use Closure;
use Illuminate\Http\Request;
use Udiko\Cms\Models\Visitor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;


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

        if (strpos($request->getRequestUri(), 'index.php') !== false || $request->getHost()!=str_replace('http://','',config('app.url'))) {
            return redirect( config('app.url') . str_replace('/index.php', '', $request->getRequestUri()));
        }
        $response = $next($request);
        $this->processVisitorData();
        if (get_option('site_maintenance') == 'Y' && !Auth::check()) {
            return undermaintenance();
        }

        if (get_option('forbidden_keyword') && str()->contains(str($request->fullUrl())->lower(), explode(",", str_replace(" ", "", get_option('forbidden_keyword') ?? '')))) {
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
