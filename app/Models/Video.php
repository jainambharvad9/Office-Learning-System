<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = ['title', 'description', 'video_path', 'duration'];

    public function progress()
    {
        return $this->hasMany(VideoProgress::class);
    }
}
