# Laravel + Blade Starter Kit

---

## Introduction

Our Laravel 12 + Blade starter kit provides the typical functionality found in the Laravel Starter kits, but with a few key differences:

- A CoreUI/AdminLTE inspired design layout
- Blade + AlpineJS code

This kit aims to fill the gap where there is no simple **Blade only** starter kit available.

Our internal goal at Laravel Daily is to start using this starter kit for our Demo applications, to avoid overwhelming our audience with Vue/Livewire/React if we had used one of the official Laravel 12 starter kits.

**Note:** This is Work in Progress kit, so it will get updates and fixes/features as we go.

---

## Screenshots

![](https://laraveldaily.com/uploads/2025/05/LoginPage.png)

![](https://laraveldaily.com/uploads/2025/05/RegisterPage.png)

![](https://laraveldaily.com/uploads/2025/05/DashboardPage.png)

![](https://laraveldaily.com/uploads/2025/05/ProfilePage.png)

---

---

## Features

### Core POS Functionality
- **Inventory Management**: Real-time stock tracking with automatic deduction on sales.
- **Tax Handling**: Configurable tax rates (percentage/fixed) and automatic calculation.
- **Product Management**: SKU/Barcode support, variations, and image management.
- **Order Processing**: 
  - Cart management
  - Customer association
  - Split payments (Cash, Card, etc.)
  - Order voiding with stock restoration
  - Receipt generation (PDF)

### Multi-Tenancy & Security
- **Company Isolation**: Strict data segregation per company.
- **User Management**: Role-based access (Admin, Manager, Cashier).

### Reporting
- Sales tracking and history.
- Inventory transaction logs.

---

## How to use it?

### Installation
1. Clone the repository
2. Install dependencies:
   ```bash
   composer install
   ```
3. Setup environment:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
4. Run migrations:
   ```bash
   php artisan migrate --force
   ```
5. (Optional) Seed roles and default data:
   ```bash
   php artisan db:seed
   ```
   
### API Documentation
See [API_DOCS.md](API_DOCS.md) for detailed endpoint documentation including:
- `/api/products/barcode/lookup`
- `/api/orders` (with new customer_id and payment fields)
- `/api/tax-rates`
- `/api/inventory`


---

## Design Elements

If you want to see examples of what design elements we have, you can [visit the Wiki](<https://github.com/LaravelDaily/starter-kit/wiki/Design-Examples-(Raw-Files)>) and see the raw HTML files.

---

## Licence

Starter kit is open-sourced software licensed under the MIT license.
