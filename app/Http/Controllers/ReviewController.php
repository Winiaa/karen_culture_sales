<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Store a newly created review in storage.
     */
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'max:1000'],
        ]);

        $review = new Review();
        $review->user_id = Auth::id();
        $review->product_id = $product->id;
        $review->rating = $request->rating;
        $review->comment = $request->comment;
        $review->status = 'pending';
        $review->save();

        return back()->with('success', 'Review submitted successfully!');
    }

    /**
     * Update the specified review in storage.
     */
    public function update(Request $request, Review $review)
    {
        if (!$review->canBeEditedBy(auth()->user())) {
            abort(403);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000'
        ]);

        $review->update([
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);

        return back()->with('success', 'Review updated successfully.');
    }

    /**
     * Remove the specified review from storage.
     */
    public function destroy(Review $review)
    {
        if ($review->user_id !== Auth::id()) {
            abort(403);
        }

        $review->delete();
        return back()->with('success', 'Review deleted successfully!');
    }
}
