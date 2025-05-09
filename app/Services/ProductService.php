<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductService
{
    /**
     * Get base query for active products
     */
    public function getBaseQuery(): Builder
    {
        return Product::with(['category', 'reviews'])
            ->whereHas('category', fn($q) => $q->where('is_active', 1))
            ->where('products.status', 'active');
    }

    /**
     * Apply search filters to query
     */
    public function applySearch(Builder $query, ?string $search): Builder
    {
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('products.title', 'like', "%{$search}%")
                  ->orWhere('products.description', 'like', "%{$search}%");
            });
        }
        return $query;
    }

    /**
     * Apply category filter to query
     */
    public function applyCategory(Builder $query, ?int $categoryId): Builder
    {
        if ($categoryId) {
            $query->where('products.category_id', $categoryId);
        }
        return $query;
    }

    /**
     * Apply sorting to query
     */
    public function applySort(Builder $query, ?string $sort, bool $isBestSellers = false): Builder
    {
        // If no sort specified and not best sellers, default to newest
        if (!$sort && !$isBestSellers) {
            return $query->orderBy('products.created_at', 'desc');
        }

        switch ($sort) {
            case 'price_asc':
                $query->orderBy('products.price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('products.price', 'desc');
                break;
            case 'newest':
                $query->orderBy('products.created_at', 'desc');
                break;
            case 'popular':
                if (!$isBestSellers && !$this->hasJoin($query, 'order_items')) {
                    $query->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
                          ->select('products.*', DB::raw('COUNT(order_items.id) as order_count'))
                          ->groupBy([
                              'products.id',
                              'products.title',
                              'products.slug',
                              'products.description',
                              'products.price',
                              'products.discount_price',
                              'products.quantity',
                              'products.category_id',
                              'products.image',
                              'products.additional_images',
                              'products.status',
                              'products.created_at',
                              'products.updated_at'
                          ]);
                }
                $query->orderBy('order_count', 'desc');
                break;
            default:
                if ($isBestSellers) {
                    $query->orderBy('order_count', 'desc');
                } else {
                    $query->orderBy('products.created_at', 'desc');
                }
        }

        return $query;
    }

    /**
     * Check if a table is already joined to the query
     */
    private function hasJoin(Builder $query, string $table): bool
    {
        $joins = $query->getQuery()->joins;
        if ($joins === null) {
            return false;
        }
        
        foreach ($joins as $join) {
            if ($join->table === $table) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get featured products
     */
    public function getFeaturedProducts(int $limit = 8): Builder
    {
        return $this->getBaseQuery()
            ->orderBy('products.created_at', 'desc')
            ->take($limit);
    }

    /**
     * Get new arrivals
     */
    public function getNewArrivals(): Builder
    {
        return $this->getBaseQuery()
            ->where('products.created_at', '>=', now()->subDays(30));
    }

    /**
     * Get best sellers
     */
    public function getBestSellers(): Builder
    {
        return $this->getBaseQuery()
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->select([
                'products.*',
                DB::raw('COUNT(order_items.id) as order_count')
            ])
            ->groupBy([
                'products.id',
                'products.title',
                'products.slug',
                'products.description',
                'products.price',
                'products.discount_price',
                'products.quantity',
                'products.category_id',
                'products.image',
                'products.additional_images',
                'products.status',
                'products.created_at',
                'products.updated_at'
            ])
            ->having('order_count', '>', 0);
    }

    /**
     * Get top best seller IDs
     */
    public function getTopBestSellerIds(int $limit = 3): array
    {
        return Product::leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->whereHas('category', fn($q) => $q->where('is_active', 1))
            ->where('products.status', 'active')
            ->select('products.id', DB::raw('COUNT(order_items.id) as order_count'))
            ->groupBy('products.id')
            ->orderByDesc('order_count')
            ->limit($limit)
            ->pluck('products.id')
            ->toArray();
    }
} 