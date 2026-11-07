# Web_programming_project
# E-commerce System Specification & System Requirements

## 1. System Role & Architecture Overview
The system consists of two primary operational boundaries:
1. **Public Storefront (Customer Facing):** Allows end-users to discover products, maintain shopping carts, execute orders via a simulated payment gateway, and track order histories[cite: 1].
2. **Protected Admin Panel (Administrative Facing):** Accessible strictly to users with the `Admin` role via a secure `/admin/*` namespace[cite: 1]. Manages core product catalog, categories, inventory levels, order processing pipelines, and system settings[cite: 1].

### Priority Classification (MoSCoW Method)
- **Must:** Required for the Phase 1 core working release[cite: 1].
- **Should:** High-priority feature planned for immediate Phase 2 iterations[cite: 1].
- **Could:** Optional enhancements implemented as timeline allows[cite: 1].

---

## 2. Authentication, Authorization & Security

### Admin Authentication & Security
| Function | Description | Priority |
|----------|-------------|----------|
| Admin Login / Logout | Secure login for administrative accounts with inactivity session timeout. | Must |
| Password & Security | Update own account password and contact credentials. Passwords MUST be hashed using `Bcrypt` or `Argon2` before persistence[cite: 1]. | Must |
| Role-Based Access Control (RBAC) | Strict middleware validation securing `/admin/*` routes and API endpoints against unauthorized non-admin access[cite: 1]. | Must |
| Manage Staff Accounts | Administrative creation of staff accounts with scoped role permissions (e.g., Order Fulfillment Staff)[cite: 1]. | Could |

### Customer Authentication & Profile
| Function | Description | Priority |
|----------|-------------|----------|
| User Registration / Login | Customer account registration and authentication via JWT/Session tokens[cite: 1]. Non-authenticated users can browse products but must log in to execute checkout[cite: 1]. | Must |
| Profile & Password Management | Update personal contact details, default shipping addresses, and change account passwords[cite: 1]. | Must |

---

## 3. Administrative Dashboard
| Function | Description | Priority |
|----------|-------------|----------|
| Overview Statistics | Real-time aggregate metrics: total revenue, order count, new customer registrations, and daily/monthly units sold[cite: 1]. | Should |
| Recent Orders & Stock Alerts | Centralized dashboard widgets displaying newly placed orders and low-stock threshold warnings[cite: 1]. | Should |
| Analytics & Sales Charts | Visual graphical representations of sales revenue grouped by day, week, or month[cite: 1]. | Could |

---

## 4. Product & Category Management
| Function | Description | Priority |
|----------|-------------|----------|
| Product List View | Paginated list of products featuring multi-criteria filtering (category, price range, stock status) and keyword search[cite: 1]. | Must |
| Create Product | Form entry for product title, description, price, category assignment, initial stock quantity, and status (Active/Hidden)[cite: 1]. | Must |
| Edit / Delete Product | Modify existing product attributes or soft-delete/hide products from public view[cite: 1]. | Must |
| Category Management | CRUD operations for organizing multi-level product categories[cite: 1]. | Must |
| Multi-Image Asset Management | Upload multiple product media files and designate a primary thumbnail image[cite: 1]. | Should |
| Product SKU & Variants | Manage configurable product options (size, color, version), each with independent SKU pricing and stock tracking[cite: 1]. | Could |

---

## 5. Inventory & Stock Management

### Technical Rules & Data Integrity
1. **Deduction Rule:** Stock quantities automatically decrease when an order reaches `Confirmed` status[cite: 1].
2. **Restock Automation:** If an order transitions to `Cancelled` (initiated by either Customer or Admin), the system MUST automatically restore stock levels: `Stock = Stock + Order_Quantity`[cite: 1].

| Function | Description | Priority |
|----------|-------------|----------|
| Manual Inventory Adjustment | Direct increment/decrement of stock levels upon receiving new inventory shipments[cite: 1]. | Must |
| Automated Stock Deduction | Automatic deduction of product inventory upon order confirmation[cite: 1]. | Should |
| Automated Inventory Restock | Automatic inventory replenishment when an order status is updated to `Cancelled`[cite: 1]. | Should |
| Low-Stock Warnings | Automated alerts triggering when product stock falls below designated threshold limits[cite: 1]. | Could |

---

## 6. Order Management & Processing Pipeline

### Status Lifecycle Flow
`Pending` → `Confirmed` → `Shipping` → `Delivered` (or `Cancelled` at any pre-fulfillment state)[cite: 1].

| Function | Description | Priority |
|----------|-------------|----------|
| Order List & Filtering | View all customer orders with filtering by order status, date range, customer ID, or order identifier[cite: 1]. | Must |
| Order Detail View | Granular view of purchased items, quantities, subtotal, shipping address, tax/fees, and payment transaction metadata[cite: 1]. | Must |
| Order Status Update | Transition orders sequentially through the status pipeline (`Pending` → `Confirmed` → `Shipping` → `Delivered`)[cite: 1]. | Must |
| Cancel & Refund Execution | Cancel pending/confirmed orders, trigger automatic inventory restocking, and log refund details[cite: 1]. | Should |
| Invoice Generation | Export or print standardized order invoices in PDF or print format[cite: 1]. | Could |

---

## 7. Customer Storefront & Product Discovery
| Function | Description | Priority |
|----------|-------------|----------|
| Homepage Layout | Featured product grids, category shortcuts, and promotional sliders[cite: 1]. | Must |
| Product Search & Filtering | Keyword search bar with faceted filtering by category, price range, and sorting options (price low-to-high, new arrivals)[cite: 1]. | Must |
| Product Detail Page (PDP) | High-resolution image galleries, detailed specifications, stock status indicators, pricing, and variant selectors[cite: 1]. | Must |

---

## 8. Cart Persistence & Checkout Loop

### Technical Rules for Cart Management
1. **Guest Storage:** Unauthenticated guest users store cart state locally in client storage (`LocalStorage`/`Cookie`)[cite: 1].
2. **Cart Merging On Authentication:** Upon successful login, client-side guest cart items are automatically merged with the user's persistent server-side cart database records[cite: 1].

| Function | Description | Priority |
|----------|-------------|----------|
| Interactive Cart | Add/remove items, adjust quantities, view real-time subtotal calculations, and maintain state persistence[cite: 1]. | Must |
| Checkout Summary | Input shipping details, select delivery options, and view final itemized order cost totals[cite: 1]. | Must |
| Simulated Payment Gateway | Process simulated payment transactions via COD, Bank Transfer/QR, or Mock E-Wallet integration[cite: 1]. | Must |

---

## 9. Customer Order Tracking & Product Reviews

### Technical Rules for Product Reviews
- **Verification Requirement:** Customers can ONLY submit ratings and text reviews for products associated with orders that have reached the `Delivered` status[cite: 1]. Unverified purchases are restricted from submitting review records[cite: 1].

| Function | Description | Priority |
|----------|-------------|----------|
| Customer Order History | View current and past order transactions, delivery status trackers, and itemized receipts[cite: 1]. | Must |
| Verified Reviews & Ratings | Submit star ratings and textual reviews strictly for items in `Delivered` orders[cite: 1]. | Should |

---

## 10. Promotions & Content Moderation
| Function | Description | Priority |
|----------|-------------|----------|
| Discount Codes & Vouchers | Admin creation of promo codes with fixed/percentage discounts, min spend constraints, usage limits, and expiration dates[cite: 1]. | Should |
| Review Moderation | Admin functionality to approve, hide, or delete customer reviews[cite: 1]. | Should |
| Promotional Banners | Admin management of home page hero banners and redirection link targets[cite: 1]. | Could |
| Static Page Management | Editable HTML/Markdown views for static pages (About Us, Return Policy, Terms of Service)[cite: 1]. | Could |

---

## 11. Reports & Store Settings
| Function | Description | Priority |
|----------|-------------|----------|
| Revenue Reporting | Financial analytics grouped by custom time intervals (daily, weekly, monthly)[cite: 1]. | Should |
| Best-Selling Product Analytics | Reports tracking top-performing SKUs by volume and revenue[cite: 1]. | Should |
| CSV/Excel Export | Data exporter for orders, product catalogs, and revenue metrics[cite: 1]. | Could |
| System & Store Configuration | Manage store name, logo assets, contact information, default shipping fees, and enabled payment methods[cite: 1]. | Should |

---

## 12. Standard Execution Flows

### Admin Operations Flow
`Login` → `Dashboard Review (Alerts & New Orders)` → `Order Management (Confirm Order → Set Status to Shipping → Delivered)` → `Inventory Adjustment` → `Reports Review` → `Logout`[cite: 1]

### Customer Purchase Flow
`Browse / Search Catalog` → `Add to Cart (Guest/User)` → `Login / Merge Cart` → `Checkout Summary` → `Simulated Payment Execution` → `Order Confirmation` → `Track Order via History` → `Receive Order (Delivered)` → `Submit Verified Product Review`[cite: 1]
