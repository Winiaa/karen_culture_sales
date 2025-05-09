<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Product Alert</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
        }
        .header {
            text-align: center;
            padding: 20px 0;
            background-color: #1a472a;
            color: #ffffff;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 10px;
        }
        .content {
            padding: 30px 20px;
        }
        .product-image {
            width: 100%;
            max-width: 400px;
            height: auto;
            margin: 20px auto;
            display: block;
            border-radius: 8px;
        }
        .product-title {
            font-size: 24px;
            color: #1a472a;
            margin: 20px 0;
            text-align: center;
        }
        .product-price {
            font-size: 20px;
            color: #2d8a62;
            text-align: center;
            margin: 15px 0;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #1a472a;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
        }
        .footer {
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #eee;
        }
        .social-links {
            margin: 20px 0;
            text-align: center;
        }
        .social-links a {
            display: inline-block;
            margin: 0 10px;
            color: #1a472a;
            text-decoration: none;
        }
        .unsubscribe {
            font-size: 12px;
            color: #666;
            text-align: center;
            margin-top: 20px;
        }
        .unsubscribe a {
            color: #666;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('images/logo.png') }}" alt="Karen Culture Sales" class="logo">
            <h1>New Product Alert</h1>
        </div>

        <div class="content">
            <h2 class="product-title">{{ $product->title }}</h2>
            
            @if($product->image)
                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->title }}" class="product-image">
            @endif

            <p style="text-align: center; color: #666;">
                We're excited to announce our latest addition to the Karen Culture Sales collection!
            </p>

            <div class="product-price">
                {{ number_format($product->price, 2) }} THB
            </div>

            <p style="text-align: center; margin: 20px 0;">
                {{ Str::limit($product->description, 200) }}
            </p>

            <div style="text-align: center;">
                <a href="{{ route('products.show', $product) }}" class="button">
                    View Product Details
                </a>
            </div>

            <div class="social-links">
                <a href="#"><i class="fab fa-facebook"></i> Facebook</a>
                <a href="#"><i class="fab fa-instagram"></i> Instagram</a>
                <a href="#"><i class="fab fa-twitter"></i> Twitter</a>
            </div>
        </div>

        <div class="footer">
            <p>Thank you for being a valued subscriber!</p>
            <p>Karen Culture Sales - Preserving and promoting Karen cultural heritage</p>
            <div class="unsubscribe">
                <a href="{{ $unsubscribeUrl }}">Unsubscribe from this newsletter</a>
            </div>
        </div>
    </div>
</body>
</html> 