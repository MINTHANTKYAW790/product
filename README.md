# Order & Inventory System (Mini SaaS)

A multi-user order and inventory system built with Laravel (Backend) and Vue 3 (Frontend).

## Features

- **Authentication**: Login system using Laravel Sanctum (API Token-based)
- **Product Management**: View available products with stock and pricing
- **Order Creation**: Place orders with automatic total price calculation
- **Order History**: View past orders with details
- **Cart System**: Add/remove products with quantity control

## Tech Stack

- **Backend**: Laravel 12 + Laravel Sanctum
- **Frontend**: Vue 3 + Vite + Vue Router + Axios
- **Database**: MySQL (via XAMPP)

## Project Structure

```
product/
├── app/
│   ├── Http/Controllers/Api/
│   │   ├── AuthController.php
│   │   ├── ProductController.php
│   │   └── OrderController.php
│   └── Models/
│       ├── User.php
│       ├── Product.php
│       ├── Order.php
│       └── OrderItem.php
├── database/
│   ├── migrations/
│   └── seeders/
├── routes/
│   └── api.php
└── frontend/
    ├── src/
    │   ├── views/
    │   │   ├── Login.vue
    │   │   └── Dashboard.vue
    │   ├── router/
    │   └── main.js
    └── package.json
```

## API Endpoints

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| POST | `/api/login` | User login | No |
| POST | `/api/logout` | User logout | Yes |
| GET | `/api/user` | Get current user | Yes |
| GET | `/api/products` | List all products | No |
| POST | `/api/orders` | Create new order | Yes |
| GET | `/api/orders` | List user orders | Yes |

## Setup Instructions

### Prerequisites

- PHP 8.2+
- Composer
- Node.js & npm
- XAMPP (MySQL)

### Backend Setup (Laravel)

1. **Navigate to project directory**:
   ```bash
   cd c:\xampp\htdocs\product
   ```

2. **Install dependencies**:
   ```bash
   composer install
   ```

3. **Configure environment**:
   Copy `.env.example` to `.env` and update database settings:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=product
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Generate application key**:
   ```bash
   php artisan key:generate
   ```

5. **Run migrations**:
   ```bash
   php artisan migrate
   ```

6. **Seed sample products**:
   ```bash
   php artisan db:seed --class=ProductSeeder
   ```

7. **Create test user** (optional):
   ```bash
   php artisan tinker
   ```
   Then run:
   ```php
   App\Models\User::create(['name' => 'Test User', 'email' => 'test@example.com', 'password' => bcrypt('password')]);
   ```

8. **Start Laravel server**:
   ```bash
   php artisan serve
   ```
   The API will be available at `http://localhost:8000`

### Frontend Setup (Vue)

1. **Navigate to frontend directory**:
   ```bash
   cd c:\xampp\htdocs\product\frontend
   ```

2. **Install dependencies**:
   ```bash
   npm install
   ```

3. **Start development server**:
   ```bash
   npm run dev
   ```
   The frontend will be available at `http://localhost:5173`

## Usage

1. Start the Laravel backend server
2. Start the Vue frontend server
3. Open `http://localhost:5173` in your browser
4. Login with test credentials:
   - Email: `test@example.com`
   - Password: `password`
5. Browse products and add items to cart
6. Place an order and view order history

## Database Schema

### users
| Column | Type | Description |
|--------|------|-------------|
| id | BigInt | Primary key |
| name | String | User name |
| email | String | Unique email |
| password | String | Hashed password |
| timestamps | Timestamp | Created/Updated |

### products
| Column | Type | Description |
|--------|------|-------------|
| id | BigInt | Primary key |
| name | String | Product name |
| price | Decimal | Product price |
| stock | Integer | Available stock |
| timestamps | Timestamp | Created/Updated |

### orders
| Column | Type | Description |
|--------|------|-------------|
| id | BigInt | Primary key |
| user_id | ForeignId | References users |
| total_price | Decimal | Order total |
| timestamps | Timestamp | Created/Updated |

### order_items
| Column | Type | Description |
|--------|------|-------------|
| id | BigInt | Primary key |
| order_id | ForeignId | References orders |
| product_id | ForeignId | References products |
| quantity | Integer | Item quantity |
| price | Decimal | Price at order time |
| timestamps | Timestamp | Created/Updated |

## License

This project is for interview/test purposes.

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
