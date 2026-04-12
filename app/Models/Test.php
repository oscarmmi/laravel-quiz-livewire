<?php

namespace App\Models;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Test extends Model
{
    use HasFactory, Searchable, SoftDeletes;

    protected array $searchable = ['user.name', 'user.email', 'quiz.title'];

    protected $fillable = [
        'user_id',
        'quiz_id',
        'result',
        'ip_address',
        'time_spent',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'answers', 'test_id', 'question_id');
    }
}
