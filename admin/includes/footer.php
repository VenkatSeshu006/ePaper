<?php
// admin/includes/footer.php
// Use relative path to config.php
require_once __DIR__ . '/../../config.php';

// Fetch site name from settings
$stmt = $pdo->prepare("SELECT value FROM settings WHERE key_name = 'site_name'");
$stmt->execute();
$site_name_result = $stmt->fetch(PDO::FETCH_ASSOC);
$site_name = $site_name_result ? htmlspecialchars($site_name_result['value']) : 'Epapers CMS';
?>
</div>
            <!-- /.content-wrapper -->

            <!-- Footer -->
            <footer class="main-footer">
                <div class="container">
                    <strong>Copyright © <?= date('Y') ?> <a href="#"><?= $site_name ?></a>.</strong>
                    All rights reserved.
                </div>
            </footer>
        </div>
        <!-- ./wrapper -->

        <!-- AdminLTE JS -->
        <script src="<?php echo $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/ePaper/admin/assets/js/jquery.min.js'; ?>"></script>
        <script src="<?php echo $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/ePaper/admin/assets/js/bootstrap.bundle.min.js'; ?>"></script>
        <script src="<?php echo $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/ePaper/admin/assets/js/adminlte.min.js'; ?>"></script>

        <!-- Custom JS -->
        <script>
            // Add any custom JavaScript here
        </script>
    </body>
</html>