<?php
// Start output buffering to prevent headers already sent errors
ob_start();

// Start session only if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page_title = "Analytics Dashboard";
require_once '../../includes/header.php';

// Check if the logged-in user is an admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../../index.php");
    exit();
}

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../controllers/AnalyticsController.php';

// Initialize analytics controller
$analytics = new AnalyticsController($pdo);

// Get dashboard data
$days = isset($_GET['days']) ? (int)$_GET['days'] : 30;
$dashboard_data = $analytics->getDashboardStats($days);
?>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Date Range Picker -->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Analytics Dashboard</h1>
            </div>
            <div class="col-sm-6">
                <div class="float-right">
                    <select id="dayFilter" class="form-control" style="width: 150px; display: inline-block;">
                        <option value="7" <?= $days == 7 ? 'selected' : '' ?>>Last 7 days</option>
                        <option value="30" <?= $days == 30 ? 'selected' : '' ?>>Last 30 days</option>
                        <option value="90" <?= $days == 90 ? 'selected' : '' ?>>Last 90 days</option>
                    </select>
                    <button class="btn btn-primary ml-2" onclick="exportData()">
                        <i class="fas fa-download"></i> Export
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        
        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3><?= number_format($dashboard_data['overall_stats']['total_views'] ?? 0) ?></h3>
                        <p>Total Views</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-eye"></i>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3><?= number_format($dashboard_data['overall_stats']['unique_visitors'] ?? 0) ?></h3>
                        <p>Unique Visitors</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3><?= count($dashboard_data['top_editions'] ?? []) ?></h3>
                        <p>Active Editions</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-newspaper"></i>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3><?= $dashboard_data['overall_stats']['active_days'] ?? 0 ?></h3>
                        <p>Active Days</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-calendar"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Daily Views Trend</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="dailyViewsChart" height="100"></canvas>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Top Categories</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="categoriesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Tables Row -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Top Editions</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Edition</th>
                                        <th>Category</th>
                                        <th>Views</th>
                                        <th>Unique</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($dashboard_data['top_editions'])): ?>
                                        <?php foreach ($dashboard_data['top_editions'] as $edition): ?>
                                            <tr>
                                                <td>
                                                    <a href="../editions/view.php?id=<?= $edition['edition_id'] ?>" target="_blank">
                                                        <?= htmlspecialchars(substr($edition['title'], 0, 30)) ?>...
                                                    </a>
                                                </td>
                                                <td><?= htmlspecialchars($edition['category_name']) ?></td>
                                                <td><span class="badge badge-primary"><?= number_format($edition['views']) ?></span></td>
                                                <td><span class="badge badge-success"><?= number_format($edition['unique_visitors']) ?></span></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center">No data available</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Recent Activity</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>Action</th>
                                        <th>Edition</th>
                                        <th>IP</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($dashboard_data['recent_activity'])): ?>
                                        <?php foreach ($dashboard_data['recent_activity'] as $activity): ?>
                                            <tr>
                                                <td><?= date('H:i', strtotime($activity['created_at'])) ?></td>
                                                <td>
                                                    <span class="badge badge-info">
                                                        <?= ucfirst(str_replace('_', ' ', $activity['event_type'])) ?>
                                                    </span>
                                                </td>
                                                <td><?= htmlspecialchars(substr($activity['edition_title'] ?? 'N/A', 0, 20)) ?></td>
                                                <td><small><?= htmlspecialchars($activity['ip_address']) ?></small></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center">No recent activity</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Dashboard data from PHP
const dashboardData = <?= json_encode($dashboard_data) ?>;

// Daily Views Chart
const dailyCtx = document.getElementById('dailyViewsChart').getContext('2d');
const dailyLabels = dashboardData.daily_stats.map(item => item.date);
const dailyViews = dashboardData.daily_stats.map(item => parseInt(item.daily_views));
const dailyUnique = dashboardData.daily_stats.map(item => parseInt(item.daily_unique));

new Chart(dailyCtx, {
    type: 'line',
    data: {
        labels: dailyLabels,
        datasets: [{
            label: 'Total Views',
            data: dailyViews,
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.1)',
            tension: 0.1
        }, {
            label: 'Unique Visitors',
            data: dailyUnique,
            borderColor: 'rgb(255, 99, 132)',
            backgroundColor: 'rgba(255, 99, 132, 0.1)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Categories Pie Chart
const categoriesCtx = document.getElementById('categoriesChart').getContext('2d');
const categoryLabels = dashboardData.top_categories.map(item => item.name);
const categoryData = dashboardData.top_categories.map(item => parseInt(item.views));

new Chart(categoriesCtx, {
    type: 'doughnut',
    data: {
        labels: categoryLabels,
        datasets: [{
            data: categoryData,
            backgroundColor: [
                '#FF6384',
                '#36A2EB',
                '#FFCE56',
                '#4BC0C0',
                '#9966FF',
                '#FF9F40'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true
    }
});

// Day filter change handler
document.getElementById('dayFilter').addEventListener('change', function() {
    const days = this.value;
    window.location.href = `?days=${days}`;
});

// Export function
function exportData() {
    const days = document.getElementById('dayFilter').value;
    const startDate = new Date();
    startDate.setDate(startDate.getDate() - days);
    const endDate = new Date();
    
    const start = startDate.toISOString().split('T')[0];
    const end = endDate.toISOString().split('T')[0];
    
    window.open(`../../controllers/AnalyticsController.php?action=export&start_date=${start}&end_date=${end}&format=csv`);
}

// Auto-refresh every 5 minutes
setInterval(function() {
    location.reload();
}, 300000);
</script>

<?php require_once '../../includes/footer.php'; ?>

<?php
// End output buffering
ob_end_flush();
?>