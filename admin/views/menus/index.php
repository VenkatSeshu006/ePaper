<?php
$page_title = "Menus"; // Set the page title
require_once '../../includes/header.php'; // Include the shared header
require_once '../../../config.php'; // Database connection

// Fetch predefined menus (header and footer)
$stmt = $pdo->query("SELECT * FROM menus ORDER BY location ASC");
$menus = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="content-header">
    <div class="container-fluid">
        <h1>Menus</h1>
    </div>
</div>
<section class="content">
    <div class="container-fluid">
        <?php if (empty($menus)): ?>
            <div class="alert alert-warning">
                No menus available. Please ensure the database is properly configured.
            </div>
        <?php else: ?>
            <!-- List of Predefined Menus -->
            <div class="row">
                <?php foreach ($menus as $menu): ?>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title"><?= htmlspecialchars($menu['name']) ?></h3>
                            </div>
                            <div class="card-body">
                                <p><strong>Location:</strong> <?= ucfirst(htmlspecialchars($menu['location'])) ?></p>
                                <a href="edit_menu.php?menu_id=<?= $menu['id'] ?>" class="btn btn-primary">Edit Menu</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php require_once '../../includes/footer.php'; // Include the shared footer ?>