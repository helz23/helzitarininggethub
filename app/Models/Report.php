<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'operation_type',
        'user_id',
        'admin_id',
        'file_id'
    ];
    use HasFactory;
}
