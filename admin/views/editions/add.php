<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);



$page_title = "Add Edition"; // Set the page title
require_once '../../includes/header.php'; // Include the shared header
require_once '../../../config.php'; // Database connection
require_once '../../controllers/EditionController.php';
require_once '../../controllers/CategoryController.php';

$editionController = new EditionController($pdo);
$categoryController = new CategoryController($pdo);
$categories = $categoryController->getAllCategories();

$error = null; // Initialize error variable

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Retrieve form data
        $title = trim($_POST['title']);
        $alias = trim($_POST['alias']);
        $description = trim($_POST['description']);
        $category_id = $_POST['category_id'];
        $edition_date = $_POST['edition_date'];
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;

        // Validate required fields
        if (empty($title) || empty($alias) || empty($category_id) || empty($edition_date)) {
            $error = "All fields are required.";
        } else {
            // Add the edition
            if ($editionController->addEdition($title, $alias, $description, $category_id, $edition_date, $is_featured)) {
                // Redirect to index.php on success
                header("Location: index.php");
                exit(); // Ensure no further code is executed
            } else {
                $error = "Failed to add edition.";
            }
        }
    } catch (Exception $e) {
        // Handle unexpected errors
        $error = "An error occurred: " . $e->getMessage();
    }
}
?>
<div class="content-header">
    <div class="container-fluid">
        <h1>Add Edition</h1>
    </div>
</div>
<section class="content">
    <div class="container-fluid">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" name="title" id="title" class="form-control" required oninput="generateAlias()">
            </div>
            <div class="form-group">
                <label for="alias">Alias</label>
                <input type="text" name="alias" id="alias" class="form-control" readonly>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" class="form-control"></textarea>
            </div>
            <div class="form-group">
                <label for="category_id">Category</label>
                <select name="category_id" id="category_id" class="form-control" required>
                    <option value="">Select a category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="edition_date">Edition Date</label>
                <input type="date" name="edition_date" id="edition_date" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="is_featured">Featured</label>
                <input type="checkbox" name="is_featured" id="is_featured">
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</section>
<script>
    function generateAlias() {
        const titleInput = document.getElementById('title');
        const aliasInput = document.getElementById('alias');
        // Convert the title to a URL-friendly alias
        let alias = titleInput.value
            .toLowerCase() // Convert to lowercase
            .replace(/[^a-z0-9\s-]/g, '') // Remove special characters
            .replace(/\s+/g, '-') // Replace spaces with hyphens
            .trim(); // Trim leading/trailing whitespace
        aliasInput.value = alias;
    }
</script>
<?php require_once '../../includes/footer.php'; // Include the shared footer ?>