<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Social extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    public $timestamps = true;
    public $incrementing = false;
    protected $table = 'socials';
}
