<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavedChart extends Model
{
    protected $fillable = ['title', 'chart_data', 'user_id', 'file_record_id'];
    
    protected $casts = [
        'chart_data' => 'array'
    ];

    public function fileRecord() {
        return $this->belongsTo(FileRecord::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}