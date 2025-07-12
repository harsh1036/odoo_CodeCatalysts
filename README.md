<<<<<<< HEAD
# 👚 ReWear – Community Clothing Exchange

### 🚀 Developed by Team CodeCatalysts

**A sustainable solution for fashion-conscious communities to exchange unused clothes through a direct swap or point-based system.**

---

## 🌍 Project Overview

**ReWear** is a web-based platform that encourages sustainability in fashion by promoting the reuse of wearable clothing. Users can list unused clothes, browse available items, and either request direct swaps or redeem them using earned points. ReWear helps reduce textile waste by turning closets into opportunities for conscious sharing.

---

## 🔑 Key Features

### ✅ User Authentication
- Secure email/password signup and login
- Role-based access for users and admins

### 🏠 Landing Page
- Introduction to the ReWear concept
- Call-to-action buttons: 
  - `Start Swapping`
  - `Browse Items`
  - `List an Item`
- Featured items carousel for popular or latest listings

### 👤 User Dashboard
- User profile management
- Points balance tracking
- My Listings: view and manage uploaded items
- Swap history: ongoing and completed swaps

### 🧥 Item Detail Page
- Image gallery and full description of the item
- Uploader’s information
- Options to:
  - Send Swap Request
  - Redeem via Points
- Real-time item availability status

### 📤 Add New Item
- Upload images (multi-image support)
- Provide details: 
  - Title, Description
  - Category, Type, Size, Condition, Tags
- Submit for listing (goes to admin for approval)

### 🛠️ Admin Panel
- View and moderate item listings
- Approve or reject submissions
- Remove spam or inappropriate content
- Lightweight interface for quick actions

---

## 🧩 Tech Stack

- **Frontend:** HTML, CSS, JavaScript, Bootstrap
- **Backend:** PHP (PDO)
- **Database:** MySQL
- **Security:** Session Management, SQL Injection Protection
- **Email:** PHPMailer (SMTP)
- **Deployment:** Localhost (XAMPP) | Ready for LAMP Stack Hosting

---

## 🧪 Status

✅ MVP Complete  
✅ CRUD Functionalities  
✅ Admin Module Ready  
🔜 Future Enhancements:  
- Chat feature for swap negotiation  
- Pickup/Delivery coordination  
- Mobile responsiveness improvements  
- Gamified rewards for eco-actions

---

## 👥 Team CodeCatalysts

| Name              | Role             |
|-------------------|------------------|
| **Harsh Vora**    | Team Leader, Backend Developer |
| Priyanshu Rathod  | Frontend Developer |
| Bhavya Radiya     | UI/UX Designer |
| Riya Patel        | QA & Documentation |

📧 **Team Leader Contact:** harshvora1003@gmail.com

📩 Reach out to us via [harshvora1003@gmail.com](mailto:harshvora1003@gmail.com)

Together, let's make fashion circular. ♻️
=======
# Renewable Cloth Website

A modern, responsive website for a sustainable fashion brand with user authentication system.

## Features

- **User Authentication**: Login and signup functionality with session management
- **Responsive Design**: Mobile-friendly interface using Bootstrap 5
- **Modern UI**: Beautiful gradient designs and smooth animations
- **Security**: Password hashing and SQL injection protection
- **Database Integration**: MySQL database with automatic table creation

## File Structure

```
odoo1/
├── auth/
│   ├── login.php          # Login page
│   ├── signup.php         # Signup page
│   ├── logout.php         # Logout functionality
│   ├── session.php        # Session management
│   └── database.php       # Database connection
├── index.php              # Main homepage
└── README.md             # This file
```

## Prerequisites

- XAMPP (Apache + MySQL + PHP)
- Web browser
- Text editor

## Installation

1. **Start XAMPP**
   - Open XAMPP Control Panel
   - Start Apache and MySQL services

2. **Database Setup**
   - The database and tables will be created automatically when you first access the website
   - Database name: `renewable_cloth`
   - Default credentials: `root` (no password)

3. **File Placement**
   - Place all files in your XAMPP `htdocs` folder
   - Example: `C:\xampp\htdocs\odoo1\`

4. **Access the Website**
   - Open your web browser
   - Navigate to: `http://localhost/odoo1/`

## Database Configuration

The database connection is configured in `auth/database.php`:

```php
$host = "localhost";
$dbname = "renewable_cloth";
$username = "root";
$password = "";
```

If you need to change these settings, edit the file accordingly.

## Features Explained

### Authentication System
- **Login**: Users can log in with email and password
- **Signup**: New users can create accounts with validation
- **Session Management**: Secure session handling across pages
- **Logout**: Users can safely log out

### Security Features
- Password hashing using PHP's `password_hash()`
- SQL injection protection with prepared statements
- Input validation and sanitization
- Session-based authentication

### User Interface
- **Bootstrap 5**: Modern, responsive framework
- **Font Awesome**: Beautiful icons throughout the site
- **Custom CSS**: Gradient backgrounds and smooth animations
- **Mobile Responsive**: Works on all device sizes

### Website Sections
- **Hero Section**: Eye-catching introduction
- **Features**: Highlighting sustainable practices
- **Products**: Showcase of eco-friendly clothing
- **About**: Company information and mission
- **Contact**: Contact form for inquiries
- **Footer**: Links and newsletter signup

## Customization

### Colors
The primary color scheme uses green tones for sustainability:
- Primary: `#4CAF50`
- Secondary: `#45a049`
- Accent: `#8BC34A`

### Adding Products
To add new products, edit the products section in `index.php`.

### Database Schema
The users table structure:
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

## Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Ensure MySQL is running in XAMPP
   - Check database credentials in `auth/database.php`

2. **Page Not Found**
   - Verify files are in the correct directory
   - Check Apache is running in XAMPP

3. **Login Issues**
   - Ensure the database and tables are created
   - Check if the user account exists

### Error Logs
Check XAMPP error logs at:
- Apache: `C:\xampp\apache\logs\error.log`
- MySQL: `C:\xampp\mysql\data\mysql_error.log`

## Browser Compatibility

- Chrome (recommended)
- Firefox
- Safari
- Edge

## Performance Tips

- Optimize images for web use
- Enable browser caching
- Use a CDN for external resources
- Compress CSS and JavaScript files

## Security Recommendations

- Change default database credentials in production
- Use HTTPS in production
- Implement rate limiting for login attempts
- Regular security updates
- Backup database regularly

## Support

For issues or questions:
1. Check the troubleshooting section
2. Review error logs
3. Ensure all prerequisites are met
4. Verify file permissions

## License

This project is open source and available under the MIT License.

---

**Note**: This is a demonstration website. For production use, implement additional security measures and follow web development best practices. 
>>>>>>> 6e183b1 (Implement landing page, admin panel, user/admin roles, and test data)
