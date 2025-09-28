<?php
$page_title = "Categories"; // Set the page title
require_once '../../includes/header.php'; // Include the shared header
require_once '../../../config.php'; // Database connection
require_once '../../controllers/CategoryController.php';

$categoryController = new CategoryController($pdo);
$categories = $categoryController->getAllCategories();
?>
<div class="content-header">
    <div class="container-fluid">
        <h1>Categories</h1>
    </div>
</div>
<section class="content">
    <div class="container-fluid">
        <!-- Display Success Message -->
        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
            <div class="alert alert-success" role="alert">
                Category deleted successfully!
            </div>
        <?php endif; ?>

        <!-- Display Error Messages -->
        <?php if (isset($_GET['error'])): ?>
            <?php if ($_GET['error'] == 1): ?>
                <div class="alert alert-danger" role="alert">
                    Invalid request. Please provide a valid category ID.
                </div>
            <?php elseif ($_GET['error'] == 2): ?>
                <div class="alert alert-danger" role="alert">
                    Failed to delete the category.
                </div>
            <?php elseif ($_GET['error'] == 3): ?>
                <div class="alert alert-danger" role="alert">
                    An unexpected error occurred. Please try again later.
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <a href="add.php" class="btn btn-primary mb-3">Add New Category</a>
        <div class="table-responsive">
            <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Alias</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($categories)): ?>
                    <?php foreach ($categories as $category): ?>
                        <tr>
                            <td><?= htmlspecialchars($category['id']) ?></td>
                            <td><?= htmlspecialchars($category['name']) ?></td>
                            <td><?= htmlspecialchars($category['alias']) ?></td>
                            <td>
                                <a href="edit.php?id=<?= $category['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="delete.php?id=<?= $category['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center">No categories found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
            </table>
        </div>
    </div>
</section>
<?php require_once '../../includes/footer.php'; // Include the shared footer ?>