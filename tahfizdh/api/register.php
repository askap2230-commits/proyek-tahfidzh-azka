<?php
// api/register.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitasi input
    $full_name = htmlspecialchars(strip_tags($_POST['full_name'] ?? ''));
    $nik = htmlspecialchars(strip_tags($_POST['nik'] ?? ''));
    $place_birth = htmlspecialchars(strip_tags($_POST['place_birth'] ?? ''));
    $date_birth = $_POST['date_birth'] ?? null;
    $gender = $_POST['gender'] ?? '';
    $phone = htmlspecialchars(strip_tags($_POST['phone'] ?? ''));
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $address = htmlspecialchars(strip_tags($_POST['address'] ?? ''));
    
    // Data Pendidikan
    $last_education = htmlspecialchars(strip_tags($_POST['last_education'] ?? ''));
    $school_name = htmlspecialchars(strip_tags($_POST['school_name'] ?? ''));
    
    // Data Program
    $program_choice = $_POST['program_choice'] ?? 'Tahfidz Intensif';
    $memorization_juz = intval($_POST['memorization_juz'] ?? 0);
    $can_read_quran = $_POST['can_read_quran'] ?? 'Belum';
    
    // Data Orang Tua
    $parent_name = htmlspecialchars(strip_tags($_POST['parent_name'] ?? ''));
    $parent_phone = htmlspecialchars(strip_tags($_POST['parent_phone'] ?? ''));
    $parent_occupation = htmlspecialchars(strip_tags($_POST['parent_occupation'] ?? ''));
    
    // Validasi
    $errors = [];
    if (empty($full_name)) $errors[] = 'Nama lengkap wajib diisi';
    if (empty($phone)) $errors[] = 'Nomor telepon wajib diisi';
    if (empty($address)) $errors[] = 'Alamat wajib diisi';
    if (empty($gender)) $errors[] = 'Jenis kelamin wajib dipilih';
    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Format email tidak valid';
    
    if (empty($errors)) {
        $database = new Database();
        $db = $database->getConnection();
        
        $query = "INSERT INTO registrations 
                  (full_name, nik, place_birth, date_birth, gender, phone, email, address, 
                   last_education, school_name, program_choice, memorization_juz, can_read_quran,
                   parent_name, parent_phone, parent_occupation, registration_date, status) 
                  VALUES 
                  (:full_name, :nik, :place_birth, :date_birth, :gender, :phone, :email, :address,
                   :last_education, :school_name, :program_choice, :memorization_juz, :can_read_quran,
                   :parent_name, :parent_phone, :parent_occupation, CURDATE(), 'pending')";
        
        $stmt = $db->prepare($query);
        
        $result = $stmt->execute([
            ':full_name' => $full_name,
            ':nik' => $nik ?: null,
            ':place_birth' => $place_birth ?: null,
            ':date_birth' => $date_birth ?: null,
            ':gender' => $gender,
            ':phone' => $phone,
            ':email' => $email ?: null,
            ':address' => $address,
            ':last_education' => $last_education ?: null,
            ':school_name' => $school_name ?: null,
            ':program_choice' => $program_choice,
            ':memorization_juz' => $memorization_juz,
            ':can_read_quran' => $can_read_quran,
            ':parent_name' => $parent_name ?: null,
            ':parent_phone' => $parent_phone ?: null,
            ':parent_occupation' => $parent_occupation ?: null
        ]);
        
        if ($result) {
            $registration_id = $db->lastInsertId();
            
            // Kirim notifikasi WhatsApp sederhana (opsional)
            // Anda bisa integrate dengan API WhatsApp nanti
            
            echo json_encode([
                'success' => true, 
                'message' => 'Pendaftaran berhasil! Kami akan menghubungi Anda dalam 1x24 jam.',
                'registration_id' => $registration_id
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menyimpan data pendaftaran']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan']);
}
?>