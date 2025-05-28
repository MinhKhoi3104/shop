# Simple Green - Organic Market

A PHP-based e-commerce website for Simple Green Organic Market, featuring product management, shopping cart functionality, and MoMo payment integration.

## Features

- Product catalog with categories
- Shopping cart functionality
- User-friendly interface
- MoMo payment integration
- Responsive design
- Order management

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- XAMPP (or similar local development environment)
- Web browser with JavaScript enabled

## Installation

### 1. XAMPP Setup

1. Download and install XAMPP from [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. Start Apache and MySQL services from XAMPP Control Panel
3. Place the project files in `C:\xampp\htdocs\shop` (Windows) or `/Applications/XAMPP/htdocs/shop` (Mac)

### 2. Database Setup

1. Open your web browser and navigate to `http://localhost/phpmyadmin`
2. Create a new database named `shop`:
   ```sql
   CREATE DATABASE shop;
   ```
3. Select the `shop` database and import the `schema.sql` file from the project root directory

### 3. Configuration

1. Create a `config.php` file in the root directory with your database credentials:
```php
<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'shop');
```

2. Update MoMo payment credentials in `momo.php`:
```php
$partnerCode = "YOUR_PARTNER_CODE";
$accessKey = "YOUR_ACCESS_KEY";
$secretKey = "YOUR_SECRET_KEY";
```

### 4. File Structure

```
shop/
├── assets/
│   ├── css/
│   ├── js/
│   └── img/
├── templates/
│   ├── header.php
│   └── footer.php
├── config.php
├── functions.php
├── index.php
├── product.php
├── checkout.php
├── momo.php
├── order_success.php
├── schema.sql
└── README.md
```

## Usage

1. Start XAMPP (Apache and MySQL)
2. Navigate to `http://localhost/shop` in your web browser
3. Browse products, add items to cart, and proceed to checkout
4. Test MoMo payment integration (minimum order: 50,000 VND)

## Payment Integration

### MoMo Payment

- Minimum order amount: 50,000 VND
- Test environment: https://test-payment.momo.vn
- Production environment: https://payment.momo.vn

### Payment Methods

1. Cash on Delivery (COD)
2. Bank Transfer
3. MoMo Wallet

## Development

### Adding Products

1. Access phpMyAdmin
2. Navigate to the `products` table
3. Insert new products with required fields:
   - name
   - description
   - price
   - image
   - category

### Customizing

- Modify templates in `templates/` directory
- Update styles in `assets/css/`
- Add JavaScript functionality in `assets/js/`
