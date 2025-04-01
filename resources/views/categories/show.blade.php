@extends('layouts.app')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('categories.index') }}">Categories</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $category->name }}</li>
        </ol>
    </nav>

    <div class="row mb-4">
        <div class="col-md-12">
            <h1>{{ $category->name }}</h1>
            @if($category->description)
                <p class="lead">{{ $category->description }}</p>
            @endif
        </div>
    </div>

    <div class="row">
        @forelse($products as $product)
            <div class="col-md-3 mb-4">
                <x-product-card :product="$product" />
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    No products found in this category.
                </div>
            </div>
        @endforelse
    </div>

    <div class="row">
        <div class="col-12">
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection 