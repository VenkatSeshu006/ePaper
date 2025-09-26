<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../../config.php'; // Database connection

try {
    $requestData = json_decode(file_get_contents('php://input'), true);

    if (empty($requestData['items'])) {
        echo json_encode(['status' => 'error', 'message' => 'No data received.']);
        exit;
    }

    $menuId = $requestData['menu_id'];
    $data = $requestData['items'];

    // Check if the menu exists
    $stmt = $pdo->prepare("SELECT id FROM menus WHERE id = :menu_id");
    $stmt->execute(['menu_id' => $menuId]);
    if (!$stmt->fetch()) {
        throw new Exception('Invalid menu ID.');
    }

    // Clear existing menu items
    $stmt = $pdo->prepare("DELETE FROM menu_items WHERE menu_id = :menu_id");
    $stmt->execute(['menu_id' => $menuId]);

    // Insert new menu items
    foreach ($data as $item) {
        $stmt = $pdo->prepare("
            INSERT INTO menu_items (menu_id, title, type, object_id, url, order_number)
            VALUES (:menu_id, :title, :type, :object_id, :url, :order_number)
        ");
        $stmt->execute([
            'menu_id' => $menuId,
            'title' => $item['title'],
            'type' => $item['type'],
            'object_id' => $item['object_id'] ?? null,
            'url' => $item['url'] ?? null,
            'order_number' => $item['order_number']
        ]);
    }

    // Return success response
    echo json_encode(['status' => 'success', 'message' => 'Menu saved successfully!']);
} catch (Exception $e) {
    // Log the error for debugging purposes
    error_log('Error saving menu: ' . $e->getMessage());

    // Return error response
    echo json_encode(['status' => 'error', 'message' => 'An error occurred while saving the menu.']);
}