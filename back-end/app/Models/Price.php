<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Price extends Model
{
    public $timestamps = false;

    protected $fillable = ['product_id', 'amount', 'currency_label', 'currency_symbol'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
