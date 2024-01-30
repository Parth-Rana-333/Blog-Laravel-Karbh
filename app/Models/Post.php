<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;

class Post extends Model
{
    use HasFactory, SoftDeletes, Sluggable;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'content',
    ];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => ['title', 'id'],
                'separator' => '%'
            ],
        ];
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category() {
        return $this->belongsToMany(Category::class);
    }

    public function tags() {
        return $this->hasMany(Tag::class, 'post_id');
    }

    public function comments() {
        return $this->hasMany(Comment::class);
    }

    public function image() {
        return $this->morphOne(PostImage::class, 'imageable');
    }
}
