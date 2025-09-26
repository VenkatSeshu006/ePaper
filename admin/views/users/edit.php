<?php
$page_title = "Edit User";
require_once '../../includes/header.php';

// Check if the logged-in user is an admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../../index.php");
    exit();
}

require_once '../../../config.php'; // Database connection

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$userId = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: index.php");
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = $_POST['password'];

    if (empty($newPassword)) {
        $error = 'Password is required.';
    } else {
        // Hash the new password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        try {
            // Update the user's password in the database
            $stmt = $pdo->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$hashedPassword, $userId]);

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
        <h1>Edit User: <?= htmlspecialchars($user['username']) ?></h1>
    </div>
</div>
<section class="content">
    <div class="container-fluid">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="password">New Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Password</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</section>
<?php require_once '../../includes/footer.php'; ?>