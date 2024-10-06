<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileGroup extends Model
{
    protected $fillable=[
      'file_id',
      'group_id',
    ];
    use HasFactory;
}
