<?php

namespace App;

use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Illuminate\Database\Eloquent\Model;

class Miniature extends Model
{
    use Sluggable, SluggableScopeHelpers;

    protected $fillable = ['label', 'progress'];

    public static function boot()
    {
        parent::boot();

        Miniature::deleting(function($miniature) {
            foreach($miniature->photos as $photo) {
                $photo->delete();
            }
        });

        Miniature::saved(function ($miniature) {
            $miniature->collection->touch();
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
                'source' => 'label'
            ]
        ];
    }

    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }

    public function photos()
    {
        return $this->morphMany(Photo::class, 'imageable');
    }

    public function getFeaturedImageAttribute()
    {
        foreach($this->photos as $photo) {
            return $photo;
        }
        return null;
    }
}
