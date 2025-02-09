<?php
namespace Udiko\Cms\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Category extends Model
{
    use SoftDeletes;

    protected $fillable=[
        'type','url','status','name','description','slug','icon','sort'
      ];

    public function posts()
    {
    return $this->hasMany(Post::class)->select((new Post)->selected);
    }
    public function medias()
    {
        return $this->hasMany(Post::class, 'parent_id', 'id')->whereType('media');
    }

}
