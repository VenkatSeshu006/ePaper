<?php
class CategoryController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Add a new category to the database.
     *
     * @param string $name The name of the category.
     * @param string $alias The alias (URL-friendly version) of the category.
     * @param string|null $description The description of the category.
     * @param string|null $imagePath The path to the category image.
     * @return bool True if the category was added successfully, false otherwise.
     */
    public function addCategory($name, $alias, $description, $imagePath) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO categories (name, alias, description, image_path)
                VALUES (:name, :alias, :description, :image_path)
            ");
            return $stmt->execute([
                'name' => $name,
                'alias' => $alias,
                'description' => $description,
                'image_path' => $imagePath
            ]);
        } catch (Exception $e) {
            error_log("Error adding category: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetch all categories from the database.
     *
     * @return array An array of categories, or an empty array if none are found.
     */
    public function getAllCategories() {
        try {
            $stmt = $this->pdo->query("SELECT id, name, alias, description, image_path FROM categories ORDER BY name ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error fetching categories: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Delete a category by its ID.
     *
     * @param int $id The ID of the category to delete.
     * @return bool True if the category was deleted successfully, false otherwise.
     */
    public function deleteCategory($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM categories WHERE id = :id");
            return $stmt->execute(['id' => $id]);
        } catch (Exception $e) {
            error_log("Error deleting category: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetch a single category by its ID.
     *
     * @param int $id The ID of the category to fetch.
     * @return array|null The category data, or null if not found.
     */
    public function getCategoryById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT id, name, alias, description, image_path FROM categories WHERE id = :id");
            $stmt->execute(['id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error fetching category by ID: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Update an existing category.
     *
     * @param int $id The ID of the category to update.
     * @param string $name The updated name of the category.
     * @param string $alias The updated alias of the category.
     * @param string|null $description The updated description of the category.
     * @param string|null $imagePath The updated path to the category image.
     * @return bool True if the category was updated successfully, false otherwise.
     */
    public function updateCategory($id, $name, $alias, $description, $imagePath) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE categories
                SET name = :name,
                    alias = :alias,
                    description = :description,
                    image_path = :image_path
                WHERE id = :id
            ");
            return $stmt->execute([
                'id' => $id,
                'name' => $name,
                'alias' => $alias,
                'description' => $description,
                'image_path' => $imagePath
            ]);
        } catch (Exception $e) {
            error_log("Error updating category: " . $e->getMessage());
            return false;
        }
    }
}
?>