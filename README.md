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
5. Click "Go" button to execute the program.
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
├── admin/                    # Admin portal pages
├── config/
│   ├── database.php          # Database connection
│   └── session.php           # Session management
├── css/                      # Stylesheets (Vanilla CSS)
├── database/
│   ├── schema.sql            # Database structure
│   └── sample_data.sql       # Test data
├── handlers/
│   ├── admin/                # Admin-specific logic
│   ├── member/               # Member-specific logic (Reporting, Logs)
│   ├── trainer/              # Trainer-specific logic (CMS, PDF Generation)
│   ├── login_handler.php     # Unified login logic
│   └── upload_profile_picture.php # Shared profile image handler
├── html/                     # Member portal pages
├── includes/
│   ├── auth.php              # Authentication & Session security
│   ├── db_functions.php      # Reusable database queries
│   ├── fpdf.php              # PDF generation library
│   ├── font/                 # Custom fonts for reports
│   └── *_sidebar.php         # Role-specific navigation menus
├── images/                   # Static assets & icons
├── trainer/                  # Trainer portal pages
├── uploads/                  # User-uploaded profile pictures
└── utils/                    # Helper utilities
```

## Features Implemented

### Authentication & Security

- ✅ Role-based Access Control (Admin, Trainer, Member)
- ✅ Secured Session Management with 30-minute timeout
- ✅ CSRF protection and Input sanitization
- ✅ Password Management (Change Password system)
- ✅ Profile Management (Update info & Upload profile pictures)

### Reporting & Analytics (New! 🚀)

- ✅ **Trainer PDF Reports**: Generate comprehensive member progress reports in PDF format.
- ✅ **Member CSV Exports**: Download routines and diet plans as CSV files for offline tracking.
- ✅ Dynamic progress visualization for health metrics.

### Member Portal

- ✅ Personal dashboard with real-time statistics.
- ✅ **Health Tracking**: Log weight, heart rate, sleep duration, and mood.
- ✅ **Diet & Rutines**: View trainer-assigned plans and track completion.
- ✅ **Calorie Calculator**: Calculate and log personal food intake.
- ✅ Class booking system and trainer/app rating.

### Trainer Portal

- ✅ **Content Management**: Upload and manage workout/educational content.
- ✅ Member management dashboard.
- ✅ Interactive diet and routine builders.
- ✅ Direct messaging system with members.
- ✅ Member progress audit via PDF report generation.

### Admin Portal

- ✅ System-wide statistics and user management.
- ✅ Add/Modify Trainers and Members.
- ✅ Trainer-Member assignment system.
- ✅ Global profile and security settings.

## Database Tables

- **users** - Unified authentication table.
- **members/trainers/admins** - Detailed profile information.
- **routines / diet_plans** - Plan structures.
- **progress_tracking** - Time-series health data.
- **messages** - Real-time chat history.
- **workout_content** - Trainer's digital asset library.
- **ratings / routine_progress** - Feedback and completion metrics.

## Key Differences: Trainer vs Member (Calorie Logic)

### Trainer - Diet Plan Creation

- Trainers define the standard plan for members (Meal, Food, Weight, Calories).
- Located in: `trainer/diet_plan.php`

### Member - Food Tracking

- Members log actual consumption and can add non-plan items using the calculator.
- Located in: `html/member_diet.php`

## Troubleshooting

### Database Connection

- Verify `config/database.php` matches your local MySQL settings.
- Ensure the database name is `pranayom_db`.

### PDF Rendering Issues

- Ensure `includes/fpdf.php` and `includes/font/` directory are present.
- Check write permissions for the `uploads/` directory for profile pictures.

## Development Notes

- **Language Stack**: PHP 8.x, MySQL, Vanilla JavaScript, Vanilla CSS.
- **Reporting**: Uses FPDF for server-side document generation.
- **Responsive Design**: All sidebars and dashboards are optimized for different screen sizes.

---

**Note**: This project is for educational/development purposes. Always implement SSL and production-grade environment variables before deploying to a live server.
