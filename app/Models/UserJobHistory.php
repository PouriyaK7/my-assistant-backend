<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserJobHistory extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    public $timestamps = true;
    public $incrementing = true;
    protected $table = 'user_job_history';
}
