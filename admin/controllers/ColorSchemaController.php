<?php
require_once '../../config.php'; // Include the database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Retrieve the submitted color values from the form
        $colors = [
            'primary_color' => $_POST['primary_color'],
            'secondary_color' => $_POST['secondary_color'],
            'background_color' => $_POST['background_color'],
            'text_color' => $_POST['text_color'],
            'link_color' => $_POST['link_color'],
            'header_background' => $_POST['header_background'],
            'header_text_color' => $_POST['header_text_color'],
            'footer_background' => $_POST['footer_background'],
            'secondary_header_background' => $_POST['secondary_header_background'],
            'secondary_header_text_color' => $_POST['secondary_header_text_color'],
            'secondary_header_button_color' => $_POST['secondary_header_button_color']
        ];

        // Update each color in the database
        foreach ($colors as $key => $value) {
            $stmt = $pdo->prepare("UPDATE color_schema SET value = :value WHERE key_name = :key");
            $stmt->execute(['value' => $value, 'key' => $key]);
        }

        // Redirect back to the admin dashboard with a success message
        header('Location: ../views/page-settings/index.php?success=1');
        exit;
    } catch (Exception $e) {
        // Handle any errors that occur during the update process
        echo "An error occurred: " . $e->getMessage();
    }
}
?>