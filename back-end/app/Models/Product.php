<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute as AttributeCast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

class Product extends Model
{
    protected $table = 'products';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = ['id', 'name', 'description', 'in_stock', 'gallery', 'category_id', 'brand'];

    protected $casts = [
        'in_stock' => 'boolean',
    ];

    /**
     * Category this model is restricted to; subclasses such as Tech and Clothes
     * override it so their queries only ever see their own products.
     */
    protected static function categoryName(): ?string
    {
        return null;
    }

    protected static function booted(): void
    {
        $category = static::categoryName();

        if ($category !== null) {
            static::addGlobalScope('category', function (Builder $query) use ($category) {
                $query->whereHas('category', fn (Builder $q) => $q->where('name', $category));
            });
        }
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function price(): HasOne
    {
        return $this->hasOne(Price::class, 'product_id');
    }

    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(AttributeValue::class, 'product_attributes', 'product_id', 'attribute_value_id');
    }

    /**
     * Attribute options flattened to the { id, name, value } rows the GraphQL API exposes.
     */
    public function productAttributes(): Collection
    {
        return $this->attributeValues
            ->sortBy('id')
            ->map(fn (AttributeValue $value) => [
                'id' => $value->attribute->id,
                'name' => $value->attribute->name,
                'value' => $value->value,
            ])
            ->values();
    }

    protected function amount(): AttributeCast
    {
        return AttributeCast::get(fn () => (float) ($this->price->amount ?? 0));
    }

    protected function currencyLabel(): AttributeCast
    {
        return AttributeCast::get(fn () => $this->price->currency_label ?? 'N/A');
    }

    protected function currencySymbol(): AttributeCast
    {
        return AttributeCast::get(fn () => $this->price->currency_symbol ?? '-');
    }
}
