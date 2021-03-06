<?php

namespace App;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Photo extends Model
{
    use Sluggable;

    protected $fillable = ['url', 'caption', 'title'];

    public static function boot()
    {
        parent::boot();

        Photo::deleting( function($photo) {
            Storage::disk('s3-public')->delete($photo->url);
            Storage::disk('s3-public')->delete($photo->thumb_url);
        });

        Photo::saved(function ($photo) {
            $photo->imageable->touch();
        });
    }

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    public function imageable()
    {
        return $this->morphTo();
    }

    public function getSiblingsAttribute()
    {
        return $this->imageable->photos->reject(function($photo) {
            return $photo->id === $this->id;
        });
    }

    public function getFullUrlAttribute()
    {
        return Storage::disk('s3-public')->url($this->url);
    }

    public function getFullThumbUrlAttribute()
    {
        return Storage::disk('s3-public')->url($this->thumb_url);
    }
}
