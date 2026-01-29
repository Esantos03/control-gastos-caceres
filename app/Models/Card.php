<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Card extends Model
{
    protected $fillable = ['name', 'last_digits', 'type'];

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }
}
