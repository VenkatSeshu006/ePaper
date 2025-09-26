<?php
$page_title = "Edit Category"; // Set the page title
require_once '../../includes/header.php'; // Include the shared header
require_once '../../../config.php'; // Database connection
require_once '../../controllers/CategoryController.php';
$categoryController = new CategoryController($pdo);
$error = null; // Initialize error variable
$success = false; // Initialize success flag
// Check if the category ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php?error=1"); // Redirect with an error query parameter
    exit();
}
$category_id = $_GET['id'];
// Fetch the category details
$category = $categoryController->getCategoryById($category_id);
if (!$category) {
    header("Location: index.php?error=2"); // Redirect with an error query parameter
    exit();
}
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $alias = trim($_POST['alias']);
    $description = trim($_POST['description']);
    $imagePath = $category['image_path']; // Default to current image path

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../../uploads/categories/';
        $fileName = basename($_FILES['image']['name']);
        $filePath = $uploadDir . $fileName;

        // Check if the upload directory exists, if not, create it
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Move the uploaded file to the uploads directory
        if (move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
            $imagePath = $filePath;
        } else {
            $error = "Failed to upload image.";
        }
    }

    // Validate required fields
    if (empty($name) || empty($alias)) {
        $error = "Name and Alias are required.";
    } else {
        // Attempt to update the category
        if ($categoryController->updateCategory($category_id, $name, $alias, $description, $imagePath)) {
            $success = true; // Mark as successful
            $category = $categoryController->getCategoryById($category_id); // Refresh category data
        } else {
            $error = "Failed to update category.";
        }
    }
}
?>
<div class="content-header">
    <div class="container-fluid">
        <h1>Edit Category</h1>
    </div>
</div>
<section class="content">
    <div class="container-fluid">
        <!-- Display Success Message -->
        <?php if ($success): ?>
            <div class="alert alert-success" role="alert">
                Category updated successfully!
            </div>
        <?php endif; ?>
        <!-- Display Error Message -->
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($category['name']) ?>" required oninput="generateAlias()">
            </div>
            <div class="form-group">
                <label for="alias">Alias</label>
                <input type="text" name="alias" id="alias" class="form-control" value="<?= htmlspecialchars($category['alias']) ?>" readonly>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" class="form-control"><?= htmlspecialchars($category['description']) ?></textarea>
            </div>
            <div class="form-group">
                <label for="image">Category Image (Recommended: 800x1123 pixels)</label>
                <input type="file" name="image" id="image" class="form-control-file">
                <?php if ($category['image_path']): ?>
                    <img src="<?= htmlspecialchars($category['image_path']) ?>" alt="Current Category Image" style="max-width: 200px; margin-top: 10px;">
                <?php else: ?>
                    <img src="<?= get_setting('site_logo') ?>" alt="Default Site Logo" style="max-width: 200px; margin-top: 10px;">
                <?php endif; ?>
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</section>
<script>
    function generateAlias() {
        const nameInput = document.getElementById('name');
        const aliasInput = document.getElementById('alias');
        // Convert the name to a URL-friendly alias
        let alias = nameInput.value
            .toLowerCase() // Convert to lowercase
            .replace(/[^a-z0-9\s-]/g, '') // Remove special characters
            .replace(/\s+/g, '-') // Replace spaces with hyphens
            .trim(); // Trim leading/trailing whitespace
        aliasInput.value = alias;
    }
    // Automatically generate the alias when the page loads
    window.onload = function () {
        generateAlias();
    };
</script>
<?php require_once '../../includes/footer.php'; // Include the shared footer ?>