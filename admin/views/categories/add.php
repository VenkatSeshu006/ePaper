<?php
$page_title = "Add Category"; // Set the page title
require_once '../../includes/header.php'; // Include the shared header
require_once '../../../config.php'; // Database connection
require_once '../../../public/includes/helpers.php'; // Include helper functions
require_once '../../controllers/CategoryController.php';
$categoryController = new CategoryController($pdo);
$error = null; // Initialize error variable
// Open the debug log file
$logFile = fopen('debug.log', 'a'); // Create or append to debug.log
fwrite($logFile, "------------------- New Request -------------------\n");
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $alias = trim($_POST['alias']);
    $description = trim($_POST['description']);
    $imagePath = null;

    fwrite($logFile, "Form submitted with data:\n");
    fwrite($logFile, "Name: $name\n");
    fwrite($logFile, "Alias: $alias\n");
    fwrite($logFile, "Description: $description\n");

    // Validate required fields
    if (empty($name) || empty($alias)) {
        $error = "Name and Alias are required.";
        fwrite($logFile, "Error: Name and Alias are required.\n");
    } else {
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
                fwrite($logFile, "Image uploaded successfully: $filePath\n");
            } else {
                $error = "Failed to upload image.";
                fwrite($logFile, "Error: Failed to upload image.\n");
            }
        } else {
            // If no image is uploaded, set the default image path to the site logo
            $imagePath = get_setting('site_logo');
            fwrite($logFile, "No image uploaded. Using default image path: $imagePath\n");
        }

        // Attempt to add the category
        fwrite($logFile, "Attempting to add category...\n");
        if ($categoryController->addCategory($name, $alias, $description, $imagePath)) {
            fwrite($logFile, "Category added successfully. Redirecting to index.php...\n");
            // Redirect to index.php with success query parameter
            header("Location: index.php?success=1");
            fclose($logFile); // Close the log file before redirection
            exit(); // Ensure no further code is executed after redirection
        } else {
            $error = "Failed to add category.";
            fwrite($logFile, "Error: Failed to add category.\n");
        }
    }
}
fclose($logFile); // Close the log file
?>
<div class="content-header">
    <div class="container-fluid">
        <h1>Add Category</h1>
    </div>
</div>
<section class="content">
    <div class="container-fluid">
        <!-- Display Success Message -->
        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
            <div class="alert alert-success" role="alert">
                Category added successfully!
            </div>
        <?php endif; ?>
        <!-- Display Error Message -->
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" class="form-control" required oninput="generateAlias()">
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
                <label for="image">Category Image (Recommended: 800x1123 pixels)</label>
                <input type="file" name="image" id="image" class="form-control-file">
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
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
</script>
<?php require_once '../../includes/footer.php'; // Include the shared footer ?>