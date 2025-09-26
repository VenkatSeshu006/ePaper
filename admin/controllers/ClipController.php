<?php
// admin/controllers/ClipController.php
class ClipController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Fetch all clips
    public function getAllClips() {
        $stmt = $this->pdo->prepare("
            SELECT ci.id, ci.edition_id, ci.image_id, ci.clip_path, ci.created_at
            FROM clipped_images ci
            ORDER BY ci.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Delete a single clip
    public function deleteClip($clip_id) {
        // Fetch clip path to delete file
        $stmt = $this->pdo->prepare("SELECT clip_path FROM clipped_images WHERE id = :id");
        $stmt->execute(['id' => $clip_id]);
        $clip = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($clip) {
            $file_path = $_SERVER['DOCUMENT_ROOT'] . $clip['clip_path'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
            $stmt = $this->pdo->prepare("DELETE FROM clipped_images WHERE id = :id");
            return $stmt->execute(['id' => $clip_id]);
        }
        return false;
    }

    // Delete multiple clips
    public function deleteMultipleClips($clip_ids) {
        if (empty($clip_ids)) return false;

        // Fetch paths for all clips to delete files
        $placeholders = str_repeat('?,', count($clip_ids) - 1) . '?';
        $stmt = $this->pdo->prepare("SELECT clip_path FROM clipped_images WHERE id IN ($placeholders)");
        $stmt->execute($clip_ids);
        $clips = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($clips as $clip) {
            $file_path = $_SERVER['DOCUMENT_ROOT'] . $clip['clip_path'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }

        // Delete from database
        $stmt = $this->pdo->prepare("DELETE FROM clipped_images WHERE id IN ($placeholders)");
        return $stmt->execute($clip_ids);
    }

    // Delete clips older than a specific time
    public function deleteClipsOlderThan($time_frame) {
        $date_condition = '';
        switch ($time_frame) {
            case '1day':
                $date_condition = 'DATE_SUB(NOW(), INTERVAL 1 DAY)';
                break;
            case '1week':
                $date_condition = 'DATE_SUB(NOW(), INTERVAL 1 WEEK)';
                break;
            case '1month':
                $date_condition = 'DATE_SUB(NOW(), INTERVAL 1 MONTH)';
                break;
            default:
                return false;
        }

        // Fetch paths for clips to delete files
        $stmt = $this->pdo->prepare("SELECT clip_path FROM clipped_images WHERE created_at < $date_condition");
        $stmt->execute();
        $clips = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($clips as $clip) {
            $file_path = $_SERVER['DOCUMENT_ROOT'] . $clip['clip_path'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }

        // Delete from database
        $stmt = $this->pdo->prepare("DELETE FROM clipped_images WHERE created_at < $date_condition");
        return $stmt->execute();
    }
}
?>