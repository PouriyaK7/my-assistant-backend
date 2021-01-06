<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NoteParticipator extends Model
{
    use HasFactory;

    protected $fillable = ['user', 'note', 'permission', 'created_at', 'updated_at'];
    public $timestamps = true;
    public $incrementing = false;
    protected $table = 'note_participators';
    protected $primaryKey = ['user', 'note'];
}
