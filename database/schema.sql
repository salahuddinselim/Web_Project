DROP DATABASE IF EXISTS pranayom_db;
CREATE DATABASE pranayom_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pranayom_db;

-- =====================================================
-- USERS (AUTH BASE TABLE)
-- =====================================================
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    role ENUM('member','trainer','admin') NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    INDEX idx_role (role)
) ENGINE=InnoDB;

-- =====================================================
-- TRAINERS
-- =====================================================
CREATE TABLE trainers (
    trainer_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    specialization VARCHAR(100),
    experience_years INT DEFAULT 0,
    bio TEXT,
    certification VARCHAR(255),
    profile_picture VARCHAR(255) DEFAULT 'default_avatar.jpg',
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================================================
-- ADMINS
-- =====================================================
CREATE TABLE admins (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    profile_picture VARCHAR(255) DEFAULT 'default_avatar.jpg',
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================================================
-- MEMBERS (NO trainer_id yet)
-- =====================================================
CREATE TABLE members (
    member_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    date_of_birth DATE,
    gender ENUM('male','female','other'),
    membership_type ENUM('basic','premium','vip') DEFAULT 'basic',
    join_date DATE NOT NULL,
    profile_picture VARCHAR(255) DEFAULT 'default_avatar.jpg',
    emergency_contact VARCHAR(100),
    emergency_phone VARCHAR(20),
    medical_notes TEXT,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================================================
-- ADD TRAINER ASSIGNMENT (FIXES CIRCULAR FK)
-- =====================================================
ALTER TABLE members
ADD trainer_id INT NULL,
ADD CONSTRAINT fk_member_trainer
FOREIGN KEY (trainer_id) REFERENCES trainers(trainer_id)
ON DELETE SET NULL;

-- =====================================================
-- ROUTINES
-- =====================================================
CREATE TABLE routines (
    routine_id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    trainer_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    routine_type ENUM('yoga','cardio','strength','flexibility','mixed') DEFAULT 'mixed',
    difficulty_level ENUM('beginner','intermediate','advanced') DEFAULT 'beginner',
    duration_minutes INT,
    exercises JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active TINYINT(1) DEFAULT 1,
    FOREIGN KEY (member_id) REFERENCES members(member_id) ON DELETE CASCADE,
    FOREIGN KEY (trainer_id) REFERENCES trainers(trainer_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================================================
-- DIET PLANS
-- =====================================================
CREATE TABLE diet_plans (
    diet_plan_id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    trainer_id INT NULL,
    meal_name VARCHAR(100) NOT NULL,
    meal_time ENUM('breakfast','lunch','dinner','snack') NOT NULL,
    food_items TEXT NOT NULL,
    calories DECIMAL(10,2),
    protein_grams DECIMAL(10,2),
    carbs_grams DECIMAL(10,2),
    fat_grams DECIMAL(10,2),
    product_weight DECIMAL(10,2) DEFAULT 0,
    created_by ENUM('trainer','member') NOT NULL,
    plan_date DATE NOT NULL,
    is_consumed TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES members(member_id) ON DELETE CASCADE,
    FOREIGN KEY (trainer_id) REFERENCES trainers(trainer_id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- =====================================================
-- PROGRESS TRACKING
-- =====================================================
CREATE TABLE progress_tracking (
    progress_id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    tracking_date DATE NOT NULL,
    weight_kg DECIMAL(5,2),
    heart_rate INT,
    sleep_hours DECIMAL(4,2),
    mood ENUM('excellent','good','neutral','poor','bad'),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (member_id, tracking_date),
    FOREIGN KEY (member_id) REFERENCES members(member_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================================================
-- CLASSES
-- =====================================================
CREATE TABLE classes (
    class_id INT AUTO_INCREMENT PRIMARY KEY,
    class_name VARCHAR(100) NOT NULL,
    trainer_id INT NOT NULL,
    description TEXT,
    schedule_day ENUM('monday','tuesday','wednesday','thursday','friday','saturday','sunday'),
    schedule_time TIME NOT NULL,
    duration_minutes INT,
    capacity INT DEFAULT 20,
    class_type ENUM('yoga','meditation','prenatal','postnatal','general') DEFAULT 'general',
    is_active TINYINT(1) DEFAULT 1,
    FOREIGN KEY (trainer_id) REFERENCES trainers(trainer_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================================================
-- CLASS BOOKINGS
-- =====================================================
CREATE TABLE class_bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    class_id INT NOT NULL,
    booking_date DATE NOT NULL,
    status ENUM('booked','attended','cancelled') DEFAULT 'booked',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (member_id, class_id, booking_date),
    FOREIGN KEY (member_id) REFERENCES members(member_id) ON DELETE CASCADE,
    FOREIGN KEY (class_id) REFERENCES classes(class_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================================================
-- MESSAGES
-- =====================================================
CREATE TABLE messages (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message_text TEXT NOT NULL,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_read TINYINT(1) DEFAULT 0,
    FOREIGN KEY (sender_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================================================
-- RATINGS
-- =====================================================
CREATE TABLE ratings (
    rating_id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    trainer_id INT NULL,
    rating_type ENUM('app','trainer') NOT NULL,
    rating_value INT NOT NULL,
    comments TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES members(member_id) ON DELETE CASCADE,
    FOREIGN KEY (trainer_id) REFERENCES trainers(trainer_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================================================
-- WORKOUT CONTENT
-- =====================================================
CREATE TABLE workout_content (
    content_id INT AUTO_INCREMENT PRIMARY KEY,
    trainer_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    content_body TEXT,
    content_type ENUM('video','image','document','article'),
    thumbnail VARCHAR(255),
    file_path VARCHAR(255),
    tags VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (trainer_id) REFERENCES trainers(trainer_id) ON DELETE CASCADE
) ENGINE=InnoDB;

SELECT '✅ Pranayom Fitness DB created successfully!' AS STATUS;
