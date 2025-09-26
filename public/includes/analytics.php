<?php
// Analytics tracking functions

function track_analytics($event_type, $edition_id = null, $category_id = null, $user_id = null) {
    global $pdo;
    
    // Get user information
    $ip_address = get_client_ip();
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $page_url = $_SERVER['REQUEST_URI'] ?? '';
    $referrer = $_SERVER['HTTP_REFERER'] ?? '';
    
    // Generate or get session ID
    $session_id = '';
    if (session_status() === PHP_SESSION_ACTIVE) {
        // Session is already active, use it
        if (!isset($_SESSION['analytics_session_id'])) {
            $_SESSION['analytics_session_id'] = uniqid('sess_', true);
        }
        $session_id = $_SESSION['analytics_session_id'];
    } else {
        // Session not active, generate a temporary session ID based on IP and user agent
        $session_id = 'temp_' . md5($ip_address . $user_agent . date('Y-m-d'));
    }
    
    try {
        // Insert into analytics table
        $stmt = $pdo->prepare("
            INSERT INTO analytics (event_type, edition_id, category_id, user_id, ip_address, user_agent, page_url, referrer, session_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$event_type, $edition_id, $category_id, $user_id, $ip_address, $user_agent, $page_url, $referrer, $session_id]);
        
        // Update daily summary
        update_daily_analytics($event_type, $edition_id, $category_id, $session_id);
        
    } catch (PDOException $e) {
        // Log error but don't break the page
        error_log("Analytics tracking error: " . $e->getMessage());
    }
}

function update_daily_analytics($event_type, $edition_id, $category_id, $session_id) {
    global $pdo;
    
    $today = date('Y-m-d');
    
    try {
        // Check if record exists for today
        $stmt = $pdo->prepare("
            SELECT id, total_views, unique_visitors 
            FROM analytics_daily 
            WHERE date = ? AND event_type = ? AND edition_id = ? AND category_id = ?
        ");
        $stmt->execute([$today, $event_type, $edition_id, $category_id]);
        $daily_record = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($daily_record) {
            // Update existing record
            $new_total = $daily_record['total_views'] + 1;
            
            // Check if this is a unique visitor for today
            $stmt = $pdo->prepare("
                SELECT COUNT(*) as count 
                FROM analytics 
                WHERE DATE(created_at) = ? AND event_type = ? AND edition_id = ? AND session_id = ?
            ");
            $stmt->execute([$today, $event_type, $edition_id, $session_id]);
            $session_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            $unique_increment = ($session_count == 1) ? 1 : 0;
            $new_unique = $daily_record['unique_visitors'] + $unique_increment;
            
            $stmt = $pdo->prepare("
                UPDATE analytics_daily 
                SET total_views = ?, unique_visitors = ?, updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$new_total, $new_unique, $daily_record['id']]);
            
        } else {
            // Create new record
            $stmt = $pdo->prepare("
                INSERT INTO analytics_daily (date, event_type, edition_id, category_id, total_views, unique_visitors)
                VALUES (?, ?, ?, ?, 1, 1)
            ");
            $stmt->execute([$today, $event_type, $edition_id, $category_id]);
        }
        
    } catch (PDOException $e) {
        error_log("Daily analytics update error: " . $e->getMessage());
    }
}

function get_client_ip() {
    $ip_keys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
    
    foreach ($ip_keys as $key) {
        if (!empty($_SERVER[$key])) {
            $ip = $_SERVER[$key];
            // Handle comma-separated IPs (from proxies)
            if (strpos($ip, ',') !== false) {
                $ip = trim(explode(',', $ip)[0]);
            }
            // Validate IP
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return $ip;
            }
        }
    }
    
    return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
}

function get_analytics_summary($days = 30) {
    global $pdo;
    
    $start_date = date('Y-m-d', strtotime("-$days days"));
    
    try {
        // Get total views and unique visitors
        $stmt = $pdo->prepare("
            SELECT 
                SUM(total_views) as total_views,
                SUM(unique_visitors) as unique_visitors
            FROM analytics_daily 
            WHERE date >= ?
        ");
        $stmt->execute([$start_date]);
        $summary = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Get top editions
        $stmt = $pdo->prepare("
            SELECT 
                ad.edition_id,
                e.title,
                SUM(ad.total_views) as views,
                SUM(ad.unique_visitors) as unique_visitors
            FROM analytics_daily ad
            LEFT JOIN editions e ON ad.edition_id = e.id
            WHERE ad.date >= ? AND ad.event_type = 'edition_view' AND ad.edition_id IS NOT NULL
            GROUP BY ad.edition_id, e.title
            ORDER BY views DESC
            LIMIT 10
        ");
        $stmt->execute([$start_date]);
        $top_editions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'summary' => $summary,
            'top_editions' => $top_editions
        ];
        
    } catch (PDOException $e) {
        error_log("Analytics summary error: " . $e->getMessage());
        return null;
    }
}
?>