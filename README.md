# Pranayom Fitness Management System - PHP & MySQL Setup Guide

## Prerequisites

- XAMPP installed with Apache and MySQL running
- Web browser
- Text editor (optional, for configuration)

## Database Setup Instructions

### Step 1: Start XAMPP Services

1. Open XAMPP Control Panel
2. Start **Apache** service
3. Start **MySQL** service

### Step 2: Import Database Schema

1. Open your web browser and go to: `http://localhost/phpmyadmin`
2. Click on "SQL" tab at the top
3. Open the file: `Web_Project/database/schema.sql`
4. Copy all the contents and paste into the SQL query box
5. Click "Go" button to execute
6. You should see a success message: "Database schema created successfully!"

### Step 3: Import Sample Data

1. Still in phpMyAdmin, make sure you're in the SQL tab
2. Open the file: `Web_Project/database/sample_data.sql`
3. Copy all the contents and paste into the SQL query box
4. Click "Go" button to execute
5. You should see success messages with test login credentials

## Test Login Credentials

### Member Login

- URL: `http://localhost/Web_Project/html/login.php`
- Username: `member_afia`
- Password: `password123`

### Trainer Login

- URL: `http://localhost/Web_Project/html/login.php`
- Username: `trainer_sarah`
- Password: `password123`
- (Click "Trainer" toggle before logging in)

### Admin Login

- URL: `http://localhost/Web_Project/admin/login.php`
- Username: `admin1`
- Password: `password123`

## Project Structure

```
Web_Project/
├── config/
│   ├── database.php          # Database connection
│   └── session.php           # Session management
├── includes/
│   ├── auth.php              # Authentication functions
│   ├── db_functions.php      # Database helper functions
│   ├── member_sidebar.php    # Member sidebar component
│   ├── trainer_sidebar.php   # Trainer sidebar component
│   └── admin_sidebar.php     # Admin sidebar component
├── handlers/
│   ├── login_handler.php     # Member/Trainer login
│   ├── admin_login_handler.php  # Admin login
│   ├── logout_handler.php    # Logout
│   ├── member/               # Member-specific handlers
│   ├── trainer/              # Trainer-specific handlers
│   └── admin/                # Admin-specific handlers
├── database/
│   ├── schema.sql            # Database structure
│   └── sample_data.sql       # Test data
├── html/                     # Member pages
├── trainer/                  # Trainer pages
├── admin/                    # Admin pages
├── css/                      # Stylesheets
└── images/                   # Images and media
```

## Features Implemented

### Authentication System

- ✅ Separate login for Member/Trainer and Admin
- ✅ Role-based access control
- ✅ Session management with timeout
- ✅ Secure password hashing

### Member Portal

- ✅ Dashboard with dynamic data
- ✅ View assigned routines
- ✅ View diet plans
- ✅ Track progress (weight, heart rate, sleep, mood)
- ✅ Rate app and trainer
- ✅ Calorie calculator for personal food tracking

### Trainer Portal

- ✅ Dashboard with statistics
- ✅ View assigned members
- ✅ Create routines for members
- ✅ Create diet plans with calorie/weight input
- ✅ View member progress logs
- ✅ Chat with members

### Admin Portal

- ✅ Dashboard with system statistics
- ✅ Add new members
- ✅ Add new trainers
- ✅ Assign trainers to members
- ✅ Manage profiles

## Database Tables

- **users** - Unified authentication for all roles
- **members** - Member profiles and details
- **trainers** - Trainer profiles and specializations
- **admins** - Admin profiles
- **routines** - Workout routines assigned to members
- **diet_plans** - Diet plans (trainer-created and member-created)
- **progress_tracking** - Member health metrics tracking
- **classes** - Available fitness classes
- **class_bookings** - Member class reservations
- **messages** - Chat system between members and trainers
- **ratings** - App and trainer ratings
- **routine_progress** - Exercise completion tracking
- **workout_content** - Trainer's content library

## Key Differences: Trainer vs Member (Calorie Logic)

### Trainer - Diet Plan Creation

- Trainers can create diet plans for their assigned members
- Input fields include: meal name, food items, **weight (grams)**, **calories**
- Trainer sets these values for the member's diet plan
- Located in: `trainer/diet_plan.php`

### Member - Food Tracking

- Members can view diet plans created by their trainer
- Members can also add their own food items with calorie calculator
- Input fields for personal tracking: food name, weight, calories
- Located in: `html/member_diet.php`

## Troubleshooting

### Database Connection Error

- Check if MySQL is running in XAMPP
- Verify database name is `pranayom_db`
- Check `config/database.php` for correct credentials

### Login Not Working

- Ensure database is imported correctly
- Clear browser cookies/cache
- Check browser console for JavaScript errors

### Page Not Found (404)

- Ensure Apache is running
- Check file paths in code match your directory structure
- Verify `.php` extension is used, not `.html`

### Session Issues

- Clear browser cookies
- Check PHP session settings in XAMPP
- Restart Apache service

## Development Notes

- All passwords are hashed using PHP's `password_hash()` function
- CSRF protection is implemented for forms
- SQL injection protection via PDO prepared statements
- Session timeout is set to 30 minutes
- All user inputs are sanitized and validated

## Next Steps

1. Import the database schema and sample data
2. Test login with provided credentials
3. Explore different user roles (member, trainer, admin)
4. Customize the application as needed
5. Add your own data and users

## Support

For issues or questions, check the implementation plan document or review the code comments in each file.

---

**Note**: This is a development setup. For production use, ensure proper security measures including HTTPS, strong passwords, and secure server configuration.
