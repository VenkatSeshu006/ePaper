<?php
// Start output buffering to prevent headers already sent errors
ob_start();

// Start session only if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page_title = "Site Settings";
require_once '../../includes/header.php';

// Check if the logged-in user is an admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../../index.php");
    exit();
}

require_once '../../../config.php'; // Database connection

// Fetch all settings from the database
$stmt = $pdo->query("SELECT * FROM settings");
$settings = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $settings[$row['key_name']] = $row['value'];
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Update text fields in the database
        foreach ($_POST as $key => $value) {
            if (!in_array($key, ['site_favicon', 'site_logo', 'area_mapping_logo'])) { // Exclude file inputs
                $stmt = $pdo->prepare("UPDATE settings SET value = ?, updated_at = NOW() WHERE key_name = ?");
                $stmt->execute([$value, $key]);
            }
        }

        // Handle file uploads
        $uploadDir = "../../../uploads/settings/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (!empty($_FILES['site_favicon']['tmp_name'])) {
            $faviconPath = $uploadDir . basename($_FILES['site_favicon']['name']);
            move_uploaded_file($_FILES['site_favicon']['tmp_name'], $faviconPath);
            $stmt = $pdo->prepare("UPDATE settings SET value = ? WHERE key_name = 'site_favicon'");
            $stmt->execute(["/uploads/settings/" . basename($_FILES['site_favicon']['name'])]);
        }

        if (!empty($_FILES['site_logo']['tmp_name'])) {
            $logoPath = $uploadDir . basename($_FILES['site_logo']['name']);
            move_uploaded_file($_FILES['site_logo']['tmp_name'], $logoPath);
            $stmt = $pdo->prepare("UPDATE settings SET value = ? WHERE key_name = 'site_logo'");
            $stmt->execute(["/uploads/settings/" . basename($_FILES['site_logo']['name'])]);
        }

        if (!empty($_FILES['area_mapping_logo']['tmp_name'])) {
            $areaMappingLogoPath = $uploadDir . basename($_FILES['area_mapping_logo']['name']);
            move_uploaded_file($_FILES['area_mapping_logo']['tmp_name'], $areaMappingLogoPath);
            $stmt = $pdo->prepare("UPDATE settings SET value = ? WHERE key_name = 'area_mapping_logo'");
            $stmt->execute(["/uploads/settings/" . basename($_FILES['area_mapping_logo']['name'])]);
        }

        // Redirect to reload the page
        header("Location: index.php?success=1");
        exit(); // Stop further script execution
    } catch (PDOException $e) {
        $error = 'Error: ' . $e->getMessage();
    }
}

// Check for success message in the URL
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $success = 'Settings updated successfully.';
}
?>
<div class="content-header">
    <div class="container-fluid">
        <h1>Site Settings</h1>
    </div>
</div>
<section class="content">
    <div class="container-fluid">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST" action="" enctype="multipart/form-data">
            <div class="row">
                <!-- Left Column (50%) -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">General Settings</h5><br>

                            <!-- Site Name -->
                            <div class="form-group">
                                <label for="site_name">Site Name</label>
                                <input type="text" name="site_name" id="site_name" class="form-control" value="<?= htmlspecialchars($settings['site_name'] ?? '') ?>" required>
                            </div>

                            <!-- Site Logo -->
                            <div class="form-group">
                                <label for="site_logo">Site Logo</label>
                                <input type="file" name="site_logo" id="site_logo" class="form-control">
                                <?php if (!empty($settings['site_logo'])): ?>
                                    <img src="<?= htmlspecialchars($settings['site_logo']) ?>" alt="Current Logo" style="max-width: 150px; margin-top: 10px;">
                                <?php endif; ?>
                            </div>

                            <!-- Favicon -->
                            <div class="form-group">
                                <label for="site_favicon">Favicon</label>
                                <input type="file" name="site_favicon" id="site_favicon" class="form-control">
                                <?php if (!empty($settings['site_favicon'])): ?>
                                    <img src="<?= htmlspecialchars($settings['site_favicon']) ?>" alt="Current Favicon" style="max-width: 50px; margin-top: 10px;">
                                <?php endif; ?>
                            </div>

                            <!-- Area Mapping Logo -->
                            <div class="form-group">
                                <label for="area_mapping_logo">Area Mapping Logo</label>
                                <input type="file" name="area_mapping_logo" id="area_mapping_logo" class="form-control">
                                <?php if (!empty($settings['area_mapping_logo'])): ?>
                                    <img src="<?= htmlspecialchars($settings['area_mapping_logo']) ?>" alt="Current Area Mapping Logo" style="max-width: 150px; margin-top: 10px;">
                                <?php endif; ?>
                            </div>

                            <!-- Area Mapping Logo Placement Position -->
                            <div class="form-group">
                                <label>Area Mapping Logo Placement Position</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="area_mapping_logo_position" id="position_top" value="top" <?= (!isset($settings['area_mapping_logo_position']) || $settings['area_mapping_logo_position'] === 'top') ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="position_top">
                                        On Top
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="area_mapping_logo_position" id="position_bottom" value="bottom" <?= (isset($settings['area_mapping_logo_position']) && $settings['area_mapping_logo_position'] === 'bottom') ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="position_bottom">
                                        On Bottom
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column (50%) -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Contact & SEO Settings</h5><br>

                            <!-- Site Email -->
                            <div class="form-group">
                                <label for="site_email">Site Email</label>
                                <input type="email" name="site_email" id="site_email" class="form-control" value="<?= htmlspecialchars($settings['site_email'] ?? '') ?>" required>
                            </div>

                            <!-- Meta Title -->
                            <div class="form-group">
                                <label for="meta_title">Meta Title</label>
                                <input type="text" name="meta_title" id="meta_title" class="form-control" value="<?= htmlspecialchars($settings['meta_title'] ?? '') ?>" required>
                            </div>

                            <!-- Meta Description -->
                            <div class="form-group">
                                <label for="meta_description">Meta Description</label>
                                <textarea name="meta_description" id="meta_description" class="form-control" rows="3"><?= htmlspecialchars($settings['meta_description'] ?? '') ?></textarea>
                            </div>

                            <!-- Google Analytics GA4 -->
                            <div class="form-group">
                                <label for="google_analytics_id">Google Analytics GA4 Measurement ID</label>
                                <input type="text" name="google_analytics_id" id="google_analytics_id" class="form-control" value="<?= htmlspecialchars($settings['google_analytics_id'] ?? '') ?>" placeholder="G-XXXXXXXXXX">
                                <small class="form-text text-muted">Enter your GA4 Measurement ID (e.g., G-XXXXXXXXXX)</small>
                            </div>

                            <!-- Google AdSense -->
                            <div class="form-group">
                                <label for="google_adsense_id">Google AdSense Publisher ID</label>
                                <input type="text" name="google_adsense_id" id="google_adsense_id" class="form-control" value="<?= htmlspecialchars($settings['google_adsense_id'] ?? '') ?>" placeholder="ca-pub-xxxxxxxxxxxxxxxx">
                                <small class="form-text text-muted">Enter your AdSense Publisher ID (e.g., ca-pub-xxxxxxxxxxxxxxxx)</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>
</section>
<?php require_once '../../includes/footer.php'; ?>

<?php
// End output buffering
ob_end_flush();