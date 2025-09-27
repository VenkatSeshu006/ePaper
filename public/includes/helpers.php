<?php
// public/includes/helpers.php

// Only include config if $pdo is not already available
if (!isset($pdo)) {
    require_once __DIR__ . '/../../config.php';
}

function get_setting($key) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT value FROM settings WHERE key_name = :key");
    $stmt->execute(['key' => $key]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$result) {
        return '';
    }
    
    $value = $result['value'];
    
    // Handle logo and image paths - ensure they have the correct ePaper prefix
    if (in_array($key, ['site_logo', 'site_favicon', 'area_mapping_logo']) && !empty($value)) {
        // If path starts with /uploads/ but doesn't have /ePaper/, add it
        if (strpos($value, '/uploads/') === 0 && strpos($value, '/ePaper/') === false) {
            $value = '/ePaper' . $value;
        }
        // If path doesn't start with /, add /ePaper/ prefix
        else if (strpos($value, '/') !== 0) {
            $value = '/ePaper/uploads/settings/' . $value;
        }
    }
    
    return $value;
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

function ensure_web_path($path) {
    // Ensure path starts with forward slash for web access
    if (empty($path)) {
        return '';
    }
    if (substr($path, 0, 1) !== '/') {
        return '/' . $path;
    }
    return $path;
}

function get_base_url() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    return $protocol . $_SERVER['HTTP_HOST'];
}
?>