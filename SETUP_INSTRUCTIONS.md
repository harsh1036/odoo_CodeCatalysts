# ReWear Setup Instructions

## 🚀 Quick Start Guide

### Prerequisites
- XAMPP (Apache + MySQL)
- PHP 7.4 or higher
- Web browser

### Step 1: Start XAMPP
1. Open XAMPP Control Panel
2. Start Apache and MySQL services
3. Ensure both services are running (green status)

### Step 2: Database Setup
1. Open phpMyAdmin: http://localhost/phpmyadmin
2. Click "Import" in the top menu
3. Choose the `database.sql` file from the project
4. Click "Go" to import the database
5. Verify that `rewear_db` database is created with all tables

### Step 3: Access the Application
1. Navigate to: http://localhost/odoo/odoo_CodeCatalysts/
2. You should see the ReWear login/registration page

### Step 4: Test the Application
Use these demo credentials:

**Admin Login:**
- Username: `admin`
- Password: `admin123`

**User Login:**
- Username: `user1`
- Password: `user123`

## 🔧 Configuration

### Database Configuration
The database connection is configured in `config/database.php`:
```php
private $host = 'localhost';
private $db_name = 'rewear_db';
private $username = 'root';
private $password = '';
```

If you have different MySQL credentials, update these values.

### File Permissions
Ensure the following directories have write permissions:
- `uploads/` (for item images)
- `logs/` (for application logs)

## 📁 Project Structure

```
odoo_CodeCatalysts/
├── config/
│   └── database.php          # Database configuration
├── auth/
│   ├── login.php            # Login handler
│   ├── register.php         # Registration handler
│   └── logout.php           # Logout handler
├── Admin/
│   └── dashboard.php        # Admin dashboard
├── User/
│   └── dashboard.php        # User dashboard
├── index.php                # Main landing page
├── database.sql             # Database schema
├── setup.php                # Setup verification script
└── README.md                # Project documentation
```

## 🎯 Features Implemented

### Authentication System
- ✅ User registration with validation
- ✅ Secure login with password hashing
- ✅ Role-based access control (Admin/User)
- ✅ Session management
- ✅ Logout functionality

### Admin Dashboard
- ✅ Statistics overview
- ✅ User management (placeholder)
- ✅ Item management (placeholder)
- ✅ Pending approvals (placeholder)
- ✅ Swap requests (placeholder)
- ✅ Reports (placeholder)

### User Dashboard
- ✅ Profile overview with points
- ✅ My items management (placeholder)
- ✅ Browse items (placeholder)
- ✅ Add new items (placeholder)
- ✅ Swap history (placeholder)

### Security Features
- ✅ SQL injection protection (PDO prepared statements)
- ✅ Password hashing (password_hash)
- ✅ Session-based authentication
- ✅ Input validation and sanitization
- ✅ Role-based access control

## 🐛 Troubleshooting

### Common Issues

**1. Database Connection Error**
- Ensure MySQL is running in XAMPP
- Check database credentials in `config/database.php`
- Verify `rewear_db` database exists

**2. Page Not Found (404)**
- Ensure Apache is running in XAMPP
- Check file paths and permissions
- Verify URL: http://localhost/odoo/odoo_CodeCatalysts/

**3. Login Not Working**
- Verify database tables are imported correctly
- Check if demo users exist in the database
- Ensure password hashing is working

**4. Session Issues**
- Check PHP session configuration
- Ensure cookies are enabled in browser
- Verify session storage permissions

### Verification Script
Run `setup.php` to verify your installation:
http://localhost/odoo/odoo_CodeCatalysts/setup.php

This script will check:
- Database connection
- Table existence
- Default users
- File structure

## 🔄 Next Steps

The current implementation includes the core authentication system and dashboard frameworks. To complete the full ReWear application, you would need to implement:

1. **Item Management**
   - Add item form with image upload
   - Item listing and search
   - Item approval system

2. **Swap System**
   - Swap request functionality
   - Point-based redemption
   - Swap history tracking

3. **User Features**
   - Profile management
   - Points system
   - Communication between users

4. **Admin Features**
   - User management
   - Content moderation
   - Analytics and reports

## 📞 Support

For technical support or questions:
- **Team Leader:** Harsh Vora (harshvora1003@gmail.com)
- **Project:** ReWear - Community Clothing Exchange
- **Team:** CodeCatalysts

---

**Happy coding! ♻️** 