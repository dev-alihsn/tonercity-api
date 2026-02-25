# Business Requirements Document (BRD) &

# Working Plan

## 1. Project Overview

**Project Name:** E-commerce System
**Objective:**
To design and develop a scalable e-commerce platform that enables customers to browse products,
place orders, make online payments, and track shipments, while allowing administrators to manage
products, inventory, orders, payments, and shipping operations efficiently.

## 2. Stakeholders

```
Business Owner
Customers (End Users)
Admin / Operations Team
Shipping Providers
Payment Gateway Providers
Development Team
```
## 3. User Roles

```
Guest User
Registered Customer
Admin
```
## 4. Functional Requirements

### 4.0 Multilingual Support

```
The system will support multiple languages
Full support for Arabic and English
Language switcher for users
RTL (Right-to-Left) support for Arabic
Translated UI, validation messages, and system notifications
Multilingual product data (name, description, attributes)
```
## 4. Functional Requirements

### 4.1 Customer Features

```
User registration and authentication
```
#### • • • • • • • • • • • • • • • •


```
Browse products by category
Search and filter products
View product details
Add and remove items from cart
Checkout process
Online payment processing
Order confirmation
Order history
Shipment tracking
Email notifications
```
### 4.2 Admin Features

```
Secure admin login
Dashboard overview (orders, revenue, stock alerts)
Product management (Create, Read, Update, Delete)
Category management
Inventory management
Order management
Shipment management
Payment transaction tracking
User management
Reports and data export
System settings management (site name, logo, theme colors)
```
### 4.3 Inventory Management

```
Stock quantity tracking per product
Low-stock alerts
Automatic stock deduction after successful orders
```
### 4.4 Shipping Management

```
Shipping rate calculation
Integration with one or more shipping providers
Shipment statuses:
Pending
Packed
Shipped
Delivered
Tracking number management
Shipment status updates
```
### 4.5 Payment Gateway Integration

```
Secure online payment processing
```
#### • • • • • • • • • • • • • • • • • • • • • • • • • • • • • • • • • •


```
Support for credit/debit cards
Integration with BNPL platforms (Tabby & Tamara)
Payment statuses:
Pending
Paid
Failed
Refunded
Payment webhook handling
Transaction logging
```
### 4.6 ZATCA (Zakat, Tax and Customs Authority) Integration

```
Compliance with Saudi ZATCA e-invoicing (FATOORA) regulations
Generation of compliant tax invoices (XML format)
QR code generation for invoices
VAT calculation and reporting
Invoice submission and clearance
Secure storage of invoices and audit logs
Ability to export invoices for accounting and regulatory purposes
```
## 7. Technology Stack

```
Backend Framework: Laravel
Frontend Interaction: Livewire 3
Admin Panel: Filament 4
UI Components: MaryUI
Database: MySQL / PostgreSQL /  Sqlite for dev stage
Authentication: Laravel built-in authentication
Payment Gateways:  Myfatora or whatever is available 
Shipping Integration: API-based shipping providers
Hosting: VPS or Cloud Hosting, Laravel cloud for dev or testing stages
```
## 8. System Architecture

The system follows a clean and maintainable architecture based on separation of concerns:

```
Controllers
```
```
Handle HTTP requests and validation
```
```
Delegate business logic to service classes
```
```
Service Layer
```
```
Contains core business logic
```
#### • • • • • • • • • • • • • • • • • • • • • • • • • • • • • •


```
Handles orders, inventory, payments, and shipping
```
```
Ensures reusability and testability
```
```
Livewire Components
```
```
Responsible for UI rendering
```
```
Handle user interactions and action triggers
```
```
Do not contain business logic
```
```
Filament Admin Panel
```
```
Used for all admin and operational dashboards
```
```
Manages products, inventory, orders, users, and reports
```
```
Models
```
```
Represent database entities
```
```
Define relationships and basic data operations
```
## 9. Order Processing Flow

```
Customer adds products to cart
Customer initiates checkout
Controller validates request data
OrderService creates the order
InventoryService updates stock levels
PaymentService processes the payment
ShippingService creates shipment and tracking
Order status is updated
Customer receives confirmation
```
# Working Plan

## Phase 1 – Planning & Setup

```
Finalize requirements
Database schema design
System architecture design
Laravel project setup
Livewire, Filament, and MaryUI configuration
```
#### • • • • • • • • • • • •

#### 1.

#### 2.

#### 3.

#### 4.

#### 5.

#### 6.

#### 7.

#### 8.

#### 9.

#### •

#### •

#### •

#### •

#### •


**Deliverables:**

```
Approved BRD
Database diagram
```
## Phase 2 – Static Frontend Design

```
Customer-facing UI implementation using Livewire and MaryUI
Static pages (home, product listing, product details)
Cart and checkout UI (static, no business logic)
```
## Phase 3 – Core Backend Development

```
Authentication system
Product and category management
Inventory management logic
Cart and checkout business logic
Order management system
Payment gateway integration (including Tabby & Tamara)
ZATCA e-invoicing integration
Shipping provider integration
```
## Phase 4 – Admin & Operations

```
Admin dashboard using Filament
Inventory alerts and monitoring
Order and shipment management
Payment reports and logs
```
## Phase 5 – Manual Testing & Optimization

```
Manual functional testing by the developer
Payment and shipping sandbox testing
Bug fixing
Performance optimization
```
#### • • • • • • • • • • • • • • • • • • • • •


