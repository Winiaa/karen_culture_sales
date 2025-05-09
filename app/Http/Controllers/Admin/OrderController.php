<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Display admin dashboard with order statistics.
     */
    public function dashboard()
    {
        $totalOrders = Order::count();
        $pendingOrders = Order::where('order_status', 'processing')->count();
        $completedOrders = Order::where('order_status', 'delivered')->count();
        
        // Calculate revenue excluding cancelled orders
        $completedOrdersCount = Order::where('payment_status', 'completed')
                                    ->where('order_status', '!=', 'cancelled')
                                    ->count();
        $totalRevenue = Order::where('payment_status', 'completed')
                            ->where('order_status', '!=', 'cancelled')
                            ->sum('total_amount');
        $averageOrderValue = $completedOrdersCount > 0 ? $totalRevenue / $completedOrdersCount : 0;
        
        $totalProducts = \App\Models\Product::count();
        $totalCategories = \App\Models\Category::count();
        $totalUsers = \App\Models\User::count();

        // Get latest orders
        $latestOrders = Order::with(['user', 'orderItems.product'])
            ->where(function($q) {
                $q->whereHas('payment', function($q) {
                    $q->where(function($q) {
                        // Show all cash on delivery orders
                        $q->where('payment_method', 'cash_on_delivery');
                        // For credit card orders, show completed payments
                        $q->orWhere(function($q) {
                            $q->where('payment_method', 'stripe')
                              ->where('payment_status', 'completed');
                        });
                    });
                });
                // Include cancelled orders
                $q->orWhere('order_status', 'cancelled');
            })
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Monthly revenue
        $monthlyRevenue = Order::where('payment_status', 'completed')
            ->where('order_status', '!=', 'cancelled')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('total_amount');
            
        // Today's revenue
        $todayRevenue = Order::where('payment_status', 'completed')
            ->where('order_status', '!=', 'cancelled')
            ->whereDate('created_at', now()->toDateString())
            ->sum('total_amount');
            
        // Yearly revenue
        $yearlyRevenue = Order::where('payment_status', 'completed')
            ->where('order_status', '!=', 'cancelled')
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');
            
        // Generate sales data for the last 7 days
        $salesData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $daySales = Order::where('payment_status', 'completed')
                ->where('order_status', '!=', 'cancelled')
                ->whereDate('created_at', $date->toDateString())
                ->sum('total_amount');
            
            $salesData[] = [
                'date' => $date->format('D'),
                'sales' => $daySales ? $daySales / 100 : 0 // Scale down for display
            ];
        }

        // Get top products by order count
        $topProducts = \App\Models\Product::withCount('orderItems')
            ->orderBy('order_items_count', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalOrders',
            'pendingOrders',
            'completedOrders',
            'totalRevenue',
            'totalProducts',
            'totalCategories',
            'totalUsers',
            'latestOrders',
            'monthlyRevenue',
            'todayRevenue',
            'yearlyRevenue',
            'averageOrderValue',
            'salesData',
            'topProducts'
        ));
    }

    /**
     * Display a listing of the orders.
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'payment', 'delivery'])
            ->where(function($q) {
                $q->whereHas('payment', function($q) {
                    $q->where(function($q) {
                        // Show all cash on delivery orders
                        $q->where('payment_method', 'cash_on_delivery');
                        // For credit card orders, show completed payments and cancelled orders
                        $q->orWhere(function($q) {
                            $q->where('payment_method', 'stripe')
                              ->where(function($q) {
                                  $q->where('payment_status', 'completed')
                                    ->orWhereHas('order', function($q) {
                                        $q->where('order_status', 'cancelled');
                                    });
                              });
                        });
                    });
                });
            });

        // Add search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Add status filter
        if ($request->has('status')) {
            $query->where('order_status', $request->status);
        }

        // Add payment method filter
        if ($request->has('payment_method')) {
            $query->whereHas('payment', function($q) use ($request) {
                $q->where('payment_method', $request->payment_method);
            });
        }

        // Add date range filter
        if ($request->has('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->has('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $orders = $query->latest()->paginate(15);

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        // Check if this is a pending credit card payment
        if ($order->payment && 
            $order->payment->payment_method === 'stripe' && 
            $order->payment->payment_status === 'pending') {
            abort(404, 'Order not found');
        }

        $order->load(['user', 'orderItems.product', 'payment', 'delivery']);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update the status of the specified order.
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'order_status' => 'required|in:processing,shipped,delivered,cancelled',
            'payment_status' => 'required|in:pending,completed,failed,refunded',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            // Update order status
            $order->update([
                'order_status' => $request->order_status
            ]);

            // Only allow payment status updates in specific cases
            if ($order->payment) {
                $allowPaymentUpdate = false;

                // Allow payment update if:
                // 1. Current status is pending and new status is completed/failed
                // 2. It's a cash on delivery order and status is being marked as completed
                if (
                    ($order->payment_status === 'pending' && in_array($request->payment_status, ['completed', 'failed'])) ||
                    ($order->payment->payment_method === 'cash_on_delivery' && $request->payment_status === 'completed' && $order->order_status === 'delivered')
                ) {
                    $allowPaymentUpdate = true;
                }

                if ($allowPaymentUpdate && $order->payment_status !== $request->payment_status) {
                    $order->payment->update([
                        'payment_status' => $request->payment_status
                    ]);
                }
            }

            // Handle delivery status updates
            if ($request->order_status === 'shipped' && !$order->delivery) {
                $delivery = $order->delivery()->create([
                    'user_id' => $order->user_id,
                    'recipient_name' => $order->user ? $order->user->name : 'Customer',
                    'recipient_phone' => $order->user->phone ?? 'Not provided',
                    'recipient_address' => "$order->shipping_address, $order->shipping_city, $order->shipping_state, $order->shipping_country $order->shipping_zip",
                    'delivery_status' => 'pending'
                ]);
            }
            
            // Update delivery status if order is delivered
            if ($request->order_status === 'delivered' && $order->delivery) {
                $oldStatus = $order->delivery->delivery_status;
                $order->delivery->update([
                    'delivery_status' => 'delivered',
                    'delivered_at' => now()
                ]);
            }

            // Handle order cancellation
            if ($request->order_status === 'cancelled') {
                foreach ($order->orderItems as $item) {
                    $item->product->increment('quantity', $item->quantity);
                }
            }

            DB::commit();

            return back()->with('success', 'Order status updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Order status update failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Unable to update order status. Please try again.');
        }
    }

    /**
     * Update the delivery information for the specified order.
     */
    public function updateDelivery(Request $request, Order $order)
    {
        \Log::info('Updating delivery information for order #' . $order->id, $request->all());
        
        try {
            $validated = $request->validate([
                // Required fields
                'recipient_name' => 'required|string|max:255',
                'recipient_phone' => 'required|string|max:20',
                'recipient_address' => 'required|string',
                'delivery_status' => 'required|string|in:pending,assigned,picked_up,out_for_delivery,delivered,failed',
                
                // Optional fields - all are now truly optional
                'tracking_number' => 'nullable|string|max:255',
                'estimated_delivery_date' => 'nullable|date_format:Y-m-d',
                'carrier' => 'nullable|string|max:255',
                'internal_notes' => 'nullable|string',
                'customer_instructions' => 'nullable|string',
            ]);

            DB::beginTransaction();
            
            // Prepare data
            $data = $validated;
            
            // Handle the estimated delivery date
            if (!empty($data['estimated_delivery_date'])) {
                $data['estimated_delivery_date'] = \Carbon\Carbon::createFromFormat('Y-m-d', $data['estimated_delivery_date'])->startOfDay();
                \Log::info('Estimated delivery date set to: ' . $data['estimated_delivery_date']);
            }
            
            // Format carrier name if provided
            if (!empty($data['carrier'])) {
                $data['carrier'] = trim($data['carrier']);
            }
            
            // Ensure recipient information is explicitly set
            $recipientData = [
                'recipient_name' => $data['recipient_name'],
                'recipient_phone' => $data['recipient_phone'],
                'recipient_address' => $data['recipient_address'],
            ];
            
            \Log::info('Recipient data:', $recipientData);
            
            // Check if delivery record exists
            if ($order->delivery) {
                \Log::info('Updating existing delivery record');
                
                // Update existing delivery record with explicit recipient data
                $order->delivery->update(array_merge($data, $recipientData));
                
                // If delivery status changed to delivered, record delivered_at time
                if ($data['delivery_status'] === 'delivered' && $order->delivery->delivery_status !== 'delivered') {
                    \Log::info('Marking delivery as delivered');
                    $order->delivery->update(['delivered_at' => now()]);
                    $order->update(['order_status' => 'delivered']);
                }
                
                // If delivery status changed to out_for_delivery, make order non-cancellable for COD orders
                if ($data['delivery_status'] === 'out_for_delivery' && $order->delivery->delivery_status !== 'out_for_delivery') {
                    \Log::info('Checking if order should be made non-cancellable');
                    // For cash on delivery orders, prevent cancellation
                    if ($order->payment && $order->payment->payment_method === 'cash_on_delivery') {
                        $order->update(['is_cancellable' => false]);
                        \Log::info('Order marked as non-cancellable');
                    }
                }
                
                // If order is still in processing status but delivery status changed to out_for_delivery or picked_up, update to shipped
                if ($order->order_status === 'processing' && in_array($data['delivery_status'], ['out_for_delivery', 'picked_up', 'assigned'])) {
                    \Log::info('Updating order status to shipped');
                    $order->update(['order_status' => 'shipped']);
                }
            } else {
                \Log::info('Creating new delivery record');
                
                // Create new delivery record
                $deliveryData = array_merge($data, $recipientData);
                $deliveryData['order_id'] = $order->id;
                $deliveryData['user_id'] = $order->user_id;
                
                // If delivery status is delivered, include delivered_at timestamp
                if ($data['delivery_status'] === 'delivered') {
                    $deliveryData['delivered_at'] = now();
                    $order->update(['order_status' => 'delivered']);
                    \Log::info('New delivery marked as delivered');
                }
                
                // Create the delivery and store it in a variable
                $delivery = $order->delivery()->create($deliveryData);
                \Log::info('New delivery record created with ID: ' . $delivery->id);
                
                // Update order status based on delivery status
                if (in_array($data['delivery_status'], ['out_for_delivery', 'picked_up', 'assigned']) && $order->order_status === 'processing') {
                    $order->update(['order_status' => 'shipped']);
                    \Log::info('Order status updated to shipped');
                }
            }

            DB::commit();
            \Log::info('Delivery update completed successfully');
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Delivery information updated successfully.',
                    'delivery' => $order->delivery->fresh()
                ]);
            }
            
            return back()->with('success', 'Delivery information updated successfully.');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            \Log::warning('Validation failed when updating delivery:', [
                'errors' => $e->errors(),
                'order_id' => $order->id
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            
            return back()->withErrors($e->errors())->withInput();
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating delivery: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to update delivery information: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Unable to update delivery information: ' . $e->getMessage());
        }
    }

    /**
     * Update the order's payment information.
     */
    public function updatePayment(Request $request, Order $order)
    {
        try {
            DB::beginTransaction();
            
            $request->validate([
                'payment_method' => 'required|in:stripe,cash_on_delivery',
                'payment_status' => 'required|in:pending,completed,failed',
                'transaction_id' => 'nullable|string|max:255',
            ]);
            
            if ($order->payment) {
                $order->payment->update([
                    'payment_method' => $request->payment_method,
                    'payment_status' => $request->payment_status,
                    'transaction_id' => $request->transaction_id,
                ]);
                
                // Update the order payment status as well
                $order->update(['payment_status' => $request->payment_status]);
            } else {
                // Create a new payment record if one doesn't exist
                $order->payment()->create([
                    'payment_method' => $request->payment_method,
                    'payment_status' => $request->payment_status,
                    'transaction_id' => $request->transaction_id,
                ]);
                
                $order->update(['payment_status' => $request->payment_status]);
            }
            
            DB::commit();
            
            return back()->with('success', 'Payment information updated successfully.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', 'Failed to update payment information: ' . $e->getMessage());
        }
    }

    /**
     * Assign a driver to the order delivery.
     */
    public function assignDriver(Request $request, Order $order)
    {
        try {
            $request->validate([
                'driver_id' => 'required|exists:drivers,id',
            ]);
            
            // Log the attempt
            \Log::info('Attempting to assign driver to order', [
                'order_id' => $order->id,
                'driver_id' => $request->driver_id
            ]);
            
            DB::beginTransaction();
            
            $driver = Driver::with('user')->findOrFail($request->driver_id);
            
            // Check if order has a delivery record
            if (!$order->delivery) {
                \Log::info('Creating new delivery record for order', ['order_id' => $order->id]);
                
                // Create a basic delivery record
                $delivery = $order->delivery()->create([
                    'user_id' => $order->user_id,
                    'recipient_name' => $order->user ? $order->user->name : 'Customer',
                    'recipient_phone' => $order->user->phone ?? 'Not provided',
                    'recipient_address' => "$order->shipping_address, $order->shipping_city, $order->shipping_state, $order->shipping_country $order->shipping_zip",
                    'delivery_status' => 'pending',
                ]);
                
                if (!$delivery) {
                    throw new \Exception('Failed to create delivery record.');
                }
                
                // Refresh the order to get the new delivery
                $order->refresh();
            }
            
            if (!$order->delivery) {
                throw new \Exception('Delivery record not found after creation.');
            }
            
            // Log before assigning driver
            \Log::info('About to assign driver', [
                'delivery_id' => $order->delivery->id,
                'driver_id' => $driver->id
            ]);
            
            // Assign the driver to the delivery
            $order->delivery->assignDriver($driver);
            
            DB::commit();
            
            // Log success
            \Log::info('Driver assigned successfully', [
                'order_id' => $order->id,
                'driver_id' => $driver->id,
                'delivery_id' => $order->delivery->id
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Driver assigned successfully.',
                    'driver' => [
                        'id' => $driver->id,
                        'name' => $driver->user->name,
                        'phone' => $driver->phone_number
                    ]
                ]);
            }
            
            return back()->with('success', 'Driver assigned successfully.');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            \Log::error('Validation error while assigning driver:', [
                'order_id' => $order->id,
                'errors' => $e->errors()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please select a valid driver.'
                ], 422);
            }
            return back()->withErrors($e->errors())->withInput();
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error assigning driver:', [
                'order_id' => $order->id,
                'driver_id' => $request->driver_id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $errorMessage = $e->getMessage();
            
            // Check for specific error messages from the Delivery model
            if (str_contains($errorMessage, 'not active')) {
                $errorMessage = 'Selected driver is not active.';
            } else if (str_contains($errorMessage, 'maximum number of active deliveries')) {
                $errorMessage = 'Selected driver has reached their maximum delivery limit.';
            } else if (str_contains($errorMessage, 'already assigned')) {
                $errorMessage = 'This driver is already assigned to this delivery.';
            } else if ($e instanceof \Illuminate\Database\QueryException) {
                \Log::error('Database error details:', [
                    'sql' => $e->getSql(),
                    'bindings' => $e->getBindings()
                ]);
                $errorMessage = 'Database error: There was an issue updating the delivery information. Please try again.';
            } else {
                $errorMessage = 'Failed to assign driver. Please try again.';
            }
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }
            
            return back()->with('error', $errorMessage);
        }
    }

    /**
     * Display sales report with filtering options.
     */
    public function salesReport(Request $request)
    {
        // Initialize query
        $query = Order::with(['orderItems.product.category', 'user', 'payment'])
                     ->where(function($q) {
                         $q->whereHas('payment', function($q) {
                             $q->where(function($q) {
                                 // Show all cash on delivery orders
                                 $q->where('payment_method', 'cash_on_delivery');
                                 // For credit card orders, show completed payments and refunded payments
                                 $q->orWhere(function($q) {
                                     $q->where('payment_method', 'stripe')
                                       ->where(function($q) {
                                           $q->where('payment_status', 'completed')
                                             ->orWhere('payment_status', 'refunded');
                                       });
                                 });
                             });
                         })
                         // Also include orders that are marked as cancelled (which are typically refunded)
                         ->orWhere('order_status', 'cancelled');
                     });
        
        // Apply date filters
        if ($request->has('date_range')) {
            switch ($request->date_range) {
                case 'today':
                    $query->whereDate('created_at', now()->toDateString());
                    break;
                case 'yesterday':
                    $query->whereDate('created_at', now()->subDay()->toDateString());
                    break;
                case 'this_week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'last_week':
                    $query->whereBetween('created_at', [
                        now()->subWeek()->startOfWeek(), 
                        now()->subWeek()->endOfWeek()
                    ]);
                    break;
                case 'this_month':
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    break;
                case 'last_month':
                    $query->whereMonth('created_at', now()->subMonth()->month)
                          ->whereYear('created_at', now()->subMonth()->year);
                    break;
                case 'this_year':
                    $query->whereYear('created_at', now()->year);
                    break;
                case 'custom':
                    if ($request->filled('start_date') && $request->filled('end_date')) {
                        $query->whereBetween('created_at', [
                            $request->start_date . ' 00:00:00',
                            $request->end_date . ' 23:59:59'
                        ]);
                    }
                    break;
            }
        }
        
        // Filter by payment status
        if ($request->filled('payment_status') && in_array($request->payment_status, ['pending', 'completed', 'refunded'])) {
            if ($request->payment_status === 'refunded') {
                $query->where('order_status', 'cancelled');
            } else {
                // For completed payments, exclude cancelled orders
                if ($request->payment_status === 'completed') {
                    $query->where('payment_status', 'completed')
                          ->where('order_status', '!=', 'cancelled');
                } else {
                    $query->where('payment_status', $request->payment_status);
                }
            }
        }
        
        // Filter by order status
        if ($request->filled('order_status') && in_array($request->order_status, ['processing', 'shipped', 'delivered', 'cancelled'])) {
            $query->where('order_status', $request->order_status);
        }
        
        // Filter by category
        if ($request->filled('category_id')) {
            $query->whereHas('orderItems.product', function($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }
        
        // Get orders and execute query
        $orders = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Calculate summary statistics - exclude cancelled orders from revenue
        $completedOrders = $orders->where('payment_status', 'completed')
                                ->where('order_status', '!=', 'cancelled');
        $totalRevenue = $completedOrders->sum('total_amount');
        $completedOrdersCount = $completedOrders->count();
        $totalOrders = $orders->total();
        $averageOrderValue = $completedOrdersCount > 0 ? $totalRevenue / $completedOrdersCount : 0;
        
        // Get order items for product-level reporting
        $orderItems = collect();
        foreach ($orders as $order) {
            // Only include items from completed, non-cancelled orders
            if ($order->payment_status === 'completed' && $order->order_status !== 'cancelled') {
                $orderItems = $orderItems->concat($order->orderItems);
            }
        }
        
        // Get top selling products
        $topProducts = $orderItems->groupBy('product_id')
            ->map(function ($items) {
                $product = $items->first()->product;
                return [
                    'product' => $product,
                    'total_quantity' => $items->sum('quantity'),
                    'total_revenue' => $items->sum(function ($item) {
                        return $item->quantity * $item->product->final_price;
                    })
                ];
            })
            ->sortByDesc('total_quantity')
            ->take(10)
            ->values();
            
        // Get category performance
        $categoryPerformance = $orderItems
            ->groupBy('product.category_id')
            ->map(function ($items) {
                $category = $items->first()->product->category;
                return [
                    'category' => $category,
                    'total_quantity' => $items->sum('quantity'),
                    'total_revenue' => $items->sum(function ($item) {
                        return $item->quantity * $item->product->final_price;
                    })
                ];
            })
            ->sortByDesc('total_revenue')
            ->values();
            
        // Get all categories for the filter dropdown
        $categories = \App\Models\Category::all();
        
        return view('admin.reports.sales', compact(
            'orders',
            'totalRevenue',
            'totalOrders',
            'averageOrderValue',
            'topProducts',
            'categoryPerformance',
            'categories'
        ));
    }

    /**
     * Export sales report as CSV
     */
    public function exportSalesReport(Request $request)
    {
        // Use the same filtering logic as in the salesReport method
        $query = Order::with(['orderItems.product.category', 'user', 'payment']);
        
        // Apply date filters
        if ($request->has('date_range')) {
            switch ($request->date_range) {
                case 'today':
                    $query->whereDate('created_at', now()->toDateString());
                    break;
                case 'yesterday':
                    $query->whereDate('created_at', now()->subDay()->toDateString());
                    break;
                case 'this_week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'last_week':
                    $query->whereBetween('created_at', [
                        now()->subWeek()->startOfWeek(), 
                        now()->subWeek()->endOfWeek()
                    ]);
                    break;
                case 'this_month':
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    break;
                case 'last_month':
                    $query->whereMonth('created_at', now()->subMonth()->month)
                          ->whereYear('created_at', now()->subMonth()->year);
                    break;
                case 'this_year':
                    $query->whereYear('created_at', now()->year);
                    break;
                case 'custom':
                    if ($request->filled('start_date') && $request->filled('end_date')) {
                        $query->whereBetween('created_at', [
                            $request->start_date . ' 00:00:00',
                            $request->end_date . ' 23:59:59'
                        ]);
                    }
                    break;
            }
        }
        
        // Filter by payment status
        if ($request->filled('payment_status') && in_array($request->payment_status, ['pending', 'completed', 'refunded'])) {
            if ($request->payment_status === 'refunded') {
                $query->where('order_status', 'cancelled');
            } else {
                // For completed payments, exclude cancelled orders
                if ($request->payment_status === 'completed') {
                    $query->where('payment_status', 'completed')
                          ->where('order_status', '!=', 'cancelled');
                } else {
                    $query->where('payment_status', $request->payment_status);
                }
            }
        }
        
        // Filter by order status
        if ($request->filled('order_status') && in_array($request->order_status, ['processing', 'shipped', 'delivered', 'cancelled'])) {
            $query->where('order_status', $request->order_status);
        }
        
        // Filter by category
        if ($request->filled('category_id')) {
            $query->whereHas('orderItems.product', function($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }
        
        // Get orders and execute query
        $orders = $query->orderBy('created_at', 'desc')->get();
        
        // Generate CSV filename
        $filename = 'sales_report_' . date('Y-m-d_His') . '.csv';
        
        // Create CSV file
        $handle = fopen('php://temp', 'r+');
        
        // Add CSV header row
        fputcsv($handle, [
            'Order ID',
            'Date',
            'Customer',
            'Customer Email',
            'Items Count',
            'Total Amount',
            'Order Status',
            'Payment Status',
            'Payment Method'
        ]);
        
        // Add order data rows
        foreach ($orders as $order) {
            // Format date in Excel-friendly format (MM/DD/YYYY HH:MM:SS)
            $orderDate = $order->created_at ? $order->created_at->format('m/d/Y H:i:s') : 'N/A';
            
            // Determine payment status - show Refunded for cancelled orders
            $paymentStatus = $order->order_status === 'cancelled' ? 'Refunded' : $order->payment_status;
            
            fputcsv($handle, [
                $order->id,
                $orderDate,
                $order->user->name,
                $order->user->email,
                $order->orderItems->sum('quantity'),
                $order->total_amount,
                $order->order_status,
                $paymentStatus,
                $order->payment ? $order->payment->payment_method : 'N/A'
            ]);
        }
        
        // Reset file pointer to start
        rewind($handle);
        
        // Get contents of file
        $csv = stream_get_contents($handle);
        fclose($handle);
        
        // Create response with CSV content
        $response = response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ]);
        
        return $response;
    }
}
