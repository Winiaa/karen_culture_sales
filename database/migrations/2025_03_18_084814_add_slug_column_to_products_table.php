<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\Product;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, add the column without unique constraint
        if (!Schema::hasColumn('products', 'slug')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('slug')->nullable()->after('title');
            });
        }

        // Now populate the slugs with unique values
        $products = Product::all();
        foreach ($products as $product) {
            $slug = Str::slug($product->title);
            $count = Product::where('slug', 'LIKE', $slug . '%')
                ->where('id', '!=', $product->id)
                ->count();
                
            if ($count > 0) {
                $slug = $slug . '-' . ($count + 1);
            }
            
            $product->slug = $slug;
            $product->save();
        }

        // Finally, add the unique constraint if it doesn't exist
        if (!Schema::hasIndex('products', 'products_slug_unique')) {
            Schema::table('products', function (Blueprint $table) {
                $table->unique('slug');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique('products_slug_unique');
            
            if (Schema::hasColumn('products', 'slug')) {
                $table->dropColumn('slug');
            }
        });
    }
};
