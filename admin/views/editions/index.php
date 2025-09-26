<?php
$page_title = "Editions"; // Set the page title
require_once '../../includes/header.php'; // Include the shared header

require_once '../../../config.php'; // Database connection
require_once '../../controllers/EditionController.php';
require_once '../../controllers/CategoryController.php';

$editionController = new EditionController($pdo);
$categoryController = new CategoryController($pdo);

$editions = $editionController->getAllEditions();
$categories = $categoryController->getAllCategories();
?>
<div class="content-header">
    <div class="container-fluid">
        <h1>Editions</h1>
    </div>
</div>
<section class="content">
    <div class="container-fluid">
        <a href="add.php" class="btn btn-primary mb-3">Add New Edition</a>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Date</th>
                    <th>Featured</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($editions as $edition): ?>
                    <tr>
                        <td><?= htmlspecialchars($edition['id']) ?></td>
                        <td><?= htmlspecialchars($edition['title']) ?></td>
                        <td><?= htmlspecialchars($edition['category_name']) ?></td>
                        <td><?= htmlspecialchars($edition['edition_date']) ?></td>
                        <td><?= $edition['is_featured'] ? 'Yes' : 'No' ?></td>
                        <td>
                            <a href="edit.php?id=<?= $edition['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="delete.php?id=<?= $edition['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                            <a href="pages_upload/upload.php?id=<?= $edition['id'] ?>" class="btn btn-sm btn-primary">Upload Page</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php require_once '../../includes/footer.php'; // Include the shared footer ?>