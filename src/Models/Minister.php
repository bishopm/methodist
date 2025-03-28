<?php

namespace Bishopm\Methodist\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Minister extends Model
{
    public $table = 'ministers';
    protected $guarded = ['id'];

    public function circuit(): BelongsTo
    {
        return $this->belongsTo(Circuit::class);
    }

}
