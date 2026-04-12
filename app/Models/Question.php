<?php

namespace App\Models;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use HasFactory, Searchable, SoftDeletes;

    protected array $searchable = ['question_text'];

    protected $fillable = [
        'question_text',
        'code_snippet',
        'answer_explanation',
        'more_info_link',
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(Option::class)->inRandomOrder();
    }

    public function quizzes()
    {
        return $this->belongsToMany(Quiz::class);
    }
}
