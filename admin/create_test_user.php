<?php
// Simple script to create a test admin user
require_once '../config.php';

$username = 'admin';
$password = 'admin123'; // Test password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$role = 'admin';

try {
    // Check if admin user already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    
    if ($stmt->rowCount() > 0) {
        // Update existing admin password
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = ?");
        $stmt->execute([$hashed_password, $username]);
        echo "Admin password updated successfully!<br>";
    } else {
        // Create new admin user
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->execute([$username, $hashed_password, $role]);
        echo "Admin user created successfully!<br>";
    }
    
    echo "Login credentials:<br>";
    echo "Username: admin<br>";
    echo "Password: admin123<br>";
    echo "<br><a href='views/login.php'>Go to Login</a>";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>