# Web_programming_project
# Admin Functions of the E-commerce System

## 1. Role Overview
The **Administrator (Admin)** manages the whole online store through a separate, protected admin panel. Unlike customers, who browse and buy products, the admin controls the store's data: products, orders, promotions and reports. Admin pages are accessible only after logging in with an admin account.

Priority levels (MoSCoW method):
- **Must** – required for the first working version
- **Should** – important but can come later
- **Could** – nice to have if time allows

## 2. Authentication & Admin Account
| Function | Description | Priority |
|----------|-------------|----------|
| Admin login / logout | Log in with a protected admin account; session timeout after inactivity. | Must |
| Change password / profile | Update own password and contact information. | Must |
| Role-based access | Only users with the admin role can access /admin pages. | Must |
| Manage staff accounts | Create staff accounts with limited permissions (e.g. order staff only). | Could |

## 3. Dashboard
| Function | Description | Priority |
|----------|-------------|----------|
| Overview statistics | View total revenue, number of orders, new customers, and products sold today / this month. | Should |
| Recent orders & alerts | See latest orders and low-stock warnings on one screen. | Should |
| Sales chart | Revenue chart by day, week or month. | Could |

## 4. Product & Category Management
| Function | Description | Priority |
|----------|-------------|----------|
| View product list | List all products with search, filter (category, price, status) and pagination. | Must |
| Add product | Enter name, description, price, category, images, stock quantity, status. | Must |
| Edit / delete product | Update product information or hide / remove a product. | Must |
| Manage categories | Add, edit, delete and arrange product categories. | Must |
| Upload images | Upload multiple images per product and choose a main image. | Should |
| Product variants | Manage size / color / version, each with its own price and stock. | Could |

## 5. Inventory Management
| Function | Description | Priority |
|----------|-------------|----------|
| Update stock quantity | Increase or decrease stock when new goods arrive. | Must |
| Automatic stock deduction | Stock decreases automatically when an order is confirmed. | Should |
| Low-stock alert | Highlight products whose quantity is below a set limit. | Could |

## 6. Order Management
| Function | Description | Priority |
|----------|-------------|----------|
| View order list | List all orders; filter by status, date, or customer. | Must |
| View order details | See items, quantities, total price, shipping address, payment method. | Must |
| Update order status | Pending → Confirmed → Shipping → Delivered, or Cancelled. | Must |
| Cancel / refund order | Cancel an order and record the refund if payment was made. | Should |
| Print invoice | Export or print the invoice of an order. | Could |

## 7. Promotions & Vouchers
| Function | Description | Priority |
|----------|-------------|----------|
| Create discount codes | Set code, discount (% or fixed amount), minimum order value, usage limit, expiry date. | Should |
| Enable / disable promotions | Turn a voucher or sale campaign on or off. | Should |
| Product sale price | Set a temporary sale price for selected products. | Could |

## 8. Reviews & Content
| Function | Description | Priority |
|----------|-------------|----------|
| Moderate reviews | Approve, hide or delete customer reviews and ratings. | Should |
| Manage banners | Change homepage banners / slides and their links. | Could |
| Manage static pages | Edit About Us, Return Policy, Contact pages. | Could |

## 9. Reports & Statistics
| Function | Description | Priority |
|----------|-------------|----------|
| Revenue report | Revenue by day, month or custom period. | Should |
| Best-selling products | Top products by quantity sold or revenue. | Should |
| Export report | Export data to Excel / CSV. | Could |

## 10. System Settings
| Function | Description | Priority |
|----------|-------------|----------|
| Store information | Edit store name, logo, hotline, address, email. | Should |
| Shipping & payment settings | Configure shipping fees and enable payment methods (COD, bank transfer, e-wallet). | Should |

## 11. Typical Admin User Flow
**Login** → **Dashboard** (check new orders and low-stock alerts) → **Order Management** (confirm order → update status to Shipping → Delivered) → **Product Management** (add new product / update stock) → **Reports** (check monthly revenue) → **Logout**

**Summary:** For the first working version, the team will focus on all **Must** functions (login, product, category, inventory and order management). **Should** and **Could** functions will be added in later weeks according to the development plan.


# customer
1. Sign up and log in without an account (hoặc: Register and log in when you don't have an account)
2. Account management (hoặc: Manage profile / Manage personal information)
3. Check transaction history
4. View transaction history
5. View top-up packages from the publisher

## 2. System Functional Analysis
### 2.1. Customer Functions
* **Account Management:** Sign up, sign in.
* **Browse Products & Packages:**
  * View supported games list.
  * View top-up packages for each game.
* **Transaction Execution:**
  * Enter Character ID / Player ID.
  * Select top-up package.
  * Online payment.
* **Management & Support:**
  * Check transaction status.
  * View top-up history.
  * Submit support requests.
### 2.2. Admin Functions
* **System Management:**
  * User account management.
  * Game list management.
  * Top-up package and pricing management.
* **Business Management:**
  * Order management.
  * Payment transaction management.
  * Promotion & discount management.
* **Reporting & Support:**
  * Revenue statistics & analytics.
  * Customer ticket & complaint handling.
