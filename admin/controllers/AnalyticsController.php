<?php
// admin/controllers/AnalyticsController.php

// Use relative path to config.php
require_once __DIR__ . '/../../config.php';

class AnalyticsController {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function getDashboardStats($days = 30) {
        $start_date = date('Y-m-d', strtotime("-$days days"));
        
        try {
            // Get overall statistics
            $stmt = $this->pdo->prepare("
                SELECT 
                    SUM(total_views) as total_views,
                    SUM(unique_visitors) as unique_visitors,
                    COUNT(DISTINCT date) as active_days
                FROM analytics_daily 
                WHERE date >= ?
            ");
            $stmt->execute([$start_date]);
            $overall_stats = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Get daily views for chart
            $stmt = $this->pdo->prepare("
                SELECT 
                    date,
                    SUM(total_views) as daily_views,
                    SUM(unique_visitors) as daily_unique
                FROM analytics_daily 
                WHERE date >= ?
                GROUP BY date
                ORDER BY date ASC
            ");
            $stmt->execute([$start_date]);
            $daily_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get top editions
            $stmt = $this->pdo->prepare("
                SELECT 
                    ad.edition_id,
                    e.title,
                    c.name as category_name,
                    SUM(ad.total_views) as views,
                    SUM(ad.unique_visitors) as unique_visitors
                FROM analytics_daily ad
                LEFT JOIN editions e ON ad.edition_id = e.id
                LEFT JOIN categories c ON e.category_id = c.id
                WHERE ad.date >= ? AND ad.event_type = 'edition_view' AND ad.edition_id IS NOT NULL
                GROUP BY ad.edition_id, e.title, c.name
                ORDER BY views DESC
                LIMIT 10
            ");
            $stmt->execute([$start_date]);
            $top_editions = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get top categories
            $stmt = $this->pdo->prepare("
                SELECT 
                    c.id,
                    c.name,
                    SUM(ad.total_views) as views,
                    SUM(ad.unique_visitors) as unique_visitors
                FROM analytics_daily ad
                LEFT JOIN editions e ON ad.edition_id = e.id
                LEFT JOIN categories c ON e.category_id = c.id
                WHERE ad.date >= ? AND ad.event_type = 'edition_view' AND c.id IS NOT NULL
                GROUP BY c.id, c.name
                ORDER BY views DESC
                LIMIT 10
            ");
            $stmt->execute([$start_date]);
            $top_categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get recent activity
            $stmt = $this->pdo->prepare("
                SELECT 
                    a.event_type,
                    a.edition_id,
                    e.title as edition_title,
                    a.ip_address,
                    a.created_at
                FROM analytics a
                LEFT JOIN editions e ON a.edition_id = e.id
                WHERE a.created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
                ORDER BY a.created_at DESC
                LIMIT 20
            ");
            $stmt->execute();
            $recent_activity = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'overall_stats' => $overall_stats,
                'daily_stats' => $daily_stats,
                'top_editions' => $top_editions,
                'top_categories' => $top_categories,
                'recent_activity' => $recent_activity
            ];
            
        } catch (PDOException $e) {
            error_log("Analytics controller error: " . $e->getMessage());
            return null;
        }
    }
    
    public function getEditionAnalytics($edition_id, $days = 30) {
        $start_date = date('Y-m-d', strtotime("-$days days"));
        
        try {
            // Get edition details
            $stmt = $this->pdo->prepare("
                SELECT e.*, c.name as category_name
                FROM editions e
                LEFT JOIN categories c ON e.category_id = c.id
                WHERE e.id = ?
            ");
            $stmt->execute([$edition_id]);
            $edition = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Get daily stats for this edition
            $stmt = $this->pdo->prepare("
                SELECT 
                    date,
                    total_views,
                    unique_visitors
                FROM analytics_daily 
                WHERE edition_id = ? AND date >= ? AND event_type = 'edition_view'
                ORDER BY date ASC
            ");
            $stmt->execute([$edition_id, $start_date]);
            $daily_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get total stats
            $stmt = $this->pdo->prepare("
                SELECT 
                    SUM(total_views) as total_views,
                    SUM(unique_visitors) as unique_visitors
                FROM analytics_daily 
                WHERE edition_id = ? AND date >= ? AND event_type = 'edition_view'
            ");
            $stmt->execute([$edition_id, $start_date]);
            $total_stats = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return [
                'edition' => $edition,
                'daily_stats' => $daily_stats,
                'total_stats' => $total_stats
            ];
            
        } catch (PDOException $e) {
            error_log("Edition analytics error: " . $e->getMessage());
            return null;
        }
    }
    
    public function exportAnalytics($start_date, $end_date, $format = 'csv') {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    ad.date,
                    ad.event_type,
                    e.title as edition_title,
                    c.name as category_name,
                    ad.total_views,
                    ad.unique_visitors
                FROM analytics_daily ad
                LEFT JOIN editions e ON ad.edition_id = e.id
                LEFT JOIN categories c ON e.category_id = c.id
                WHERE ad.date BETWEEN ? AND ?
                ORDER BY ad.date DESC, ad.total_views DESC
            ");
            $stmt->execute([$start_date, $end_date]);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if ($format === 'csv') {
                return $this->generateCSV($data);
            }
            
            return $data;
            
        } catch (PDOException $e) {
            error_log("Export analytics error: " . $e->getMessage());
            return null;
        }
    }
    
    private function generateCSV($data) {
        $output = fopen('php://temp', 'r+');
        
        // Add CSV headers
        fputcsv($output, ['Date', 'Event Type', 'Edition Title', 'Category', 'Total Views', 'Unique Visitors']);
        
        // Add data rows
        foreach ($data as $row) {
            fputcsv($output, [
                $row['date'],
                $row['event_type'],
                $row['edition_title'] ?? 'N/A',
                $row['category_name'] ?? 'N/A',
                $row['total_views'],
                $row['unique_visitors']
            ]);
        }
        
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);
        
        return $csv;
    }
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    $analytics = new AnalyticsController($pdo);
    
    header('Content-Type: application/json');
    
    switch ($_GET['action']) {
        case 'dashboard':
            $days = isset($_GET['days']) ? (int)$_GET['days'] : 30;
            $data = $analytics->getDashboardStats($days);
            echo json_encode($data);
            break;
            
        case 'edition':
            $edition_id = isset($_GET['edition_id']) ? (int)$_GET['edition_id'] : 0;
            $days = isset($_GET['days']) ? (int)$_GET['days'] : 30;
            $data = $analytics->getEditionAnalytics($edition_id, $days);
            echo json_encode($data);
            break;
            
        case 'export':
            $start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
            $end_date = $_GET['end_date'] ?? date('Y-m-d');
            $format = $_GET['format'] ?? 'csv';
            
            if ($format === 'csv') {
                $csv = $analytics->exportAnalytics($start_date, $end_date, 'csv');
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="analytics_' . $start_date . '_to_' . $end_date . '.csv"');
                echo $csv;
            } else {
                $data = $analytics->exportAnalytics($start_date, $end_date);
                echo json_encode($data);
            }
            break;
            
        default:
            echo json_encode(['error' => 'Invalid action']);
    }
    exit;
}
?>