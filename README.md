# 🛍️ Social Store - Management System

## 🌍 About the Project
This PHP-based management system allows administrators, users, and clients to efficiently handle various operations, such as managing content, users, and service requests.
I developed this website during my curricular internship in the IT department of the **Portuguese Red Cross - Braga Delegation** to support the **Ponto Vermelho** initiative, improving user and content management.  

**Ponto Vermelho** is a social store within the **Ponto Cruz** project, which promotes the **inclusion of elderly individuals and long-term unemployed people (50+ years old)** through **sewing, embroidery, and clothing recycling workshops**. It encourages **sustainable fashion**, **community engagement**, and **social reintegration**, allowing participants to showcase and sell their creations.

---

## 🚀 Features

### 🔑 Administrators
- 🛠 Manage users and clients (add, edit, delete)
- 📰 Handle articles and content
- 📋 Manage requests and vouchers
- 📡 Monitor system status

### 👤 Users
- 📝 Create and edit profile
- 📌 Request services or articles
- 🔍 View system information

### 🏥 Clients
- 🎯 Access specific services
- 🏷 Update personal information (if allowed)

---

## 🛠 How Each Function Works

### 🔍 check_status.php
- Checks if a user is logged in and retrieves their status from the database.
- Redirects users with restricted access.

### 🛠 config.php
- Defines database connection settings and initializes a connection.

### 💳 creditos.php
- Ensures the user is logged in before allowing access to credit-related features.

### ❌ delete-*.php (e.g., delete-user.php, delete-article.php)
- Deletes records from respective database tables.
- Shows alerts confirming success or failure.

---

## ⚠️ Possible Issues & Improvements

### 📱 Responsiveness
- The site may have display issues on mobile devices.

### 🔒 Security
- Some critical pages may need additional protection.

### ⚡ Optimization
- Page loading speed can be improved.

### 📂 Files Organization
- Some files could be more organized.

---

## 🏗 How to Run the Project

1. Install a local server (XAMPP, WAMP, etc.).
2. Place the files in the `htdocs` folder or equivalent.
3. Configure the database in `config.php`.
4. Access via a web browser.

---

## 🤝 Contributing
Contributions are welcome! If you have suggestions for improvements or new features, feel free to create an issue or submit a pull request.

---

📢 This document can be updated as new features are added! 🚀

