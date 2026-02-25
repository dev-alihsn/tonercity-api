# Tonercity E-commerce API — Working Plan

> **Stack:** Laravel 12 · Filament 4 · MySQL/SQLite · Pest 4
> **Architecture:** Controllers → Form Requests → Service Layer → Eloquent Models

---

## Phase 1 — Foundation & Database Layer

Set up all models, migrations, factories, seeders, and relationships. This is the backbone everything else depends on.

### 1.1 User & Authentication

- [ ] `users` migration (add `role` enum, `language` enum to default migration)
- [ ] `User` model (casts, role helpers like `isAdmin()`)
- [ ] `UserFactory` + `UserSeeder`
- [ ] API authentication scaffolding (Laravel Sanctum — token-based)
- [ ] Register / Login / Logout / Me endpoints
- [ ] Form Requests: `RegisterRequest`, `LoginRequest`

### 1.2 Addresses

- [ ] `addresses` migration
- [ ] `Address` model + relationship (`User hasMany Address`)
- [ ] `AddressFactory` + `AddressSeeder`
- [ ] CRUD endpoints: `AddressController`
- [ ] Form Requests: `StoreAddressRequest`, `UpdateAddressRequest`

### 1.3 Categories (with Translations)

- [ ] `categories` migration (self-referencing `parent_id`)
- [ ] `category_translations` migration
- [ ] `Category` model + `CategoryTranslation` model
- [ ] Relationships: `Category hasMany CategoryTranslation`, self-referencing parent/children
- [ ] `CategoryFactory` + `CategorySeeder`
- [ ] API endpoints: list (nested tree), show
- [ ] Translation helper/trait for locale-aware accessors

### 1.4 Products (with Translations)

- [ ] `products` migration
- [ ] `product_translations` migration
- [ ] `Product` model + `ProductTranslation` model
- [ ] Relationships: `Product belongsTo Category`, `Product hasMany ProductTranslation`, `Product hasOne Inventory`
- [ ] `ProductFactory` + `ProductSeeder`
- [ ] API endpoints: list (filterable, searchable, paginated), show
- [ ] Form Requests: `ListProductsRequest` (filter/sort validation)

### 1.5 Media System

- [ ] `media` migration
- [ ] `product_media` pivot migration
- [ ] `Media` model
- [ ] Relationship: `Product belongsToMany Media` (via `product_media`)
- [ ] `Product` thumbnail relationship (via `thumbnail_id`)
- [ ] Media upload service (`MediaService`)

### 1.6 Inventory

- [ ] `inventories` migration
- [ ] `Inventory` model + relationship to `Product`
- [ ] `InventoryFactory` + `InventorySeeder`
- [ ] `InventoryService` — stock check, deduct, restock, low-stock alerts

### 1.7 Settings

- [ ] `settings` migration (key-value with JSON)
- [ ] `Setting` model
- [ ] `SettingSeeder` (site name, logo, theme defaults)
- [ ] API endpoint: GET settings (public)

### 1.8 Run Pint & Tests

- [ ] Run `vendor/bin/pint --dirty --format agent`
- [ ] Write feature tests for all Phase 1 endpoints
- [ ] Ensure all tests pass with `php artisan test --compact`

---

## Phase 2 — Cart & Wishlist

Server-side cart and wishlist functionality.

### 2.1 Cart

- [ ] `carts` migration (`user_id`, nullable for guest carts via session/token)
- [ ] `cart_items` migration (`cart_id`, `product_id`, `quantity`)
- [ ] `Cart` + `CartItem` models
- [ ] `CartService` — add, update quantity, remove, clear, calculate totals
- [ ] API endpoints: get cart, add item, update item, remove item, clear cart
- [ ] Validate stock availability on add/update
- [ ] Form Requests: `AddToCartRequest`, `UpdateCartItemRequest`

### 2.2 Wishlist

- [ ] `wishlists` migration (`user_id`, `product_id`, unique constraint)
- [ ] `Wishlist` model
- [ ] API endpoints: list, add, remove
- [ ] `WishlistService`

### 2.3 Tests

- [ ] Feature tests for cart CRUD operations
- [ ] Feature tests for wishlist operations
- [ ] Edge cases: out-of-stock, duplicate wishlist

---

## Phase 3 — Orders & Checkout

### 3.1 Orders

- [ ] `orders` migration
- [ ] `order_items` migration
- [ ] `Order` + `OrderItem` models
- [ ] Relationships: `Order belongsTo User`, `Order belongsTo Address`, `Order hasMany OrderItems`, `Order hasOne Payment`, `Order hasOne Shipment`, `Order hasOne Invoice`
- [ ] `OrderFactory` + `OrderSeeder`

### 3.2 Checkout Flow

- [ ] `CheckoutController` — single endpoint to place an order
- [ ] `CheckoutRequest` (validate address, cart not empty, stock available)
- [ ] `OrderService`:
  - Create order + order items from cart
  - Call `InventoryService` to deduct stock
  - Clear the cart
  - Return order with payment link
- [ ] Order status management (pending → paid → shipped → delivered)
- [ ] API endpoints: place order, list user orders, show order details, cancel order

### 3.3 Tests

- [ ] Checkout happy path
- [ ] Checkout with insufficient stock
- [ ] Order listing & filtering
- [ ] Order cancellation rules

---

## Phase 4A — Multi-Language Factories & Data Seeding

**Status:** ✅ COMPLETED

Enables automatic EN/AR translation generation and realistic Arabic product/category names in factories.

### 4A.1 Product Factory Multi-Language Support

- [x] Updated `ProductFactory` to auto-create EN/AR translations via `configure()` hook
- [x] Added `arabicProductName()` method with 15 realistic product names in Arabic
- [x] Added `arabicDescription()` method with 8 product descriptions in Arabic
- [x] Added `vendor_id` field to product definition (defaults to null)
- [x] Added `withoutTranslations()` state for tests that create translations manually

### 4A.2 Category Factory Multi-Language Support

- [x] Updated `CategoryFactory` to auto-create EN/AR translations via `configure()` hook
- [x] Added `arabicCategoryName()` method with 14 category names in Arabic
- [x] Added `arabicCategoryDescription()` method with realistic Arabic descriptions
- [x] Added `withoutTranslations()` state for backward compatibility with tests

### 4A.3 Seeder Updates

- [x] Updated `CategorySeeder` to use direct `Category::create()` instead of factory (avoid translation duplication)
- [x] Updated `ProductSeeder` to remove manual translation creation (now handled by factory)

---

## Phase 4B — Multi-Vendor Architecture

**Status:** ✅ COMPLETED

Enables future marketplace functionality with vendor-specific products and commission tracking.

### 4B.1 Vendor Model & Structure

- [x] Created `Vendor` model with relationships:
  - `belongsTo(User)` — vendor owner
  - `hasMany(Product)` — vendor's products
  - `belongsTo(Media)` — vendor logo
- [x] Created `VendorFactory` with admin state (`commission_rate: 0.00`)
- [x] Created `vendors` migration with fields: `user_id`, `name`, `slug`, `description`, `logo_id`, `is_active`, `commission_rate`

### 4B.2 Product-Vendor Integration

- [x] Added `vendor_id` FK to `products` table via migration
- [x] Updated `Product` model:
  - Added `vendor_id` to fillable
  - Added `vendor()` BelongsTo relationship
- [x] Updated `ProductFactory` to include `vendor_id` (nullable, defaults to null)

### 4B.3 Admin Vendor Setup

- [x] Created `AdminVendorSeeder` — creates "Admin Vendor" linked to admin user with 0% commission
- [x] Updated `DatabaseSeeder` execution order to run `AdminVendorSeeder` after users
- [x] All new products default to admin vendor in seeding flow

---

## Phase 4C — Role-Based Access Control (Spatie Permissions)

**Status:** ✅ COMPLETED

Replaces simple `users.role` enum with fine-grained permission management via Spatie Laravel Permissions.

### 4C.1 Installation & Setup

- [x] Installed `spatie/laravel-permission` package
- [x] Published package config and migrations
- [x] Ran migrations to create Spatie tables:
  - `roles`
  - `permissions`
  - `model_has_roles`
  - `model_has_permissions`
  - `role_has_permissions`

### 4C.2 Roles & Permissions Definition

- [x] Created `RolePermissionSeeder` with 3 roles:
  - **admin**: manage_products, manage_categories, manage_vendors, manage_orders, manage_users, manage_payments, manage_shipments, view_reports, manage_settings, manage_permissions
  - **customer**: place_order, view_own_orders, view_own_profile, manage_own_address, manage_wishlist, manage_cart
  - **vendor**: manage_own_products, view_vendor_orders, view_vendor_sales, manage_vendor_profile

### 4C.3 User Model & Factory Updates

- [x] Updated `User` model:
  - Added `HasRoles` trait from Spatie
  - Removed `role` from fillable (replaced by Spatie roles)
  - Added helper methods: `isAdmin()`, `isCustomer()`, `isVendor()`, `canManageProduct()`
  - Added `vendor()` HasOne relationship to Vendor model
- [x] Updated `UserFactory`:
  - Removed `role` from definition
  - Added `admin()`, `customer()`, `vendor()` states that auto-assign roles via afterCreating hooks
  - Roles created on-demand if they don't exist

### 4C.4 Database Schema Changes

- [x] Created migration to drop `users.role` column
- [x] All roles now managed through `model_has_roles` pivot table

### 4C.5 Seeder & Controller Updates

- [x] Updated `UserSeeder` to assign roles to users after creation
- [x] Updated `DatabaseSeeder` execution order:
  1. RolePermissionSeeder (creates roles/permissions)
  2. UserSeeder (creates users and assigns roles)
  3. AdminVendorSeeder (creates admin vendor)
  4. Other seeders
- [x] Updated `AuthController::register()` to assign 'customer' role to new users
- [x] Updated `AuthController::me()` to return roles and permissions arrays

---

## Phase 4 — Payments

### 4.1 Payment Infrastructure

- [ ] `payments` migration
- [ ] `Payment` model
- [ ] `PaymentService` (strategy pattern for multiple providers)
- [ ] Payment provider interface: `PaymentProviderContract`

### 4.2 Payment Providers

- [ ] Stripe / MyFatoorah integration
- [ ] Tabby (BNPL) integration
- [ ] Tamara (BNPL) integration
- [ ] Webhook controllers for each provider
- [ ] Payment status updates → Order status sync

### 4.3 Payment Endpoints

- [ ] Initiate payment (redirect URL or session)
- [ ] Payment callback / webhook handler
- [ ] Payment status check
- [ ] Refund endpoint (admin)

### 4.4 Tests

- [ ] Payment creation and status transitions
- [ ] Webhook signature verification
- [ ] Refund flow

---

## Phase 5 — Shipping

### 5.1 Shipment Management

- [ ] `shipments` migration
- [ ] `Shipment` model
- [ ] `ShippingService`:
  - Create shipment after payment confirmed
  - Calculate shipping rates
  - Update shipment status
  - Store tracking number
- [ ] Shipping provider interface: `ShippingProviderContract`

### 5.2 Shipping Endpoints

- [ ] Get shipping rates for an address
- [ ] Track shipment by order
- [ ] Admin: update shipment status

### 5.3 Tests

- [ ] Shipment creation flow
- [ ] Status transitions
- [ ] Rate calculation

---

## Phase 6 — ZATCA E-Invoicing

### 6.1 Invoice Generation

- [ ] `invoices` migration
- [ ] `Invoice` model
- [ ] `ZatcaService`:
  - Generate compliant XML invoice
  - Calculate VAT
  - Generate QR code
  - Submit to ZATCA API
  - Store XML and QR paths
- [ ] Auto-generate invoice on successful payment

### 6.2 Invoice Endpoints

- [ ] View invoice (customer)
- [ ] Download invoice PDF/XML
- [ ] Admin: list all invoices, export

### 6.3 Tests

- [ ] Invoice generation from order
- [ ] VAT calculation accuracy
- [ ] QR code generation

---

## Phase 7 — Notifications & Email

- [ ] Order confirmation email (customer)
- [ ] Payment received notification
- [ ] Shipment status update notification
- [ ] Low-stock alert (admin)
- [ ] Use Laravel Notifications (mail channel, optionally database channel)
- [ ] Queue all notifications (`ShouldQueue`)

---

## Phase 8 — Admin Panel (Filament 4)

### 8.1 Resources

- [ ] `ProductResource` — CRUD with translation tabs
- [ ] `CategoryResource` — CRUD with nested tree view
- [ ] `OrderResource` — list, view, update status
- [ ] `UserResource` — list, view, toggle role
- [ ] `PaymentResource` — list, view
- [ ] `ShipmentResource` — list, update status
- [ ] `InvoiceResource` — list, download
- [ ] `SettingResource` — edit site config
- [ ] `InventoryResource` — stock levels, low-stock filters

### 8.2 Dashboard

- [ ] Total orders / revenue widgets
- [ ] Recent orders list
- [ ] Low-stock alerts widget
- [ ] Orders by status chart

### 8.3 Tests

- [ ] Admin authentication gates
- [ ] Resource CRUD operations

---

## Phase 9 — Multilingual Support (i18n)

- [ ] Translation trait/concern for models with `*_translations` tables
- [ ] Locale detection middleware (from header `Accept-Language` or user preference)
- [ ] API responses return translated fields based on locale
- [ ] Validation messages in Arabic & English
- [ ] Error messages in Arabic & English

---

## Phase 10 — Polish, Optimization & Security

### 10.1 Performance

- [ ] Eager loading audit (prevent N+1 queries)
- [ ] Database indexing review
- [ ] API response caching where appropriate
- [ ] Rate limiting on auth & checkout endpoints

### 10.2 Security

- [ ] Input sanitization review
- [ ] Authorization policies for all resources
- [ ] CORS configuration
- [ ] API versioning (`/api/v1/`)

### 10.3 Documentation

- [ ] API documentation (OpenAPI / Swagger or Scribe)
- [ ] Postman collection export

### 10.4 Final Testing

- [ ] Full test suite green
- [ ] Manual sandbox testing (payments, shipping)
- [ ] Load testing on critical endpoints

---

## Dependency Graph

```
Phase 1 (Foundation)
  ├── Phase 2 (Cart & Wishlist)
  │     └── Phase 3 (Orders & Checkout)
  │           ├── Phase 4 (Payments)
  │           │     └── Phase 6 (ZATCA)
  │           └── Phase 5 (Shipping)
  ├── Phase 7 (Notifications) — after Phase 3
  ├── Phase 8 (Admin Panel) — can start after Phase 1, grows with each phase
  └── Phase 9 (i18n) — can start after Phase 1, applies across all phases

Phase 10 (Polish) — after all other phases
```

---

## Estimated Effort Per Phase

| Phase | Description | Effort |
|-------|-------------|--------|
| 1 | Foundation & Database | Large |
| 2 | Cart & Wishlist | Medium |
| 3 | Orders & Checkout | Large |
| 4 | Payments | Large |
| 5 | Shipping | Medium |
| 6 | ZATCA | Medium |
| 7 | Notifications | Small |
| 8 | Admin Panel | Large |
| 9 | i18n | Small–Medium |
| 10 | Polish & Security | Medium |
