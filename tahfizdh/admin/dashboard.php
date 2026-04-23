<?php
// admin/dashboard.php - Add registration management
require_once '../config/database.php';

// Check login
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Get statistics
$stats = [];

// Messages/Registrations
$stmt = $db->query("SELECT COUNT(*) as total FROM registrations");
$stats['registrations'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Pending registrations
$stmt = $db->query("SELECT COUNT(*) as total FROM registrations WHERE status = 'pending'");
$stats['pending'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Approved registrations
$stmt = $db->query("SELECT COUNT(*) as total FROM registrations WHERE status = 'approved'");
$stats['approved'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM products WHERE is_active = 1");
$stats['products'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM testimonials WHERE is_active = 1");
$stats['testimonials'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM services WHERE is_active = 1");
$stats['services'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Get recent registrations
$stmt = $db->query("SELECT * FROM registrations ORDER BY created_at DESC LIMIT 10");
$registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all registrations for management
$stmt = $db->query("SELECT * FROM registrations ORDER BY created_at DESC");
$all_registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get products etc
$stmt = $db->query("SELECT * FROM products ORDER BY display_order ASC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $db->query("SELECT * FROM services ORDER BY display_order ASC");
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $db->query("SELECT * FROM testimonials ORDER BY display_order ASC");
$testimonials = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Rumah Tahfidzh Hikmah</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        :root {
            --primary: #92eb34;
            --primary-dark: #11ad5d;
            --dark: #0f1a24;
            --dark-light: #1a2a36;
            --text-light: #ffffff;
            --text-gray: #e0e0e0;
            --glass-bg: rgba(255, 255, 255, 0.03);
            --glass-border: rgba(255, 255, 255, 0.05);
        }
        body {
            font-family: 'Space Grotesk', sans-serif;
            background: var(--dark);
            color: var(--text-light);
            overflow-x: hidden;
        }
        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 280px;
            background: rgba(15, 26, 36, 0.95);
            backdrop-filter: blur(20px);
            border-right: 1px solid var(--glass-border);
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }
        .sidebar-header {
            padding: 30px 25px;
            border-bottom: 1px solid var(--glass-border);
            text-align: center;
        }
        .sidebar-header img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin-bottom: 10px;
        }
        .sidebar-header h3 {
            background: linear-gradient(135deg, #92eb34, #11ad5d);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .nav-menu {
            padding: 20px 15px;
        }
        .nav-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 14px 20px;
            margin-bottom: 8px;
            border-radius: 50px;
            color: var(--text-gray);
            text-decoration: none;
            transition: 0.3s;
        }
        .nav-item:hover, .nav-item.active {
            background: rgba(146,235,52,0.1);
            color: var(--primary);
        }
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 30px 40px;
        }
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            background: var(--glass-bg);
            padding: 15px 30px;
            border-radius: 60px;
            border: 1px solid var(--glass-border);
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        .stat-card {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 30px;
            padding: 25px;
            transition: 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            border-color: var(--primary);
        }
        .stat-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #92eb34, #11ad5d);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            font-size: 1.5rem;
        }
        .stat-info h3 {
            font-size: 1.8rem;
            color: var(--primary);
        }
        .dashboard-section {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 30px;
            padding: 30px;
            margin-bottom: 40px;
        }
        .section-title {
            font-size: 1.3rem;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--primary);
            display: inline-block;
        }
        .data-table {
            width: 100%;
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid var(--glass-border);
        }
        th {
            color: var(--primary);
        }
        .status-badge {
            padding: 4px 12px;
            border-radius: 50px;
            font-size: 0.85rem;
            display: inline-block;
        }
        .status-pending { background: rgba(255, 193, 7, 0.2); color: #ffc107; }
        .status-approved { background: rgba(146, 235, 52, 0.2); color: #92eb34; }
        .status-rejected { background: rgba(255, 68, 68, 0.2); color: #ff4444; }
        .status-interview { background: rgba(0, 150, 255, 0.2); color: #0096ff; }
        .action-btn {
            background: transparent;
            border: none;
            color: var(--text-gray);
            cursor: pointer;
            margin: 0 5px;
            font-size: 1.1rem;
            transition: 0.3s;
        }
        .action-btn:hover {
            color: var(--primary);
        }
        .btn-add {
            background: linear-gradient(135deg, #92eb34, #11ad5d);
            border: none;
            padding: 10px 20px;
            border-radius: 50px;
            color: white;
            cursor: pointer;
            font-weight: 600;
            margin-bottom: 20px;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background: var(--dark-light);
            border-radius: 30px;
            padding: 30px;
            width: 90%;
            max-width: 800px;
            max-height: 80vh;
            overflow-y: auto;
            border: 1px solid var(--primary);
        }
        .modal-content input, .modal-content textarea, .modal-content select {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            background: rgba(255,255,255,0.05);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            color: white;
        }
        .mobile-menu-btn {
            display: none;
        }
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                position: fixed;
                z-index: 100;
            }
            .sidebar.active {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
                padding: 20px;
            }
            .mobile-menu-btn {
                display: block;
                position: fixed;
                top: 20px;
                left: 20px;
                z-index: 101;
                background: var(--primary);
                padding: 12px;
                border-radius: 50%;
                cursor: pointer;
            }
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        .detail-row {
            background: rgba(146,235,52,0.05);
        }
        .detail-content {
            padding: 20px;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        .detail-item {
            margin-bottom: 10px;
        }
        .detail-item strong {
            color: var(--primary);
        }
    </style>
</head>
<body>
    <div class="mobile-menu-btn" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </div>
    
    <div class="dashboard-container">
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <i class="fas fa-quran" style="font-size: 2.5rem; color: var(--primary);"></i>
                <h3>Admin Panel</h3>
                <p style="font-size: 0.85rem;">Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></p>
            </div>
            <div class="nav-menu">
                <a href="#" class="nav-item active" data-tab="overview"><i class="fas fa-tachometer-alt"></i> Overview</a>
                <a href="#" class="nav-item" data-tab="registrations"><i class="fas fa-users"></i> Pendaftaran</a>
                <a href="#" class="nav-item" data-tab="products"><i class="fas fa-cube"></i> Aktivitas</a>
                <a href="#" class="nav-item" data-tab="services"><i class="fas fa-cogs"></i> Program</a>
                <a href="#" class="nav-item" data-tab="testimonials"><i class="fas fa-star"></i> Testimoni</a>
                <a href="logout.php" class="nav-item"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </aside>
        
        <main class="main-content">
            <div class="top-bar">
                <h2><i class="fas fa-shield-alt"></i> Dashboard Control Panel</h2>
                <div><i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($_SESSION['admin_username']); ?></div>
            </div>
            
            <!-- Overview Tab -->
            <div id="overview-tab" class="tab-content">
                <div class="stats-grid">
                    <div class="stat-card"><div class="stat-icon"><i class="fas fa-user-plus"></i></div><div class="stat-info"><h3><?php echo $stats['registrations']; ?></h3><p>Total Pendaftar</p></div></div>
                    <div class="stat-card"><div class="stat-icon"><i class="fas fa-clock"></i></div><div class="stat-info"><h3><?php echo $stats['pending']; ?></h3><p>Pending</p></div></div>
                    <div class="stat-card"><div class="stat-icon"><i class="fas fa-check-circle"></i></div><div class="stat-info"><h3><?php echo $stats['approved']; ?></h3><p>Diterima</p></div></div>
                    <div class="stat-card"><div class="stat-icon"><i class="fas fa-cube"></i></div><div class="stat-info"><h3><?php echo $stats['products']; ?></h3><p>Aktivitas</p></div></div>
                </div>
                
                <div class="dashboard-section">
                    <h3 class="section-title"><i class="fas fa-clock"></i> Pendaftar Terbaru</h3>
                    <div class="data-table">
                        <table>
                            <thead>
                                <tr><th>Nama</th><th>Telepon</th><th>Program</th><th>Status</th><th>Tanggal Daftar</th><th>Aksi</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach($registrations as $reg): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($reg['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($reg['phone']); ?></td>
                                    <td><?php echo htmlspecialchars($reg['program_choice']); ?></td>
                                    <td><span class="status-badge status-<?php echo $reg['status']; ?>"><?php echo ucfirst($reg['status']); ?></span></td>
                                    <td><?php echo date('d/m/Y', strtotime($reg['created_at'])); ?></td>
                                    <td>
                                        <button class="action-btn" onclick="viewRegistration(<?php echo $reg['id']; ?>)"><i class="fas fa-eye"></i></button>
                                        <button class="action-btn" onclick="updateStatus(<?php echo $reg['id']; ?>)"><i class="fas fa-edit"></i></button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Registrations Tab -->
            <div id="registrations-tab" class="tab-content" style="display:none;">
                <div class="dashboard-section">
                    <h3 class="section-title"><i class="fas fa-users"></i> Manajemen Pendaftaran Santri</h3>
                    <div class="data-table">
                        <table>
                            <thead>
                                <tr><th>ID</th><th>Nama</th><th>Telepon</th><th>Program</th><th>Status</th><th>Tanggal</th><th>Aksi</th></tr>
                            </thead>
                            <tbody id="registrations-list">
                                <?php foreach($all_registrations as $reg): ?>
                                <tr id="reg-row-<?php echo $reg['id']; ?>">
                                    <td><?php echo $reg['id']; ?></td>
                                    <td><?php echo htmlspecialchars($reg['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($reg['phone']); ?></td>
                                    <td><?php echo htmlspecialchars($reg['program_choice']); ?></td>
                                    <td><span class="status-badge status-<?php echo $reg['status']; ?>"><?php echo ucfirst($reg['status']); ?></span></td>
                                    <td><?php echo date('d/m/Y', strtotime($reg['created_at'])); ?></td>
                                    <td>
                                        <button class="action-btn" onclick="viewRegistration(<?php echo $reg['id']; ?>)"><i class="fas fa-eye"></i></button>
                                        <button class="action-btn" onclick="updateStatus(<?php echo $reg['id']; ?>)"><i class="fas fa-edit"></i></button>
                                        <button class="action-btn" onclick="deleteRegistration(<?php echo $reg['id']; ?>)"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Products Tab -->
            <div id="products-tab" class="tab-content" style="display:none;">
                <div class="dashboard-section">
                    <h3 class="section-title">Aktivitas Management</h3>
                    <button class="btn-add" onclick="showAddModal('product')"><i class="fas fa-plus"></i> Add Aktivitas</button>
                    <div class="data-table">
                        <table>
                            <thead><tr><th>Image</th><th>Title</th><th>Badge</th><th>Order</th><th>Actions</th></tr></thead>
                            <tbody id="products-list">
                                <?php foreach($products as $product): ?>
                                <tr data-id="<?php echo $product['id']; ?>" data-type="product">
                                    <td><img src="../<?php echo $product['image_url']; ?>" width="50" style="border-radius: 10px;"></td>
                                    <td><?php echo htmlspecialchars($product['title']); ?></td>
                                    <td><span class="status-badge"><?php echo $product['badge']; ?></span></td>
                                    <td><?php echo $product['display_order']; ?></td>
                                    <td>
                                        <button class="action-btn" onclick="editItem('product', <?php echo $product['id']; ?>)"><i class="fas fa-edit"></i></button>
                                        <button class="action-btn" onclick="deleteItem('product', <?php echo $product['id']; ?>)"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Services Tab -->
            <div id="services-tab" class="tab-content" style="display:none;">
                <div class="dashboard-section">
                    <h3 class="section-title">Program Management</h3>
                    <button class="btn-add" onclick="showAddModal('service')"><i class="fas fa-plus"></i> Add Program</button>
                    <div class="data-table">
                        <table>
                            <thead><tr><th>Icon</th><th>Title</th><th>Description</th><th>Order</th><th>Actions</th></tr></thead>
                            <tbody id="services-list">
                                <?php foreach($services as $service): ?>
                                <tr data-id="<?php echo $service['id']; ?>" data-type="service">
                                    <td><i class="fas <?php echo $service['icon_class']; ?>"></i></td>
                                    <td><?php echo htmlspecialchars($service['title']); ?></td>
                                    <td><?php echo substr(htmlspecialchars($service['description']), 0, 50); ?>...</td>
                                    <td><?php echo $service['display_order']; ?></td>
                                    <td>
                                        <button class="action-btn" onclick="editItem('service', <?php echo $service['id']; ?>)"><i class="fas fa-edit"></i></button>
                                        <button class="action-btn" onclick="deleteItem('service', <?php echo $service['id']; ?>)"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Testimonials Tab -->
            <div id="testimonials-tab" class="tab-content" style="display:none;">
                <div class="dashboard-section">
                    <h3 class="section-title">Testimoni Management</h3>
                    <button class="btn-add" onclick="showAddModal('testimonial')"><i class="fas fa-plus"></i> Add Testimoni</button>
                    <div class="data-table">
                        <table>
                            <thead><tr><th>Author</th><th>Content</th><th>Rating</th><th>Order</th><th>Actions</th></tr></thead>
                            <tbody id="testimonials-list">
                                <?php foreach($testimonials as $testimonial): ?>
                                <tr data-id="<?php echo $testimonial['id']; ?>" data-type="testimonial">
                                    <td><?php echo htmlspecialchars($testimonial['author_name']); ?></td>
                                    <td><?php echo substr(htmlspecialchars($testimonial['content']), 0, 50); ?>...</td>
                                    <td><?php echo str_repeat('⭐', $testimonial['rating']); ?></td>
                                    <td><?php echo $testimonial['display_order']; ?></td>
                                    <td>
                                        <button class="action-btn" onclick="editItem('testimonial', <?php echo $testimonial['id']; ?>)"><i class="fas fa-edit"></i></button>
                                        <button class="action-btn" onclick="deleteItem('testimonial', <?php echo $testimonial['id']; ?>)"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Modal untuk View Registration -->
    <div id="viewModal" class="modal">
        <div class="modal-content">
            <h3 id="modal-title">Detail Pendaftaran</h3>
            <div id="modal-detail-content"></div>
            <button class="action-btn" onclick="closeViewModal()" style="margin-top: 20px; padding: 10px 20px; background: var(--primary); border-radius: 10px;">Tutup</button>
        </div>
    </div>
    
    <!-- Modal untuk Update Status -->
    <div id="statusModal" class="modal">
        <div class="modal-content">
            <h3>Update Status Pendaftaran</h3>
            <form id="statusForm">
                <input type="hidden" id="status-reg-id">
                <select id="new-status" required>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved (Diterima)</option>
                    <option value="rejected">Rejected (Ditolak)</option>
                    <option value="interview">Interview (Panggilan Interview)</option>
                </select>
                <textarea id="status-notes" placeholder="Catatan (opsional)" rows="3"></textarea>
                <button type="submit" class="btn-add" style="width:100%; margin-top:15px;">Update Status</button>
                <button type="button" class="action-btn" onclick="closeStatusModal()" style="width:100%; margin-top:10px;">Batal</button>
            </form>
        </div>
    </div>
    
    <!-- Modal untuk Add/Edit Item -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <h3 id="modal-add-title">Add Item</h3>
            <form id="modal-form">
                <input type="hidden" id="item-id">
                <input type="hidden" id="item-type">
                <div id="modal-fields"></div>
                <button type="submit" class="btn-add" style="width:100%; margin-top:15px;">Save</button>
                <button type="button" class="action-btn" onclick="closeModal()" style="width:100%; margin-top:10px;">Cancel</button>
            </form>
        </div>
    </div>
    
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
        }
        
        // Tab switching
        document.querySelectorAll('.nav-item[data-tab]').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const tab = link.getAttribute('data-tab');
                document.querySelectorAll('.tab-content').forEach(t => t.style.display = 'none');
                document.getElementById(`${tab}-tab`).style.display = 'block';
                document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
                link.classList.add('active');
                if(window.innerWidth <= 768) toggleSidebar();
            });
        });
        
        // View Registration Detail
        async function viewRegistration(id) {
            const response = await fetch(`api/admin_actions.php?action=get_registration&id=${id}`);
            const data = await response.json();
            
            const detailHtml = `
                <div class="detail-content">
                    <div class="detail-item"><strong>Nama Lengkap:</strong><br>${data.full_name || '-'}</div>
                    <div class="detail-item"><strong>NIK:</strong><br>${data.nik || '-'}</div>
                    <div class="detail-item"><strong>Tempat/Tgl Lahir:</strong><br>${data.place_birth || '-'} / ${data.date_birth || '-'}</div>
                    <div class="detail-item"><strong>Jenis Kelamin:</strong><br>${data.gender || '-'}</div>
                    <div class="detail-item"><strong>Telepon:</strong><br>${data.phone || '-'}</div>
                    <div class="detail-item"><strong>Email:</strong><br>${data.email || '-'}</div>
                    <div class="detail-item"><strong>Alamat:</strong><br>${data.address || '-'}</div>
                    <div class="detail-item"><strong>Pendidikan Terakhir:</strong><br>${data.last_education || '-'}</div>
                    <div class="detail-item"><strong>Program Pilihan:</strong><br>${data.program_choice || '-'}</div>
                    <div class="detail-item"><strong>Kemampuan Baca Quran:</strong><br>${data.can_read_quran || '-'}</div>
                    <div class="detail-item"><strong>Jumlah Hafalan:</strong><br>${data.memorization_juz || 0} Juz</div>
                    <div class="detail-item"><strong>Nama Orang Tua:</strong><br>${data.parent_name || '-'}</div>
                    <div class="detail-item"><strong>Telepon Orang Tua:</strong><br>${data.parent_phone || '-'}</div>
                    <div class="detail-item"><strong>Pekerjaan Orang Tua:</strong><br>${data.parent_occupation || '-'}</div>
                    <div class="detail-item"><strong>Status:</strong><br><span class="status-badge status-${data.status}">${data.status}</span></div>
                    <div class="detail-item"><strong>Tanggal Daftar:</strong><br>${data.registration_date || '-'}</div>
                    <div class="detail-item"><strong>Catatan:</strong><br>${data.notes || '-'}</div>
                </div>
            `;
            document.getElementById('modal-detail-content').innerHTML = detailHtml;
            document.getElementById('viewModal').style.display = 'flex';
        }
        
        function closeViewModal() {
            document.getElementById('viewModal').style.display = 'none';
        }
        
        // Update Status
        let currentRegId = null;
        
        async function updateStatus(id) {
            currentRegId = id;
            const response = await fetch(`api/admin_actions.php?action=get_registration&id=${id}`);
            const data = await response.json();
            
            document.getElementById('status-reg-id').value = id;
            document.getElementById('new-status').value = data.status;
            document.getElementById('status-notes').value = data.notes || '';
            document.getElementById('statusModal').style.display = 'flex';
        }
        
        document.getElementById('statusForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = document.getElementById('status-reg-id').value;
            const status = document.getElementById('new-status').value;
            const notes = document.getElementById('status-notes').value;
            
            const response = await fetch('api/admin_actions.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `action=update_status&id=${id}&status=${status}&notes=${encodeURIComponent(notes)}`
            });
            const result = await response.json();
            
            if(result.success) {
                alert(result.message);
                location.reload();
            } else {
                alert('Error: ' + result.message);
            }
        });
        
        function closeStatusModal() {
            document.getElementById('statusModal').style.display = 'none';
        }
        
        // Delete Registration
        async function deleteRegistration(id) {
            if(confirm('Apakah Anda yakin ingin menghapus pendaftaran ini?')) {
                const response = await fetch('api/admin_actions.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `action=delete_registration&id=${id}`
                });
                const result = await response.json();
                if(result.success) {
                    alert(result.message);
                    location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            }
        }
        
        // Product/Service/Testimonial CRUD functions
        function showAddModal(type) {
            document.getElementById('modal-add-title').innerText = 'Add New ' + type.charAt(0).toUpperCase() + type.slice(1);
            document.getElementById('item-type').value = type;
            document.getElementById('item-id').value = '';
            
            let fields = '';
            if(type === 'product') {
                fields = `
                    <input type="text" name="title" placeholder="Title" required>
                    <input type="text" name="image_url" placeholder="Image URL (e.g., img/photo.jpg)" required>
                    <input type="text" name="badge" placeholder="Badge (HOT/NEW/SALE)">
                    <input type="text" name="link_url" placeholder="Link URL">
                    <input type="number" name="display_order" placeholder="Display Order" value="0">
                `;
            } else if(type === 'service') {
                fields = `
                    <input type="text" name="icon_class" placeholder="Icon Class (e.g., fa-cubes)" required>
                    <input type="text" name="title" placeholder="Title" required>
                    <textarea name="description" placeholder="Description" required></textarea>
                    <input type="number" name="display_order" placeholder="Display Order" value="0">
                `;
            } else if(type === 'testimonial') {
                fields = `
                    <input type="text" name="author_name" placeholder="Author Name" required>
                    <input type="text" name="author_avatar" placeholder="Avatar Initial (1 letter)">
                    <textarea name="content" placeholder="Testimonial Content" required></textarea>
                    <select name="rating">
                        <option value="5">⭐⭐⭐⭐⭐</option>
                        <option value="4">⭐⭐⭐⭐</option>
                        <option value="3">⭐⭐⭐</option>
                        <option value="2">⭐⭐</option>
                        <option value="1">⭐</option>
                    </select>
                    <input type="number" name="display_order" placeholder="Display Order" value="0">
                `;
            }
            document.getElementById('modal-fields').innerHTML = fields;
            document.getElementById('modal').style.display = 'flex';
        }
        
        function closeModal() {
            document.getElementById('modal').style.display = 'none';
        }
        
        document.getElementById('modal-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const type = document.getElementById('item-type').value;
            const id = document.getElementById('item-id').value;
            
            formData.append('action', id ? 'update' : 'add');
            formData.append('type', type);
            if(id) formData.append('id', id);
            
            const response = await fetch('api/admin_actions.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            if(result.success) {
                alert(result.message);
                location.reload();
            } else {
                alert('Error: ' + result.message);
            }
        });
        
        async function editItem(type, id) {
            const response = await fetch(`api/admin_actions.php?action=get&type=${type}&id=${id}`);
            const data = await response.json();
            
            document.getElementById('modal-add-title').innerText = 'Edit ' + type.charAt(0).toUpperCase() + type.slice(1);
            document.getElementById('item-type').value = type;
            document.getElementById('item-id').value = id;
            
            let fields = '';
            if(type === 'product') {
                fields = `
                    <input type="text" name="title" value="${data.title}" placeholder="Title" required>
                    <input type="text" name="image_url" value="${data.image_url}" placeholder="Image URL" required>
                    <input type="text" name="badge" value="${data.badge}" placeholder="Badge">
                    <input type="text" name="link_url" value="${data.link_url}" placeholder="Link URL">
                    <input type="number" name="display_order" value="${data.display_order}" placeholder="Display Order">
                `;
            } else if(type === 'service') {
                fields = `
                    <input type="text" name="icon_class" value="${data.icon_class}" placeholder="Icon Class" required>
                    <input type="text" name="title" value="${data.title}" placeholder="Title" required>
                    <textarea name="description" placeholder="Description" required>${data.description}</textarea>
                    <input type="number" name="display_order" value="${data.display_order}" placeholder="Display Order">
                `;
            } else if(type === 'testimonial') {
                fields = `
                    <input type="text" name="author_name" value="${data.author_name}" placeholder="Author Name" required>
                    <input type="text" name="author_avatar" value="${data.author_avatar}" placeholder="Avatar Initial">
                    <textarea name="content" placeholder="Content" required>${data.content}</textarea>
                    <select name="rating">
                        <option value="5" ${data.rating == 5 ? 'selected' : ''}>⭐⭐⭐⭐⭐</option>
                        <option value="4" ${data.rating == 4 ? 'selected' : ''}>⭐⭐⭐⭐</option>
                        <option value="3" ${data.rating == 3 ? 'selected' : ''}>⭐⭐⭐</option>
                        <option value="2" ${data.rating == 2 ? 'selected' : ''}>⭐⭐</option>
                        <option value="1" ${data.rating == 1 ? 'selected' : ''}>⭐</option>
                    </select>
                    <input type="number" name="display_order" value="${data.display_order}" placeholder="Display Order">
                `;
            }
            document.getElementById('modal-fields').innerHTML = fields;
            document.getElementById('modal').style.display = 'flex';
        }
        
        async function deleteItem(type, id) {
            if(confirm('Are you sure you want to delete this item?')) {
                const response = await fetch('api/admin_actions.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `action=delete&type=${type}&id=${id}`
                });
                const result = await response.json();
                if(result.success) {
                    alert(result.message);
                    location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            }
        }
        
        window.onclick = (event) => {
            if(event.target === document.getElementById('modal')) closeModal();
            if(event.target === document.getElementById('viewModal')) closeViewModal();
            if(event.target === document.getElementById('statusModal')) closeStatusModal();
        }
    </script>
</body>
</html>