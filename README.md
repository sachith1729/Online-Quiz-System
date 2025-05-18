# Quiz System

A web-based quiz application that allows students to take multiple-choice quizzes and view their rankings on the leaderboard. The system includes an admin dashboard for managing subjects and questions.

## Features

- Multiple choice questions organized by subjects
- Student quiz taking interface
- Leaderboard showing student rankings
- Admin dashboard for managing subjects and questions
- Secure authentication system

## Tech Stack

- Frontend: HTML, CSS, JavaScript
- Backend: PHP
- Database: MySQL
- Authentication: PHP Sessions

## Project Structure

```
quiz/
├── assets/           # CSS, JavaScript, and images
├── includes/         # PHP includes and database connection
├── admin/           # Admin dashboard files
├── css/             # Stylesheets
├── js/              # JavaScript files
└── index.php        # Main entry point
```

## Database Structure

### Tables

1. users
   - id (PRIMARY KEY)
   - username
   - password
   - email
   - role (admin/student)
   - created_at

2. subjects
   - id (PRIMARY KEY)
   - name
   - description
   - created_at

3. questions
   - id (PRIMARY KEY)
   - subject_id (FOREIGN KEY)
   - question_text
   - option_a
   - option_b
   - option_c
   - option_d
   - correct_answer
   - points

4. quiz_attempts
   - id (PRIMARY KEY)
   - user_id (FOREIGN KEY)
   - subject_id (FOREIGN KEY)
   - score
   - completed_at

## Setup Instructions

### Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)

### Database Setup

1. Create a new MySQL database named 'quiz_system'
2. Import the database schema from `database/schema.sql`

### Configuration

1. Update database connection details in `includes/config.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   define('DB_NAME', 'quiz_system');
   ```

2. Set up your web server to point to the project directory

## Pages

### Student Pages
- index.php - Home page with subject list
- quiz.php - Quiz taking interface
- leaderboard.php - Student rankings
- profile.php - User profile

### Admin Pages
- admin/dashboard.php - Admin dashboard
- admin/subjects.php - Subject management
- admin/questions.php - Question management
- admin/users.php - User management 