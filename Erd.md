# E-commerce System – ERD (Entity Relationship Diagram)

This document describes the core database entities and their relationships for the e-commerce system.

---

## 1. Users

**users**

- id (PK)
- name
- email
- password
- language (en, ar)
- created_at
- updated_at

**Relationships:**

- User **has many** Orders
- User **has many** Addresses
- User **has roles** (Spatie — admin, customer, vendor)
- User **has permissions** (Spatie — fine-grained access control)

**Note:** The `role` column has been removed. User roles are now managed via Spatie Laravel Permissions.

---

## 2. Addresses

**addresses**

- id (PK)
- user\_id (FK → users.id)
- city
- address\_line
- postal\_code
- phone
- created\_at
- updated\_at

**Relationships:**

- Address **belongs to** User
- Address **used in** Orders (shipping address)

---

## 2B. Vendors

**vendors**

- id (PK)
- user_id (FK → users.id)
- name
- slug (unique)
- description
- logo_id (nullable, FK → media.id)
- is_active (boolean)
- commission_rate (decimal: 0-100, percentage)
- created_at
- updated_at

**Relationships:**

- Vendor **belongs to** User
- Vendor **has many** Products
- Vendor **belongs to** Media (logo)

**Note:** The admin user has a default vendor (\"Admin Vendor\") with 0% commission rate.

---

## 3. Categories

**categories**

- id (PK)
- parent\_id (nullable, self-reference)
- created\_at
- updated\_at

**Relationships:**

- Category **has many** CategoryTranslations
- Category **may belong to** another Category

---

**category\_translations**

- id (PK)
- category\_id (FK → categories.id)
- locale (en, ar)
- name
- created\_at
- updated\_at

**Constraints:**

- UNIQUE (category\_id, locale)

---

## 4. Products

**products**

- id (PK)
- category_id (FK → categories.id)
- vendor_id (nullable, FK → vendors.id)
- sku
- price
- thumbnail_id (FK → media.id)
- is_active
- created_at
- updated_at

**Relationships:**

- Product **belongs to** Category
- Product **belongs to** Vendor
- Product **has many** ProductTranslations
- Product **has one** Inventory
- Product **has many** OrderItems

---

**product\_translations**

- id (PK)
- product\_id (FK → products.id)
- locale (en, ar)
- name
- description
- created\_at
- updated\_at

**Constraints:**

- UNIQUE (product\_id, locale)

**Relationships:**

- Product **belongs to** Category
- Product **has one** Inventory
- Product **has many** OrderItems

---

## 5. Inventory

**inventories**

- id (PK)
- product\_id (FK → products.id)
- quantity
- low\_stock\_level
- created\_at
- updated\_at

**Relationships:**

- Inventory **belongs to** Product

---

## 6. Orders

**orders**

- id (PK)
- user\_id (FK → users.id)
- address\_id (FK → addresses.id)
- total\_amount
- status (pending, paid, shipped, delivered)
- payment\_status
- created\_at
- updated\_at

**Relationships:**

- Order **belongs to** User
- Order **has many** OrderItems
- Order **has one** Payment
- Order **has one** Shipment
- Order **has one** Invoice (ZATCA)

---

## 7. Order Items

**order\_items**

- id (PK)
- order\_id (FK → orders.id)
- product\_id (FK → products.id)
- quantity
- price

**Relationships:**

- OrderItem **belongs to** Order
- OrderItem **belongs to** Product

---

## 8. Payments

**payments**

- id (PK)
- order\_id (FK → orders.id)
- provider (stripe, paymob, tabby, tamara)
- transaction\_id
- amount
- status (pending, paid, failed, refunded)
- created\_at
- updated\_at

**Relationships:**

- Payment **belongs to** Order

---

## 9. Shipments

**shipments**

- id (PK)
- order\_id (FK → orders.id)
- provider
- tracking\_number
- status (pending, shipped, delivered)
- created\_at
- updated\_at

**Relationships:**

- Shipment **belongs to** Order

---

## 10. Invoices (ZATCA)

**invoices**

- id (PK)
- order\_id (FK → orders.id)
- invoice\_number
- vat\_amount
- total\_with\_vat
- xml\_path
- qr\_code
- created\_at
- updated\_at

**Relationships:**

- Invoice **belongs to** Order

---

## 11. Media

**media**

- id (PK)
- disk (local, s3)
- path
- type (image, video)
- created\_at
- updated\_at

**Relationships:**

- Media **used by** Products (thumbnail & gallery)

---

**product\_media**

- id (PK)
- product\_id (FK → products.id)
- media\_id (FK → media.id)
- created\_at
- updated\_at

---

## 12. Spatie Permission Tables

These tables manage role-based access control (RBAC) using Spatie Laravel Permissions.

**roles**

- id (PK)
- name (admin, customer, vendor)
- guard_name (default: api)
- created_at
- updated_at

**permissions**

- id (PK)
- name (e.g., manage_products, place_order, view_own_orders)
- guard_name (default: api)
- created_at
- updated_at

**model_has_roles**

- role_id (FK → roles.id)
- model_type (App\\Models\\User)
- model_id (FK → users.id)

**model_has_permissions**

- permission_id (FK → permissions.id)
- model_type (App\\Models\\User)
- model_id (FK → users.id)

**role_has_permissions**

- permission_id (FK → permissions.id)
- role_id (FK → roles.id)

**Relationships:**

- User can have many roles via model_has_roles
- User can have direct permissions via model_has_permissions
- Role can have many permissions via role_has_permissions

**Permission Examples:**

- Admin: manage_products, manage_categories, manage_vendors, manage_orders, manage_users, manage_payments, manage_shipments, view_reports, manage_settings, manage_permissions
- Customer: place_order, view_own_orders, view_own_profile, manage_own_address, manage_wishlist, manage_cart
- Vendor: manage_own_products, view_vendor_orders, view_vendor_sales, manage_vendor_profile

---

## 13. Settings

Settings table has been removed. Site configuration now uses environment variables and config files.

