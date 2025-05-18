<?php
require_once 'includes/config.php';

// If user is already logged in, redirect to appropriate page
if (isLoggedIn()) {
    redirect(isAdmin() ? 'admin/dashboard.php' : 'student_dashboard.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Quiz System</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Welcome to Quiz System</h1>
            <p>Choose your portal to continue</p>
        </header>

        <div class="welcome-container">
            <div class="portal-card student-portal">
                <h2><i class="fas fa-user-graduate"></i> Student Portal</h2>
                <p>Take quizzes and track your progress</p>
                <div class="portal-buttons">
                    <a href="student_login.php" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt"></i> Sign In
                    </a>
                    <a href="signup.php" class="btn btn-secondary">
                        <i class="fas fa-user-plus"></i> Sign Up
                    </a>
                </div>
            </div>

            <div class="portal-card admin-portal">
                <h2><i class="fas fa-user-shield"></i> Admin Portal</h2>
                <p>Manage quizzes and users</p>
                <div class="portal-buttons">
                    <a href="admin_login.php" class="btn btn-danger">
                        <i class="fas fa-sign-in-alt"></i> Sign In
                    </a>
                    <a href="admin_signup.php" class="btn btn-danger-light">
                        <i class="fas fa-user-plus"></i> Sign Up
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 