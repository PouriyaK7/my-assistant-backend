<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskGroupUser extends Model
{
    use HasFactory;

    protected $fillable = ['task_group', 'user'];
    public $timestamps = true;
    public $incrementing = false;
    protected $table = 'task_group_users';
    protected $primaryKey = ['task_group', 'user'];
}
