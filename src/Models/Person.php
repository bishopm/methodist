<?php

namespace Bishopm\Methodist\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Person extends Model
{

    public $table = 'persons';
    protected $guarded = ['id'];

    public function minister(): HasOne
    {
        return $this->HasOne(Minister::class);
    }

    public function preacher(): HasOne
    {
        return $this->HasOne(Preacher::class);
    }

    public function leader(): HasOne
    {
        return $this->HasOne(Leader::class);
    }

    public function circuit(): BelongsTo
    {
        return $this->belongsTo(Circuit::class);
    }

}
