<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReviewController extends Controller
{
    /**
     * Display a listing of the reviews.
     */
    public function index(Request $request)
    {
        $query = Review::with(['user', 'product']);

        if ($request->has('rating')) {
            $query->where('rating', $request->rating);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('product', function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%");
                });
            });
        }

        $reviews = $query->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.reviews.index', compact('reviews'));
    }

    /**
     * Approve a review.
     */
    public function approve(Review $review)
    {
        try {
            $review->update(['status' => 'approved']);
            
            Log::info('Review approved successfully', [
                'review_id' => $review->id,
                'product_id' => $review->product_id,
                'user_id' => $review->user_id
            ]);

            return redirect()->route('admin.reviews.index')
                ->with('success', 'Review approved successfully.');
        } catch (\Exception $e) {
            Log::error('Error approving review', [
                'review_id' => $review->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Failed to approve review. Please try again.');
        }
    }

    /**
     * Reject a review.
     */
    public function reject(Review $review)
    {
        try {
            $review->update(['status' => 'rejected']);
            
            Log::info('Review rejected successfully', [
                'review_id' => $review->id,
                'product_id' => $review->product_id,
                'user_id' => $review->user_id
            ]);

            return redirect()->route('admin.reviews.index')
                ->with('success', 'Review rejected successfully.');
        } catch (\Exception $e) {
            Log::error('Error rejecting review', [
                'review_id' => $review->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Failed to reject review. Please try again.');
        }
    }

    /**
     * Remove the specified review from storage.
     */
    public function destroy(Review $review)
    {
        try {
            $review->delete();
            
            Log::info('Review deleted successfully', [
                'review_id' => $review->id,
                'product_id' => $review->product_id,
                'user_id' => $review->user_id
            ]);

            return redirect()->route('admin.reviews.index')
                ->with('success', 'Review deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting review', [
                'review_id' => $review->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Failed to delete review. Please try again.');
        }
    }
} 