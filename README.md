# 📚 Online Book Store

<div align="center">

![Online Book Store](https://img.shields.io/badge/Online%20Book%20Store-Web%20App-e2a85a?style=for-the-badge&logo=bookstack)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)

**A full-stack online bookstore where customers can browse, search, and purchase books — and admins manage everything.**

</div>

---

## 🗂️ Overview

**Online Book Store** is a responsive web application built with PHP MVC architecture. It supports two types of users — **Admin** and **Customer** — with full role-based access control, secure authentication, AJAX-powered search and cart, complete checkout with payment methods, and a dark-themed modern UI.

> 📘 Course: Web Technologies | Project 02 | AIU

---

## ✨ Key Features

| Feature | Description |
|---------|-------------|
| 🔐 **Secure Authentication** | Registration, login, logout with hashed passwords and Remember Me cookie |
| 👤 **Role-Based Access** | Separate dashboards and permissions for Admin and Customer |
| 📖 **Book Browsing** | Browse by category, view book details, search with AJAX filtering |
| 🛒 **Cart Management** | Add, update, and remove cart items with live AJAX updates |
| 💳 **Checkout & Payment** | Full checkout with bKash, Nagad, Credit Card, Bank Transfer, COD |
| 📦 **Order Tracking** | Customer order history with status tracking |
| ⚙️ **Admin Dashboard** | Manage books, users, orders, and view complete purchase history |
| 🖼️ **File Uploads** | Profile pictures and book cover images with MIME + size validation |
| 🌙 **Dark Theme UI** | Clean, responsive dark-themed interface |
| 🔒 **Admin Secret Key** | Secret-key-protected admin registration and deletion |

---

## 🛠️ Technology Stack

### Frontend
- **HTML5** — Semantic markup
- **CSS3** — Custom dark theme, responsive grid
- **JavaScript** — Client-side validation, AJAX calls
- **Google Fonts** — DM Sans, Playfair Display

### Backend
- **PHP** — MVC architecture, server-side validation, session management
- **MySQL** — Relational database with prepared statements

### Development Tools
- **Git** — Version control with feature branch workflow
- **VS Code** — Development environment
- **XAMPP** — Local development server (Apache + MySQL)

---

## 🚀 Quick Start

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache web server (XAMPP recommended)
- Modern web browser

### Installation

1. **Clone the Repository**
   ```bash
   git clone https://github.com/your-username/online_book_store.git
   cd online_book_store
   ```

2. **Database Setup**
   ```bash
   # Import the SQL file into MySQL
   mysql -u root -p < bookstore.sql
   ```
   Or open **phpMyAdmin** and import `bookstore.sql` manually.

3. **Configure Database**

   Open `config/database.php` and update:
   ```php
   $host     = "localhost";
   $username = "root";
   $password = "your_password";
   $database = "online_book_store";
   ```

4. **Run the Project**
   - Place the folder inside `htdocs` (XAMPP) or your web server root
   - Start Apache and MySQL from XAMPP Control Panel
   - Visit: `http://localhost/online_book_store`

### Default Admin Credentials
```
Email:    admin@gmail.com
Password: password
```

> ⚠️ Change the default password after first login.

### Admin Secret Key
The secret key required to register or delete admin accounts:
```
bookstore@admin2026
```
> 🔑 Change this in `controllers/adminRegisterController.php` and `controllers/deleteAdminController.php` before going live.

---

## 👥 Development Team

<div align="center">

| Student ID | Task | Responsibilities |
|------------|------|-----------------|
| **25-62897-2** | **Task 1 — Auth & Home** | User authentication (login/register/logout), profile management, home page with categories, basic book browsing, session & cookie handling |
| **25-60896-1** | **Task 2 — Admin Panel** | Full book CRUD, customer/admin management, view all users, purchase history dashboard, admin order processing |
| **23-55365-3** | **Task 3 — Search & Cart** | AJAX search & filtering, book details page, add/remove/update cart, live cart count in navbar |
| **23-55026-3** | **Task 4 — Checkout & Orders** | Checkout flow, payment method selection, order finalization, customer purchase history, order status updates |

</div>

---

## 📁 Project Structure

```
online_book_store/
│
├── api/                        # AJAX JSON endpoints
│   ├── add_to_cart.php
│   ├── remove_cart_item.php
│   ├── update_cart.php
│   └── search_books.php
│
├── config/                     # Configuration
│   ├── database.php            # DB connection
│   └── helpers.php             # Shared functions (isLoggedIn, sanitize, redirect)
│
├── controllers/                # Business logic
│   ├── loginController.php
│   ├── registerController.php
│   ├── adminRegisterController.php
│   ├── logoutController.php
│   ├── profileController.php
│   ├── bookController.php
│   ├── updateBookController.php
│   ├── deleteBookController.php
│   ├── deleteCustomerController.php
│   ├── deleteAdminController.php
│   ├── placeOrderController.php
│   └── updateOrderStatusController.php
│
├── models/                     # Database layer
│   └── user.php
│
├── views/                      # Presentation layer
│   ├── auth/
│   │   ├── login.php
│   │   ├── register.php
│   │   └── admin_register.php
│   ├── admin/
│   │   ├── dashboard.php
│   │   ├── manage_books.php
│   │   ├── view_orders.php
│   │   └── View_users.php
│   ├── customer/
│   │   ├── books.php
│   │   ├── book_details.php
│   │   ├── cart.php
│   │   ├── checkout.php
│   │   ├── order_history.php
│   │   ├── profile.php
│   │   └── category_books.php
│   └── layouts/
│       ├── header.php
│       └── footer.php
│
├── public/
│   ├── css/
│   │   └── style.css           # Dark theme stylesheet
│   └── uploads/
│       ├── books/              # Book cover images
│       └── profiles/           # Profile pictures
│
├── index.php                   # Home page (entry point)
└── bookstore.sql               # Database schema + seed data
```

---

## 🔧 System Architecture

```
Browser (Client)
        ↓
  HTML / CSS / JS  (Views — views/)
        ↓
  PHP Controllers  (controllers/)
        ↓
  Models & Helpers (models/, config/)
        ↓
  MySQL Database   (online_book_store)
```

**MVC Pattern:**
- **Model** → `models/user.php` — all DB queries (findByEmail, register, getAllUsers)
- **View** → `views/**/*.php` — HTML output only, no business logic
- **Controller** → `controllers/*.php` — receives POST, validates, calls model, sets session, redirects
- **Config** → `config/database.php`, `config/helpers.php` — DB connection + shared helpers

---

## 💡 Core Functionalities

### For Customers
- Register and log in securely
- Browse all books or filter by category
- Search books by title, author, or genre with live AJAX results
- View detailed book pages with stock status
- Add books to cart, update quantities, remove items
- Checkout with address confirmation and payment method selection
- View full order history with status tracking
- Update profile and upload profile picture

### For Admins
- Admin-only dashboard with total users, books, and orders at a glance
- Add, edit, and delete books with cover image upload
- View all registered users (customers and admins)
- Delete customer accounts
- Create new admin accounts (requires secret key)
- Delete admin accounts (requires secret key)
- View all purchase history across all users
- Update order status (pending → confirmed → shipped → delivered)

---

## 🗄️ Database Schema

| Table | Key Columns |
|-------|-------------|
| `users` | id, name, email, password_hash, role, profile_picture, address, phone, created_at |
| `categories` | id, name, created_at |
| `books` | id, title, author, description, price, category_id, image_path, stock, created_at |
| `cart` | id, user_id, book_id, quantity, added_at |
| `orders` | id, user_id, total_amount, status, payment_method, order_date |
| `order_items` | id, order_id, book_id, quantity, unit_price |
| `payments` | id, order_id, amount, payment_method, transaction_id, payment_date |

---

## 🔒 Security Features

- **SQL Injection Prevention** — All queries use prepared statements with `bind_param()`
- **XSS Protection** — All output escaped with `htmlspecialchars()`
- **Password Hashing** — `password_hash()` with bcrypt, verified with `password_verify()`
- **Session Management** — `session_start()` on every protected page, role-checked access
- **File Upload Validation** — MIME type and size checked server-side before saving
- **Admin Protection** — Secret key required to create or delete admin accounts
- **Role-Based Redirects** — Unauthenticated or unauthorized users are immediately redirected

---

## 🌿 Git Workflow

```
feature/taskX-studentID  →  dev  →  stage  →  main
```

| Branch | Purpose |
|--------|---------|
| `main` | Production-ready, protected — no direct pushes |
| `dev` | Integration branch — all features merge here first |
| `stage` | Pre-production testing |
| `feature/task1-25628972` | Task 1 — Auth & Home |
| `feature/task2-25608961` | Task 2 — Admin Panel |
| `feature/task3-23553653` | Task 3 — Search & Cart |
| `feature/task4-23550263` | Task 4 — Checkout & Orders |

Each student:
1. Branches off `dev`
2. Makes **at least 3 meaningful commits**
3. Opens a **Pull Request** into `dev`
4. A teammate reviews and merges using **Squash and Merge**

---

## 📋 Grading Criteria Coverage

| # | Criterion | How We Satisfy It |
|---|-----------|-------------------|
| 1 | Basic Web Security | Prepared statements, htmlspecialchars, password_hash, secret key |
| 2 | UI (HTML/CSS) | Dark theme, responsive grid, clean card components |
| 3 | Feature Completeness | All 4 tasks fully implemented and working |
| 4 | DB Usage | Correct shared schema, foreign keys, data integrity |
| 5 | Auth (Session/Cookie) | Sessions on every page, role-based access, Remember Me |
| 6 | MVC Pattern | Controllers, Views, Models, Config clearly separated |
| 7 | JS Validation | Client-side checks on all forms before submission |
| 8 | PHP Validation | Server-side validation before every DB write |
| 9 | AJAX/JSON | 4 AJAX endpoints returning JSON (search, add/update/remove cart) |
| 10 | Git Contribution | Feature branches, 3+ commits per student, PRs into dev |

---

## 🐛 Bug Reporting

Found a bug? Please open an issue with:
- A clear description of the problem
- Steps to reproduce it
- Expected vs actual behavior
- Screenshots if applicable

---

## 📄 License

This project is for academic purposes — Course: Web Technologies, Project 02.

---

<div align="center">

**Making reading more accessible, one book at a time.** 📚

</div>
