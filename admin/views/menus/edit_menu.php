<?php
$page_title = "Edit Menu"; // Set the page title
require_once '../../includes/header.php'; // Include the shared header
require_once '../../../config.php'; // Database connection

$menuId = isset($_GET['menu_id']) ? (int)$_GET['menu_id'] : null;

if (!$menuId) {
    header("Location: index.php");
    exit;
}

// Fetch the selected menu
$stmt = $pdo->prepare("SELECT * FROM menus WHERE id = :id");
$stmt->execute(['id' => $menuId]);
$menu = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$menu) {
    header("Location: index.php");
    exit;
}

// Fetch categories, editions, and pages for the left-side panels
$categories = $pdo->query("SELECT id, name FROM categories")->fetchAll(PDO::FETCH_ASSOC);
$editions = $pdo->query("SELECT id, title FROM editions")->fetchAll(PDO::FETCH_ASSOC);
$pages = $pdo->query("SELECT id, title FROM pages")->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="content-header">
    <div class="container-fluid">
        <h1>Edit <?= htmlspecialchars($menu['name']) ?></h1>
    </div>
</div>
<section class="content">
    <div class="container-fluid">
        <!-- Left Sidebar -->
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Add Menu Items</h3>
                    </div>
                    <div class="card-body">
                        <!-- Categories Panel -->
                        <div class="mb-3">
                            <h5>Categories</h5>
                            <ul id="categories-list" class="list-group">
                                <?php foreach ($categories as $category): ?>
                                    <li class="list-group-item draggable" data-type="category" data-id="<?= $category['id'] ?>" data-title="<?= htmlspecialchars($category['name']) ?>">
                                        <?= htmlspecialchars($category['name']) ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                        <!-- Editions Panel -->
                        <div class="mb-3">
                            <h5>Editions</h5>
                            <ul id="editions-list" class="list-group">
                                <?php foreach ($editions as $edition): ?>
                                    <li class="list-group-item draggable" data-type="edition" data-id="<?= $edition['id'] ?>" data-title="<?= htmlspecialchars($edition['title']) ?>">
                                        <?= htmlspecialchars($edition['title']) ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                        <!-- Pages Panel -->
                        <div class="mb-3">
                            <h5>Pages</h5>
                            <ul id="pages-list" class="list-group">
                                <?php foreach ($pages as $page): ?>
                                    <li class="list-group-item draggable" data-type="page" data-id="<?= $page['id'] ?>" data-title="<?= htmlspecialchars($page['title']) ?>">
                                        <?= htmlspecialchars($page['title']) ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                        <!-- Custom Links Panel -->
                        <div class="mb-3">
                            <h5>Custom Links</h5>
                            <form id="custom-link-form">
                                <input type="text" id="custom-link-url" placeholder="URL" class="form-control mb-2" required>
                                <input type="text" id="custom-link-title" placeholder="Link Text" class="form-control mb-2" required>
                                <button type="submit" class="btn btn-primary btn-sm">Add to Menu</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar (Menu Editor) -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Menu Structure</h3>
                    </div>
                    <div class="card-body">
                        <div id="menu-editor">
                            <ul id="menu-items" class="sortable list-group"></ul>
                        </div>
                        <button id="save-menu" class="btn btn-success mt-3">Save Menu</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Initialize SortableJS for drag-and-drop
    const menuItems = document.getElementById('menu-items');
    Sortable.create(menuItems, {
        group: 'shared',
        animation: 150,
        onEnd: function (evt) {
            updateOrderNumbers();
        }
    });

    // Add click event listeners to draggable items
    document.querySelectorAll('.draggable').forEach(item => {
        item.addEventListener('click', function () {
            const type = this.dataset.type;
            const id = this.dataset.id;
            const title = this.dataset.title;

            // Create a new menu item
            const menuItem = document.createElement('li');
            menuItem.className = 'list-group-item';
            menuItem.dataset.type = type;
            menuItem.dataset.id = id;
            menuItem.innerHTML = `
                <span>${title}</span>
                <button class="btn btn-danger btn-sm float-end remove-item">Remove</button>
            `;
            menuItems.appendChild(menuItem);

            // Add remove button functionality
            menuItem.querySelector('.remove-item').addEventListener('click', function () {
                menuItem.remove();
                updateOrderNumbers();
            });

            updateOrderNumbers();
        });
    });

    // Handle custom link form submission
    document.getElementById('custom-link-form').addEventListener('submit', function (e) {
        e.preventDefault();
        const url = document.getElementById('custom-link-url').value;
        const title = document.getElementById('custom-link-title').value;

        if (url && title) {
            const menuItem = document.createElement('li');
            menuItem.className = 'list-group-item';
            menuItem.dataset.type = 'custom';
            menuItem.dataset.url = url;
            menuItem.innerHTML = `
                <span>${title}</span>
                <button class="btn btn-danger btn-sm float-end remove-item">Remove</button>
            `;
            menuItems.appendChild(menuItem);

            // Add remove button functionality
            menuItem.querySelector('.remove-item').addEventListener('click', function () {
                menuItem.remove();
                updateOrderNumbers();
            });

            updateOrderNumbers();
        }
    });

    // Update order numbers after reordering or adding/removing items
    function updateOrderNumbers() {
        const items = Array.from(menuItems.children);
        items.forEach((item, index) => {
            item.dataset.order = index;
        });
    }

    // Load saved menu items when the page loads
    window.addEventListener('load', function () {
        const menuId = <?= $menuId ?>;

        fetch(`load_menu_items.php?menu_id=${menuId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                data.forEach(item => {
                    const menuItem = document.createElement('li');
                    menuItem.className = 'list-group-item';
                    menuItem.dataset.type = item.type;
                    menuItem.dataset.id = item.object_id || '';
                    menuItem.dataset.url = item.url || '';
                    menuItem.dataset.order = item.order_number;
                    menuItem.innerHTML = `
                        <span>${item.title}</span>
                        <button class="btn btn-danger btn-sm float-end remove-item">Remove</button>
                    `;
                    menuItems.appendChild(menuItem);

                    // Add remove button functionality
                    menuItem.querySelector('.remove-item').addEventListener('click', function () {
                        menuItem.remove();
                        updateOrderNumbers();
                    });
                });

                updateOrderNumbers(); // Update order numbers after loading items
            })
            .catch(error => {
                console.error('Error loading menu items:', error);
                showToast('Error', 'An unexpected error occurred while loading menu items.', 'danger');
            });
    });

    // Save menu structure to the server
    document.getElementById('save-menu').addEventListener('click', function () {
        const menuId = <?= $menuId ?>;

        const menuData = Array.from(menuItems.children).map(item => ({
            type: item.dataset.type,
            object_id: item.dataset.id || null,
            url: item.dataset.url || null,
            title: item.querySelector('span').innerText,
            order_number: item.dataset.order
        }));

        fetch('save_menu.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                menu_id: menuId,
                items: menuData
            })
        }).then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        }).then(data => {
            if (data.status === 'success') {
                showToast('Success', data.message, 'success');
            } else {
                showToast('Error', data.message, 'danger');
            }
        }).catch(error => {
            console.error('Error saving menu:', error);
            showToast('Error', 'An unexpected error occurred.', 'danger');
        });
    });

    // Function to display AdminLTE Toast notifications
    function showToast(title, message, type) {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        Toast.fire({
            icon: type === 'success' ? 'success' : 'error',
            title: title,
            text: message
        });
    }
</script>
<?php require_once '../../includes/footer.php'; // Include the shared footer ?>