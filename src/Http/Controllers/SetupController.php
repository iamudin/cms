<?php
namespace Udiko\Cms\Http\Controllers;

use Exception;
use \Udiko\Cms\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class SetupController extends Controller
{

    public function index(Request $request)
    {
        /*if (db_connected()) {
            $this->generate_dummy_content();
            regenerate_cache();
            recache_option();
            clear_route();
        }*/
        if($request->isMethod('post')){
            if(!session('dbcredential')){
                $dbcredential = $request->validate([
                    'db_host'=> 'required|string',
                    'db_username'=> 'required|string',
                    'db_database'=> 'required|string',
                ]);
                if($this->checkConnection($request->db_host,$request->db_username,$request->db_password,$request->db_database)){
                    Session::put('dbcredential',$dbcredential);
                    return back();
                }else{
                    return back()->with('danger','DB Connection Failure!');
                }
            }else{
                $usercredential = $request->validate([
                    'username'=> 'required|string|regex:/^[a-zA-Z\p{P}]+$/u',
                    'email'=> 'required|string',
                    'password'=> 'required|confirmed',
                ]);
                $option = $request->validate([
                    'site_title'=> 'required|string',
                    'site_description'=> 'required|string',
                ]);
                Session::put('usercredential',$usercredential);
                return back();

            }



        }
        return view('cms::install.index');

    }
    public function checkConnection($host,$username,$password,$db)
    {
        $host = $host;
        $database = $db;
        $username = $username;
        $password = $password ?? '';

        config([
            'database.connections.custom' => [
                'driver' => 'mysql',
                'host' => $host,
                'database' => $database,
                'username' => $username,
                'password' => $password,
            ],
        ]);

        try {
            DB::connection('custom')->getPdo();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    function generate_dummy_content()
    {
        $data = array('username' => 'admin', 'password' => bcrypt('admin'),'host'=>env('APP_URL'), 'email' => 'admin@email.com', 'status' => 'active', 'slug' => 'admin-web', 'name' => 'Admin Web', 'url' => 'author/admin-web', 'photo' => null, 'level' => 'admin');
        $id = User::UpdateOrcreate(['username' => 'admin'], $data);
        $a = 0;
        while ($a <= 50):
            $id->posts()->updateOrcreate(
                [
                    'title' => $title = fake()->sentence,
                    'slug' => $slug = str()->slug($title),
                    'content' => fake()->paragraph,
                    'media' => null,
                    'url' => 'berita/' . $slug,
                    'status' => 'publish',
                    'type' => 'berita',
                ]
            );
            $a++;

        endwhile;
        //create menu
        $id->posts()->updateOrcreate(
            [
                'title' => $title = 'Header',
                'slug' => $slug = str()->slug($title),
                'status' => 'publish',
                'type' => 'menu',
                'data_loop' => array(
                    ['menu_id' => 'm1', 'menu_parent' => 0,  'menu_name' => 'Profil', 'menu_description' => null, 'menu_link' => '#', 'menu_icon' => null],
                    ['menu_id' => 'm2', 'menu_parent' => 'm1',  'menu_name' => 'Visi Misi', 'menu_description' => null, 'menu_link' => '#', 'menu_icon' => null],
                    ['menu_id' => 'm3', 'menu_parent' => 'm1',  'menu_name' => 'Sejarah', 'menu_description' => null, 'menu_link' => '#', 'menu_icon' => null],
                    ['menu_id' => 'm4', 'menu_parent' => 0, 'menu_name' => 'Publikasi', 'menu_description' => null, 'menu_link' => '#', 'menu_icon' => null],
                    ['menu_id' => 'm5', 'menu_parent' => 'm4',  'menu_name' => 'Berita', 'menu_description' => null, 'menu_link' => '#', 'menu_icon' => null],
                    ['menu_id' => 'm6', 'menu_parent' => 'm4',  'menu_name' => 'Agenda', 'menu_description' => null, 'menu_link' => '#', 'menu_icon' => null]
                ),
            ]
        );

        $option = array(
            ['name' => 'site_maintenance', 'value' => 'Y', 'autoload' => 1],
            ['name' => 'post_perpage', 'value' => 10, 'autoload' => 1],
            ['name' => 'site_title', 'value' => 'Your Website Official', 'autoload' => 1],
            ['name' => 'template', 'value' => 'default', 'autoload' => 1],
            ['name' => 'admin_path', 'value' => 'panel', 'autoload' => 1],
            ['name' => 'logo', 'value' => 'noimage.webp', 'autoload' => 1],
            ['name' => 'favicon', 'value' => 'noimage.webp', 'autoload' => 1],
            ['name' => 'site_url', 'value' => env('APP_URL'), 'autoload' => 1],
            ['name' => 'site_keyword', 'value' => 'Web, Official, New', 'autoload' => 1],
            ['name' => 'site_description', 'value' => 'My Offical Web', 'autoload' => 1],
            ['name' => 'address', 'value' => 'Anggrek Streen, 2', 'autoload' => 1],
            ['name' => 'phone', 'value' => '123456789', 'autoload' => 1],
            ['name' => 'email', 'value' => 'your@email.com', 'autoload' => 1],
            ['name' => 'fax', 'value' => '123456789', 'autoload' => 1],
            ['name' => 'latitude', 'value' => null, 'autoload' => 1],
            ['name' => 'longitude', 'value' => null, 'autoload' => 1],
            ['name' => 'facebook', 'value' => 'https://fb.com/yourcompany', 'autoload' => 1],
            ['name' => 'youtube', 'value' => 'https://youtube.com/@yourchannel', 'autoload' => 1],
            ['name' => 'instagram', 'value' => null, 'autoload' => 1],
            ['name' => 'comment_status', 'value' => 0, 'autoload' => 1],
            ['name' => 'home_page', 'value' => 'default', 'autoload' => 1],
            ['name' => 'preview', 'value' => 'default', 'autoload' => 1],
            ['name' => 'icon', 'value' => 'noimage.webp', 'autoload' => 1],
        );


        foreach ($option as $row) {
            \Udiko\Cms\Models\Option::updateOrCreate([
                'name'=>$row['name']],['value'=>$row['value'],'autoload'=>$row['autoload']]);
        }
        return true;
    }
}
