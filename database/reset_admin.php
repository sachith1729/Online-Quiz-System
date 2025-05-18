<?php
require_once '../includes/config.php';

try {
    // First, check if admin exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = 'admin'");
    $stmt->execute();
    $admin = $stmt->fetch();

    if ($admin) {
        // Update existing admin password
        $new_password = 'admin123';
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = 'admin'");
        $stmt->execute([$hashed_password]);
        
        echo "Admin password reset successfully!<br>";
    } else {
        // Create new admin user
        $new_password = 'admin123';
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute(['admin', 'admin@example.com', $hashed_password, 'admin']);
        
        echo "Admin user created successfully!<br>";
    }
    
    echo "Username: admin<br>";
    echo "Password: admin123";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 