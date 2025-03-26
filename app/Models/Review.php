<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'rating',
        'comment'
    ];

    protected $table = 'Reviews';

    public $timestamps = true;

    /**
     * Get the user that owns the review.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product that owns the review.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Check if the review can be edited by the given user.
     */
    public function canBeEditedBy(?User $user)
    {
        if (!$user) {
            return false;
        }
        
        return $user->id === $this->user_id || $user->usertype === 'admin';
    }
}
