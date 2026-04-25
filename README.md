# 🏥 Healthcare Booking Management System

A comprehensive web-based Booking management system for healthcare facilities. Built with Laravel 9, this system streamlines Client registration, Booking scheduling, and medical record management.

![Laravel](https://img.shields.io/badge/Laravel-9.x-red?style=flat-square&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.1+-blue?style=flat-square&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-orange?style=flat-square&logo=mysql)
![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)

---

## 📋 Table of Contents

- [Features](#features)
- [Demo Accounts](#demo-accounts)
- [Technologies](#technologies)
- [Requirements](#requirements)
- [Installation](#installation)
- [Screenshots](#screenshots)
- [System Architecture](#system-architecture)
- [Database Schema](#database-schema)
- [Usage Guide](#usage-guide)
- [API Documentation](#api-documentation)
- [Contributing](#contributing)
- [License](#license)
- [Contact](#contact)

---

## ✨ Features

### 🔐 Multi-Role Authentication System
- **Admin**: Full system control and user management
- **provider**: Client consultations and Note management
- **Secretary**: Client registration and Booking scheduling
- **Client**: View Bookings and medical history

### 👨‍⚕️ provider Dashboard
- Daily Booking schedule
- Client medical history access
- Create and edit medical diagnoses
- Prescription management
- Booking filtering and search

### 👩‍💼 StaffDashboard
- Client registration system
- Booking booking interface
- Interactive calendar view
- Client management (CRUD operations)
- Search and filter capabilities

### 🎛️ Admin Dashboard
- System-wide statistics and analytics
- User management (Create, Read, Update, Delete)
- Activate/Deactivate user accounts
- Visual charts and reports
- City-wise distribution reports
- Top providers by Bookings

### 📊 Advanced Features
- Interactive Booking calendar
- Real-time availability checking
- Multi-city support
- Medical Note tracking
- Prescription history
- Booking status management
- Statistical reports with charts

---

## 🔑 Demo Accounts

| Role | Email | Password |
|------|-------|----------|
| **Admin** | admin@example.com | password |
| **provider** | alice@example.com | password |
| **Secretary** | secretary1@example.com | password |
| **Client** | jane@example.com | password |

---

## 🛠️ Technologies

**Backend:**
- Laravel 9.x
- PHP 8.1+
- MySQL 5.7+

**Frontend:**
- Blade Templates
- Bootstrap 5.3
- Chart.js 4.4 (for analytics)
- Vanilla JavaScript

**Authentication:**
- Laravel Sanctum
- Session-based authentication

---

## 📦 Requirements

Before installation, ensure you have:

- PHP >= 8.1
- Composer
- MySQL >= 5.7 or MariaDB
- Node.js & NPM (for asset compilation)
- Git

---

## 🚀 Installation

### 1. Clone the Repository
```bash
git clone https://github.com/yourusername/healthcare-Booking-system.git
cd healthcare-Booking-system
```

### 2. Install PHP Dependencies
```bash
composer install
```

### 3. Install Node Dependencies
```bash
npm install
npm run build
```

### 4. Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 5. Database Setup

Edit `.env` file with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=healthcare_db
DB_USERNAME=root
DB_PASSWORD=your_password
```

Create the database:
```bash
# Login to MySQL
mysql -u root -p

# Create database
CREATE DATABASE healthcare_db;
exit;
```

### 6. Run Migrations & Seeders
```bash
# Run migrations
php artisan migrate

# Seed database with demo data
php artisan db:seed
```

### 7. Start Development Server
```bash
php artisan serve
```

Visit: `http://localhost:8000`

---

## 📸 Screenshots

### Admin Dashboard
![Admin Dashboard](screenshots/admin-dashboard.png)
*System overview with statistics and charts*

### provider Dashboard
![provider Dashboard](screenshots/provider-dashboard.png)
*Daily schedule and Client management*

### Booking Calendar
![Calendar View](screenshots/calendar-view.png)
*Interactive calendar for Booking scheduling*

### Client Management
![Client List](screenshots/Client-list.png)
*Comprehensive Client records management*

---

## 🏗️ System Architecture

### User Flow Diagram
```
┌─────────────┐
│   Client   │
└──────┬──────┘
       │
       │ registers with
       ▼
┌─────────────┐      books Booking      ┌─────────────┐
│  Staff │ ─────────────────────────▶ │ Booking │
└──────┬──────┘                             └──────┬──────┘
       │                                           │
       │ manages Clients                          │ assigned to
       │                                           ▼
       │                                    ┌─────────────┐
       │                                    │  provider  │
       │                                    └──────┬──────┘
       │                                           │
       │                                           │ creates
       │                                           ▼
       │                                    ┌─────────────┐
       └───────────────────────────────────│  Note  │
                                            └─────────────┘
```

## 🗄️ Database Schema

### Core Tables

**users**
- id, name, email, password, activation

**providers**
- id, user_id, specialization, phone, city_id

**secretaries**
- id, user_id, phone, city_id

**Clients**
- id, user_id, national_id, phone, city_id, secretary_id

**Bookings**
- id, Client_id, provider_id, secretary_id, Booking_date, Booking_time, status

**diagnoses**
- id, Booking_id, symptoms, Note, prescription, notes

### Relationships
```
users (1) ─────▶ (1) providers
users (1) ─────▶ (1) secretaries
users (1) ─────▶ (1) Clients

providers (1) ─▶ (many) Bookings
Clients (1) ───▶ (many) Bookings
secretaries (1) ─▶ (many) Bookings

Bookings (1) ─▶ (1) diagnoses
```

---

## 📖 Usage Guide

### For Secretaries

1. **Register New Client**
   - Navigate to Clients → Add New Client
   - Fill Client information
   - Client receives login credentials

2. **Book Booking**
   - Go to Bookings → Create New
   - Select Client and provider
   - Choose date and time slot
   - System checks availability automatically

3. **Manage Clients**
   - View all registered Clients
   - Search by name, national ID, or phone
   - Edit Client information
   - View Client medical history

### For providers

1. **View Daily Schedule**
   - Dashboard shows today's Bookings
   - Color-coded by status
   - Quick access to Client details

2. **Create Note**
   - Open Booking details
   - Click "Add Note"
   - Enter symptoms, Note, and prescription
   - Booking automatically marked as completed

3. **View Client History**
   - Access complete medical history
   - View previous diagnoses
   - Track treatment progress

### For Admins

1. **User Management**
   - Create users with specific roles
   - Activate/Deactivate accounts
   - View user activity

2. **System Monitoring**
   - View real-time statistics
   - Generate reports
   - Monitor system usage

---

## 🔌 API Documentation

### Authentication
```http
POST /login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password"
}
```

### Bookings
```http
GET /api/Bookings
Authorization: Bearer {token}

Response: List of Bookings
```
```http
POST /api/Bookings
Authorization: Bearer {token}
Content-Type: application/json

{
  "Client_id": 1,
  "provider_id": 2,
  "Booking_date": "2024-12-30",
  "Booking_time": "10:00"
}
```

*Note: Full API documentation available in `/docs/api` after installation*

---

## 🧪 Testing

Run the test suite:
```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter BookingTest
```

---

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

### Coding Standards

- Follow PSR-12 coding standards
- Write meaningful commit messages
- Add tests for new features
- Update documentation

---

## 🐛 Known Issues

- Calendar view may be slow with 1000+ Bookings (optimization needed)
- Email notifications not yet implemented
- Mobile responsiveness needs improvement on some pages

---

## 🗺️ Roadmap

- [ ] Email/SMS notifications for Bookings
- [ ] Online payment integration
- [ ] Telemedicine video consultation
- [ ] Mobile application (React Native)
- [ ] Multi-language support
- [ ] PDF report generation
- [ ] Insurance claim management
- [ ] Inventory management for medicines

---

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
```
MIT License

Copyright (c) 2024 [Your Name]

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
```

---

## ⚠️ Disclaimer

**This is a demonstration/portfolio project.**

- Contains NO real Client data
- NOT HIPAA compliant without additional security measures
- NOT certified for production medical use
- For educational and demonstration purposes ONLY

For production deployment in healthcare:
- Consult with healthcare compliance experts
- Implement proper encryption (PHI data)
- Add comprehensive audit logging
- Obtain necessary certifications
- Implement backup and disaster recovery

---

## 👨‍💻 Author

**[Mahmoud Mohamed]**

- GitHub: [@mahmoodmohamad](https://github.com/mahmoodmohamad)
- LinkedIn: [LinkedIn](https://linkedin.com/in/mahmoudmehidy)
- Email: mamodmohamed@outlook.com
- Portfolio: [https://mahmoodmohamad.github.io/](https://mahmoodmohamad.github.io/)

---

## 🙏 Acknowledgments

- Laravel Framework - [laravel.com](https://laravel.com)
- Bootstrap - [getbootstrap.com](https://getbootstrap.com)
- Chart.js - [chartjs.org](https://www.chartjs.org)
- Icons by [Bootstrap Icons](https://icons.getbootstrap.com)

---

## 📞 Support

If you found this project helpful, please give it a ⭐️!

For support, email your.email@example.com or open an issue on GitHub.

---

<div align="center">

**Built with ❤️ using Laravel**

[⬆ Back to Top](#-healthcare-Booking-management-system)

</div>