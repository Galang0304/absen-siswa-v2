<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Absensi Siswa</title>
    
    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Style -->
    <style>
        /* Animasi untuk navbar */
        .nav-item {
            transition: all 0.3s ease;
        }
        .nav-item:hover {
            transform: translateY(-2px);
        }
        
        /* Warna status absensi */
        .status-hadir { background-color: #10B981; }
        .status-sakit { background-color: #F59E0B; }
        .status-izin { background-color: #3B82F6; }
        .status-alpha { background-color: #EF4444; }

        /* Responsif Table Styles */
        @media (max-width: 768px) {
            .container {
                width: 100%;
                padding-right: 1rem;
                padding-left: 1rem;
                margin-right: auto;
                margin-left: auto;
            }

            .table-responsive {
                display: block;
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                -ms-overflow-style: -ms-autohiding-scrollbar;
                margin: 0 auto;
            }

            .table-responsive-stack {
                width: 100%;
                margin: 0 auto;
            }

            .table-responsive-stack tr {
                display: block;
                margin-bottom: 1rem;
                border: 1px solid #ddd;
                border-radius: 8px;
                background: #fff;
            }

            .table-responsive-stack td {
                display: block;
                text-align: left;
                padding: 0.75rem;
                border-bottom: 1px solid #edf2f7;
            }

            .table-responsive-stack td:before {
                content: attr(data-label);
                font-weight: 600;
                color: #4a5568;
                display: block;
                margin-bottom: 0.5rem;
            }

            .table-responsive-stack td:last-child {
                border-bottom: none;
            }

            .table-responsive-stack thead {
                display: none;
            }

            /* Form controls pada mobile */
            .form-control {
                width: 100%;
                margin-bottom: 0.5rem;
            }

            /* Radio buttons pada mobile */
            .radio-group {
                display: flex;
                flex-wrap: wrap;
                gap: 1rem;
                margin-top: 0.5rem;
            }

            /* Margin bottom untuk konten utama agar tidak tertutup navbar mobile */
            .pb-safe {
                padding-bottom: 5rem;
            }

            /* Container centering */
            .container {
                max-width: 100%;
                margin-left: auto;
                margin-right: auto;
            }
        }

        /* Desktop styles */
        @media (min-width: 769px) {
            .container {
                max-width: 1200px;
                margin-left: auto;
                margin-right: auto;
            }

            .table-responsive {
                margin: 0 auto;
            }

            .table-responsive-stack {
                width: 100%;
                margin: 0 auto;
            }

            .table-responsive-stack td:before {
                display: none;
            }
        }
    </style>
</head>
<body class="bg-gray-100"> 