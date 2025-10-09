<?php

namespace Bishopm\Methodist\Models;

use Bishopm\Methodist\Traits\Taggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Idea extends Model
{
    use Taggable;

    public $table = 'ideas';
    protected $guarded = ['id'];
    protected $casts = [
        'published' => 'boolean'
    ];  

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

}
