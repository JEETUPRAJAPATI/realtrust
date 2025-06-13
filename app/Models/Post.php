<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class Post extends Model
{

    protected $fillable = ['user_id', 'title', 'slug', 'image', 'body', 'view_count', 'status', 'is_approved'];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getImageUrlAttribute()
    {
        if (!empty($this->attributes['image'])) {
            return URL::to(Storage::url('posts/' . $this->attributes['image']));
        }
        return null;
    }
    public function staff()
    {
        return $this->belongsTo(Staff::class, 'user_id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'user_id');
    }
    public function getAuthorAttribute()
    {
        if (isset($this->admin->name)) {
            return [
                'name' => $this->admin->name,
                'image' => isset($this->admin->image)
                    ? URL::to(Storage::url('admin/' . $this->admin->image))
                    : 'default_image.png'
            ];
        } elseif (isset($this->staff->name)) {
            return [
                'name' => $this->staff->name,
                'image' => isset($this->staff->image)
                    ? URL::to(Storage::url('user/' . $this->staff->image))
                    : 'default_image.png'
            ];
        }

        return [
            'name' => 'null',
            'image' => 'default_image.png'
        ];
    }
    public function categories()
    {
        return $this->belongsToMany(Category::class)->withTimestamps();
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public static function archives()
    {
        return static::selectRaw('year(created_at) year, monthname(created_at) month, count(*) published')
            ->groupBy('year', 'month')
            ->orderByRaw('min(created_at) desc')
            ->get()
            ->toArray();
    }
}
