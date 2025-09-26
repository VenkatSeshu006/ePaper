<?php
require_once '../../../config.php'; // Database connection

$menuId = isset($_GET['menu_id']) ? (int)$_GET['menu_id'] : null;

if (!$menuId) {
    echo json_encode([]);
    exit;
}

try {
    // Fetch menu items for the selected menu
    $stmt = $pdo->prepare("
        SELECT id, title, type, object_id, url, order_number
        FROM menu_items
        WHERE menu_id = :menu_id
        ORDER BY order_number ASC
    ");
    $stmt->execute(['menu_id' => $menuId]);
    $menuItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($menuItems);
} catch (Exception $e) {
    echo json_encode([]);
}