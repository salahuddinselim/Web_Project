USE pranayom_db;

-- =====================================================
-- USERS (Password: password123 for all)
-- =====================================================
INSERT INTO users (username, password_hash, email, role) VALUES
('admin1','$2y$10$e0MYzXyjpJS7Pd0RVvHwHeFpDMJcJYrKZRV7z1p9qJfLQqQY7qJVq','admin@pranayom.com','admin'),
('trainer1','$2y$10$e0MYzXyjpJS7Pd0RVvHwHeFpDMJcJYrKZRV7z1p9qJfLQqQY7qJVq','trainer1@pranayom.com','trainer'),
('trainer2','$2y$10$e0MYzXyjpJS7Pd0RVvHwHeFpDMJcJYrKZRV7z1p9qJfLQqQY7qJVq','trainer2@pranayom.com','trainer'),
('member1','$2y$10$e0MYzXyjpJS7Pd0RVvHwHeFpDMJcJYrKZRV7z1p9qJfLQqQY7qJVq','member1@pranayom.com','member'),
('member2','$2y$10$e0MYzXyjpJS7Pd0RVvHwHeFpDMJcJYrKZRV7z1p9qJfLQqQY7qJVq','member2@pranayom.com','member'),
('member3','$2y$10$e0MYzXyjpJS7Pd0RVvHwHeFpDMJcJYrKZRV7z1p9qJfLQqQY7qJVq','member3@pranayom.com','member');

-- =====================================================
-- ADMINS
-- =====================================================
INSERT INTO admins (user_id, full_name, phone) VALUES
(1,'System Admin','01710000000');

-- =====================================================
-- TRAINERS
-- =====================================================
INSERT INTO trainers (user_id, full_name, phone, specialization, experience_years) VALUES
(2,'Rahim Yoga Trainer','01720000001','Yoga & Meditation',5),
(3,'Karim Strength Trainer','01720000002','Strength Training',7);

-- =====================================================
-- MEMBERS
-- =====================================================
INSERT INTO members
(user_id, full_name, phone, join_date, gender, membership_type)
VALUES
(4,'Selim Member','01730000001',CURDATE(),'male','premium'),
(5,'Ayesha Member','01730000002',CURDATE(),'female','basic'),
(6,'Nabila Member','01730000003',CURDATE(),'female','vip');

-- =====================================================
-- ASSIGN TRAINERS TO MEMBERS
-- =====================================================
UPDATE members SET trainer_id = 1 WHERE member_id IN (1,2);
UPDATE members SET trainer_id = 2 WHERE member_id = 3;

-- =====================================================
-- ROUTINES
-- =====================================================
INSERT INTO routines
(member_id, trainer_id, title, description, routine_type, difficulty_level, duration_minutes, exercises)
VALUES
(1,1,'Morning Yoga Flow','Gentle morning yoga','yoga','beginner',30,
 '[{"name":"Cat-Cow","sets":1,"reps":"10 breaths"}]'),

(2,1,'Prenatal Strength','Safe pregnancy routine','strength','beginner',25,
 '[{"name":"Wall Push-ups","sets":2,"reps":10}]'),

(3,2,'Full Body Strength','Intermediate strength workout','strength','intermediate',45,
 '[{"name":"Deadlift","sets":4,"reps":8}]');

-- =====================================================
-- CLASSES
-- =====================================================
INSERT INTO classes
(class_name, trainer_id, schedule_day, schedule_time, duration_minutes, class_type)
VALUES
('Morning Yoga',1,'monday','07:00:00',60,'yoga'),
('Prenatal Care',1,'wednesday','10:00:00',60,'prenatal'),
('Strength Basics',2,'friday','18:00:00',75,'general');

-- =====================================================
-- CLASS BOOKINGS
-- =====================================================
INSERT INTO class_bookings
(member_id, class_id, booking_date)
VALUES
(1,1,'2025-01-20'),
(2,2,'2025-01-21'),
(3,3,'2025-01-22');

-- =====================================================
-- DIET PLANS
-- =====================================================
INSERT INTO diet_plans
(member_id, trainer_id, meal_name, meal_time, food_items, calories, created_by, plan_date)
VALUES
(1,1,'Healthy Breakfast','breakfast','Oats, Banana, Milk',350,'trainer',CURDATE()),
(2,1,'Light Lunch','lunch','Rice, Vegetables',450,'trainer',CURDATE()),
(3,2,'Protein Dinner','dinner','Chicken, Salad',600,'trainer',CURDATE());

-- =====================================================
-- PROGRESS TRACKING
-- =====================================================
INSERT INTO progress_tracking
(member_id, tracking_date, weight_kg, sleep_hours, mood)
VALUES
(1,CURDATE(),72.5,7.5,'good'),
(2,CURDATE(),65.2,8.0,'excellent'),
(3,CURDATE(),58.0,6.8,'neutral');

-- =====================================================
-- RATINGS
-- =====================================================
INSERT INTO ratings
(member_id, trainer_id, rating_type, rating_value, comments)
VALUES
(1,1,'trainer',5,'Excellent guidance'),
(2,1,'trainer',4,'Very supportive'),
(3,2,'trainer',5,'Great strength program');

-- =====================================================
-- WORKOUT CONTENT
-- =====================================================
INSERT INTO workout_content
(trainer_id, title, content_type, file_path, tags)
VALUES
(1,'Morning Yoga Video','video','videos/yoga.mp4','yoga,morning'),
(2,'Strength Guide PDF','document','docs/strength.pdf','strength,training');

-- =====================================================
SELECT '✅ ALL DATA INSERTED SUCCESSFULLY' AS STATUS;
SELECT 'Login: admin1 / password123' AS AdminCredentials;
SELECT 'Login: trainer1 / password123' AS TrainerCredentials;
SELECT 'Login: member1 / password123' AS MemberCredentials;
