<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrackingController extends Controller
{
    /**
     * Show the tracking form.
     */
    public function index(Request $request): View
    {
        // Check if tracking number is provided in URL
        if ($request->has('tracking_number')) {
            $tracking_number = $request->tracking_number;
            $delivery = Delivery::where('tracking_number', $tracking_number)->first();
            
            if ($delivery) {
                // Load order details and other related information
                $delivery->load(['order.orderItems.product', 'order.payment']);
                
                return view('tracking.results', [
                    'delivery' => $delivery,
                    'order' => $delivery->order,
                ]);
            } else {
                return view('tracking.index')
                    ->with('error', 'Tracking number not found. Please check and try again.');
            }
        }
        
        return view('tracking.index');
    }

    /**
     * Process the tracking request.
     */
    public function track(Request $request)
    {
        $request->validate([
            'tracking_number' => 'required|string',
        ]);

        $tracking_number = $request->tracking_number;
        $delivery = Delivery::where('tracking_number', $tracking_number)->first();

        if (!$delivery) {
            return redirect()->route('tracking.index')
                ->with('error', 'Tracking number not found. Please check and try again.');
        }

        // Load order details and other related information
        $delivery->load(['order.orderItems.product', 'order.payment']);

        return view('tracking.results', [
            'delivery' => $delivery,
            'order' => $delivery->order,
        ]);
    }
} 