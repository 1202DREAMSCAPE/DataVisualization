<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileRecord extends Model
{
    protected $fillable = ['filename', 'path', 'headers', 'preview_data'];

    protected $casts = [
        'headers' => 'array',
        'preview_data' => 'array',
    ];
}
