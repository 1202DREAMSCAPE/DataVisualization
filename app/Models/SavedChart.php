<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavedChart extends Model
{
    protected $fillable = ['title', 'chart_data', 'user_id'];
    
    protected $casts = [
        'chart_data' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}