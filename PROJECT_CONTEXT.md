# Tonercity API - Project Context

## Overview
Tonercity API is a headless e-commerce backend built with Laravel 12. It serves as an API-only application for the frontend client, meaning there are no customer-facing Blade views or Livewire components for the storefront. The only visual interface provided by the project is the Admin Panel, powered by **Filament v4**.

## Tech Stack
- **Framework**: Laravel 12 (PHP 8+)
- **Admin Panel**: Filament v5
- **Authentication**: Laravel Sanctum (Token-based API authentication)
- **Role & Permission Management**: Spatie Laravel Permission
- **Testing**: Pest 4
- **API Documentation Generation**: Scramble (dedoc/scramble)

## Architecture
The application follows a clean, service-oriented architecture:
1. **Routes**: Versioned API routes (`routes/api.php` under `v1/` prefix).
2. **Controllers**: Focused on handling HTTP requests and validation (Form Requests).
3. **Services**: Core business logic is contained here (e.g., `CartService`, `OrderService`, `InventoryService`).
4. **Models / Database**: Eloquent models with migrations, factories, and seeders.

## Existing Domains & Features

### 1. Authentication & Users
- Endpoints: `/api/v1/register`, `/api/v1/login`, `/api/v1/logout`, `/api/v1/me`
- Roles: Managed via Spatie (Admin, Customer, Vendor)
- User Profiles: Supports `User` having multiple `Address` records via `AddressController`.

### 2. Catalog (Categories & Products)
- Products and Categories support multilingual structures (`product_translations`, `category_translations`).
- Endpoints:
  - `GET /api/v1/categories` & `GET /api/v1/categories/{category}`
  - `GET /api/v1/products` & `GET /api/v1/products/{product}`
- Media: Products have media attachments managed via `MediaService`.

### 3. Cart & Wishlist
- **Cart**: Managed via `CartService`. Users can add, update, remove items, and clear their cart.
- **Wishlist**: Managed via `WishlistService` to toggle favorite products.
- Endpoints:
  - `GET /api/v1/cart`, `POST /api/v1/cart/items`, `PUT`, `DELETE`
  - `GET /api/v1/wishlist`, `POST`, `DELETE`

### 4. Checkout & Orders
- **Checkout**: A dedicated `CheckoutController` orchestrates the conversion of a Cart to an Order via `OrderService`.
- **Orders**: Tracking user orders (`Order` and `OrderItem`). 
- Endpoints:
  - `POST /api/v1/checkout`
  - `GET /api/v1/orders`, `GET /api/v1/orders/{order}`, `POST /api/v1/orders/{order}/cancel`

### 5. Inventory Management
- Handled by `InventoryService` which ensures stock checking, deduction during checkout, and prevents overselling.

### 6. Admin Panel (Filament)
- Located in `app/Filament`. Provides CRUD capabilities to administrators for managing products, categories, orders, users, etc.

## Additional Integrations (In-Progress / Planned based on DB)
- **Payments**: Models for `Payment` exist.
- **Shipments**: Models for `Shipment` exist.
- **Invoices (ZATCA)**: Models for `Invoice` exist.
- **Reviews & Questions**: Migrations exist for `reviews` and `questions`.
- **Coupons**: Migrations exist for `coupons`.

## Testing Convention
- All tests are written in Pest PHP (`tests/Feature`, `tests/Unit`).
- To run tests: `php artisan test --compact`

## Formatting
- The project enforces Laravel Pint formatting. 
- Command: `vendor/bin/pint --dirty --format agent`
