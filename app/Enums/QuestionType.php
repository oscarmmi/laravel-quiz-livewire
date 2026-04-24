<?php

namespace App\Enums;

enum QuestionType: string
{
    case SingleLine = 'single-line';
    case MultiLine = 'multi-line';
    case UniqueAnswer = 'unique-answer';
    case MultiAnswer = 'multi-answer';

    public function label(): string
    {
        return match($this) {
            self::SingleLine => 'Single Line Text',
            self::MultiLine => 'Multi Line Text',
            self::UniqueAnswer => 'Unique Answer (Radio)',
            self::MultiAnswer => 'Multiple Answers (Checkbox)',
        };
    }
}
