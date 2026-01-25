-- Migration to support daily plans and tracking
ALTER TABLE routines ADD COLUMN scheduled_date DATE NULL;

CREATE TABLE yoga_sessions (
    session_id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    routine_id INT NULL,
    session_date DATE NOT NULL,
    duration_minutes INT NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES members(member_id) ON DELETE CASCADE,
    FOREIGN KEY (routine_id) REFERENCES routines(routine_id) ON DELETE SET NULL
);
