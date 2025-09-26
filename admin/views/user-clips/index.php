<?php
// admin/views/user-clips/index.php
$page_title = "User Clips";
require_once '../../includes/header.php';
require_once '../../../config.php';
require_once '../../controllers/ClipController.php';

$clipController = new ClipController($pdo);
$clips = $clipController->getAllClips();

// Base URL for view links
$base_url = "https://" . $_SERVER['HTTP_HOST'];
?>
<div class="content-header">
    <div class="container-fluid">
        <h1>User Clips</h1>
    </div>
</div>
<section class="content">
    <div class="container-fluid">
        <!-- Success/Error Messages -->
        <?php if (isset($_GET['success'])): ?>
            <?php if ($_GET['success'] == 1): ?>
                <div class="alert alert-success" role="alert">Clip(s) deleted successfully!</div>
            <?php endif; ?>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <?php if ($_GET['error'] == 1): ?>
                <div class="alert alert-danger" role="alert">Invalid request. Please select clips to delete.</div>
            <?php elseif ($_GET['error'] == 2): ?>
                <div class="alert alert-danger" role="alert">Failed to delete clip(s).</div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Filter Buttons -->
        <div class="mb-3">
            <form action="delete.php" method="post" style="display:inline;">
                <input type="hidden" name="action" value="delete_older_than">
                <button type="submit" name="time_frame" value="1day" class="btn btn-warning mr-2">Delete Older Than 1 Day</button>
                <button type="submit" name="time_frame" value="1week" class="btn btn-warning mr-2">Delete Older Than 1 Week</button>
                <button type="submit" name="time_frame" value="1month" class="btn btn-warning">Delete Older Than 1 Month</button>
            </form>
        </div>

        <!-- Clips Table -->
        <form id="clipsForm" action="delete.php" method="post">
            <input type="hidden" name="action" value="delete_multiple">
            <div class="mb-3">
                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete the selected clips?')">Delete Selected</button>
            </div>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="selectAll"></th>
                        <th>ID</th>
                        <th>Edition ID</th>
                        <th>Image ID</th>
                        <th>Clip Preview</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($clips)): ?>
                        <?php foreach ($clips as $clip): ?>
                            <tr>
                                <td><input type="checkbox" name="clip_ids[]" value="<?= $clip['id'] ?>"></td>
                                <td><?= htmlspecialchars($clip['id']) ?></td>
                                <td><?= htmlspecialchars($clip['edition_id']) ?></td>
                                <td><?= htmlspecialchars($clip['image_id']) ?></td>
                                <td><img src="<?= htmlspecialchars($clip['clip_path']) ?>" alt="Clip" width="50"></td>
                                <td><?= htmlspecialchars($clip['created_at']) ?></td>
                                <td>
                                    <a href="<?= $base_url ?>/public/clips.php?id=<?= $clip['id'] ?>" target="_blank" class="btn btn-sm btn-info mr-2">View</a>
                                    <a href="delete.php?id=<?= $clip['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">No clips found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </form>
    </div>
</section>

<script>
document.getElementById('selectAll').addEventListener('click', function(e) {
    document.querySelectorAll('input[name="clip_ids[]"]').forEach(checkbox => {
        checkbox.checked = e.target.checked;
    });
});
</script>

<?php require_once '../../includes/footer.php'; ?>