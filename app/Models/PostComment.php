<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostComment extends Model
{
    use HasFactory;

    protected $fillable = ['user', 'post', 'type', 'created_at', 'updated_at'];
    public $timestamps = true;
    public $incrementing = false;
    protected $table = 'post_comments';
    protected $primaryKey = ['user', 'post'];
}
