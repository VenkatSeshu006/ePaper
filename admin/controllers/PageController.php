<?php
require_once '../../../config.php'; // Database connection

class PageController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Fetch all pages
    public function getAllPages() {
        $stmt = $this->pdo->query("SELECT * FROM pages ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Add a new page
    public function addPage($title, $content, $slug) {
        $stmt = $this->pdo->prepare("INSERT INTO pages (title, content, slug) VALUES (?, ?, ?)");
        return $stmt->execute([$title, $content, $slug]);
    }

    // Get a single page by ID
    public function getPageById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM pages WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update an existing page
    public function updatePage($id, $title, $content, $slug) {
        $stmt = $this->pdo->prepare("UPDATE pages SET title = ?, content = ?, slug = ? WHERE id = ?");
        return $stmt->execute([$title, $content, $slug, $id]);
    }

    // Delete a page
    public function deletePage($id) {
        $stmt = $this->pdo->prepare("DELETE FROM pages WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>