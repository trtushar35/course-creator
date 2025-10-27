<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class CourseContent extends Model
{
    use HasFactory, SoftDeletes;


    protected $fillable = [
        'module_id',
        'title',
        'type',
        'content',
        'video_source_type',
        'video_url',
        'video_file',
        'video_length',
        'file',
        'link_url',
        'order',
        'status'
    ];

    protected $casts = [
        'order' => 'integer'
    ];

    protected $appends = [
        'video_file_url',
        'file_url'
    ];

    public function module()
    {
        return $this->belongsTo(CourseModule::class, 'module_id');
    }

    public function isVideo()
    {
        return $this->type === 'video';
    }

    public function isText()
    {
        return $this->type === 'text';
    }

    public function isImage()
    {
        return $this->type === 'image';
    }

    public function isDocument()
    {
        return $this->type === 'document';
    }

    public function isLink()
    {
        return $this->type === 'link';
    }

    public function getVideoSourceAttribute()
    {
        return $this->isVideo() ? $this->video_source_type : null;
    }

    public function getVideoFileUrlAttribute()
    {
        return $this->video_file ? Storage::url($this->video_file) : null;
    }

    public function getFileUrlAttribute()
    {
        return $this->file ? Storage::url($this->file) : null;
    }

    public function getYoutubeId()
    {
        if (!$this->isVideo() || $this->video_source_type !== 'youtube' || !$this->video_url) {
            return null;
        }

        // Match various YouTube URL formats
        $patterns = [
            '/youtube\.com\/watch\?v=([^&]+)/',           
            '/youtube\.com\/embed\/([^?]+)/',         
            '/youtu\.be\/([^?]+)/',                    
            '/youtube\.com\/v\/([^?]+)/',        
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $this->video_url, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }

    public function getVideoThumbnailAttribute()
    {
        if (!$this->isVideo()) {
            return null;
        }

        if ($this->video_source_type === 'youtube') {
            $videoId = $this->getYoutubeId();
            return $videoId ? "https://img.youtube.com/vi/{$videoId}/maxresdefault.jpg" : null;
        }

        return null;
    }

    public function getEmbedUrl()
    {
        if (!$this->isVideo()) {
            return null;
        }

        switch ($this->video_source_type) {
            case 'youtube':
                $videoId = $this->getYoutubeId();
                return $videoId ? "https://www.youtube.com/embed/{$videoId}" : null;

            case 'upload':
                return $this->video_file_url;

            default:
                return null;
        }
    }

    public function getFormattedVideoLength()
    {
        if (!$this->isVideo() || !$this->video_length) {
            return null;
        }

        return $this->video_length;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}