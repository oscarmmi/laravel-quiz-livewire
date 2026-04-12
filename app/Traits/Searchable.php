<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Searchable
{
    /**
     * Scope a query to search the model's text fields.
     *
     * @param  string  $term
     * @return Builder
     */
    public function scopeSearch(Builder $query, $term)
    {
        if (empty($term)) {
            return $query;
        }

        $searchable = $this->searchable ?? [];

        if (empty($searchable)) {
            return $query;
        }

        return $query->where(function (Builder $query) use ($term, $searchable) {
            foreach ($searchable as $column) {
                if (str_contains($column, '.')) {
                    // Relationship search
                    $parts = explode('.', $column);
                    $relationColumn = array_pop($parts);
                    $relationName = implode('.', $parts);

                    $query->orWhereHas($relationName, function (Builder $query) use ($relationColumn, $term) {
                        $query->where($relationColumn, 'like', '%'.$term.'%');
                    });
                } else {
                    $query->orWhere($column, 'like', '%'.$term.'%');
                }
            }
        });
    }
}
