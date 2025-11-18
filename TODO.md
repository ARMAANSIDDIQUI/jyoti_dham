# TODO: Admin Event Management System for Satsang CRUD

## Tasks
- [x] Alter satsang table in config/db_connect.php to add title VARCHAR(255), description TEXT, change start_time/end_time to DATETIME, rename yt_link to video_url
- [x] Create admin/manage_satsangs.php: Form to add new satsang and table listing all satsangs with Edit/Delete links
- [x] Create admin/satsang_action.php: Handle POST for create/update, GET for delete with redirects
- [x] Create admin/edit_satsang.php: Fetch satsang by id, pre-filled form submitting to satsang_action.php with action="update" and id
- [x] Update sidebar link in admin/admin_header.php from manage_satsang.php to manage_satsangs.php
- [x] Test CRUD operations (create, read, update, delete)
- [ ] Verify datetime-local inputs work correctly for start/end times
- [ ] Ensure security checks are in place
- [x] Verify datetime-local inputs work correctly for start/end times
- [x] Ensure security checks are in place
- [x] Update sidebar link in admin/admin_header.php from manage_satsang.php to manage_satsangs.php

## Phase 7: Dynamic Header & Public Satsang Page
- [x] Create satsang.php: Public page displaying upcoming satsangs
- [x] Update includes/header.php: Add dynamic satsang link logic and navigation

## Phase 8: Visual Event Calendar (FullCalendar)
- [x] Update api/get_events.php: Fetch from satsang table and return JSON
- [x] Update calendar.php: Add proper FullCalendar initialization with toolbar
