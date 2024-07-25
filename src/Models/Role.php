<?php
namespace Udiko\Cms\Models;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    public $timestamps = false;
    protected $fillable = ['level', 'module','action'];
}
