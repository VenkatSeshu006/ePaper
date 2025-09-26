<?php
require_once __DIR__ . '/../../config.php'; // Database connection

class EditionController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Fetch all editions
    public function getAllEditions() {
        $stmt = $this->pdo->query("SELECT editions.*, categories.name AS category_name 
                                   FROM editions 
                                   LEFT JOIN categories ON editions.category_id = categories.id 
                                   ORDER BY editions.created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Add a new edition
    public function addEdition($title, $alias, $description, $category_id, $edition_date, $is_featured) {
        $stmt = $this->pdo->prepare("INSERT INTO editions (title, alias, description, category_id, edition_date, is_featured) 
                                     VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$title, $alias, $description, $category_id, $edition_date, $is_featured]);
    }

    // Get a single edition by ID
    public function getEditionById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM editions WHERE id = ?");
        $stmt->execute([$id]);
        $edition = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$edition) {
            error_log("Edition with ID $id not found.");
        }

        return $edition;
    }

    // Update an existing edition
    public function updateEdition($id, $title, $alias, $description, $category_id, $edition_date, $is_featured) {
        $stmt = $this->pdo->prepare("UPDATE editions 
                                     SET title = ?, alias = ?, description = ?, category_id = ?, edition_date = ?, is_featured = ? 
                                     WHERE id = ?");
        return $stmt->execute([$title, $alias, $description, $category_id, $edition_date, $is_featured, $id]);
    }

    // Delete an edition
    public function deleteEdition($id) {
        // First, delete associated images
        $this->deleteEditionImages($id);

        // Then, delete the edition
        $stmt = $this->pdo->prepare("DELETE FROM editions WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Delete all images associated with an edition
    private function deleteEditionImages($edition_id) {
        $stmt = $this->pdo->prepare("SELECT image_path FROM edition_images WHERE edition_id = ?");
        $stmt->execute([$edition_id]);
        $images = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Delete files from the server
        foreach ($images as $image_path) {
            $filePath = $_SERVER['DOCUMENT_ROOT'] . $image_path;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // Delete records from the database
        $stmt = $this->pdo->prepare("DELETE FROM edition_images WHERE edition_id = ?");
        return $stmt->execute([$edition_id]);
    }

    // Add an image to an edition
    public function addEditionImage($edition_id, $image_path, $order) {
        $stmt = $this->pdo->prepare("INSERT INTO edition_images (edition_id, image_path, `order`) 
                                     VALUES (?, ?, ?)");
        return $stmt->execute([$edition_id, $image_path, $order]);
    }

    // Get all images for a specific edition
    public function getEditionImages($edition_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM edition_images WHERE edition_id = ? ORDER BY `order` ASC");
        $stmt->execute([$edition_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update the order of images for an edition
    public function updateImageOrder($image_id, $new_order) {
        $stmt = $this->pdo->prepare("UPDATE edition_images SET `order` = ? WHERE id = ?");
        return $stmt->execute([$new_order, $image_id]);
    }

    // Delete a specific image from an edition
    public function deleteEditionImage($image_id) {
        $stmt = $this->pdo->prepare("SELECT image_path FROM edition_images WHERE id = ?");
        $stmt->execute([$image_id]);
        $image = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($image) {
            $filePath = $_SERVER['DOCUMENT_ROOT'] . $image['image_path'];
            if (file_exists($filePath)) {
                unlink($filePath); // Delete the file from the server
            }
        }

        $stmt = $this->pdo->prepare("DELETE FROM edition_images WHERE id = ?");
        return $stmt->execute([$image_id]);
    }

    // Get a specific image by ID
    public function getImageById($image_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM edition_images WHERE id = ?");
        $stmt->execute([$image_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

}
?>