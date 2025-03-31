<?php

namespace Bishopm\Methodist\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Leader extends Model
{
    public $table = 'leaders';
    protected $guarded = ['id'];
    protected $casts = [ 'roles' => 'array' ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function society(): BelongsTo
    {
        return $this->belongsTo(Society::class);
    }

}
