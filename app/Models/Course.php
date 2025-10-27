<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'summary',
        'level',
        'category',
        'price',
        'feature_image',
        'feature_video',
        'status'
    ];

    protected $casts = [
        'price' => 'decimal:2'
    ];

    protected $appends = [
        'feature_image_url',
        'feature_video_url',
        'is_active'
    ];

    public function modules()
    {
        return $this->hasMany(CourseModule::class);
    }

    public function getFormattedPriceAttribute()
    {
        return '৳' . number_format($this->price, 2);
    }

    public function getIsActiveAttribute()
    {
        return $this->status === 'Active';
    }

    public function getFeatureImageUrlAttribute()
    {
        return $this->feature_image ? Storage::url($this->feature_image) : null;
    }

    public function getFeatureVideoUrlAttribute()
    {
        return $this->feature_video ? Storage::url($this->feature_video) : null;
    }

    public function isFree()
    {
        return $this->price == 0;
    }
}