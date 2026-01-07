# 🧘 Pranayom Yoga Center Management System

[![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)](https://github.com/salahuddinselim/Web_Project)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)
[![Uptime](https://img.shields.io/badge/status-online-success.svg)](#)

A premium, comprehensive management suite for modern yoga centers. **Pranayom** is designed to bridge the gap between administrators, trainers, and members through a unified, high-performance dark-themed interface.

---

## 🌟 Vision & Purpose

Pranayom is built to provide a seamless digital experience for yoga studio management. From member progress tracking to trainer schedules and administrative control, it offers a state-of-the-art solution that prioritizes **visual excellence** and **functional simplicity**.

> [!NOTE]
> This project is a front-end showcase using pure HTML, CSS, and Vanilla JavaScript. It focuses on premium design aesthetics, emphasizing "glassmorphism," dark themes, and bio-inspired green accents.

---

## 🏗️ Portal Ecosystem

Pranayom is built on a multi-role architecture, providing specialized tools for every user level:

### 👑 Admin Portal

**Purpose**: High-level studio oversight and system configuration.

- **Capabilities**:
  - Manage the entire user database (Members & Trainers).
  - Assign Trainers to specific Members.
  - Monitor global system activity and KPIs.
  - Full administrative security control.

### 🏃 Trainer Portal

**Purpose**: Hands-on member management and content creation.

- **Capabilities**:
  - Track assigned members and their daily progress.
  - Create and assign custom Workout Routines and Diet Plans.
  - Upload instructional content (Classes/Videos).
  - Direct communication with members via private chat.

### 🧘 Member Portal

**Purpose**: Personal wellness journey and progress tracking.

- **Capabilities**:
  - Access assigned routines and diet plans.
  - Log daily health metrics (Weight, Sleep, Mood).
  - Join scheduled classes and view video content.
  - Real-time support chat with assigned Trainers.

---

## 📂 Project Structure & Page Directory

### 🗺️ Full Directory Tree

```text
Web_Project/
├── admin/                # Admin Portal (Redesign Complete)
│   ├── dashboard.html    # KPI overview & Recent activity
│   ├── add_member.html   # Member creation form
│   ├── add_trainer.html  # Trainer creation form
│   ├── assign_trainer.html # Mapping trainers to members
│   ├── login.html        # Admin-specific secure login
│   └── profile.html      # Personal settings & Password
├── trainer/              # Trainer Portal (Consistent Design)
│   ├── dashboard.html    # Oversight & Quick actions
│   ├── members.html      # List of assigned members
│   ├── routine.html      # Routine creation tool
│   ├── diet_plan.html    # Diet management interface
│   ├── progress_logs.html # Member health data tracking
│   ├── content.html      # Content upload & management
│   ├── chat.html         # Real-time messaging
│   └── profile.html      # Trainer personal profile
├── html/                 # Member Portal & Public Pages
│   ├── index.html        # Hero landing page
│   ├── member_dashboard.html # Member's central hub
│   ├── member_routines.html # Progress-linked routine tracker
│   ├── member_diet.html  # Tabbed weekly meal plans
│   ├── member_progress.html # Health metrics visualization
│   ├── member_chat.html  # Trainer-member chat interface
│   ├── member_profile.html # Member account settings
│   ├── membership.html   # Pricing & Plan details
│   ├── login.html        # Unified login with role toggle
│   ├── trainers.html     # Public trainer showcase
│   ├── contact.html      # Center contact information
│   └── (Legal pages)     # privacy, terms, etc.
├── css/
│   └── style.css         # Core design system & utilities
└── images/               # Optimized assets & placeholders
```

---

## 🚀 Getting Started

### Prerequisites

- Any modern web browser (Chrome, Firefox, Safari, Edge).
- No server installation required (Static site).

### Installation

1. Clone or download the repository.
2. Navigate to the root folder.
3. Open `html/index.html` to start your journey.

### 🔑 Authentication (Simulated)

Experience different roles using the following credentials:

| Role        | Username / Email | Password  |
| :---------- | :--------------- | :-------- |
| **Member**  | `member`         | `member`  |
| **Trainer** | `trainer`        | `trainer` |
| **Admin**   | `admin`          | `admin`   |

---

## 🛠️ Technology Stack

- **Structure**: Semantic HTML5 for accessibility and SEO.
- **Design**: Vanilla CSS3 with Custom Properties (CSS Variables) for a unified design system.
- **Logic**: ES6+ JavaScript for interactive components and navigation.
- **Visuals**: High-resolution photography and custom SVG icons.

---

## 🤝 Credits

- **Frontend Designer (Figma)**: Afia Tasnim Ria
- **Lead**: Salah Uddin Selim
