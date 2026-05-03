<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Order & Inventory System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f5f5f5;
        }
        .header {
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            font-size: 24px;
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .logout-btn {
            padding: 8px 16px;
            background-color: white;
            color: #4CAF50;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            text-decoration: none;
        }
        .content {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            padding: 20px;
            max-width: 1400px;
            margin: 0 auto;
        }
        .products-section, .orders-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            font-size: 20px;
            margin-bottom: 20px;
            color: #333;
        }
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
        }
        .product-card {
            border: 2px solid #ddd;
            border-radius: 8px;
            padding: 15px;
        }
        .product-card h3 {
            font-size: 16px;
            margin-bottom: 10px;
            color: #333;
        }
        .price {
            font-size: 18px;
            font-weight: bold;
            color: #4CAF50;
            margin-bottom: 5px;
        }
        .stock {
            font-size: 14px;
            color: #666;
        }
        .empty-cart {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        .orders-history {
            padding: 20px;
            max-width: 1400px;
            margin: 0 auto;
        }
        .orders-history h2 {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <header class="header">
        <h1>Order & Inventory System</h1>
        <div class="user-info">
            <span>Welcome, {{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
    </header>
    
    <div class="content">
        <div class="products-section">
            <h2>Available Products</h2>
            
            <div class="products-grid">
                @foreach($products as $product)
                <div class="product-card">
                    <h3>{{ $product->name }}</h3>
                    <p class="price">${{ $product->price }}</p>
                    <p class="stock">Stock: {{ $product->stock }}</p>
                </div>
                @endforeach
            </div>
        </div>
        
        <div class="orders-section">
            <h2>Quick Info</h2>
            <p>Use the Vue frontend for full ordering functionality.</p>
            <p style="margin-top: 10px;">Start Vue: <code>cd frontend && npm run dev</code></p>
            <p>Then visit: <code>http://localhost:5173</code></p>
        </div>
    </div>
    
    <div class="orders-history">
        <h2>Your Recent Orders</h2>
        @if($orders->count() > 0)
            <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                @foreach($orders as $order)
                    <div style="padding: 15px; border-bottom: 1px solid #eee;">
                        <strong>Order #{{ $order->id }}</strong> - ${{ $order->total_price }}
                        <span style="color: #666; font-size: 12px;">{{ $order->created_at->format('M d, Y') }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <p style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">No orders yet.</p>
        @endif
    </div>
</body>
</html>