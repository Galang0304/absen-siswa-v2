<?php
session_start();
require_once 'config/koneksi.php';
require_once 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// Cek apakah user sudah login
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        if (!isset($_FILES['file']['tmp_name'])) {
            throw new Exception('Tidak ada file yang diunggah');
        }

        $inputFileName = $_FILES['file']['tmp_name'];
        $spreadsheet = IOFactory::load($inputFileName);
        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow();

        // Debug: Cek jumlah baris
        error_log("Total baris: " . $highestRow);

        $pdo->beginTransaction();
        $success_count = 0;

        // Loop through rows, starting from row 8 (skip header dan petunjuk)
        for ($row = 8; $row <= $highestRow; $row++) {
            $nis = trim($worksheet->getCellByColumnAndRow(1, $row)->getValue());
            $nisn = trim($worksheet->getCellByColumnAndRow(2, $row)->getValue());
            $nama = trim($worksheet->getCellByColumnAndRow(3, $row)->getValue());
            $jenis_kelamin = trim($worksheet->getCellByColumnAndRow(4, $row)->getValue());
            $kelas_id = trim($worksheet->getCellByColumnAndRow(5, $row)->getValue());

            // Debug: Log data yang dibaca
            error_log("Baris $row: NIS=$nis, NISN=$nisn, Nama=$nama, JK=$jenis_kelamin, Kelas=$kelas_id");

            // Skip baris kosong atau contoh
            if (empty($nis) || empty($nisn) || empty($nama)) {
                continue;
            }

            // Skip jika ini adalah baris contoh (dengan background hijau)
            if ($row <= 10 && ($nis == '00012023' || $nis == '00022023' || $nis == '00032023')) {
                continue;
            }

            // Validate jenis_kelamin
            if (!in_array(strtoupper($jenis_kelamin), ['L', 'P'])) {
                throw new Exception("Jenis kelamin tidak valid pada baris $row. Gunakan L atau P");
            }

            // Validate kelas_id
            if (empty($kelas_id)) {
                throw new Exception("ID Kelas tidak boleh kosong pada baris $row");
            }

            // Check if kelas exists
            $stmt = $pdo->prepare("SELECT id FROM kelas WHERE id = ?");
            $stmt->execute([$kelas_id]);
            if (!$stmt->fetch()) {
                throw new Exception("ID Kelas tidak valid pada baris $row");
            }

            // Check for duplicate NIS/NISN
            $stmt = $pdo->prepare("SELECT id FROM siswa WHERE nis = ? OR nisn = ?");
            $stmt->execute([$nis, $nisn]);
            if ($stmt->fetch()) {
                throw new Exception("NIS atau NISN sudah terdaftar pada baris $row");
            }

            // Insert data
            $stmt = $pdo->prepare("INSERT INTO siswa (nis, nisn, nama, jenis_kelamin, kelas_id) VALUES (?, ?, ?, ?, ?)");
            $result = $stmt->execute([$nis, $nisn, $nama, strtoupper($jenis_kelamin), $kelas_id]);
            
            if ($result) {
                $success_count++;
                error_log("Berhasil insert data: NIS=$nis");
            } else {
                error_log("Gagal insert data: NIS=$nis");
                throw new Exception("Gagal menyimpan data pada baris $row");
            }
        }

        if ($success_count > 0) {
            $pdo->commit();
            $_SESSION['success'] = "Berhasil mengimport $success_count data siswa";
        } else {
            throw new Exception("Tidak ada data yang diimport");
        }

    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Error import: " . $e->getMessage());
        $_SESSION['error'] = 'Gagal import data: ' . $e->getMessage();
    }
}

header('Location: siswa.php');
exit;
?> 