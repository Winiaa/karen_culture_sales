<?php

namespace App\Helpers;

class CurrencyHelper
{
    /**
     * Format a number as currency with the application's currency symbol
     *
     * @param float $amount The amount to format
     * @param int $decimals Number of decimal places
     * @param bool $includeSymbol Whether to include the currency symbol
     * @return string
     */
    public static function format($amount, $decimals = 2, $includeSymbol = true)
    {
        $symbol = config('app.currency_symbol', '฿');
        $formatted = number_format($amount, $decimals);
        
        return $includeSymbol ? $symbol . $formatted : $formatted;
    }
    
    /**
     * Format as Thai Baht directly (without conversion)
     * This is for when the amount is already in Thai Baht
     *
     * @param float $amount Amount in Thai Baht
     * @param int $decimals Number of decimal places
     * @return string
     */
    public static function baht($amount, $decimals = 2)
    {
        // No currency conversion, just format the number directly
        return self::format($amount, $decimals);
    }
    
    /**
     * Convert USD to Thai Baht (legacy support - DEPRECATED)
     * 
     * This method is deprecated and should NOT be used anymore.
     * All amounts should be entered and stored as Thai Baht directly.
     *
     * @deprecated
     * @param float $usdAmount Amount in USD
     * @param float $exchangeRate Exchange rate from USD to THB (default 35.0)
     * @return float
     */
    public static function usdToThb($usdAmount, $exchangeRate = 35.0)
    {
        // Just return the amount without conversion to maintain consistency
        return $usdAmount;
    }
} 