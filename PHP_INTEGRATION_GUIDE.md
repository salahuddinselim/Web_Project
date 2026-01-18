# Quick PHP Integration Guide

## What Was Done

All HTML files have been copied to .php format:

- 6 member pages
- 8 trainer pages
- 5 admin pages

## What Each File Needs

### 1. PHP Authentication Header (add at very top, before <!DOCTYPE>)

**For Member Pages:**

```php
<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db_functions.php';
requireLogin('member');
$member_id = $_SESSION['member_id'];
$member_name = $_SESSION['full_name'];
?>
```

**For Trainer Pages:**

```php
<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db_functions.php';
requireLogin('trainer');
$trainer_id = $_SESSION['trainer_id'];
$trainer_name = $_SESSION['full_name'];
?>
```

**For Admin Pages:**

```php
<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db_functions.php';
requireLogin('admin');
$admin_id = $_SESSION['admin_id'];
$admin_name = $_SESSION['full_name'];
?>
```

### 2. Replace Sidebar HTML

Find the entire `<div class="sidebar">...</div>` section and replace with:

**Member pages:**

```php
<?php include __DIR__ . '/../includes/member_sidebar.php'; ?>
```

**Trainer pages:**

```php
<?php include __DIR__ . '/../includes/trainer_sidebar.php'; ?>
```

**Admin pages:**

```php
<?php include __DIR__ . '/../includes/admin_sidebar.php'; ?>
```

## Files Status

### ✅ Completed

- member_dashboard.php - Fully integrated
- member_classes.php - PHP added

### ⏳ Need PHP Integration

**Member:**

- member_routines.php
- member_diet.php
- member_progress.php
- member_chat.php
- member_profile.php

**Trainer:**

- trainer/dashboard.php
- trainer/members.php
- trainer/routine.php
- trainer/diet_plan.php
- trainer/progress_logs.php
- trainer/chat.php
- trainer/profile.php
- trainer/content.php

**Admin:**

- admin/dashboard.php
- admin/add_member.php
- admin/add_trainer.php
- admin/assign_trainer.php
- admin/profile.php

## Manual Steps (if needed)

1. Open each .php file
2. Add PHP header at line 1 (before <!DOCTYPE>)
3. Find sidebar div (usually around line 170-230)
4. Replace entire sidebar with PHP include
5. Save file

## Testing

After integration:

1. Login as member/trainer/admin
2. Navigate to each page
3. Verify sidebar shows correctly
4. Verify no PHP errors
