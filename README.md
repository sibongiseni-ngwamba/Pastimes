<h1 align="center">Pastimes 👕♻️</h1>
<p align="center"><i>Pre-Loved Fashion Marketplace.</i></p>

## Overview

Pastimes is a role-based e-commerce web application developed for the **WEDE6021 Portfolio of Evidence (POE)**. The platform provides a secure online marketplace where users can buy and sell pre-loved branded clothing while promoting **sustainable fashion** and reducing textile waste.

The application follows a **managed marketplace model** where administrators verify sellers, approve listings, manage orders, and facilitate communication between buyers and sellers.

---

## ✨ Features

### 👤 Buyer Features

- **User registration and login** 🔐
- Browse clothing listings 🛍️
- Filter products by category, brand, size, condition, and price 🔍
- Add items to shopping cart 🛒
- Update cart quantities & remove items
- Continue shopping without losing cart contents
- Checkout and place orders 💳
- View purchase history 📜
- Manage delivery addresses 📍
- Message sellers through the platform 💬

### 👔 Seller Features

- Request seller verification ✅
- Submit clothing listings for approval 📝
- Upload product images 📸
- Add item descriptions and brand information
- Manage personal listings ✏️
- Edit listings & view sold items
- Communicate with buyers 💬

### ⚙️ Administrator Features

- Manage users 👥
- Add, edit, and delete clothing items
- Approve or reject seller applications ✅❌
- Approve or reject product listings
- Manage orders and delivery statuses 📦
- Send messages to buyers and sellers 💬
- Broadcast platform announcements 📢
- View reports and marketplace activity 📊

---

## 🛠️ Technologies Used

- **PHP 8.2+**
- **MySQL**
- MySQLi Prepared Statements
- **HTML5**
- **CSS3**
- **JavaScript (ES6)**
- WAMP Server + phpMyAdmin

---

## ▶ Demo
1. Youtube demo video
- **Demo video**: https://youtu.be/zcIa88a4sdI **Removed**
2. **Video will also be in Releases  with source code**

---

## 📁 Project Structure

```text
Pastimes/
│
├── admin/
├── seller/
├── css/
├── js/
├── images/
├── uploads/
├── classes/
├── database/
│
├── index.php
├── shop.php
├── product.php
├── cart.php
├── checkout.php
├── register.php
├── login.php
├── dashboard.php
├── profile.php
├── addresses.php
├── orders.php
├── messages.php
│
└── README.md
```

---

## 🗄️ Database

Database Name:

```sql
pastimes_db
```

Core Tables:

* users
* addresses
* seller_applications
* pending_listings
* products
* product_images
* cart_items
* orders
* order_items
* messages

---

## 🚀 Installation

### 1. Clone the Repository

```bash
git clone https://github.com/yourusername/pastimes.git
```

### 2. Move Project to WAMP

Copy the project folder to:

```text
C:\wamp64\www\
```

### 3. Create Database

Open phpMyAdmin and create:

```sql
pastimes_db
```

### 4. Import Database

Import the SQL file located in:

```text
/database/pastimes_db.sql
```

### 5. Configure Database Connection

Update database credentials in:

```php
config/database.php
```

Example:

```php
$host = "localhost";
$username = "root";
$password = "";
$database = "pastimes_db";
```

### 6. Start WAMP

Ensure:

* Apache is running
* MySQL is running

### 7. Open Application

Navigate to:

```text
http://localhost/pastimes
```

---

## 👥 Default Roles


### 🛍️ Buyer

Can browse products, manage cart, place orders, and communicate with sellers.

### 👕 Seller

Can submit and manage clothing listings after administrator approval.

### ⚙️ Administrator

Has full control over users, listings, seller verification, orders, and communications.

---

## 🔒 Security Features

* Password hashing using BCRYPT
* Session-based authentication
* Role-based access control
* MySQLi prepared statements
* Input validation and sanitisation
* Protected admin routes

---

## 🚀 Future Enhancements

* 💰 Payment gateway integration
* 📦 Courier API integration
* ❤️ Wishlist functionality
* ⭐ Product ratings and reviews
* ✉️ Email notifications
* 📈 Advanced analytics dashboard

---

## 📚 Academic Information

Module: WEDE6021 – Web Development (Intermediate)

Project: Portfolio of Evidence (POE)

Institution: The Independent Institute of Education (IIE)

Purpose: Development of a fully functional role-based e-commerce platform using Object-Oriented PHP and MySQL.

---

## 👨‍💻 Authors

* Sibongiseni Collel Ngwamba
* Thokozani Masondo

---

## 📄 License

This project was developed for academic purposes as part of a university assessment.
