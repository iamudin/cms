<?php
namespace Udiko\Cms\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class NoSession extends Controller
{

    function index()
    {

       abort('404');
    }
}
