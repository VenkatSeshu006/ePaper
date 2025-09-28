<?php
$page_title = "Pages"; // Set the page title
require_once '../../includes/header.php'; // Include the shared header

require_once '../../../config.php'; // Database connection
require_once '../../controllers/PageController.php';

$pageController = new PageController($pdo);

$pages = $pageController->getAllPages();
?>
<div class="content-header">
    <div class="container-fluid">
        <h1>Pages</h1>
    </div>
</div>
<section class="content">
    <div class="container-fluid">
        <a href="add.php" class="btn btn-primary mb-3">Add New Page</a>
        <div class="table-responsive">
            <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Slug</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pages as $page): ?>
                    <tr>
                        <td><?= htmlspecialchars($page['id']) ?></td>
                        <td><?= htmlspecialchars($page['title']) ?></td>
                        <td><?= htmlspecialchars($page['slug']) ?></td>
                        <td>
                            <a href="edit.php?id=<?= $page['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="delete.php?id=<?= $page['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            </table>
        </div>
    </div>
</section>
<?php require_once '../../includes/footer.php'; // Include the shared footer ?>