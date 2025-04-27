<?php
session_start();

// Cek apakah user sudah login
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'config/koneksi.php';
require_once __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

try {
    // Buat spreadsheet baru
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Set judul worksheet
    $sheet->setTitle('Template Import Siswa');

    // Tambahkan petunjuk pengisian
    $sheet->setCellValue('A1', 'PETUNJUK PENGISIAN:');
    $sheet->mergeCells('A1:E1');
    $sheet->setCellValue('A2', '1. Lihat ID Kelas di sheet "Informasi Kelas"');
    $sheet->mergeCells('A2:E2');
    $sheet->setCellValue('A3', '2. Jenis Kelamin diisi dengan "L" atau "P"');
    $sheet->mergeCells('A3:E3');
    $sheet->setCellValue('A4', '3. Pastikan NIS dan NISN bersifat unik (tidak boleh sama dengan data yang sudah ada)');
    $sheet->mergeCells('A4:E4');
    $sheet->setCellValue('A5', '4. Format NIS dan NISN harus berupa text (klik kanan pada sel -> Format Cells -> Text)');
    $sheet->mergeCells('A5:E5');
    
    // Style untuk petunjuk
    $petunjukStyle = [
        'font' => [
            'bold' => true,
            'size' => 11,
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'FFE699'],
        ],
    ];
    $sheet->getStyle('A1:E5')->applyFromArray($petunjukStyle);
    
    // Set header kolom mulai dari baris 7
    $headers = ['NIS', 'NISN', 'Nama Lengkap', 'Jenis Kelamin (L/P)', 'ID Kelas'];
    
    // Styling untuk header
    $headerStyle = [
        'font' => [
            'bold' => true,
            'color' => ['rgb' => 'FFFFFF'],
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => '4A90E2'],
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER,
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
            ],
        ],
    ];

    // Tulis header dan terapkan style
    foreach (range('A', 'E') as $index => $column) {
        $sheet->setCellValue($column . '7', $headers[$index]);
        $sheet->getColumnDimension($column)->setAutoSize(true);
    }
    $sheet->getStyle('A7:E7')->applyFromArray($headerStyle);

    // Tambahkan contoh data
    $contohData = [
        ['00012023', '3169255540', 'Ahmad Setiawan', 'L', '4'],  // Kelas 1
        ['00022023', '3169255541', 'Siti Nurhaliza', 'P', '5'],  // Kelas 4
        ['00032023', '3169255542', 'Budi Santoso', 'L', '6'],    // Kelas 3
        ['00042023', '3169255543', 'Rina Melati', 'P', '7'],     // Kelas 5
        ['00052023', '3169255544', 'Muhammad Rizki', 'L', '8'],  // Kelas 2
        ['00062023', '3169255545', 'Putri Ayu', 'P', '9']        // Kelas 6
    ];

    $row = 8;
    foreach ($contohData as $data) {
        $col = 'A';
        foreach ($data as $value) {
            $sheet->setCellValue($col . $row, $value);
            // Set format text untuk kolom NIS dan NISN
            if ($col == 'A' || $col == 'B') {
                $sheet->getStyle($col . $row)->getNumberFormat()->setFormatCode('@');
            }
            $col++;
        }
        $row++;
    }

    // Style untuk contoh data
    $contohStyle = [
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'E8F5E9'],
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
            ],
        ],
    ];
    $sheet->getStyle('A8:E13')->applyFromArray($contohStyle);

    // Tambahkan catatan contoh
    $sheet->setCellValue('A14', 'Catatan: Data di atas hanya contoh. Hapus atau timpa dengan data yang sebenarnya.');
    $sheet->mergeCells('A14:E14');
    $sheet->getStyle('A14')->getFont()->setItalic(true);
    $sheet->getStyle('A14')->getFont()->setSize(10);
    $sheet->getStyle('A14')->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('808080'));

    // Tambahkan informasi ID Kelas
    $sheet->setCellValue('G7', 'REFERENSI ID KELAS:');
    $sheet->getStyle('G7')->getFont()->setBold(true);
    
    $kelasInfo = [
        ['4', 'Kelas 1'],
        ['5', 'Kelas 4'],
        ['6', 'Kelas 3'],
        ['7', 'Kelas 5'],
        ['8', 'Kelas 2'],
        ['9', 'Kelas 6']
    ];
    
    $row = 8;
    foreach ($kelasInfo as $info) {
        $sheet->setCellValue('G' . $row, $info[0]);
        $sheet->setCellValue('H' . $row, $info[1]);
        $row++;
    }
    
    // Style untuk informasi kelas
    $sheet->getStyle('G7:H' . ($row-1))->applyFromArray([
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
            ],
        ],
    ]);
    $sheet->getStyle('G8:H' . ($row-1))->getFill()
        ->setFillType(Fill::FILL_SOLID)
        ->setStartColor(new \PhpOffice\PhpSpreadsheet\Style\Color('F8F9FA'));
    
    $sheet->getColumnDimension('G')->setWidth(15);
    $sheet->getColumnDimension('H')->setWidth(20);

    // Tambahkan validasi untuk kolom Jenis Kelamin
    $validation = $sheet->getCell('D8')->getDataValidation();
    $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
    $validation->setFormula1('"L,P"');
    $validation->setAllowBlank(false);
    $validation->setShowDropDown(true);
    $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
    $validation->setErrorTitle('Input Error');
    $validation->setError('Pilih "L" untuk Laki-laki atau "P" untuk Perempuan');

    // Copy validasi ke 100 baris ke bawah
    for ($i = 9; $i <= 100; $i++) {
        $validation = $sheet->getCell('D' . $i)->getDataValidation();
        $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $validation->setFormula1('"L,P"');
        $validation->setAllowBlank(false);
        $validation->setShowDropDown(true);
    }

    // Tambahkan sheet informasi kelas
    $infoSheet = $spreadsheet->createSheet();
    $infoSheet->setTitle('Informasi Kelas');
    
    // Tambahkan judul di sheet informasi
    $infoSheet->setCellValue('A1', 'INFORMASI ID KELAS');
    $infoSheet->mergeCells('A1:C1');
    $infoSheet->getStyle('A1:C1')->applyFromArray([
        'font' => ['bold' => true, 'size' => 14],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'FFE699'],
        ],
    ]);

    // Tambahkan catatan penting
    $infoSheet->setCellValue('A2', 'PENTING: Gunakan ID Kelas yang sesuai saat mengisi data siswa!');
    $infoSheet->mergeCells('A2:C2');
    $infoSheet->getStyle('A2')->applyFromArray([
        'font' => ['bold' => true, 'color' => ['rgb' => 'FF0000']],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
    ]);
    
    // Header untuk informasi kelas
    $infoSheet->setCellValue('A4', 'ID Kelas');
    $infoSheet->setCellValue('B4', 'Nama Kelas');
    $infoSheet->setCellValue('C4', 'Wali Kelas');

    // Ambil data kelas beserta wali kelasnya
    $stmt = $pdo->query("
        SELECT k.id, k.nama_kelas, COALESCE(g.nama, '-') as wali_kelas 
        FROM kelas k 
        LEFT JOIN guru g ON k.guru_id = g.id 
        ORDER BY k.nama_kelas
    ");
    $kelas_list = $stmt->fetchAll();

    // Style untuk header informasi
    $headerStyle = [
        'font' => [
            'bold' => true,
            'color' => ['rgb' => 'FFFFFF'],
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => '4A90E2'],
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER,
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
            ],
        ],
    ];
    $infoSheet->getStyle('A4:C4')->applyFromArray($headerStyle);

    // Set lebar kolom
    $infoSheet->getColumnDimension('A')->setWidth(15);
    $infoSheet->getColumnDimension('B')->setWidth(30);
    $infoSheet->getColumnDimension('C')->setWidth(35);

    // Isi data kelas
    $row = 5;
    foreach ($kelas_list as $kelas) {
        $infoSheet->setCellValue('A' . $row, $kelas['id']);
        $infoSheet->setCellValue('B' . $row, $kelas['nama_kelas']);
        $infoSheet->setCellValue('C' . $row, $kelas['wali_kelas']);
        
        // Style untuk baris data
        $infoSheet->getStyle('A'.$row.':C'.$row)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);
        
        // Highlight baris alternate
        if ($row % 2 == 0) {
            $infoSheet->getStyle('A'.$row.':C'.$row)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->setStartColor(new \PhpOffice\PhpSpreadsheet\Style\Color('F5F5F5'));
        }
        
        $row++;
    }

    // Tambahkan catatan penggunaan
    $noteRow = $row + 1;
    $infoSheet->setCellValue('A' . $noteRow, 'Catatan:');
    $infoSheet->mergeCells('A'.$noteRow.':C'.$noteRow);
    $infoSheet->getStyle('A'.$noteRow)->getFont()->setBold(true);

    $notes = [
        '1. Gunakan ID Kelas yang tertera di kolom "ID Kelas" untuk mengisi data siswa.',
        '2. Pastikan ID Kelas yang digunakan sesuai dengan kelas yang dituju.',
        '3. Jika ragu, tanyakan kepada admin sistem.',
    ];

    foreach ($notes as $index => $note) {
        $noteRow++;
        $infoSheet->setCellValue('A' . $noteRow, $note);
        $infoSheet->mergeCells('A'.$noteRow.':C'.$noteRow);
        $infoSheet->getStyle('A'.$noteRow)->getFont()->setItalic(true);
    }

    // Kembali ke sheet pertama
    $spreadsheet->setActiveSheetIndex(0);

    // Bersihkan output buffer
    if (ob_get_length()) ob_clean();
    
    // Set header untuk download
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Template_Import_Siswa.xlsx"');
    header('Cache-Control: max-age=0');
    header('Cache-Control: max-age=1');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
    header('Cache-Control: cache, must-revalidate');
    header('Pragma: public');

    // Simpan file
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
} catch (Exception $e) {
    // Tampilkan error
    echo "Terjadi kesalahan: " . $e->getMessage();
    exit;
}
?> 