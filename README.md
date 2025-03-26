# Karen Culture Sales

A Laravel-based e-commerce platform for selling cultural products.

## Features

- Product management with multiple images
- Category management
- Shopping cart functionality
- Order processing
- Driver delivery system
- Payment integration (Stripe)
- Admin dashboard
- Customer reviews and ratings

## Requirements

- PHP >= 8.1
- Composer
- Node.js & NPM
- MySQL/PostgreSQL
- Laravel 10.x

## Installation

1. Clone the repository:
```bash
git clone https://github.com/yourusername/karen_culture_sales.git
cd karen_culture_sales
```

2. Install PHP dependencies:
```bash
composer install
```

3. Install NPM dependencies:
```bash
npm install
```

4. Create environment file:
```bash
cp .env.example .env
```

5. Generate application key:
```bash
php artisan key:generate
```

6. Configure your database in `.env` file:
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

7. Run migrations:
```bash
php artisan migrate
```

8. Create storage link:
```bash
php artisan storage:link
```

9. Start the development server:
```bash
php artisan serve
```

10. In a separate terminal, start Vite:
```bash
npm run dev
```

## Configuration

1. Configure your mail settings in `.env`:
```
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_email
MAIL_FROM_NAME="${APP_NAME}"
```

2. Configure Stripe settings in `.env`:
```
STRIPE_KEY=your_stripe_publishable_key
STRIPE_SECRET=your_stripe_secret_key
STRIPE_WEBHOOK_SECRET=your_stripe_webhook_secret
```

## License

This project is licensed under the MIT License - see the LICENSE file for details.
