# Attendance Management System

A PHP and MySQL-based attendance management system for schools. The project supports teacher and student logins, student management, attendance marking, attendance history, profile access, and basic activity tracking.

## Overview

This application is built as a traditional PHP web app and is designed to run on a PHP server with MySQL or MariaDB. It includes a school landing page, a login and registration flow, and role-based dashboard pages.

The current codebase is structured for local development with XAMPP, but it can also be adapted for shared hosting or a cloud platform by changing the database configuration.

## Key Features

- Teacher and student authentication
- Role-based dashboard access
- Student registration and profile details
- Add, view, edit, and delete student records
- Mark daily attendance with present, absent, and late statuses
- View attendance history for teachers and students
- Activity logging and notifications support
- Settings table for school-level configuration values
- Responsive UI built with Bootstrap and Font Awesome

## Technology Stack

- PHP
- MySQL / MariaDB
- MySQLi prepared statements
- Bootstrap 4 and Bootstrap 5 in different pages
- Font Awesome for icons
- Vanilla PHP sessions for authentication

## Project Structure

- [index.php](index.php) - Login page for the attendance system
- [main.php](main.php) - School landing page and entrance screen
- [dashboard.php](dashboard.php) - Role-based dashboard
- [register.php](register.php) - Registration form for students and teachers
- [manage_students.php](manage_students.php) - Student listing and management page
- [add_student.php](add_student.php) - Add a new student
- [edit_student.php](edit_student.php) - Edit an existing student
- [delete_student.php](delete_student.php) - Delete a student
- [view_student.php](view_student.php) - View detailed student information and attendance history
- [mark_attendance.php](mark_attendance.php) - Mark attendance for students
- [attendance_history.php](attendance_history.php) - View attendance history and export options
- [profile.php](profile.php) - Profile settings page
- [logout.php](logout.php) - Session logout handler
- [config/database.php](config/database.php) - Database connection plus schema initialization
- [attendance_system.sql](attendance_system.sql) - SQL dump of the database schema and sample data

## User Roles

### Teacher

- Log in to the system
- View dashboard summaries
- Add, edit, view, and delete students
- Mark attendance
- View attendance history for all students
- Export or print attendance records from the history page

### Student

- Log in to the system
- View personal dashboard information
- See attendance history
- Access profile settings

### Admin

Some pages allow admin access in their authorization checks, but the current login form is focused on student and teacher roles.

## Main Workflows

### Login

Users log in through [index.php](index.php). The form checks username, password, and role before creating a session.

### Registration

[register.php](register.php) creates user accounts. For student accounts, the app also creates a linked student record.

### Student Management

[manage_students.php](manage_students.php) groups students by class and provides links to view, edit, and delete records.

### Attendance Marking

[mark_attendance.php](mark_attendance.php) lets a teacher choose a date and mark each student as present, absent, or late. The app updates existing attendance rows if the same student and date already exist.

### Attendance History

[attendance_history.php](attendance_history.php) shows attendance records. Teachers can see all records, while students see only their own.

### Student Details

[view_student.php](view_student.php) shows the student profile and a table of past attendance entries.

## Database Schema

The schema is defined in [config/database.php](config/database.php) and mirrored in [attendance_system.sql](attendance_system.sql).

### Tables

- `users` - Stores login credentials and role information
- `students` - Stores student profile data and links to `users`
- `attendance` - Stores daily attendance entries
- `settings` - Stores application configuration values such as school name and time thresholds
- `notifications` - Stores system notifications for users
- `activity_log` - Stores user activity audit entries

### Important Relationships

- `students.user_id` references `users.user_id`
- `attendance.student_id` references `students.student_id`
- `attendance.marked_by` references `users.user_id`
- `notifications.user_id` references `users.user_id`
- `activity_log.user_id` references `users.user_id`

## Requirements

- PHP 7.4 or newer
- MySQL 5.7+ or MariaDB 10.x
- A local server stack such as XAMPP, WAMP, Laragon, or a compatible hosting environment

## Local Setup

1. Clone or copy the project into your web server directory.
2. Start Apache and MySQL from XAMPP or your preferred stack.
3. Create a database named `attendance_system` if it does not already exist.
4. Import [attendance_system.sql](attendance_system.sql) into MySQL, or let the app create tables automatically through [config/database.php](config/database.php).
5. Update the database credentials in [config/database.php](config/database.php) if your local setup is different.
6. Open the project in your browser using your local server URL.

## Database Configuration

The current configuration uses local development defaults:

- host: `localhost`
- username: `root`
- password: empty
- database: `attendance_system`

For production, replace these values with environment-specific credentials. If you deploy to a hosted platform, you should not rely on local `root` access or an auto-created database.

## Running the App

After setup, open the application in a browser and use the login page at [index.php](index.php).

Typical flow:

1. Register a student or teacher account.
2. Log in with the correct role.
3. Teachers can manage students and mark attendance.
4. Students can view their own attendance history.

## Deployment Notes

This app can be deployed to a PHP-capable host. It is not directly production-ready for Render in its current form because the database layer is hardcoded for local MySQL access and table creation at runtime.

If you want to deploy it on Render or a similar platform, you will need to:

- Use an external MySQL-compatible database
- Move credentials to environment variables
- Remove or refactor runtime database creation logic
- Confirm that the hosting platform supports PHP execution the way this app expects

## File Behavior Notes

- [main.php](main.php) is a presentation-focused landing page with school branding and student photos.
- [index.php](index.php) is the main authentication entry point.
- [config/database.php](config/database.php) creates the schema and inserts default settings when the connection succeeds.
- [mark_attendance.php](mark_attendance.php) and [dashboard.php](dashboard.php) both contain attendance workflows.

## Security Considerations

- Passwords are hashed with `password_hash()`.
- SQL queries use prepared statements in the main data flows.
- Session checks restrict access to protected pages.

For production hardening, consider:

- Moving database credentials out of source code
- Adding CSRF protection to forms
- Adding stricter server-side validation on all mutations
- Separating schema setup from runtime connection logic

## Sample Data

The SQL dump includes sample users, students, attendance entries, settings, and activity log rows for testing and demonstration.

## Future Improvements

- Add a dedicated admin dashboard
- Add pagination and search to student and attendance lists
- Add CSV or Excel export for all major records
- Replace inline styles with reusable CSS files
- Separate database schema installation from application bootstrapping

## License

No explicit license is included in the repository.
