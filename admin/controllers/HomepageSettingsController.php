<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../config.php'; // Include the database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Retrieve the submitted values from the form
        $display_type = $_POST['display_type'];
        $edition_category = isset($_POST['edition_category']) ? $_POST['edition_category'] : null;

        // Update display_type setting
        $stmt = $pdo->prepare("INSERT INTO settings (key_name, value) VALUES ('display_type', :value) ON DUPLICATE KEY UPDATE value = :value");
        $stmt->execute(['value' => $display_type]);

        // Update edition_category setting only if display_type is 'edition_page'
        if ($display_type === 'edition_page' && !empty($edition_category)) {
            $stmt = $pdo->prepare("INSERT INTO settings (key_name, value) VALUES ('edition_category', :value) ON DUPLICATE KEY UPDATE value = :value");
            $stmt->execute(['value' => $edition_category]);
        }

        // Redirect back to the Page Settings page with a success message
        header('Location: ../views/page-settings/index.php?success=1');
        exit;
    } catch (Exception $e) {
        // Handle any errors that occur during the update process
        echo "An error occurred: " . $e->getMessage();
    }
}
?>