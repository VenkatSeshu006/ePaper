<?php
// public/includes/helpers.php

require_once '../config.php';

function get_setting($key) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT value FROM settings WHERE key_name = :key");
    $stmt->execute(['key' => $key]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['value'] : '';
}

function get_color($key) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT value FROM color_schema WHERE key_name = :key");
    $stmt->execute(['key' => $key]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['value'] : '';
}

function get_menu_items($location) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT mi.*, m.location
        FROM menu_items mi
        JOIN menus m ON mi.menu_id = m.id
        WHERE m.location = :location
        ORDER BY mi.order_number
    ");
    $stmt->execute(['location' => $location]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_menu_item_url($item) {
    switch ($item['type']) {
        case 'category':
            return "/category/{$item['object_id']}";
        case 'edition':
            return "/edition/{$item['object_id']}";
        case 'page':
            return "/page/{$item['object_id']}";
        case 'custom':
            return $item['url'];
        default:
            return '#';
    }
}
?>