<?php
require_once 'includes/config.php';

// If user is already logged in, redirect to appropriate page
if (isLoggedIn()) {
    redirect(isAdmin() ? 'admin/dashboard.php' : 'student_dashboard.php');
}

// Handle registration
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } else {
        try {
            // Check if username or email already exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            if ($stmt->rowCount() > 0) {
                $error = "Username or email already exists";
            } else {
                // Create new user
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'student')");
                $stmt->execute([$username, $email, $hashed_password]);
                
                // Get the newly created user
                $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
                $stmt->execute([$username]);
                $user = $stmt->fetch();
                
                // Set session and redirect to student dashboard
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                redirect('student_dashboard.php');
            }
        } catch(PDOException $e) {
            $error = "Registration failed. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration - Quiz System</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Student Registration</h1>
            <nav>
                <a href="index.php"><i class="fas fa-home"></i> Back to Home</a>
            </nav>
        </header>

        <div class="auth-container">
            <div class="register-form">
                <h2><i class="fas fa-user-plus"></i> Create Student Account</h2>
                <?php if (isset($error)): ?>
                    <div class="error"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
                <?php endif; ?>
                <form method="POST" class="animated-form">
                    <div class="form-group">
                        <label for="username"><i class="fas fa-user"></i> Username:</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope"></i> Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password"><i class="fas fa-lock"></i> Password:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm-password"><i class="fas fa-lock"></i> Confirm Password:</label>
                        <input type="password" id="confirm-password" name="confirm_password" required>
                    </div>
                    <button type="submit">
                        <i class="fas fa-user-plus"></i> Create Account
                    </button>
                </form>
                <div class="form-footer">
                    Already have an account? <a href="student_login.php">Login here</a>
                </div>
            </div>
        </div>
    </div>

    <script src="js/main.js"></script>

    <style>
        /* Additional styles for signup page */
        .animated-form {
            animation: slideUp 0.5s ease;
        }

        .form-group {
            position: relative;
            margin-bottom: 25px;
        }

        .form-group label i {
            margin-right: 8px;
            color: var(--secondary-color);
        }

        .form-group input {
            padding-left: 35px;
        }

        .form-group i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            transition: var(--transition);
        }

        .form-group.focused i {
            color: var(--secondary-color);
        }

        .form-group.has-value label {
            transform: translateY(-20px);
            font-size: 0.8rem;
            color: var(--secondary-color);
        }

        .ripple {
            position: absolute;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            transform: scale(0);
            animation: ripple 0.6s linear;
            pointer-events: none;
        }

        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid #fff;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 1s linear infinite;
            margin-right: 8px;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .form-footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .form-footer a {
            color: var(--secondary-color);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }

        .form-footer a:hover {
            color: var(--success-color);
            text-decoration: underline;
        }
    </style>
</body>
</html> 