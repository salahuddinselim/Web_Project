# Pranayom Yoga Center Management System

Welcome to the **Pranayom Yoga Center Management System** project. This is a web-based interface designed to manage a yoga training center, facilitating interactions between Administrators, Trainers, and Members.

## Project Description

This project demonstrates the front-end design and basic logic for a Yoga Studio website. It focuses on a clean, modern aesthetic ("Pranayom") with specific features for different user roles. The system is built using **pure HTML, CSS, and Vanilla JavaScript** without any external frameworks, making it lightweight and easy to understand for educational purposes.

## Key Features

### Public Pages

- **Landing Page (`index.html`)**: A welcoming home page displaying the hero section, yoga offerings, and user testimonials.
- **Membership Plans (`membership.html`)**: Detailed pricing cards for Monthly, Annual, and Class Pack subscriptions, plus non-member drop-in rates.
- **Privacy Policy (`policy.html`)**: Comprehensive privacy policy details.
- **Terms & Conditions (`terms.html`)**: Full terms of service and user agreements.

### Authentication

- **Login Page (`login.html`)**: A dark-themed, modern login interface.
  - Includes a **Role Toggle** to switch between "Member" and "Trainer".
  - Simulates authentication logic using JavaScript.

## Folder Structure

The project is organized into clear directories to separate concerns:

```
Web_Project/
│
├── css/
│   └── style.css       # Global styles and shared layout classes
│
├── html/
│   ├── classes.html          # Classes schedule and info
│   ├── contact.html          # Contact form and info
│   ├── index.html            # Home/Landing page
│   ├── login.html            # Login interface
│   ├── member_chat.html      # Member chat interface
│   ├── member_classes.html   # Member booked classes
│   ├── member_dashboard.html # Member main dashboard
│   ├── member_diet.html      # Member diet plan
│   ├── member_profile.html   # Member profile settings
│   ├── member_progress.html  # Member progress tracking
│   ├── member_routines.html  # Member daily routines
│   ├── membership.html       # Pricing and plans
│   ├── policy.html           # Privacy Policy
│   ├── terms.html            # Terms and Conditions
│   └── trainers.html         # Trainers list
│
├── images/             # Folder for project assets (images, icons)
│
└── README.md           # Project documentation
```

## How to Run

Since this is a static site, you do not need any backend server.

1.  Navigate to the `html` folder.
2.  Open `index.html` in your web browser (Chrome, Firefox, Edge, etc.).
3.  Navigate through the links (Home, Membership Plans, Login).

## Login Credentials (Simulated)

The login functionality is simulated with JavaScript. Use the following credentials to test the different roles:

| Role        | Username / Email | Password  |
| :---------- | :--------------- | :-------- |
| **Member**  | `member`         | `member`  |
| **Trainer** | `trainer`        | `trainer` |
| **Admin**   | `admin`          | `admin`   |

_(Note: The actual email format logic on the login page also accepts simple usernames for ease of testing)_

## Technology Stack

- **HTML5**: For semantic structure.
- **CSS3**: For styling (Flexbox, Grid, Custom Themes).
- **JavaScript (Vanilla)**: For simple form handling and navigation logic.

### Member Portal

- **Dashboard (`member_dashboard.html`)**: Central hub for members to view stats, schedule, and quick actions.
- **Routines (`member_routines.html`)**: Interactive daily routine tracker with progress achievements.
- **Diet Plan (`member_diet.html`)**: Weekly meal plan overview with day tabs.
- **Progress (`member_progress.html`)**: Visual charts and statistics for health metrics.
- **Chat (`member_chat.html`)**: Messaging interface for connecting with trainers and other members.
- **Profile (`member_profile.html`)**: User profile management and password settings.

## Credits

- **Frontend Designer (Figma)**: Afia Tasnim Ria
- **Lead**: Salah Uddin Selim
