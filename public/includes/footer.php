<!-- public/includes/footer.php -->
<footer>
    <div class="footer-content">
        <div class="footer-menu">
            <ul>
                <!-- Dynamically generate footer menu items from the database -->
                <?php foreach (get_menu_items('footer') as $item): ?>
                    <li><a href="<?php echo get_menu_item_url($item); ?>"><?php echo $item['title']; ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="footer-copyright">
            <p>&copy; <?php echo date('Y'); ?> <?php echo get_setting('site_name'); ?>. All rights reserved.</p>
        </div>
    </div>
</footer>