# 🐾 Animal Adoption System

The Animal Adoption System is a web-based platform developed to streamline the pet adoption process. It supports three user roles—**Adopter**, **Shelter Personnel**, and **Admin**—each with tailored features to ensure a smooth, responsible, and efficient adoption journey.

---

## 🌐 Demo

> 🖥️ *Currently hosted in a local development environment using XAMPP.*

---

## 📁 Project Structure
Animal-Adoption-System/

adopter/ # Adopter-specific pages and features

admin/ # Admin dashboard and user management

shelter/ # Shelter dashboard and pet management tools

css/ # Global and role-specific stylesheets

images/ # Uploaded pet images

includes/ # Reusable components (e.g. navbars, session checks)

database/ # SQL schema and sample data

homepage.php # Landing page

login.php / logout.php # User authentication

register.php # Unified user registration

db_connection.php # Database connection config

---

## ⚙️ Technologies Used

- **Backend**: PHP 8.x
- **Database**: MySQL (MariaDB 10.x)
- **Frontend**: HTML5, CSS3, JavaScript
- **Local Server**: XAMPP (Apache, MySQL, PHP)
- **Version Control**: Git + GitHub

---

## 🧪 Key Features

### 👤 Adopter Module
- Pet browsing with filters
- Compatibility quiz for personalized pet matches
- Application submission and status tracking
- Interview scheduling after approval
- Post-adoption follow-up messaging

### 🏢 Shelter Personnel Module
- Add/manage pet profiles
- Application review and interview coordination
- Post-adoption communication with adopters
- Dashboard with pending tasks and history logs

### 🛠 Admin Module
- Manage users (adopters/shelters)
- Monitor and moderate pet listings
- View platform-wide adoption activity
- Send system-wide notifications

---

## 🐘 Database Initialization

To set up the system database:

1. Open **phpMyAdmin**
2. Create a new database named: 
---

## ⚙️ Technologies Used

- **Backend**: PHP 8.x
- **Database**: MySQL (MariaDB 10.x)
- **Frontend**: HTML5, CSS3, JavaScript
- **Local Server**: XAMPP (Apache, MySQL, PHP)
- **Version Control**: Git + GitHub

---

## 🧪 Key Features

### 👤 Adopter Module
- Pet browsing with filters
- Compatibility quiz for personalized pet matches
- Application submission and status tracking
- Interview scheduling after approval
- Post-adoption follow-up messaging

### 🏢 Shelter Personnel Module
- Add/manage pet profiles
- Application review and interview coordination
- Post-adoption communication with adopters
- Dashboard with pending tasks and history logs

### 🛠 Admin Module
- Manage users (adopters/shelters)
- Monitor and moderate pet listings
- View platform-wide adoption activity
- Send system-wide notifications

---

## 🐘 Database Initialization

To set up the system database:

1. Open **phpMyAdmin**
2. Create a new database named:

---

## ⚙️ Technologies Used

- **Backend**: PHP 8.x
- **Database**: MySQL (MariaDB 10.x)
- **Frontend**: HTML5, CSS3, JavaScript
- **Local Server**: XAMPP (Apache, MySQL, PHP)
- **Version Control**: Git + GitHub

---

## 🧪 Key Features

### 👤 Adopter Module
- Pet browsing with filters
- Compatibility quiz for personalized pet matches
- Application submission and status tracking
- Interview scheduling after approval
- Post-adoption follow-up messaging

### 🏢 Shelter Personnel Module
- Add/manage pet profiles
- Application review and interview coordination
- Post-adoption communication with adopters
- Dashboard with pending tasks and history logs

### 🛠 Admin Module
- Manage users (adopters/shelters)
- Monitor and moderate pet listings
- View platform-wide adoption activity
- Send system-wide notifications

---

## 🐘 Database Initialization

To set up the system database:

1. Open **phpMyAdmin**
2. Create a new database named: animal_adoption_system
3. Import the following SQL file: /database/animal_adoption_system.sql

This will:
- Create all required tables (`users`, `pets`, `applications`, etc.)
- Set up foreign key relationships
- Insert sample data for testing

---

## 🔐 Login Credentials (Demo Users)

> *(These are sample accounts for testing in a local environment)*

| Role       | Email                            | Password       |
|------------|----------------------------------|----------------|
| Adopter    | azmisahi@gmail.com               | azmisahi       |
| Shelter    | fluffyfriendsrescue@gmail.com    | fluffyfriends  |
| Admin      | admin01@test .com                | admin123       |

---

## 🚀 Getting Started (Local Setup)

1. Install [XAMPP](https://www.apachefriends.org/index.html)
2. Clone or download this repository into your `htdocs` directory
3. Start Apache and MySQL from XAMPP Control Panel
4. Import the SQL file in `/database/` into `phpMyAdmin`
5. Open your browser and go to: http://localhost/Animal-Adoption-System/homepage.php

---

## 🛡️ Security Notes

- All passwords are hashed using PHP’s `password_hash()`
- Session validation restricts role access across pages
- Input fields are sanitized to prevent SQL injection and XSS
- Uploaded images are validated by file type and size

---

## 📌 Future Enhancements

- Deploy to a live server with HTTPS and domain name
- Integrate email/SMS notifications
- Add real-time chat between adopters and shelters
- Develop mobile-friendly or native mobile app version

---

## 📄 License

This project is built for educational purposes under the **Final Year Project** requirement of Multimedia University. Feel free to fork or adapt for personal learning.

---

## 🙋 Contact

For inquiries or collaboration:
- 👤 Azmi Bin Mohd Sahi
- ✉️ 1221303866@student.mmu.edu.my

