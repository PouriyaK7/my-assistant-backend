<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSkill extends Model
{
    use HasFactory;

    protected $fillable = ['user', 'skill'];
    public $timestamps = true;
    public $incrementing = false;
    protected $table = 'user_skills';
    protected $primaryKey = ['user', 'skill'];
}
