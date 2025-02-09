<?php
namespace Udiko\Cms\Http\Controllers;

use App\Http\Controllers\Controller;
use Udiko\Cms\Models\Tag;
use Udiko\Cms\Models\Post;
use Udiko\Cms\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Udiko\Cms\Models\Category;
use Udiko\Cms\Http\Controllers\MediaController;
use Illuminate\Support\Facades\Route;
use Carbon\Carbon;
use PhpParser\Node\Stmt\Break_;

class PostController extends Controller implements HasMiddleware
{

    public static function middleware(): array {
        return [
            new Middleware('auth')
        ];
    }
    public function index(Request $request)
    {
        $request->user()->hasRole(get_post_type(),__FUNCTION__);
        return view('cms::backend.posts.index');
    }

    public function comments(Request $req)
    {
        if ($req->status) {
            $cek = Comment::where('id', $req->status)->first();
            if ($cek->status == 1) {
                Comment::where('id', $req->status)->update(['status' => 0]);
            } else {
                Comment::where('id', $req->status)->update(['status' => 1]);
            }
            return back()->with('success', 'Success');

        }
        $data['comments'] = Comment::withwherehas('post')->orderBy('created_at', 'desc')->get();

        return view('views::backend.comments', $data);
    }
    public function summer_file_upload(Request $req)
    {
        if ($files = $req->file('file')) {
            $id = $req->id;
            if (!is_dir(public_path('upload/' . get_post_type()))) {
                mkdir(public_path('upload/' . get_post_type()));
            }
            $date = Post::wherePostId($id)->first()->created_at;
            $per = array($this->dirpost($date)->y, $this->dirpost($date)->y . '/' . $id);
            foreach ($per as $value) {
                if (!is_dir(public_path('upload/' . get_post_type() . '/' . $value))) {
                    mkdir(public_path('upload/' . get_post_type() . '/' . $value));
                }
            }
            $dir = 'upload/' . get_post_type() . '/' . $this->dirpost($date)->y . '/' . $id . '/';
            $path = public_path($dir);
            $type = allowed_ext($req->file->getClientOriginalExtension());
            $mime = $req->file->getClientMimeType();
            abort_if(!allow_mime($mime), '403');
            $namewithextension = $req->file->getClientOriginalName(); //Name with extension 'filename.jpg'
            $fname = explode('.', $namewithextension)[0];
            $name = Str::slug(now() . ' ' . $fname) . '.' . $req->file->getClientOriginalExtension();
            if ($type):
                if ($type == 'image'):
                    $img = Image::make($files);
                    $img->resize(null, 1200, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                    $img = $img->save($path . $name);
                    $filename = url($dir . $name);
                    $namepath = $dir . $name;

                else:
                    $req->file->move($path, $name);
                    $filename = url($dir . $name);
                    $namepath = $dir . $name;
                endif;
                $this->media_store($id, $mime, $namepath, $name, $fname);
                return response()->json(['status' => true, 'msg' => 'Berhasil diupload', 'filename' => $filename]);
            else:
                return response()->json(['status' => false, 'msg' => 'Format file tidak didukung', 'filename' => null]);

            endif;
        }
    }

public function create(Request $request){
$request->user()->hasRole(get_post_type(),__FUNCTION__);
    $newpost = $request->user()->posts()->create([
        'type' => get_post_type(),
        'url' => get_post_type() . '/' . rand(),
        'status' => 'draft',
    ]);
    return to_route(get_post_type() . '.edit', $newpost->id);
}

public function edit(Request $request, Post $post,$id){
abort_if(!is_numeric($id),'403');
$request->user()->hasRole(get_post_type(),'update');
$module = current_module();

$data = $request->user()->isAdmin() ? $post->with('category','user','medias')->whereType(get_post_type())->find($id) : $post->whereBelongsTo($request->user())->with('category','user','medias')->whereType(get_post_type())->find($id);
if (!$data) {
    return redirect(admin_url(get_post_type()))->with('danger', get_module_info('title') . ' Tidak Ditemukan');
}
$field = (!empty($data->data_field)) ? collect($data->data_field) : [];
$looping_data = $data->data_loop ? (collect($module->form->looping_data)->where([0], 'Sort')->first() ? collect($data->data_loop)->sortBy('sort') : $data->data_loop) : [];
return view('cms::backend.posts.form',[
        'post'=>$data,
        'looping_data'=>$looping_data,
        'field'=>$field,
        'module'=> $module,
        'tags'=> Tag::get(),
        'category'=> $module->form->category ? Category::query()->whereType(get_post_type())->select('id','name')->orderBy('sort')->get() : null
]);
}
public function destroy(Request $request,Post $post){
    $request->user()->hasRole(get_post_type(),'delete');
    if($post->medias->count()){
        Post::whereParentId($post->id)->whereParentType('post')->whereType('media')->delete();
        recache_media();
    }
    $post->delete();
    switch(get_post_type()){
        case 'banner':
        recache_banner();
        break;
        case 'menu':
        recache_menu();
        break;
        default:
        regenerate_cache();
        break;
    }
}
public function show(Post $post,$id){
abort_if(!is_numeric($id),'403');

    $data = $post->with('category','user','tags')->find($id);
    if (!$data || $data->type != get_post_type()) {
        return redirect(admin_url(get_post_type()))->with('danger', get_module_info('title') . ' Tidak Ditemukan');
    }
    return $data;
}
public function update(Request $request, Post $post){
    $request->user()->hasRole(get_post_type(),'update');
    $module = current_module();
    if($module->form->custom_field){

    foreach(collect($module->form->custom_field)->whereNotIn([1],['break']) as $row){
        $custom_field[_us($row[0])] = (isset($row[2]) ? 'required' : 'nullable');
    }
    foreach(collect($module->form->custom_field)->whereIn([1],['file']) as $row){
        $k = _us($row[0]);
        if($request->hasFile($k)){
        $request->validate([
           $k =>'nullable|file|mimetypes:'.allow_mime(),
        ]);
        }
    }
}
    $post_field =  [
        'title'=>'required|string|regex:/^[0-9a-zA-Z\s\p{P}\,\(\)]+$/u|min:5|'. Rule::unique('posts')->where('type',$post->type)->whereNull('deleted_at')->ignore($post->id),
        'media'=> 'nullable|file|mimetypes:image/jpeg,image/png',
        'content'=> 'nullable',
        'sort'=> 'nullable|numeric',
        'parent_id'=> 'nullable|exists:posts,id',
        'keyword'=> 'nullable|string|regex:/^[a-zA-Z,]+$/u',
        'description'=> 'nullable|string|regex:/^[a-zA-Z\s\p{P}]+$/u',
        'redirect_to'=> 'nullable|url',
        'category_id'=> 'nullable|string',
        'media_description'=> 'nullable|string|regex:/^[a-zA-Z\s\p{P}]+$/u',
        'pinned'=> 'nullable|in:N,Y',
        'allow_comment'=> 'nullable|in:N,Y',
        'status'=> 'required|string',
        'mime'=> 'nullable|in:html,'.allow_mime(),
    ];

    $data = $request->validate($post_field);
    $data['pinned'] =  isset($request->pinned) ? 'Y': 'N';
    $data['short_content'] =  isset($request->content) && strlen($request->content) > 0 ? str(strip_tags($request->content))->words(25,'...') : null;
    $post->tags()->sync($request->tags, true);
    $data['allow_comment'] =   isset($request->allow_comment) ? 'Y': 'N';
    if($request->mime=='html'){
        make_custom_view($post->id,$request->content);
    }
    if($pp = $module->form->post_parent){
        if($pid=$request->parent_id){
            $custom_field[_us($pp[0])] = Post::find($pid)?->title;

        }
    }
    if($module->form->custom_field){
    foreach (collect($module->form->custom_field)->where([1], '!=', 'break') as $key => $value) {
        $fieldname = _us($value[0]);
        switch ($value[1]) {
            case 'file':
                $custom_field[$fieldname] = $request->hasFile($fieldname) ?
                upload_media($post,$request->file($fieldname),$fieldname,'post') : strip_tags($request->$fieldname);
            break;
            default:
                $custom_field[$fieldname] = strip_tags($request->$fieldname) ?? null;
            break;
        }
    }
}
    if($module->form->custom_field || $module->form->post_parent){
        $data['data_field'] = $custom_field;
    }

    if($request->hasFile('media')){
        $data['media'] = upload_media($post,$request->file('media'),'thumbnail','post');
    }
    if($request->has('tanggal_entry')){
        $timedate = $request->tanggal_entry ?? date('Y-m-d H:i:s');
        $data['created_at'] = $timedate;
    }
    $data['url'] = $post->type!='halaman' ? $post->type.'/'.str($request->title)->slug() : str($request->title)->slug();
    $data['slug'] = str($request->title)->slug();

    // dd($request->all());
    if($looping_data = $module->form->looping_data){
        $datanya = [];
        $jmlh = 0;
    foreach ($looping_data as $y) {
        if ($y[1] != 'file') {
            $r = _us($y[0]);
            $jmlh = ($request->$r) ? count($request->$r) : 0;
        }
    }

    if ($jmlh > 0) {
        for ($i = 0; $i < $jmlh; $i++) {

            foreach ($looping_data as $y) {
                $r = _us($y[0]);
                $as = $request->$r;
                if (isset($as[$i])) {

                    $h[$r] = ($y[1] == 'file') ? (is_file($as[$i]) ?  upload_media($post,$as[$i],$r.$i,'post') : $as[$i]) : strip_tags($as[$i]);
                } else {
                    $h[$r] = null;
                }
            }
            array_push($datanya, $h);
        }
    }
        $data['data_loop'] = $datanya;
        if(get_post_type()=='menu'){

            $fixd = json_decode($request->menu_json, true);
            $mnews = [];
            processMenu($fixd, $datanya, $mnews);
            $data['data_loop'] = $mnews;
        }
    }
        $beforelength = strlen($post);
        $beforestatus = $post->status;
        $beforetitle= $post->title;
        $post->update($data);
        $timequery = query()->whereId($post->id)->first();
        $time['created_at'] =  $beforestatus!='publish' &&  empty($beforetitle) ? now() : $post->created_at;
        $time['updated_at'] =  strlen($timequery) != $beforelength? now() : $post->updated_at;
        query()->whereId($post->id)->update($time);
        $this->recache(get_post_type());
        return back()->with('success',$module->title.' Berhasil diperbarui');
}
public function recache($type){
    regenerate_cache();
    if($type=='menu'){
        recache_menu();
    }
    if($type=='banner'){
        recache_banner();
    }
}
    public function datatable(Request $req)
    {
        $data = $req->user()->isAdmin() ? Post::select(array_merge((new Post)->selected,['data_loop']))->with('user', 'category', 'comments')->withCount('childs')->withCount('visitors')->whereType(get_post_type()) : Post::select((new Post)->selected)->with('user', 'category', 'comments')->withCount('childs')->withCount('visitors')->whereType(get_post_type())->whereBelongsTo($req->user());
        return DataTables::of($data)
            ->addIndexColumn()
            ->order(function ($query) use ($req) {
                if ($req->has('order')) {
                    $columns = $req->columns;
                    foreach ($req->order as $order) {
                        $column = $columns[$order['column']]['data'];
                        $dir = $order['dir'];
                        $query->orderBy($column, $dir);
                    }
                }
            })
            ->addColumn('title', function ($row) {

                $category = current_module()->form->category ? ( !empty($row->category) ? "<i class='fa fa-tag'></i> " . $row->category?->name : "<i class='fa fa-tag'></i> <i class='text-warning'>Uncategorized</i>") : '';
                $label = ($row->allow_comment == 'Y') ? "<i class='fa fa-comments'></i> " . $row->comments->count() : '';
                $custom = ($row->mime == 'html') ? '<i class="text-muted">_HTML</i>' : '';
                $tit = (current_module()->web->detail || current_module()->name == 'media') ? ((!empty($row->title)) ? ($row->status=='publish' ? '<a title="Klik untuk melihat di tampilan web" href="' . url($row->url.'/') . '" target="_blank">' . $row->title . '</a> ' . $custom :$row->title ) : '<i class="text-muted">__Tanpa Judul__</i>') : ((!empty($row->title)) ? $row->title : '<i class="text-muted">__Tanpa Judul__</i>');

                $draft = ($row->status != 'publish') ? "<i class='badge badge-warning'>Draft</i> " : "";

                $pin =  $row->pinned == 'Y' ? '<span class="badge badge-danger"> <i class="fa fa-star"></i> Disematkan</span>&nbsp;':'';

                $b = '<b class="text-primary">' . $tit . '</b><br>';
                $b .= '<small class="text-muted"> ' . $pin . ' <i class="fa fa-user-o"></i> ' . $row->user->name . '  '.$category.' ' . $label . ' ' . $draft . '</small>';
                return $b;
            })
            ->addColumn('created_at', function ($row) {
                return '<small class="badge text-muted">' . date('d-m-Y H:i:s', strtotime($row->created_at)) . '</small>';
            })
            ->addColumn('visitors_count', function ($row) {
                return '<center><small class="badge text-muted"> <i class="fa fa-line-chart"></i> <b>' . $row->visitors_count . '</b></small></center>';
            })
            ->addColumn('updated_at', function ($row) {
                return ($row->updated_at) ? '<small class="badge text-muted">' . date('d-m-Y H:i:s', strtotime($row->updated_at)) . '</small>' : '<small class="badge text-muted">NULL</small>';
            })
            ->addColumn('thumbnail', function ($row) {
                return '<img class="rounded lazyload" src="/shimmer.gif" height="50" width="70" data-src="' . $row->thumbnail . '"/>';
            })
            ->addColumn('data_field', function ($row) {
                $custom = _us( current_module()->datatable->custom_column);
                return ($custom && !empty($row->data_field) && isset($row->data_field[$custom])) ? '<span class="text-muted">' .$row->data_field[$custom] . '</span>' : '<span class="text-muted">__</span>';
            })

            ->addColumn('parents', function ($row) {
                if (current_module()->form->post_parent):
                    $custom = _us(current_module()->form->post_parent[0]);
                    return (!empty($row->data_field) && !empty($row->data_field[$custom])) ? '<span class="text-muted">' . $row->data_field[$custom] . '</span>' : '<span class="text-muted">__</span>';
                else:
                    return '-';
                endif;
            })
            ->addColumn('category', function ($row) {
               return $row->category->name ?? '__';
            })
            // || $row->type!='media' && empty($row->child_count)) || ($row->type == 'menu' && empty($row->data_loop)
            ->addColumn('action', function ($row) {

                $btn = '<div style="text-align:right"><div class="btn-group ">';

                $btn .= current_module()->web->detail && $row->status=='publish' ? '<a target="_blank" href="' .url($row->url.'/').'"  class="btn btn-info btn-sm fa fa-globe"></a>':'';

                $btn .= Route::has($row->type.'.edit') ?'<a href="' . route(get_post_type().'.edit', $row->id).'"  class="btn btn-warning btn-sm fa '.($row->type=='media' ? 'fa-eye' : 'fa-edit').'"></a>':'';
                $btn .= $row->type=='media' ? '<button title="Copy URL media" class="btn btn-sm btn-info fa fa-copy" onclick="copy(\''.route('stream',basename($row->media)).'\')"></button>' : '';
                $btn .= $row->type=='media' && $row->id == $row->parent_id ? '<button title="Hapus Media" class="btn btn-sm btn-danger fa fa-trash" onclick="deleteAlert(\''.basename($row->media).'\')"></button>' : '';


                $btn .= ($row->type != 'media' && Route::has($row->type . '.destroyer') && empty($row->childs_count)) ? ($row->type == 'menu' && !empty($row->data_loop) ? '': '<button onclick="deleteAlert(\''.route($row->type.'.destroyer',$row->id).'\')" class="btn btn-danger btn-sm fa fa-trash-o"></button>' ) :'';
                $btn .= '</div></div>';
                return $btn;
            })
            ->rawColumns(['created_at','category', 'updated_at', 'visitors_count', 'action', 'title', 'data_field', 'parents', 'thumbnail'])
            ->orderColumn('visitors_count', '-visited $1')
            ->orderColumn('updated_at', '-updated_at $1')
            ->orderColumn('created_at', '-created_at $1')
            ->only(['visitors_count', 'action', 'category','title', 'created_at', 'updated_at', 'data_field', 'parents', 'thumbnail'])
            ->filterColumn('title', function ($query, $keyword) {
                $query->whereRaw("CONCAT(posts.title,'-',posts.title) like ?", ["%{$keyword}%"]);
            })
            ->filterColumn('data_field', function ($query, $keyword) {
                $query->whereRaw("CONCAT(posts.data_field,'-',posts.data_field) like ?", ["%{$keyword}%"]);
            })
            ->filterColumn('parents', function ($query, $keyword) {
                $query->whereRaw("CONCAT(posts.data_field,'-',posts.data_field) like ?", ["%{$keyword}%"]);
            })
            ->toJson();
    }

}
