<?php
namespace Udiko\Cms\Http\Controllers;
use Udiko\Cms\Models\Post;
use Illuminate\Http\Request;
use Udiko\Cms\Models\Option;
use Udiko\Cms\Models\Visitor;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

use function PHPUnit\Framework\directoryExists;

class PanelController extends Controller implements HasMiddleware
{
    public static function middleware(): array {
        return [
            new Middleware('auth')
        ];
    }

    protected function toDashboard($request){
        if(!$request->segment(2))
        return to_route('panel.dashboard')->send();
    }
    function index(Request $request){
        $this->toDashboard($request);
        $user = $request->user();
        $da = array();
        for ($i=0; $i<=6; $i++) {
          array_push($da,date("Y-m-d", strtotime("-".$i." days")));
        }
        $weekago = json_decode(json_encode(collect($da)->sort()),true);
        $type = collect(get_module())->where('name','!=','media')->pluck('name')->toArray();
        $lastpublish = Post::select(['created_at','id','user_id','status','type','title'])->with('user')->whereIn('type',$type)->latest('created_at')->limit(5)->get();
        return view('cms::backend.dashboard',[
            'latest'=>$lastpublish,
            'weekago'=>$weekago,
            'type'=>$user->isAdmin() ? collect(get_module()) : collect(get_module())->whereIn('name', $user->get_modules->pluck('module')->toArray())->where('public',true),
            'posts'=>$user->posts,
            'visitor'=> new Visitor,
        ]);
    }
    public function visitor(Request $request)
    {
        $data = Visitor::query()->latest('created_at');
        return Datatables::of($data)
            ->addIndexColumn()
            ->filter(function ($instance) use ($request) {

                if ($time = $request->timevisit) {
                    $instance->whereDate('created_at',$time);
                }
            }
            )
            ->addColumn('created_at', function ($row) {
                return '<code>' . $row->created_at->diffForHumans() . '</code>';
            })
            ->addColumn('ip_location', function ($row) {
                $city = json_decode($row->ip_location)->city ?? null;
                $country = json_decode($row->ip_location)->country ?? null;
                $region = json_decode($row->ip_location)->region ?? null;
                $code = json_decode($row->ip_location)->countryCode ?? null;
                $ipinfo = $row->ip_location ? $region . ', ' . $city . '<br><img style="display:inline" height="10" src="' . thumb('backend/images/flags/' . str($code)->upper() . '.svg') . '"> ' . $country : 'N/A';
                return '<span class="badge badge-info">' . $row->ip . '</span><br><small>' . $ipinfo . '</small>';
            })
            ->addColumn('reference', function ($row) {
                return str($row->reference)->limit(70);
            })
            ->addColumn('page', function ($row) {
                return '<a href="'.$row->page.'">'.str($row->page)->limit( 70).'</a>';
            })
            ->rawColumns(['created_at', 'ip_location', 'reference', 'page'])
            ->toJson();
    }

    public function setting(Request $request, Option $option){
        admin_only();
        $data['web_type'] = config('modules.config.web_type');
        $data['option'] = array_merge(config('modules.config.option') ?? [], [
            ['Icon','file'],
            ['Nama','text'],
            ['Alamat','text'],
            ['Telepon','text'],
            ['Whatsapp','text'],
            ['Fax','text'],
            ['Email','text'],
            ['Latitude','text'],
            ['Longitude','text'],
            ['Link Maps','text'],
            ['Facebook','text'],
            ['Youtube','text'],
            ['Instagram','text'],
            ['Twitter','text'],
        ]);
        $data['site_attribute'] = array(
            ['Alamat Situs Web', 'site_url', 'text'],
            ['Nama Situs Web', 'site_title', 'text'],
            ['Deskripsi Situs Web', 'site_description', 'text'],
            ['SEO Meta Keyword', 'site_meta_keyword', 'text'],
            ['SEO Meta Description', 'site_meta_description', 'text'],
            ['Google Analytics Code', 'google_analytics_code', 'text'],
            ['Postingan Perhalaman', 'post_perpage', 'number'],
            ['Logo', 'logo', 'file'],
            ['Favicon', 'favicon', 'file'],
            ['Preview', 'preview', 'file'],
        );
        $data['shortcut'] = array(
            ['Control + F5', 'ctrl_f5'],
            ['Control + U', 'ctrl_u'],
            ['Control + R', 'ctrl_r'],
            ['Control + P', 'ctrl_p'],
            ['Control + S', 'ctrl_s'],
            ['Right Click', 'right_click'],
            ['Frame Embed', 'frame_embed'],
        );
        $data['security'] = array(

            ['Block IP', '0.0.0.0,0.0.1.0,..,..'],
            ['Forbidden Keyword', 'Judi Online, Gacor, xxx, other'],
            ['Forbidden Redirect', 'Eg: https://yourpage.url or other'],
            ['Time Limit Login', 'default 10 times'],
            ['Time Limit Reload', 'default 10 times'],
            ['Limit Duration', 'in minute default 1 minute'],
            ['Roles', 'operator,editor,publisher']);

        $data['home_page'] = Post::whereType('halaman')->whereStatus('publish')->whereMime('html')->select('id', 'title')->get();
        if ($request->isMethod('POST')) {

            if ($value = $request->home_page) {
                Option::updateOrCreate(['name' => 'home_page'], ['value' => $value]);
            }
            foreach ($data['option'] as $row) {
                $key = _us($row[0]);

                    if($row[1]=='file'){
                        $request->validate([$key=>'nullable|file|mimetypes:'.allow_mime()]);
                        $fid = $option->updateOrCreate(['name'=>$key],['value'=>get_option($key),'autoload'=>1]);
                        if($request->hasFile($key)){
                        $fid->update(['value'=> upload_media($fid,$request->file($key),$key,'option')]);
                        }
                    }else{
                        $value = $request->$key;
                        $fid = $option->updateOrCreate(['name'=>$key],['value'=>strip_tags($value),'autoload'=>1]);
                    }

            }
            foreach (array_merge($data['security'],[ ['Site Maintenance', '']]) as $row) {
                $key = _us($row[0]);
                $value = $request->$key ?? null;

                    if($key=='block_ip'){
                        $request->validate(['block_ip'=>'nullable|ip']);
                    }

                    $option->updateOrCreate(['name'=> $key],['value' => strip_tags($value), 'autoload' => 1]);

        }

            foreach ($data['site_attribute'] as $row) {
                $key = $row[1];
                if ($row[2] == 'file') {
                    $request->validate([$key=>'nullable|file|mimetypes:'.allow_mime()]);

                    $fid = $option->updateOrCreate(['name'=>$key],['value'=>get_option($key),'autoload'=>1]);
                    if ($value = $request->hasFile($key)) {
                     $fid->update(['value'=>upload_media($fid,$request->file($key),$key,'option')]);
                    }
                } else {
                   $value = $request->$key;
                   $option->updateOrCreate(['name' => $key],['value' => strip_tags($value), 'autoload' => 1]);

                }
            }
            foreach ($data['shortcut'] as $row) {
                   $key = $row[1];
                   $value = $request->$key;
                   $option->updateOrCreate(['name' => $key],['value' => strip_tags($value), 'autoload' => 1]);

            }
            if($val = $request->admin_path){
                if(in_array($val,['admin','login','adminpanel','webadmin','masuk','sipanel'])){
                    return back()->with('danger','Login path dengan kata kunci "'.$val.'" tidak diizinkan');
                }
                $option->updateOrCreate(['name'=>'admin_path'],['value'=>$val,'autoload'=>1]);
                if($val!=get_option('admin_path')){
                    recache_option();
                    Artisan::call('route:cache');
                    return to_route('setting')->with('success', 'Berhasil disimpan');
                }
            }
            recache_option();
            return  back()->with('success', 'Berhasil disimpan');
        }
        return view('cms::backend.setting',$data);

    }
public function appearance(Request $request){
admin_only();
return view('cms::backend.appearance');
}

public function editorTemplate(Request $request){
    admin_only();
    $path = resource_path('views/template/'.template());
    if(!file_exists($path.'/home.blade.php')){
        $myfile = fopen($path.'/home.blade.php', "w") or die("Unable to open file!");
        fwrite($myfile, '<h1>You Script Here</h1>');
        fclose($myfile);
    }
    $file = $request->edit ?? '/home.blade.php';

    if($file=='/styles.css'){
        $file = '/styles.css';
        $path = public_path('template/'.template());
        if(!file_exists($path.$file)){
            $myfile = fopen($path.$file, "w") or die("Unable to open file!");
            fwrite($myfile, 'body,html { }');
            fclose($myfile);
        }
    }elseif($file=='/scripts.js'){
        $file = '/scripts.js';
        $path = public_path('template/'.template());
        if(!file_exists($path.$file)){
            $myfile = fopen($path.$file, "w") or die("Unable to open file!");
            fwrite($myfile, '$( document ).ready(function() {
        console.log( "document loaded" );
    });');
            fclose($myfile);
        }
    }else{
    }
    if($request->isMethod('post')){
        switch($request->type){
            case 'create_dir':
                $dir = str($request->dirname)->slug();
                if(!is_dir($path.'/'.$dir)){
                    mkdir($path.'/'.$dir);
                    return response()->json(['msg'=>'success']);
                }
            break;
            case 'create_file':
                $filepath = $request->filepath ?? null;
                $filename = str($request->filename)->slug().'.blade.php';
                if(!file_exists($path.'/'.$filename)){
                    $myfile = fopen($path.$filepath.'/'.$filename, "w") or die("Unable to open file!");
                    fwrite($myfile, '<h1>You Script Here</h1>');
                    fclose($myfile);
                    return response()->json(['msg'=>'success']);
                }
            break;
            case 'delete_file':
                $filename = $request->filename;
                if(strpos($filename,'modules.blade.php') !==false){
                    return to_route('appearance')->with('danger','Action denied!');
                }
                if(file_exists($path.$filename)){
                   unlink($path.$filename);
                return response()->json(['msg'=>'success']);
                }
            break;
            case 'change_file':
            if($content = $request->file_src){
                $data = $content;
                $file = $path  . $file;
                $myfile = fopen($file, "w") or die("Unable to open file!");
                fwrite($myfile, $data);
                fclose($myfile);
                if(strpos($file,'modules.blade.php') !==false){
                Artisan::call('optimize');
                }
            }

            return back()->with('success','Perubahan Tersimpan');
            break;
        }

    }
    $src = $file && file_exists($path.$file) && is_file($path.$file) ? (file_get_contents($path.$file) ? file_get_contents($path.$file) : 'Here You Script') : null;
    if(!$src){
        return to_route('appearance.editor')->with('danger','Source tidak ditemukan!');
    }
   $type = match(pathinfo($file,PATHINFO_EXTENSION)){
    'php' => 'application/x-httpd-php',
    'css'=> 'text/css',
    'js'=> 'text/javascript'
   };

    return view('cms::backend.editortemplate',['view'=>$src,'type'=>$type]);
    }
}
