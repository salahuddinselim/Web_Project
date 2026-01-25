<?php
require_once __DIR__ . '/config/database.php';

try {
    // 1. Add scheduled_date to routines if it doesn't exist
    $result = $pdo->query("SHOW COLUMNS FROM routines LIKE 'scheduled_date'");
    if ($result->rowCount() == 0) {
        $pdo->exec("ALTER TABLE routines ADD COLUMN scheduled_date DATE NULL");
        echo "✅ Added scheduled_date to routines table\n";
    }

    // 2. Create yoga_sessions table if it doesn't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS yoga_sessions (
        session_id INT AUTO_INCREMENT PRIMARY KEY,
        member_id INT NOT NULL,
        routine_id INT NULL,
        session_date DATE NOT NULL,
        duration_minutes INT NOT NULL,
        notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (member_id) REFERENCES members(member_id) ON DELETE CASCADE,
        FOREIGN KEY (routine_id) REFERENCES routines(routine_id) ON DELETE SET NULL
    )");
    echo "✅ Yoga sessions table ready\n";

    // 3. Add is_consumed to diet_plans if it doesn't exist
    $result = $pdo->query("SHOW COLUMNS FROM diet_plans LIKE 'is_consumed'");
    if ($result->rowCount() == 0) {
        $pdo->exec("ALTER TABLE diet_plans ADD COLUMN is_consumed TINYINT(1) DEFAULT 0");
        echo "✅ Added is_consumed to diet_plans table\n";
    }

    // 4. Add completed_exercises to routines (JSON storage for which exercises a member checked off)
    $result = $pdo->query("SHOW COLUMNS FROM routines LIKE 'completed_exercises'");
    if ($result->rowCount() == 0) {
        $pdo->exec("ALTER TABLE routines ADD COLUMN completed_exercises TEXT NULL");
        echo "✅ Added completed_exercises to routines table\n";
    }

    // 5. Add product_weight to diet_plans
    $result = $pdo->query("SHOW COLUMNS FROM diet_plans LIKE 'product_weight'");
    if ($result->rowCount() == 0) {
        $pdo->exec("ALTER TABLE diet_plans ADD COLUMN product_weight DECIMAL(10,2) DEFAULT 0");
        echo "✅ Added product_weight to diet_plans table\n";
    }

    echo "\nDatabase migration completed successfully!";
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
