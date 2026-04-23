<?php
// index.php - Updated with registration form
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Fetch data from database
$services = $db->query("SELECT * FROM services WHERE is_active = 1 ORDER BY display_order ASC")->fetchAll(PDO::FETCH_ASSOC);
$products = $db->query("SELECT * FROM products WHERE is_active = 1 ORDER BY display_order ASC")->fetchAll(PDO::FETCH_ASSOC);
$testimonials = $db->query("SELECT * FROM testimonials WHERE is_active = 1 ORDER BY display_order ASC")->fetchAll(PDO::FETCH_ASSOC);
$settings = [];
$stmt = $db->query("SELECT setting_key, setting_value FROM settings");
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $settings[$row['setting_key']] = $row['setting_value'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $settings['site_title'] ?? 'Rumah Tahfidzh Hikmah'; ?> - Pendaftaran Santri Baru</title>
    <link rel="icon" href="img/gambar_logo_ari-removebg-preview.png">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        :root {
            --primary: #0fc518d3;
            --primary-dark: #05a10de1;
            --dark: #0f1a24;
            --text-light: #ffffff;
            --text-gray: #e0e0e0;
            --glass-bg: rgba(255, 255, 255, 0.03);
            --glass-border: rgba(255, 255, 255, 0.05);
            --gradient-primary: linear-gradient(135deg, #05af1cd3, #00cc22f1);
        }
        body {
            font-family: 'Space Grotesk', sans-serif;
            background: var(--dark);
            color: var(--text-light);
            line-height: 1.6;
            overflow-x: hidden;
        }
        header {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            width: 90%;
            max-width: 1400px;
            padding: 15px 30px;
            background: rgba(15, 26, 36, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 100px;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo-container {
            display: flex;
            align-items: center;
            gap: 15px;
            cursor: pointer;
        }
        .logo-container img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
            background: var(--gradient-primary);
            padding: 2px;
        }
        .logo-text {
            font-size: 1.3rem;
            font-weight: 700;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        nav {
            display: flex;
            gap: 5px;
        }
        nav a {
            padding: 10px 20px;
            color: var(--text-gray);
            text-decoration: none;
            font-weight: 500;
            border-radius: 50px;
            transition: 0.3s;
        }
        nav a:hover, nav a.active {
            background: var(--gradient-primary);
            color: white;
        }
        .showcase {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            position: relative;
            background-image: url('img/gambar aku aja.webp');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        .showcase::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(15, 26, 36, 0.85), rgba(26, 38, 52, 0.9));
            z-index: 1;
        }
        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 1000px;
        }
        .hero-badge {
            display: inline-block;
            padding: 8px 20px;
            background: rgba(255,255,255,0.1);
            border-radius: 50px;
            margin-bottom: 30px;
            font-size: 0.9rem;
        }
        .showcase h1 {
            font-size: clamp(3rem, 8vw, 5rem);
            font-weight: 700;
            margin-bottom: 20px;
        }
        .showcase h1 span {
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .hero-description {
            font-size: 1.2rem;
            color: var(--text-gray);
            max-width: 600px;
            margin: 0 auto 40px;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 30px;
        }
        .section {
            padding: 100px 0;
        }
        .section-header {
            text-align: center;
            margin-bottom: 60px;
        }
        .section-header h2 {
            font-size: clamp(2rem, 5vw, 3rem);
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: inline-block;
        }
        .section-header p {
            color: var(--text-gray);
            margin-top: 15px;
            font-size: 1.1rem;
        }
        .services, .products, .reviews {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
        }
        .service-box, .product-box, .review-box {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 30px;
            padding: 50px 30px;
            transition: 0.3s;
            text-align: center;
        }
        .service-box:hover, .product-box:hover, .review-box:hover {
            transform: translateY(-15px);
            border-color: var(--primary);
            box-shadow: 0 0 30px rgba(3, 97, 19, 0.89);
        }
        .service-icon {
            width: 90px;
            height: 90px;
            background: var(--gradient-primary);
            border-radius: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: white;
            margin: 0 auto 30px;
            transform: rotate(45deg);
        }
        .service-icon i {
            transform: rotate(-45deg);
        }
        .product-image {
            height: 250px;
            overflow: hidden;
            border-radius: 20px;
            position: relative;
        }
        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: 0.5s;
        }
        .product-box:hover .product-image img {
            transform: scale(1.1);
        }
        .product-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            background: var(--gradient-primary);
            padding: 8px 16px;
            border-radius: 50px;
            font-weight: 600;
        }
        .btn {
            padding: 14px 35px;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            background: var(--gradient-primary);
            color: white;
            border: none;
            transition: 0.3s;
        }
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(41, 255, 77, 0.5);
        }
        
        /* Registration Form Styles */
        .registration-form {
            max-width: 900px;
            margin: 0 auto;
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 30px;
            padding: 50px;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--primary);
            font-weight: 500;
        }
        .form-group label .required {
            color: #ff4444;
        }
        .form-group input, 
        .form-group select, 
        .form-group textarea {
            width: 100%;
            padding: 14px 18px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            color: white;
            font-size: 1rem;
            transition: 0.3s;
        }
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 15px rgba(15, 197, 24, 0.3);
        }
        .form-group select option {
            background: var(--dark);
        }
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }
        .form-section-title {
            font-size: 1.3rem;
            color: var(--primary);
            margin: 30px 0 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--primary);
            display: inline-block;
        }
        .radio-group {
            display: flex;
            gap: 20px;
            margin-top: 10px;
        }
        .radio-group label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-weight: normal;
        }
        .radio-group input[type="radio"] {
            width: auto;
            cursor: pointer;
        }
        .btn-submit {
            width: 100%;
            padding: 16px;
            font-size: 1.1rem;
            margin-top: 20px;
        }
        .success-message {
            background: rgba(15, 197, 24, 0.2);
            border: 1px solid var(--primary);
            padding: 15px 20px;
            border-radius: 15px;
            margin-bottom: 20px;
            text-align: center;
            color: var(--primary);
            animation: slideIn 0.5s ease;
        }
        .error-message {
            background: rgba(255, 68, 68, 0.2);
            border: 1px solid #ff4444;
            padding: 15px 20px;
            border-radius: 15px;
            margin-bottom: 20px;
            text-align: center;
            color: #ff8888;
        }
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .footer {
            background: linear-gradient(135deg, #0a141c, #0f1a24);
            border-top: 1px solid rgba(15, 197, 24, 0.2);
            padding: 60px 0 30px;
            text-align: center;
            margin-top: 80px;
        }
        .mobile-menu-btn {
            display: none;
            font-size: 1.5rem;
            cursor: pointer;
        }
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }
            nav {
                display: none;
                position: absolute;
                top: 80px;
                left: 20px;
                right: 20px;
                background: var(--dark);
                flex-direction: column;
                border-radius: 20px;
                padding: 20px;
            }
            nav.active {
                display: flex;
            }
            .mobile-menu-btn {
                display: flex;
            }
            .services, .products, .reviews {
                grid-template-columns: 1fr;
            }
            .registration-form {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <header id="header">
        <div class="logo-container" onclick="window.scrollTo({top: 0, behavior: 'smooth'})">
            <img src="img/gambar_logo_ari-removebg-preview.png" alt="Logo">
            <span class="logo-text"><?php echo $settings['footer_text'] ?? 'generasi Qur\'ani indonesia'; ?></span>
        </div>
        <nav id="navMenu">
            <a href="#home" class="active">Home</a>
            <a href="#services">Layanan</a>
            <a href="#products">Aktivitas</a>
            <a href="#reviews">Testimoni</a>
            <a href="#about">Tentang</a>
            <a href="#registration">Daftar</a>
            <a href="admin/login.php">Admin</a>
        </nav>
        <div class="mobile-menu-btn" onclick="toggleMobileMenu()">
            <i class="fas fa-bars"></i>
        </div>
    </header>

    <section class="showcase" id="home">
        <div class="hero-content">
            <span class="hero-badge" data-aos="fade-up"><?php echo $settings['hero_badge'] ?? 'بسم الله الرحمن الرحيم'; ?></span>
            <h1 data-aos="fade-up"><?php echo $settings['hero_title'] ?? 'Welcome to <span>quran school</span>'; ?></h1>
            <p class="hero-description" data-aos="fade-up"><?php echo $settings['site_description'] ?? 'Menjadikan santri yang sukses dan berpegang teguh kepada assunnah dan al quran'; ?></p>
            <div data-aos="fade-up">
                <a href="#registration" class="btn">Daftar Sekarang</a>
            </div>
        </div>
    </section>

    <div class="container">
        <section class="section" id="services">
            <div class="section-header" data-aos="fade-up">
                <h2>Program Unggulan</h2>
                <p>Berbagai program yang kami tawarkan untuk mencetak generasi Qur'ani</p>
            </div>
            <div class="services">
                <?php foreach($services as $service): ?>
                <div class="service-box" data-aos="fade-up">
                    <div class="service-icon">
                        <i class="fas <?php echo $service['icon_class']; ?>"></i>
                    </div>
                    <h3><?php echo htmlspecialchars($service['title']); ?></h3>
                    <p><?php echo htmlspecialchars($service['description']); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="section" id="products">
            <div class="section-header" data-aos="fade-up">
                <h2>Aktivitas Santri</h2>
                <p>Kegiatan sehari-hari santri di pondok pesantren</p>
            </div>
            <div class="products">
                <?php foreach($products as $product): ?>
                <div class="product-box" data-aos="fade-up">
                    <div class="product-image">
                        <img src="<?php echo $product['image_url']; ?>" alt="<?php echo htmlspecialchars($product['title']); ?>">
                        <?php if($product['badge']): ?>
                        <div class="product-badge"><?php echo $product['badge']; ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="product-content" style="padding: 25px;">
                        <h3><?php echo htmlspecialchars($product['title']); ?></h3>
                        <a href="<?php echo $product['link_url'] ?: '#'; ?>" target="_blank">
                            <button class="btn" style="margin-top: 15px;">Lihat Detail</button>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="section" id="reviews">
            <div class="section-header" data-aos="fade-up">
                <h2>Testimoni Santri & Orang Tua</h2>
                <p>Apa kata mereka tentang pengalaman di pondok kami</p>
            </div>
            <div class="reviews">
                <?php foreach($testimonials as $testimonial): ?>
                <div class="review-box" data-aos="fade-up">
                    <div class="review-quote" style="font-size: 3rem; color: var(--primary); opacity: 0.5;">❝</div>
                    <p>"<?php echo htmlspecialchars($testimonial['content']); ?>"</p>
                    <div class="review-author" style="display: flex; align-items: center; gap: 15px; margin-top: 20px;">
                        <div class="review-avatar" style="width: 50px; height: 50px; background: var(--gradient-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                            <?php echo $testimonial['author_avatar'] ?: substr($testimonial['author_name'], 0, 1); ?>
                        </div>
                        <h3><?php echo htmlspecialchars($testimonial['author_name']); ?></h3>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="section" id="about">
            <div class="section-header" data-aos="fade-up">
                <h2>Tentang Kami</h2>
            </div>
            <p data-aos="fade-up" style="max-width: 900px; margin: 0 auto; text-align: center; background: var(--glass-bg); padding: 50px; border-radius: 30px;">
                Rumah Tahfidzh Hikmah adalah pondok pesantren tahfidz Al-Qur'an yang berkomitmen mencetak generasi Qur'ani 
                yang berpegang teguh kepada Al-Qur'an dan As-Sunnah. Kami menyediakan program unggulan menghafal Al-Qur'an 
                dengan metode yang mudah dan menyenangkan, didukung oleh tenaga pengajar yang profesional dan berpengalaman. 
                Lingkungan yang nyaman, asri, dan kondusif untuk fokus menghafal Al-Qur'an.
            </p>
        </section>

        <!-- Registration Section -->
        <section class="section" id="registration">
            <div class="section-header" data-aos="fade-up">
                <h2>Pendaftaran Santri Baru</h2>
                <p>Isi formulir di bawah ini untuk mendaftar menjadi santi</p>
            </div>
            
            <div class="registration-form" data-aos="fade-up">
                <div id="form-message"></div>
                
                <form id="registrationForm" method="POST">
                    <h3 class="form-section-title"><i class="fas fa-user"></i> Data Pribadi</h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nama Lengkap <span class="required">*</span></label>
                            <input type="text" name="full_name" placeholder="Nama lengkap sesuai KTP" required>
                        </div>
                        <div class="form-group">
                            <label>NIK (Opsional)</label>
                            <input type="text" name="nik" placeholder="Nomor Induk Kependudukan" maxlength="16">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Tempat Lahir</label>
                            <input type="text" name="place_birth" placeholder="Tempat lahir">
                        </div>
                        <div class="form-group">
                            <label>Tanggal Lahir</label>
                            <input type="date" name="date_birth">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Jenis Kelamin <span class="required">*</span></label>
                            <div class="radio-group">
                                <label><input type="radio" name="gender" value="Laki-laki" required> Laki-laki</label>
                                <label><input type="radio" name="gender" value="Perempuan" required> Perempuan</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Nomor Telepon/WA <span class="required">*</span></label>
                            <input type="tel" name="phone" placeholder="0812xxxx" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" placeholder="email@example.com">
                        </div>
                        <div class="form-group">
                            <label>Pendidikan Terakhir</label>
                            <select name="last_education">
                                <option value="">Pilih Pendidikan</option>
                                <option value="SD">SD Sederajat</option>
                                <option value="SMP">SMP Sederajat</option>
                                <option value="SMA">SMA Sederajat</option>
                                <option value="D1/D2/D3">D1/D2/D3</option>
                                <option value="S1">S1</option>
                                <option value="S2">S2</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Alamat Lengkap <span class="required">*</span></label>
                        <textarea name="address" placeholder="Alamat lengkap sesuai KTP" required></textarea>
                    </div>
                    
                    <h3 class="form-section-title"><i class="fas fa-graduation-cap"></i> Data Pendidikan & Program</h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Program Pilihan <span class="required">*</span></label>
                            <select name="program_choice" required>
                                <option value="Tahfidz Intensif">Tahfidz Intensif (Target Hafal 30 Juz)</option>
                                <option value="Tahfidz Reguler">Tahfidz Reguler (Target Hafal 10 Juz)</option>
                                <option value="Program Anak">Program Anak (Usia 7-12 Tahun)</option>
                                <option value="Program Dewasa">Program Dewasa (Usia 13-25 Tahun)</option>
                                <option value="Program Online">Program Online (Via Zoom)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Kemampuan Baca Al-Quran</label>
                            <select name="can_read_quran">
                                <option value="Belum">Belum Bisa</option>
                                <option value="Sedang Belajar">Sedang Belajar</option>
                                <option value="Ya">Sudah Bisa</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Jumlah Hafalan Saat Ini (Juz)</label>
                        <input type="number" name="memorization_juz" min="0" max="30" value="0" placeholder="0">
                    </div>
                    
                    <div class="form-group">
                        <label>Asal Sekolah/Institusi</label>
                        <input type="text" name="school_name" placeholder="Nama sekolah atau institusi terakhir">
                    </div>
                    
                    <h3 class="form-section-title"><i class="fas fa-users"></i> Data Orang Tua/Wali</h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nama Orang Tua/Wali</label>
                            <input type="text" name="parent_name" placeholder="Nama ayah/ibu/wali">
                        </div>
                        <div class="form-group">
                            <label>Nomor Telepon Orang Tua</label>
                            <input type="tel" name="parent_phone" placeholder="Nomor telepon orang tua">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Pekerjaan Orang Tua</label>
                        <input type="text" name="parent_occupation" placeholder="Pekerjaan orang tua/wali">
                    </div>
                    
                    <button type="submit" class="btn btn-submit">
                        <i class="fas fa-paper-plane"></i> Daftar Sekarang
                    </button>
                </form>
            </div>
        </section>
    </div>

    <footer class="footer">
        <div class="footer-content">
            <p>&copy; 2024 Rumah Tahfidzh Hikmah. All Rights Reserved. Made with <span class="heart">❤️</span> for Quran Generation</p>
            <p style="margin-top: 15px; font-size: 0.85rem;">Jl. Pendidikan No. 123, Kota Santri | Telp: (021) 1234567</p>
        </div>
    </footer>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({ duration: 1000, once: true });
        
        function toggleMobileMenu() {
            document.getElementById('navMenu').classList.toggle('active');
        }
        
        document.querySelectorAll('nav a').forEach(link => {
            link.addEventListener('click', function(e) {
                if(this.getAttribute('href').startsWith('#')) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if(target) {
                        window.scrollTo({ top: target.offsetTop - 100, behavior: 'smooth' });
                    }
                }
                if(window.innerWidth <= 768) toggleMobileMenu();
            });
        });
        
        // Registration Form Submission
        document.getElementById('registrationForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            const messageDiv = document.getElementById('form-message');
            
            // Clear previous messages
            messageDiv.innerHTML = '';
            
            // Show loading
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
            submitBtn.disabled = true;
            
            const formData = new FormData(this);
            
            try {
                const response = await fetch('api/register.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    messageDiv.innerHTML = `<div class="success-message">
                        <i class="fas fa-check-circle"></i> ${result.message}
                    </div>`;
                    this.reset();
                    
                    // Scroll to message
                    messageDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
                } else {
                    messageDiv.innerHTML = `<div class="error-message">
                        <i class="fas fa-exclamation-triangle"></i> ${result.message}
                    </div>`;
                }
            } catch (error) {
                messageDiv.innerHTML = `<div class="error-message">
                    <i class="fas fa-exclamation-triangle"></i> Terjadi kesalahan. Silakan coba lagi.
                </div>`;
                console.error('Error:', error);
            } finally {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        });
        
        // Smooth scroll for logo click
        document.querySelector('.logo-container').addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    </script>
</body>
</html>