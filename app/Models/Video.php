<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = ['title', 'description', 'video_path', 'duration', 'category_id', 'part_number'];

    public function progress()
    {
        return $this->hasMany(VideoProgress::class);
    }

    public function category()
    {
        return $this->belongsTo(VideoCategory::class, 'category_id');
    }
}
