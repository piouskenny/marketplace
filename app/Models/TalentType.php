<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TalentType extends Model
{
    protected $fillable = [
        'slug',
        'label',
        'icon',
        'description',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    /**
     * Users who hold this talent classification.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'talent_type_user')
                    ->withPivot('completed_at')
                    ->withTimestamps();
    }
}
