<?php
// public/categories.php
require_once '../config.php';
require_once 'includes/header.php';

// Fetch categories from the database
$stmt = $pdo->query("SELECT * FROM categories");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare a statement to fetch the latest edition for each category
$stmt_latest_edition = $pdo->prepare("
    SELECT e.id
    FROM editions e
    WHERE e.category_id = :category_id
    ORDER BY e.edition_date DESC
    LIMIT 1
");

foreach ($categories as &$category) {
    // Fetch the latest edition for the current category
    $stmt_latest_edition->execute(['category_id' => $category['id']]);
    $latest_edition = $stmt_latest_edition->fetch(PDO::FETCH_ASSOC);
    $category['latest_edition_id'] = $latest_edition ? $latest_edition['id'] : null;
}
unset($category); // Break the reference with the last element
?>

<link rel="stylesheet" href="assets/css/categories.css?v=<?php echo time(); ?>">>

<div class="main-content">
    <div class="boxed-layout">
        <h1>Categories</h1>
        <div class="categories-grid">
            <?php foreach ($categories as $category): ?>
                <?php
                $latest_edition_id = $category['latest_edition_id'];
                ?>
                <div class="category-card">
                    <a href="edition.php?id=<?php echo htmlspecialchars($latest_edition_id); ?>">
                        <img src="<?php echo htmlspecialchars($category['image_path']); ?>" alt="<?php echo htmlspecialchars($category['name']); ?>">
                        <h2><?php echo htmlspecialchars($category['name']); ?></h2>
                        <p><?php echo htmlspecialchars($category['description']); ?></p>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>