<!-- public/includes/header.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    // Include database configuration
    require_once '../config.php';

    // Function to get setting value
    function get_setting($key) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT value FROM settings WHERE key_name = :key");
        $stmt->execute(['key' => $key]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['value'] : '';
    }

    // Function to get color value
    function get_color($key) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT value FROM color_schema WHERE key_name = :key");
        $stmt->execute(['key' => $key]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['value'] : '';
    }

    // Function to get menu items
    function get_menu_items($location) {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT mi.*, m.location
            FROM menu_items mi
            JOIN menus m ON mi.menu_id = m.id
            WHERE m.location = :location
            ORDER BY mi.order_number
        ");
        $stmt->execute(['location' => $location]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Function to get menu item URL
    function get_menu_item_url($item) {
        switch ($item['type']) {
            case 'category':
                return "/category/{$item['object_id']}";
            case 'edition':
                return "/edition/{$item['object_id']}";
            case 'page':
                return "/page/{$item['object_id']}";
            case 'custom':
                return $item['url'];
            default:
                return '#';
        }
    }

    // Function to get the latest featured edition URL
    function get_latest_featured_edition_url() {
        global $pdo;
        try {
            // First try to get the latest featured edition
            $stmt = $pdo->prepare("
                SELECT id FROM editions 
                WHERE is_featured = 1 
                ORDER BY edition_date DESC, created_at DESC 
                LIMIT 1
            ");
            $stmt->execute();
            $featured = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($featured) {
                return "edition.php?id=" . $featured['id'];
            }
            
            // If no featured edition, get the latest edition overall
            $stmt = $pdo->prepare("
                SELECT id FROM editions 
                ORDER BY edition_date DESC, created_at DESC 
                LIMIT 1
            ");
            $stmt->execute();
            $latest = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($latest) {
                return "edition.php?id=" . $latest['id'];
            }
            
            // Fallback to categories page if no editions exist
            return "categories.php";
        } catch (Exception $e) {
            // Fallback to categories page on error
            return "categories.php";
        }
    }
    ?>
    <title><?php echo get_setting('site_name'); ?></title>
    <link rel="icon" href="<?php echo get_setting('site_favicon'); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    
    <?php
    // Google Analytics GA4
    $ga_id = get_setting('google_analytics_id');
    if (!empty($ga_id)): ?>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo htmlspecialchars($ga_id); ?>"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', '<?php echo htmlspecialchars($ga_id); ?>');
    </script>
    <?php endif; ?>
    
    <?php
    // Google AdSense Auto Ads
    $adsense_id = get_setting('google_adsense_id');
    if (!empty($adsense_id)): ?>
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=<?php echo htmlspecialchars($adsense_id); ?>" crossorigin="anonymous"></script>
    <script>
      (adsbygoogle = window.adsbygoogle || []).push({
        google_ad_client: "<?php echo htmlspecialchars($adsense_id); ?>",
        enable_page_level_ads: true
      });
    </script>
    <?php endif; ?>
    
    <style>
        :root {
            --primary-color: <?php echo get_color('primary_color'); ?>;
            --secondary-color: <?php echo get_color('secondary_color'); ?>;
            --background-color: <?php echo get_color('background_color'); ?>;
            --text-color: <?php echo get_color('text_color'); ?>;
            --link-color: <?php echo get_color('link_color'); ?>;
            --header-background: <?php echo get_color('header_background'); ?>;
            --header-text-color: <?php echo get_color('header_text_color'); ?>;
            --footer-background: <?php echo get_color('footer_background'); ?>;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .boxed-layout {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        header {
            position: relative;
            width: 100%;
            border-bottom: 1px solid #e0e0e0;
            background-color: var(--header-background);
            padding: 20px 0;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo-container {
            flex-shrink: 0;
        }

        .logo-container a {
            display: block;
            text-decoration: none;
        }

        .logo-container img {
            max-width: 300px;
            max-height: 80px;
            width: auto;
            height: auto;
            object-fit: contain;
            transition: opacity 0.3s ease;
        }

        .logo-container a:hover img {
            opacity: 0.8;
        }

        .nav-menu {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
        }

        .nav-menu li {
            margin-left: 30px;
        }

        .nav-menu li a {
            color: var(--header-text-color);
            text-decoration: none;
            font-size: 16px;
            font-weight: bold;
            text-transform: capitalize;
            padding: 10px 15px;
            display: block;
            border-radius: 5px;
        }

        .hamburger {
            display: none;
            cursor: pointer;
            background: none;
            border: none;
            padding: 10px;
        }

        .hamburger span {
            display: block;
            width: 25px;
            height: 3px;
            background: var(--header-text-color);
            margin: 5px 0;
            transition: 0.3s;
        }

        .hamburger.active span:nth-child(1) {
            transform: rotate(45deg) translate(5px, 5px);
        }

        .hamburger.active span:nth-child(2) {
            opacity: 0;
        }

        .hamburger.active span:nth-child(3) {
            transform: rotate(-45deg) translate(7px, -7px);
        }

        .off-canvas-menu {
            position: fixed;
            top: 0;
            left: -280px;
            width: 280px;
            height: 100%;
            background-color: var(--header-background);
            list-style: none;
            margin: 0;
            padding: 20px 0;
            transition: left 0.3s ease;
            z-index: 1000;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }

        .off-canvas-menu.active {
            left: 0;
        }

        .off-canvas-menu li {
            margin: 10px 0;
        }

        .off-canvas-menu li a {
            color: var(--header-text-color);
            text-decoration: none;
            font-size: 16px;
            font-weight: bold;
            padding: 15px 20px;
            display: block;
            text-transform: capitalize;
        }

        /* Main content styles */
        .main-content {
            flex: 1;
            padding: 20px 0;
        }

        /* Responsive styles for tablets */
        @media (max-width: 1024px) and (min-width: 769px) {
            .header-container {
                flex-direction: row;
                align-items: center;
                padding: 15px 20px;
                display: flex;
            }

            .logo-container {
                flex: 0 0 80%;
                max-width: 80%;
            }

            .logo-container a {
                display: block;
            }

            .logo-container img {
                max-width: 100%;
                height: auto;
                max-height: 60px;
            }

            .nav-menu {
                display: none;
            }

            .hamburger {
                display: block;
                flex: 0 0 20%;
                max-width: 20%;
                text-align: right;
                position: relative;
                top: auto;
                right: auto;
                transform: none;
            }

            .off-canvas-menu {
                display: block;
                padding-top: 70px;
            }

            .off-canvas-menu li a {
                font-size: 20px;
                padding: 22px 30px;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            }
        }

        /* Responsive styles */
        @media (max-width: 768px) {
            .header-container {
                flex-direction: row;
                justify-content: space-around;
                align-items: center;
                padding: 15px 20px;
                display: flex;
            }

            .logo-container {
                flex: 0 0 80%;
                max-width: 80%;
            }

            .logo-container a {
                display: block;
            }

            .logo-container img {
                max-width: 100%;
                height: auto;
                max-height: 50px;
            }

            .nav-menu {
                display: none;
            }

            .hamburger {
                display: block;
                flex: 0 0 20%;
                max-width: 20%;
                text-align: right;
                position: relative;
                top: auto;
                right: auto;
                transform: none;
            }

            .off-canvas-menu {
                display: block;
                padding-top: 60px;
            }

            .off-canvas-menu li a {
                font-size: 18px;
                padding: 20px 25px;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            }
        }

        @media (min-width: 769px) {
            .hamburger {
                display: none;
            }

            .off-canvas-menu {
                display: none;
            }
        }

        /* Footer styles */
        footer {
            background-color: var(--footer-background);
            color: white;
            padding: 20px 0;
            text-align: center;
            margin-top: auto;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .footer-menu ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px; /* Space between items */
        }

        .footer-menu ul li {
            position: relative;
            padding: 0 10px; /* Padding for touch targets */
        }

        /* Add pipe separator between items (except last one) */
        .footer-menu ul li:not(:last-child)::after {
            content: "|";
            position: absolute;
            right: -2px;
            color: white;
            opacity: 0.7;
        }

        .footer-menu ul li a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            text-transform: capitalize;
            display: block;
        }

        .footer-menu ul li a:hover {
            text-decoration: underline;
        }

        .footer-copyright {
            margin-top: 15px;
            font-size: 14px;
            opacity: 0.8;
        }

         /* Responsive styles */
         @media (max-width: 768px) {
             .footer-menu ul {
                 flex-wrap: nowrap; /* Keep horizontal on mobile */
                 overflow-x: auto; /* Scroll if too many items */
                 white-space: nowrap; /* Prevent wrapping */
                 gap: 10px;
             }

             .footer-menu ul li a {
                 font-size: 14px;
             }

             .footer-menu ul li {
                 padding: 0 8px;
             }
         }

         @media (max-width: 576px) {
             .footer-menu ul li a {
                 font-size: 13px;
             }

             .footer-menu ul li {
                 padding: 0 6px;
             }
         }
    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo-container">
                <a href="<?php echo get_latest_featured_edition_url(); ?>">
                    <img src="<?php echo get_setting('site_logo'); ?>" alt="Site Logo">
                </a>
            </div>
            <nav>
                <ul class="nav-menu">
                    <?php foreach (get_menu_items('header') as $item): ?>
                        <li><a href="<?php echo get_menu_item_url($item); ?>"><?php echo htmlspecialchars($item['title']); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </nav>
            <button class="hamburger" aria-label="Toggle navigation">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
        <ul class="off-canvas-menu">
            <?php foreach (get_menu_items('header') as $item): ?>
                <li><a href="<?php echo get_menu_item_url($item); ?>"><?php echo htmlspecialchars($item['title']); ?></a></li>
            <?php endforeach; ?>
        </ul>
    </header>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const hamburger = document.querySelector('.hamburger');
            const offCanvasMenu = document.querySelector('.off-canvas-menu');

            hamburger.addEventListener('click', function () {
                hamburger.classList.toggle('active');
                offCanvasMenu.classList.toggle('active');
            });

            offCanvasMenu.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', function () {
                    hamburger.classList.remove('active');
                    offCanvasMenu.classList.remove('active');
                });
            });
        });
    </script>