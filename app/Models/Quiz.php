<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quiz extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'published',
        'public',
        'created_by',
        'user_id',
    ];

    protected $casts = [
        'published' => 'boolean',
        'public'    => 'boolean',
        'created_by' => \App\Enums\TestCreatedBy::class,
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopePublic($q)
    {
        return $q->where('public', true);
    }

    public function scopePublished($q)
    {
        return $q->where('published', true);
    }

    public function scopeNotByUser($q)
    {
        return $q->where('user_id', '!=', auth()->id());
    }

    public function scopeAvailableFor($query, $user = null)
    {
        $userId = $user instanceof User ? $user->id : $user;
        $userId ??= auth()->id();

        return $query->where(function ($q) use ($userId) {
            $q->where('created_by', \App\Enums\TestCreatedBy::Admin)
              ->orWhere('user_id', $userId);
        });
    }
}
