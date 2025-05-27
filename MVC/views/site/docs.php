    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            color: #333;
        }
        h1, h2, h3 {
            color: #2c3e50;
        }
        code {
            background: #eee;
            padding: 2px 6px;
            border-radius: 4px;
        }
        pre {
            background: #f0f0f0;
            padding: 10px;
            border-radius: 5px;
            overflow-x: auto;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px 12px;
            text-align: left;
        }
        th {
            background-color: #eee;
        }
        hr {
            margin: 30px 0;
        }
    </style>

<body>
<div class="container">
<h1>UltraGranit Technical Documentation</h1>
<h2>Overview</h2>
<p>UltraGranit is a custom-built MVC-based web application in PHP for selling granite-based products. It features frontend views, admin control, session management, Telegram bot integration, real-time exchange rate updates, and a layered data-access model.</p>

<h2>Core Components</h2>
<h3>Architecture</h3>
<ul>
    <li><strong>MVC</strong> pattern: Custom implementation with controller/action routing</li>
    <li><strong>Core Classes</strong>:
        <ul>
            <li><code>Core</code>: Application bootstrapper and DI container</li>
            <li><code>Router</code>: Routes URL path to controller and action</li>
            <li><code>Controller</code>: Base controller class for routing requests and rendering views</li>
            <li><code>Model</code>: ActiveRecord-style base model</li>
            <li><code>Session</code>: Session wrapper for storing user data</li>
            <li><code>Template</code>: View renderer with parameter injection</li>
        </ul>
    </li>
</ul>

<h2>Backend Modules</h2>
<h3>Product Management</h3>
<pre><code>ProductController:
  actionIndex, actionView, actionAdd, actionEdit, actionDelete
  actionLoadReviews, actionAddReview, actionDeleteReview
  actionSearchAjax, actionLoadMore</code></pre>
<h3>Basket Module</h3>
<pre><code>BasketController:
  actionIndex, actionAdd, actionRemove, actionClear</code></pre>
<h3>Order Management</h3>
<pre><code>OrderController:
  actionCreate</code></pre>
<h3>Admin Panel</h3>
<pre><code>AdminController:
  actionIndex, actionUpdatePrice, actionUpdateOrderStatus
  actionUpdateExchangeRate, actionToggleAutoUpdate</code></pre>

<h3>Pages</h3>
<p>AboutController, ContactsController, SiteController (e.g. <code>actionPrivacy()</code> for GDPR page)</p>

<h2>Models</h2>
<ul>
    <li><code>Product</code>, <code>Category</code>, <code>Basket</code>, <code>Order</code>, <code>ProductReview</code>, <code>Users</code></li>
</ul>

<h2>API Endpoints</h2>
<h3>Public JSON API</h3>
<table>
    <thead><tr><th>Method</th><th>Path</th><th>Description</th></tr></thead>
    <tbody>
    <tr><td>GET</td><td>/product/load-reviews</td><td>Load reviews by product ID</td></tr>
    <tr><td>POST</td><td>/product/add-review</td><td>Add product review (AJAX)</td></tr>
    <tr><td>GET</td><td>/product/search-ajax</td><td>Search products and categories</td></tr>
    <tr><td>GET</td><td>/product/load-more</td><td>Product pagination</td></tr>
    <tr><td>GET</td><td>/api/currency</td><td>Get USD exchange rate</td></tr>
    <tr><td>POST</td><td>/order/create</td><td>Submit new order</td></tr>
    <tr><td>POST</td><td>/product/deleteReview</td><td>Delete product review (admin-only)</td></tr>
    </tbody>
</table>

<h3>Basket Operations</h3>
<table>
    <thead><tr><th>Method</th><th>Path</th><th>Description</th></tr></thead>
    <tbody>
    <tr><td>POST</td><td>/basket/add</td><td>Add product to basket</td></tr>
    <tr><td>POST</td><td>/basket/remove</td><td>Remove product from basket</td></tr>
    <tr><td>POST</td><td>/basket/clear</td><td>Clear basket</td></tr>
    <tr><td>GET</td><td>/basket/index</td><td>View basket</td></tr>
    </tbody>
</table>

<h3>User Actions</h3>
<table>
    <thead><tr><th>Method</th><th>Path</th><th>Description</th></tr></thead>
    <tbody>
    <tr><td>POST</td><td>/users/login</td><td>Login form</td></tr>
    <tr><td>POST</td><td>/users/register</td><td>Register form</td></tr>
    <tr><td>GET</td><td>/account</td><td>View user account</td></tr>
    <tr><td>GET</td><td>/login</td><td>Login page</td></tr>
    <tr><td>GET</td><td>/register</td><td>Registration page</td></tr>
    </tbody>
</table>

<h3>Admin API & Tools</h3>
<table>
    <thead><tr><th>Method</th><th>Path</th><th>Description</th></tr></thead>
    <tbody>
    <tr><td>POST</td><td>/admin/update-price</td><td>Update material price</td></tr>
    <tr><td>POST</td><td>/admin/update-status</td><td>Change order status</td></tr>
    <tr><td>POST</td><td>/admin/update-rate</td><td>Manually set exchange rate</td></tr>
    <tr><td>POST</td><td>/admin/toggle-autoupdate</td><td>Toggle auto-currency update</td></tr>
    <tr><td>GET</td><td>/admin</td><td>Admin dashboard</td></tr>
    </tbody>
</table>

<h2>Features</h2>
<ul>
    <li><strong>Telegram Notifications:</strong> Sends new order info to admins</li>
    <li><strong>Currency API:</strong> PrivatBank USD rate, optionally auto-updated</li>
    <li><strong>GDPR Compliance:</strong> Cookie consent popup + Privacy Policy</li>
</ul>

<h2>Frontend Interactions (JS)</h2>
<ul>
    <li><code>review.js</code>: Handles AJAX review submit/delete</li>
    <li><code>search.js</code>: Live search for products/categories</li>
</ul>

<h2>Database Structure (Selected)</h2>
<p><code>users</code>, <code>product</code>, <code>category</code>, <code>basket</code>, <code>order</code>, <code>order_products</code>, <code>product_reviews</code>, <code>currency</code>, <code>materials</code></p>

<h2>Launch Requirements</h2>
<ul>
    <li>PHP 8+, MySQL, web server</li>
    <li>Database credentials in <code>MVC/config/db.php</code></li>
    <li>Composer dependencies installed</li>
</ul>

<h2>Documentation</h2>
<ul>
    <li><code>DOCS.md</code> — технічний опис</li>
    <li><code><a href="https://editor.swagger.io/ " style="color: #d63384">swagger.yaml</a></code> — API</li>
</ul>

<h2>Author</h2>
<p>Oleksandr Koneschuk</p>
</div>
</body>
</html>
