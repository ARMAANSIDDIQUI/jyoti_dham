# TODO List for Jyotidham Updates

## Phase 1: Database Connection & Initialization
- [x] Ensure dependencies: vlucas/phpdotenv, cloudinary/cloudinary_php, google/apiclient, fullcalendar/core
- [x] Create .env file with DB_HOST, DB_NAME, DB_USER, DB_PASS, CLOUDINARY_URL, GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET, GOOGLE_REDIRECT_URI
- [x] Create config/db_connect.php with PDO connection and load .env
- [x] Create config/db_init.php to auto-create users and family_members tables if not exist

## Phase 2: Unified Authentication & Complex Registration
- [ ] Update register.php with comprehensive form (all user fields, family_size, dynamic family inputs, profile_image)
- [ ] Update register_action.php with transaction, Cloudinary upload, password_hash, insert user and family members
- [ ] Update login.php for users and admins, session management, role-based redirect
- [ ] Update logout.php
- [ ] Update site header/navigation with conditional links

## Phase 3: Profile & Family Management (Edit)
- [ ] Update profile.php as edit page with user details, profile picture, family management
- [ ] Update update_profile.php for handling updates and Cloudinary image replacement
- [ ] Update add_family_member.php for adding family members and updating family_size
- [ ] Update delete_family_member.php for deleting and updating family_size

## Phase 4: Admin Panel & Event Management
- [x] Create admin/auth_check.php
- [x] Create admin/dashboard.php
- [x] Create admin/manage_users.php
- [ ] Create local events table schema (id, title, description, start_time, end_time, created_by_admin_id)
- [ ] Create admin/manage_events.php with FullCalendar.js integration
- [ ] Create admin/calendar_api.php with load_events.php, create_event.php, update_event.php, delete_event.php

## Phase 5: Google Calendar Integration (User & Admin)
- [ ] Create google_calendar_service.php with OAuth and API functions
- [ ] Create settings.php for users to connect Google Calendar
- [ ] Create oauth_callback.php for handling OAuth callback
- [ ] Update admin/calendar_api.php to sync events to users' Google Calendars after creation

## Phase 6: Security & Integration Review
- [ ] Review all features for security: prepared statements, password hashing, .env usage, htmlspecialchars, CSRF protection
- [ ] Confirm admin pages are protected
- [ ] Test all functionalities

## Additional Requirements
- [ ] Add satsang end_date to schema and logic
- [ ] Update user_events junction table for user-event relationships
- [ ] Filter events by user in management pages
