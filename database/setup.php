<?php
require_once '../includes/config.php';

try {
    // Read SQL file
    $sql = file_get_contents('quiz_system.sql');
    
    // Split SQL commands
    $commands = array_filter(array_map('trim', explode(';', $sql)));
    
    // Execute each command separately
    foreach ($commands as $command) {
        if (!empty($command)) {
            $conn->exec($command);
        }
    }
    
    echo "Database setup completed successfully!<br>";
    echo "You can now <a href='../index.php'>go back to the home page</a>.";
} catch(PDOException $e) {
    echo "Error setting up database: " . $e->getMessage();
}
?> 