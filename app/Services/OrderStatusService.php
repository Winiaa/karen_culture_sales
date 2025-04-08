<?php

namespace App\Services;

class OrderStatusService
{
    /**
     * Get the badge color for an order status
     *
     * @param string $orderStatus
     * @return string
     */
    public function getOrderStatusBadgeColor(string $orderStatus): string
    {
        return match($orderStatus) {
            'delivered' => 'success',
            'shipped' => 'info',
            'processing' => 'primary',
            'cancelled' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Get the display text for an order status
     *
     * @param string $orderStatus
     * @return string
     */
    public function getOrderStatusText(string $orderStatus): string
    {
        return match($orderStatus) {
            'processing' => 'Order Processing',
            'shipped' => 'Order Shipped',
            'delivered' => 'Order Delivered',
            'cancelled' => 'Order Cancelled',
            default => ucfirst($orderStatus)
        };
    }

    /**
     * Get the badge color for a payment status
     *
     * @param string $paymentStatus
     * @param string $orderStatus
     * @param string|null $paymentMethod
     * @return string
     */
    public function getPaymentStatusBadgeColor(string $paymentStatus, string $orderStatus, ?string $paymentMethod = null): string
    {
        if ($orderStatus === 'cancelled') {
            return $paymentMethod === 'stripe' ? 'info' : 'danger';
        }

        return match($paymentStatus) {
            'completed' => 'success',
            'pending' => 'warning',
            default => 'info'
        };
    }

    /**
     * Get the display text for a payment status
     *
     * @param string $paymentStatus
     * @param string $orderStatus
     * @param string|null $paymentMethod
     * @return string
     */
    public function getPaymentStatusText(string $paymentStatus, string $orderStatus, ?string $paymentMethod = null): string
    {
        if ($orderStatus === 'cancelled') {
            return $paymentMethod === 'stripe' ? 'Refunded' : 'Cancelled';
        }

        return match($paymentStatus) {
            'completed' => 'Payment Completed',
            'pending' => 'Payment Pending',
            default => ucfirst($paymentStatus)
        };
    }

    /**
     * Get the timeline line color for an order status
     *
     * @param string $orderStatus
     * @return string
     */
    public function getOrderStatusLineColor(string $orderStatus): string
    {
        return match($orderStatus) {
            'cancelled' => '#dc3545',
            'delivered', 'shipped' => '#28a745',
            default => '#e9ecef'
        };
    }

    /**
     * Get the timeline line color for a payment status
     *
     * @param string $paymentStatus
     * @param string $orderStatus
     * @param string|null $paymentMethod
     * @return string
     */
    public function getPaymentStatusLineColor(string $paymentStatus, string $orderStatus, ?string $paymentMethod = null): string
    {
        if ($orderStatus === 'cancelled') {
            return $paymentMethod === 'stripe' ? '#0dcaf0' : '#dc3545';
        }

        return match($paymentStatus) {
            'completed' => '#28a745',
            default => '#e9ecef'
        };
    }
} 