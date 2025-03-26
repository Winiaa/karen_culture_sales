<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'category_name',
        'description',
        'image',
        'icon'
    ];

    /**
     * Get the products for the category.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get the name attribute.
     * This accessor allows us to use $category->name while internally using category_name
     */
    public function getNameAttribute(): string
    {
        return $this->category_name;
    }

    /**
     * Set the name attribute.
     * This mutator allows us to set name while internally using category_name
     */
    public function setNameAttribute($value): void
    {
        $this->attributes['category_name'] = $value;
    }

    protected $table = 'Categories';
}
