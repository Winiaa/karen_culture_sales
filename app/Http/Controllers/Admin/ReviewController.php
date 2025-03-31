<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Display a listing of the reviews.
     */
    public function index(Request $request)
    {
        $query = Review::with(['user', 'product'])
            ->latest();

        // Search by customer or product
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhereHas('product', function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%");
                });
            });
        }

        // Filter by rating
        if ($request->has('rating')) {
            $query->where('rating', $request->rating);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $reviews = $query->paginate(15);

        return view('admin.reviews.index', compact('reviews'));
    }

    /**
     * Approve a review.
     */
    public function approve(Review $review)
    {
        $review->update(['status' => 'approved']);

        return response()->json([
            'success' => true,
            'message' => 'Review approved successfully'
        ]);
    }

    /**
     * Reject a review.
     */
    public function reject(Review $review)
    {
        $review->update(['status' => 'rejected']);

        return response()->json([
            'success' => true,
            'message' => 'Review rejected successfully'
        ]);
    }

    /**
     * Remove the specified review.
     */
    public function destroy(Review $review)
    {
        $review->delete();

        return response()->json([
            'success' => true,
            'message' => 'Review deleted successfully'
        ]);
    }
} 