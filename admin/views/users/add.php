<?php
$page_title = "Add User";
require_once '../../includes/header.php';

// Check if the logged-in user is an admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../../index.php");
    exit();
}

require_once '../../../config.php'; // Database connection

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Validate inputs
    if (empty($username) || empty($password)) {
        $error = 'Username and password are required.';
    } elseif (!in_array($role, ['admin', 'editor'])) {
        $error = 'Invalid role selected.';
    } else {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            // Insert the new user into the database
            $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $stmt->execute([$username, $hashedPassword, $role]);

            header("Location: index.php");
            exit();
        } catch (PDOException $e) {
            $error = 'Error: ' . $e->getMessage();
        }
    }
}
?>
<div class="content-header">
    <div class="container-fluid">
        <h1>Add User</h1>
    </div>
</div>
<section class="content">
    <div class="container-fluid">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="role">Role</label>
                <select name="role" id="role" class="form-control" required>
                    <option value="admin">Admin</option>
                    <option value="editor">Editor</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Add User</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</section>
<?php require_once '../../includes/footer.php'; ?>