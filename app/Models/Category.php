<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    protected static function booted()
    {
        static::created(function ($category) {
            Cache::forget('categories.id_name');
        });

        static::updated(function ($category) {
            Cache::forget('categories.id_name');
        });

        static::deleted(function ($category) {
            Cache::forget('categories.id_name');
        });
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class);
    }
}
