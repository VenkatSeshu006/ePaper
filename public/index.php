<?php
// Set the page title (optional, for header.php)
$page_title = "Epaper Homepage";

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config.php';

// Function to get the latest featured edition URL (same as in header.php)
function get_latest_featured_edition_url() {
    global $pdo;
    try {
        // First try to get the latest featured edition
        $stmt = $pdo->prepare("
            SELECT id FROM editions 
            WHERE is_featured = 1 
            ORDER BY edition_date DESC, created_at DESC 
            LIMIT 1
        ");
        $stmt->execute();
        $featured = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($featured) {
            return "edition.php?id=" . $featured['id'];
        }
        
        // If no featured edition, get the latest edition overall
        $stmt = $pdo->prepare("
            SELECT id FROM editions 
            ORDER BY edition_date DESC, created_at DESC 
            LIMIT 1
        ");
        $stmt->execute();
        $latest = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($latest) {
            return "edition.php?id=" . $latest['id'];
        }
        
        // Fallback to categories page if no editions exist
        return "categories.php";
    } catch (Exception $e) {
        // Fallback to categories page on error
        return "categories.php";
    }
}

// Redirect to the latest featured edition
$redirect_url = get_latest_featured_edition_url();
header('Location: ' . $redirect_url);
exit;
?>