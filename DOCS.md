# UltraGranit Technical Documentation

## Overview
UltraGranit is a custom-built MVC-based web application in PHP for selling granite-based products. It features frontend views, admin control, session management, Telegram bot integration, real-time exchange rate updates, and a layered data-access model.

---

## Core Components

### Architecture
- **MVC** pattern: Custom implementation with controller/action routing
- **Core Classes**:
    - `Core`: Application bootstrapper and DI container
    - `Router`: Routes URL path to controller and action
    - `Controller`: Base controller class for routing requests and rendering views
    - `Model`: ActiveRecord-style base model
    - `Session`: Session wrapper for storing user data
    - `Template`: View renderer with parameter injection

---

## Backend Modules

### Product Management
- `ProductController`
    - `actionIndex` – lists all products
    - `actionView($id)` – view single product with photos and breadcrumbs
    - `actionAdd($categoryId)` – add new product
    - `actionEdit($id)` – edit existing product
    - `actionDelete($id)` – delete a product
    - `actionLoadReviews()` – load reviews via AJAX
    - `actionAddReview()` – add review via AJAX (with validation and profanity filter)
    - `actionDeleteReview()` – delete review (admin only)
    - `actionSearchAjax()` – live product search
    - `actionLoadMore()` – pagination with sorting

### Basket Module
- `BasketController`
    - `actionIndex` – show products in basket
    - `actionAdd($productId)` – add product
    - `actionRemove($itemId)` – remove item
    - `actionClear()` – clear all

### Order Management
- `OrderController`
    - `actionCreate()` – create order (API used in frontend)

### Admin Panel
- `AdminController`
    - `actionIndex()` – dashboard with materials, orders, currency rate
    - `actionUpdatePrice()` – update material price
    - `actionUpdateOrderStatus()` – mark order as processed
    - `actionUpdateExchangeRate()` – set USD rate
    - `actionToggleAutoUpdate()` – enable/disable auto-updating

### Pages
- `AboutController`, `ContactsController`, `SiteController` (e.g. `actionPrivacy()` for GDPR page)

---

## Models
- `Product`: CRUD, photo management, filtering, pagination
- `Category`: Create/update/delete categories
- `Basket`: Handles session-based or DB-persistent baskets
- `Order`: Order logic and order-product relation
- `ProductReview`: Add/delete/get reviews
- `Users`: User authentication, admin check

---

## API Endpoints

### Public JSON API

| Method | Path                     | Description                         |
|--------|--------------------------|-------------------------------------|
| GET    | /product/load-reviews    | Load reviews by product ID          |
| POST   | /product/add-review      | Add product review (AJAX)           |
| GET    | /product/search-ajax     | Search products and categories      |
| GET    | /product/load-more       | Product pagination                  |
| GET    | /api/currency            | Get USD exchange rate               |
| POST   | /order/create            | Submit new order                    |
| POST   | /product/deleteReview    | Delete product review (admin-only)  |

### Basket Operations (via forms or buttons)

| Method | Path           | Description                |
|--------|----------------|----------------------------|
| POST   | /basket/add    | Add product to basket      |
| POST   | /basket/remove | Remove product from basket |
| POST   | /basket/clear  | Clear basket               |
| GET    | /basket/index  | View basket                |

### User Actions

| Method | Path            | Description          |
|--------|------------------|----------------------|
| POST   | /users/login     | Login form           |
| POST   | /users/register  | Register form        |
| GET    | /account         | View user account    |
| GET    | /login           | Login page           |
| GET    | /register        | Registration page    |

### Admin API & Tools

| Method | Path                   | Description                    |
|--------|------------------------|--------------------------------|
| POST   | /admin/update-price    | Update material price          |
| POST   | /admin/update-status   | Change order status            |
| POST   | /admin/update-rate     | Manually set exchange rate     |
| POST   | /admin/toggle-autoupdate | Toggle auto-currency update |
| GET    | /admin                 | Admin dashboard                |


## Features

### Telegram Notifications
- Class: `TelegramBot`
- On order: sends order details to all admins/managers via Telegram

### Currency API Integration
- Uses PrivatBank open API for real-time USD rate
- Controlled via `CurrencyUpdater::updateIfNeeded()` on each load

### GDPR Compliance
- Cookie consent popup via `cookieconsent` library
- Privacy Policy on `/site/privacy`

---

## Frontend Interactions (JS)
- `review.js`: AJAX review submit/delete
- `search.js`: live product and category search

---

## Database Structure (Selected)
- `users`, `product`, `category`, `basket`, `order`, `order_products`, `product_reviews`, `currency`, `materials`

---

## Launch Requirements
- PHP 8+, MySQL, web server
- Database credentials in `MVC/config/db.php`
- Composer dependencies installed

---

## Author
Oleksandr Koneschuk
