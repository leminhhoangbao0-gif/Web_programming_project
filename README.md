# Web_programming_project
# E-commerce System Specification & System Requirements

## 1. System Role & Architecture Overview
The system consists of two primary operational boundaries:
1. **Public Storefront (Customer Facing):** Allows end-users to discover products, maintain shopping carts, execute orders via a simulated payment gateway, and track order histories.
2. **Protected Admin Panel (Administrative Facing):** Accessible strictly to users with the `Admin` role via a secure `/admin/*` namespace. Manages core product catalog, categories, inventory levels, order processing pipelines, and system settings.

### Priority Classification (MoSCoW Method)
- **Must:** Required for the Phase 1 core working release.
- **Should:** High-priority feature planned for immediate Phase 2 iterations.
- **Could:** Optional enhancements implemented as timeline allows.


## 2. Authentication, Authorization & Security

### Admin Authentication & Security
| Function | Description | Priority |
|----------|-------------|----------|
| Admin Login / Logout | Secure login for administrative accounts with inactivity session timeout. | Must |
| Password & Security | Update own account password and contact credentials. Passwords MUST be hashed using `Bcrypt` or `Argon2` before persistence. | Must |
| Role-Based Access Control (RBAC) | Strict middleware validation securing `/admin/*` routes and API endpoints against unauthorized non-admin access. | Must |
| Manage Staff Accounts | Administrative creation of staff accounts with scoped role permissions (e.g., Order Fulfillment Staff). | Could |

### Customer Authentication & Profile
| Function | Description | Priority |
|----------|-------------|----------|
| User Registration / Login | Customer account registration and authentication via JWT/Session tokens. Non-authenticated users can browse products but must log in to execute checkout. | Must |
| Profile & Password Management | Update personal contact details, default shipping addresses, and change account passwords. | Must |


## 3. Administrative Dashboard
| Function | Description | Priority |
|----------|-------------|----------|
| Overview Statistics | Real-time aggregate metrics: total revenue, order count, new customer registrations, and daily/monthly units sold. | Should |
| Recent Orders & Stock Alerts | Centralized dashboard widgets displaying newly placed orders and low-stock threshold warnings. | Should |
| Analytics & Sales Charts | Visual graphical representations of sales revenue grouped by day, week, or month. | Could |


## 4. Product & Category Management
| Function | Description | Priority |
|----------|-------------|----------|
| Product List View | Paginated list of products featuring multi-criteria filtering (category, price range, stock status) and keyword search. | Must |
| Create Product | Form entry for product title, description, price, category assignment, initial stock quantity, and status (Active/Hidden). | Must |
| Edit / Delete Product | Modify existing product attributes or soft-delete/hide products from public view. | Must |
| Category Management | CRUD operations for organizing multi-level product categories. | Must |
| Multi-Image Asset Management | Upload multiple product media files and designate a primary thumbnail image. | Should |
| Product SKU & Variants | Manage configurable product options (size, color, version), each with independent SKU pricing and stock tracking. | Could |


## 5. Inventory & Stock Management

### Technical Rules & Data Integrity
1. **Deduction Rule:** Stock quantities automatically decrease when an order reaches `Confirmed` status.
2. **Restock Automation:** If an order transitions to `Cancelled` (initiated by either Customer or Admin), the system MUST automatically restore stock levels: `Stock = Stock + Order_Quantity`.

| Function | Description | Priority |
|----------|-------------|----------|
| Manual Inventory Adjustment | Direct increment/decrement of stock levels upon receiving new inventory shipments. | Must |
| Automated Stock Deduction | Automatic deduction of product inventory upon order confirmation. | Should |
| Automated Inventory Restock | Automatic inventory replenishment when an order status is updated to `Cancelled`. | Should |
| Low-Stock Warnings | Automated alerts triggering when product stock falls below designated threshold limits. | Could |


## 6. Order Management & Processing Pipeline

### Status Lifecycle Flow
`Pending` → `Confirmed` → `Shipping` → `Delivered` (or `Cancelled` at any pre-fulfillment state).

| Function | Description | Priority |
|----------|-------------|----------|
| Order List & Filtering | View all customer orders with filtering by order status, date range, customer ID, or order identifier. | Must |
| Order Detail View | Granular view of purchased items, quantities, subtotal, shipping address, tax/fees, and payment transaction metadata. | Must |
| Order Status Update | Transition orders sequentially through the status pipeline (`Pending` → `Confirmed` → `Shipping` → `Delivered`). | Must |
| Cancel & Refund Execution | Cancel pending/confirmed orders, trigger automatic inventory restocking, and log refund details. | Should |
| Invoice Generation | Export or print standardized order invoices in PDF or print format. | Could |


## 7. Customer Storefront & Product Discovery
| Function | Description | Priority |
|----------|-------------|----------|
| Homepage Layout | Featured product grids, category shortcuts, and promotional sliders. | Must |
| Product Search & Filtering | Keyword search bar with faceted filtering by category, price range, and sorting options (price low-to-high, new arrivals). | Must |
| Product Detail Page (PDP) | High-resolution image galleries, detailed specifications, stock status indicators, pricing, and variant selectors. | Must |


## 8. Cart Persistence & Checkout Loop

### Technical Rules for Cart Management
1. **Guest Storage:** Unauthenticated guest users store cart state locally in client storage (`LocalStorage`/`Cookie`).
2. **Cart Merging On Authentication:** Upon successful login, client-side guest cart items are automatically merged with the user's persistent server-side cart database records.

| Function | Description | Priority |
|----------|-------------|----------|
| Interactive Cart | Add/remove items, adjust quantities, view real-time subtotal calculations, and maintain state persistence. | Must |
| Checkout Summary | Input shipping details, select delivery options, and view final itemized order cost totals. | Must |
| Simulated Payment Gateway | Process simulated payment transactions via COD, Bank Transfer/QR, or Mock E-Wallet integration. | Must |


## 9. Customer Order Tracking & Product Reviews

### Technical Rules for Product Reviews
- **Verification Requirement:** Customers can ONLY submit ratings and text reviews for products associated with orders that have reached the `Delivered` status. Unverified purchases are restricted from submitting review records.

| Function | Description | Priority |
|----------|-------------|----------|
| Customer Order History | View current and past order transactions, delivery status trackers, and itemized receipts. | Must |
| Verified Reviews & Ratings | Submit star ratings and textual reviews strictly for items in `Delivered` orders. | Should |


## 10. Promotions & Content Moderation
| Function | Description | Priority |
|----------|-------------|----------|
| Discount Codes & Vouchers | Admin creation of promo codes with fixed/percentage discounts, min spend constraints, usage limits, and expiration dates. | Should |
| Review Moderation | Admin functionality to approve, hide, or delete customer reviews. | Should |
| Promotional Banners | Admin management of home page hero banners and redirection link targets. | Could |
| Static Page Management | Editable HTML/Markdown views for static pages (About Us, Return Policy, Terms of Service). | Could |


## 11. Reports & Store Settings
| Function | Description | Priority |
|----------|-------------|----------|
| Revenue Reporting | Financial analytics grouped by custom time intervals (daily, weekly, monthly). | Should |
| Best-Selling Product Analytics | Reports tracking top-performing SKUs by volume and revenue. | Should |
| CSV/Excel Export | Data exporter for orders, product catalogs, and revenue metrics. | Could |
| System & Store Configuration | Manage store name, logo assets, contact information, default shipping fees, and enabled payment methods. | Should |


## 12. Standard Execution Flows

### Admin Operations Flow
`Login` → `Dashboard Review (Alerts & New Orders)` → `Order Management (Confirm Order → Set Status to Shipping → Delivered)` → `Inventory Adjustment` → `Reports Review` → `Logout`

### Customer Purchase Flow
`Browse / Search Catalog` → `Add to Cart (Guest/User)` → `Login / Merge Cart` → `Checkout Summary` → `Simulated Payment Execution` → `Order Confirmation` → `Track Order via History` → `Receive Order (Delivered)` → `Submit Verified Product Review`
