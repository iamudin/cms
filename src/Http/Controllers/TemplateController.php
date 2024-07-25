<?php
namespace Udiko\Cms\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class TemplateController extends Controller implements HasMiddleware
{
    public static function middleware(): array {
        return [
            new Middleware('auth')
        ];
    }
    public function index()
    {
        return to_route('dashboard');
        return view('cms::categories.index');
    }
}

