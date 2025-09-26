<?php
$page_title = "Edit Page"; // Set the page title
require_once '../../includes/header.php'; // Include the shared header

require_once '../../../config.php'; // Database connection
require_once '../../controllers/PageController.php';

$pageController = new PageController($pdo);

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$page = $pageController->getPageById($_GET['id']);
if (!$page) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $content = $_POST['content']; // Content from Quill
    $slug = trim($_POST['slug']);

    if (empty($title) || empty($slug)) {
        $error = "Title and Slug are required.";
    } else {
        if ($pageController->updatePage($_GET['id'], $title, $content, $slug)) {
            header("Location: index.php");
            exit();
        } else {
            $error = "Failed to update page.";
        }
    }
}
?>
<div class="content-header">
    <div class="container-fluid">
        <h1>Edit Page</h1>
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
                <input type="text" name="title" id="title" class="form-control" value="<?= htmlspecialchars($page['title']) ?>" required oninput="generateSlug()">
            </div>
            <div class="form-group">
                <label for="slug">Slug</label>
                <input type="text" name="slug" id="slug" class="form-control" value="<?= htmlspecialchars($page['slug']) ?>" readonly>
            </div>
            <div class="form-group">
                <label for="content">Content</label>
                <div id="editor" style="height: 300px;"><?= htmlspecialchars_decode($page['content']) ?></div>
                <textarea name="content" id="content" style="display: none;"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</section>

<!-- Quill CSS -->
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">

<!-- Quill JS -->
<script src="https://cdn.quilljs.com/1.3.7/quill.js"></script>
<script>
    // Initialize Quill
    var quill = new Quill('#editor', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                ['link', 'image'],
                ['clean']
            ]
        }
    });

    // Generate Slug from Title
    function generateSlug() {
        const titleInput = document.getElementById('title');
        const slugInput = document.getElementById('slug');

        // Convert the title to a URL-friendly slug
        let slug = titleInput.value
            .toLowerCase() // Convert to lowercase
            .replace(/[^a-z0-9\s-]/g, '') // Remove special characters
            .replace(/\s+/g, '-') // Replace spaces with hyphens
            .trim(); // Trim leading/trailing whitespace

        slugInput.value = slug;
    }

    // Pre-fill the slug field on page load
    window.onload = function () {
        generateSlug();
    };

    // Save Quill content to hidden textarea before form submission
    document.querySelector('form').addEventListener('submit', function () {
        const contentInput = document.getElementById('content');
        const quillContent = document.querySelector('#editor .ql-editor').innerHTML;
        contentInput.value = quillContent;
    });
</script>
<?php require_once '../../includes/footer.php'; // Include the shared footer ?>