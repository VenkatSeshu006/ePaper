<?php
$page_title = "Page Settings"; // Set the page title
require_once '../../includes/header.php'; // Include the shared header
require_once '../../../config.php'; // Database connection

// Fetch current color schema from the database
$colors = [];
$stmt = $pdo->query("SELECT key_name, value FROM color_schema");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $colors[$row['key_name']] = $row['value'];
}

// Fetch all categories for Homepage Display Settings
$categories = [];
$stmt = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $categories[] = $row;
}

// Fetch current homepage display settings
$homepage_settings = [];
$stmt = $pdo->query("SELECT key_name, value FROM settings WHERE key_name IN ('display_type', 'edition_category')");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $homepage_settings[$row['key_name']] = $row['value'];
}
?>
<div class="content-header">
    <div class="container-fluid">
        <h1>Page Settings</h1>
    </div>
</div>

<!-- Success Message -->
<?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
    <div class="alert alert-success" role="alert">
        Settings updated successfully!
    </div>
<?php endif; ?>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- Column 1: Color Schema -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Color Schema Settings</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="../../controllers/ColorSchemaController.php">
                            <div class="form-group">
                                <label for="primary_color">Primary Color:</label>
                                <input type="color" id="primary_color" name="primary_color" class="form-control" value="<?= htmlspecialchars($colors['primary_color'] ?? '#4CAF50') ?>">
                            </div>

                            <div class="form-group">
                                <label for="secondary_color">Secondary Color:</label>
                                <input type="color" id="secondary_color" name="secondary_color" class="form-control" value="<?= htmlspecialchars($colors['secondary_color'] ?? '#FFC107') ?>">
                            </div>

                            <div class="form-group">
                                <label for="background_color">Background Color:</label>
                                <input type="color" id="background_color" name="background_color" class="form-control" value="<?= htmlspecialchars($colors['background_color'] ?? '#FFFFFF') ?>">
                            </div>

                            <div class="form-group">
                                <label for="text_color">Text Color:</label>
                                <input type="color" id="text_color" name="text_color" class="form-control" value="<?= htmlspecialchars($colors['text_color'] ?? '#333333') ?>">
                            </div>

                            <div class="form-group">
                                <label for="link_color">Link Color:</label>
                                <input type="color" id="link_color" name="link_color" class="form-control" value="<?= htmlspecialchars($colors['link_color'] ?? '#1976D2') ?>">
                            </div>

                            <div class="form-group">
                                <label for="header_background">Header Background:</label>
                                <input type="color" id="header_background" name="header_background" class="form-control" value="<?= htmlspecialchars($colors['header_background'] ?? '#4CAF50') ?>">
                            </div>

                            <div class="form-group">
                                <label for="header_text_color">Header Menu Text Color:</label>
                                <input type="color" id="header_text_color" name="header_text_color" class="form-control" value="<?= htmlspecialchars($colors['header_text_color'] ?? '#FFFFFF') ?>">
                            </div>

                            <div class="form-group">
                                <label for="footer_background">Footer Background:</label>
                                <input type="color" id="footer_background" name="footer_background" class="form-control" value="<?= htmlspecialchars($colors['footer_background'] ?? '#333333') ?>">
                            </div>

                            <!-- Secondary Header Color Settings -->
                            <hr>
                            <h5>Secondary Header Settings (Edition Page)</h5>
                            
                            <div class="form-group">
                                <label for="secondary_header_background">Secondary Header Background:</label>
                                <input type="color" id="secondary_header_background" name="secondary_header_background" class="form-control" value="<?= htmlspecialchars($colors['secondary_header_background'] ?? '#2c3e50') ?>">
                            </div>

                            <div class="form-group">
                                <label for="secondary_header_text_color">Secondary Header Text Color:</label>
                                <input type="color" id="secondary_header_text_color" name="secondary_header_text_color" class="form-control" value="<?= htmlspecialchars($colors['secondary_header_text_color'] ?? '#ffffff') ?>">
                            </div>

                            <div class="form-group">
                                <label for="secondary_header_button_color">Secondary Header Button Color:</label>
                                <input type="color" id="secondary_header_button_color" name="secondary_header_button_color" class="form-control" value="<?= htmlspecialchars($colors['secondary_header_button_color'] ?? '#3498db') ?>">
                            </div>

                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Column 2: Homepage Display Settings -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Homepage Display Settings</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="../../controllers/HomepageSettingsController.php" id="homepage-settings-form">
                            <div class="form-group">
                                <label for="display_type">What to Display on Homepage?</label>
                                <select id="display_type" name="display_type" class="form-control" onchange="toggleCategoryDropdown()">
                                    <option value="all_categories" <?= ($homepage_settings['display_type'] ?? 'all_categories') == 'all_categories' ? 'selected' : '' ?>>All Categories</option>
                                    <option value="edition_page" <?= ($homepage_settings['display_type'] ?? 'all_categories') == 'edition_page' ? 'selected' : '' ?>>Edition Page</option>
                                </select>
                            </div>

                            <!-- Category Dropdown (Hidden by Default) -->
                            <div class="form-group" id="category-dropdown" style="display: none;">
                                <label for="edition_category">Select Category for Editions:</label>
                                <select id="edition_category" name="edition_category" class="form-control">
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category['id'] ?>" <?= ($homepage_settings['edition_category'] ?? '') == $category['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($category['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    // Function to toggle the category dropdown visibility
    function toggleCategoryDropdown() {
        const displayType = document.getElementById('display_type').value;
        const categoryDropdown = document.getElementById('category-dropdown');
        if (displayType === 'edition_page') {
            categoryDropdown.style.display = 'block';
        } else {
            categoryDropdown.style.display = 'none';
        }
    }

    // Initialize the dropdown visibility on page load
    document.addEventListener('DOMContentLoaded', function () {
        toggleCategoryDropdown();
    });
</script>

<?php require_once '../../includes/footer.php'; // Include the shared footer ?>