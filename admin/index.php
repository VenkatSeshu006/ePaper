<?php

error_reporting(E_ALL);  // Report all PHP errors
ini_set('display_errors', 1);  // Display errors on the screen

$page_title = "Admin Dashboard"; // Set the page title
require_once 'includes/header.php'; // Include the shared header (corrected path)
require_once '../config.php'; // Include the shared header (corrected path)

// Total storage limit (5GB). Update this value to change the limit.
$storageLimit = 5 * 1024 * 1024 * 1024; // 5GB in bytes

// Function to calculate used storage for a specific directory (or whole uploads if no subdir)
function getUsedStorage($directory) {
    $usedSpace = 0;
    if (is_dir($directory)) {
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file) {
            if ($file->isFile()) {
                $usedSpace += $file->getSize();
            }
        }
    }
    return $usedSpace;
}

// Get all folders in uploads/ and their sizes
$uploadDir = "../uploads/";
$folderSizes = [];
$usedStorage = 0;

if (is_dir($uploadDir)) {
    $dirs = array_filter(glob($uploadDir . '*'), 'is_dir');
    foreach ($dirs as $dir) {
        $folderName = basename($dir);
        $size = getUsedStorage($dir);
        $folderSizes[$folderName] = $size;
        $usedStorage += $size;
    }
}

$freeStorage = max(0, $storageLimit - $usedStorage);

// Convert bytes to GB for display
function formatBytes($bytes) {
    return round($bytes / (1024 * 1024 * 1024), 2) . ' GB';
}
?>
<div class="content-header">
    <div class="container-fluid">
        <h1>Admin Dashboard</h1>
    </div>
</div>
<section class="content">
    <div class="container-fluid">
        <!-- Storage Usage Chart -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Storage Usage</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="storageChart" width="300" height="300"></canvas>
                    </div>
                    <div class="card-footer">
                        <div class="legend">
                            <?php foreach ($folderSizes as $folder => $size): ?>
                                <span class="legend-item" style="color: #333;">
                                    <?= htmlspecialchars($folder) ?>: <?= formatBytes($size) ?>
                                </span>
                            <?php endforeach; ?>
                            <span class="legend-item" style="color: #333;">
                                Free: <?= formatBytes($freeStorage) ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Analytics Widget -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Analytics Overview</h3>
                        <div class="card-tools">
                            <a href="views/analytics/index.php" class="btn btn-primary btn-sm">View Full Analytics</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php
                        // Get analytics data for dashboard widget
                        try {
                            // Get total views today
                            $stmt = $pdo->prepare("SELECT COUNT(*) as total_views FROM analytics WHERE DATE(created_at) = CURDATE()");
                            $stmt->execute();
                            $todayViews = $stmt->fetch()['total_views'] ?? 0;

                            // Get total views this week
                            $stmt = $pdo->prepare("SELECT COUNT(*) as total_views FROM analytics WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
                            $stmt->execute();
                            $weekViews = $stmt->fetch()['total_views'] ?? 0;

                            // Get most viewed edition today
                            $stmt = $pdo->prepare("SELECT edition_id, COUNT(*) as views FROM analytics WHERE DATE(created_at) = CURDATE() GROUP BY edition_id ORDER BY views DESC LIMIT 1");
                            $stmt->execute();
                            $topEdition = $stmt->fetch();

                            // Get unique visitors today (using session_id instead of analytics_session_id)
                            $stmt = $pdo->prepare("SELECT COUNT(DISTINCT session_id) as unique_visitors FROM analytics WHERE DATE(created_at) = CURDATE()");
                            $stmt->execute();
                            $uniqueVisitors = $stmt->fetch()['unique_visitors'] ?? 0;

                            // Get total views overall
                            $stmt = $pdo->prepare("SELECT COUNT(*) as total_views FROM analytics");
                            $stmt->execute();
                            $totalViews = $stmt->fetch()['total_views'] ?? 0;
                        } catch (Exception $e) {
                            $todayViews = 0;
                            $weekViews = 0;
                            $topEdition = null;
                            $uniqueVisitors = 0;
                            $totalViews = 0;
                        }
                        ?>
                        <div class="row">
                            <div class="col-6">
                                <div class="info-box bg-info">
                                    <span class="info-box-icon"><i class="fas fa-eye"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Today's Views</span>
                                        <span class="info-box-number"><?= number_format($todayViews) ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="info-box bg-success">
                                    <span class="info-box-icon"><i class="fas fa-users"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Unique Visitors</span>
                                        <span class="info-box-number"><?= number_format($uniqueVisitors) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-6">
                                <div class="info-box bg-warning">
                                    <span class="info-box-icon"><i class="fas fa-chart-line"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Week Views</span>
                                        <span class="info-box-number"><?= number_format($weekViews) ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="info-box bg-danger">
                                    <span class="info-box-icon"><i class="fas fa-globe"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Views</span>
                                        <span class="info-box-number"><?= number_format($totalViews) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Storage Stats -->
        <div class="row mt-3">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <p><strong>Total Storage:</strong> <?= formatBytes($storageLimit) ?></p>
                        <p><strong>Used Storage:</strong> <?= formatBytes($usedStorage) ?></p>
                        <p><strong>Free Storage:</strong> <?= formatBytes($freeStorage) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Chart.js for Storage Usage Chart -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Prepare data for the donut chart
    const folderLabels = <?php echo json_encode(array_keys($folderSizes)); ?>;
    const folderData = <?php echo json_encode(array_values($folderSizes)); ?>;
    const freeData = <?= $freeStorage ?>;

    const ctx = document.getElementById('storageChart').getContext('2d');
    const storageChart = new Chart(ctx, {
        type: 'doughnut', // Use donut chart for circular look
        data: {
            labels: [...folderLabels, 'Free Storage'],
            datasets: [{
                data: [...folderData, freeData],
                backgroundColor: [ // Random colors via Chart.js defaults
                    '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40', '#C9CBCF'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom', // Legend below chart for responsiveness
                    labels: {
                        generateLabels: function(chart) {
                            return chart.data.labels.map((label, i) => ({
                                text: `${label}: ${formatBytes(chart.data.datasets[0].data[i])}`,
                                fillStyle: chart.data.datasets[0].backgroundColor[i],
                                hidden: false,
                                lineWidth: 0,
                                index: i
                            }));
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            const bytes = context.raw;
                            const gb = (bytes / (1024 * 1024 * 1024)).toFixed(2) + ' GB';
                            return `${context.label}: ${gb}`;
                        }
                    }
                }
            }
        }
    });

    // Helper function to format bytes (used in JS for tooltips)
    function formatBytes(bytes) {
        return (bytes / (1024 * 1024 * 1024)).toFixed(2) + ' GB';
    }
</script>

<!-- CSS for legend and analytics widgets -->
<style>
.legend {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    justify-content: center;
    font-size: 14px;
}

.legend-item {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    margin: 5px 0;
}

.legend-item::before {
    content: '';
    display: inline-block;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background-color: currentColor;
}

/* Analytics widget styles */
.info-box {
    display: block;
    min-height: 90px;
    background: #fff;
    width: 100%;
    box-shadow: 0 1px 1px rgba(0,0,0,0.1);
    border-radius: 2px;
    margin-bottom: 15px;
    position: relative;
    padding: 10px;
}

.info-box-icon {
    border-top-left-radius: 2px;
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
    border-bottom-left-radius: 2px;
    display: block;
    float: left;
    height: 70px;
    width: 70px;
    text-align: center;
    font-size: 45px;
    line-height: 70px;
    background: rgba(0,0,0,0.2);
    color: rgba(255,255,255,0.8);
}

.info-box-content {
    padding: 5px 10px;
    margin-left: 70px;
}

.info-box-text {
    text-transform: uppercase;
    font-weight: bold;
    font-size: 12px;
    display: block;
}

.info-box-number {
    display: block;
    font-weight: bold;
    font-size: 18px;
}

.bg-info { background-color: #17a2b8 !important; color: white; }
.bg-success { background-color: #28a745 !important; color: white; }
.bg-warning { background-color: #ffc107 !important; color: white; }
.bg-danger { background-color: #dc3545 !important; color: white; }

/* Timeline styles */
.timeline {
    position: relative;
    margin: 0 0 30px 0;
    padding: 0;
    list-style: none;
}

.timeline:before {
    content: '';
    position: absolute;
    top: 0;
    bottom: 0;
    width: 4px;
    background: #ddd;
    left: 31px;
    margin: 0;
    border-radius: 2px;
}

.time-label {
    margin-bottom: 15px;
}

.time-label > span {
    font-weight: 600;
    color: #fff;
    font-size: 12px;
    display: inline-block;
    padding: 5px;
    border-radius: 4px;
}

.timeline-item {
    background: #fff;
    border-radius: 3px;
    width: calc(100% - 50px);
    margin-left: 50px;
    margin-top: 10px;
    color: #444;
    padding: 0;
    position: relative;
    box-shadow: 0 1px 1px rgba(0,0,0,0.1);
}

.timeline-header {
    margin: 0;
    color: #555;
    border-bottom: 1px solid #f4f4f4;
    padding: 10px 15px;
    font-size: 16px;
    line-height: 1.1;
}

.timeline-body {
    padding: 15px;
    font-size: 14px;
}

.bg-blue { background-color: #007bff !important; }
</style>

<?php require_once 'includes/footer.php'; // Include the shared footer (corrected path) ?>