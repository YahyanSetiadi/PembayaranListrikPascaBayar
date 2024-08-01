<?php
// Pastikan pengguna sudah login dan ada data pengguna yang dapat ditampilkan
session_start(); // Memulai sesi jika belum dimulai
if (!isset($_SESSION['username'])) {
    // Jika pengguna belum login, redirect ke halaman login
    header("Location: login.php");
    exit();
}

// Koneksi ke database
require_once 'conn/db_connect.php';

// Inisialisasi variabel
$id_pelanggan = '';
$data_tagihan = array();
$data_pelanggan = array();

// Array nama bulan
$nama_bulan = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];

// Proses pencarian jika form disubmit
if (isset($_POST['search'])) {
    $id_pelanggan = $_POST['id_pelanggan'];

    // Query untuk mengambil data dari tabel tagihan berdasarkan id_pelanggan
    $query_tagihan = "SELECT * FROM tagihan WHERE id_pelanggan = ?";
    if ($stmt = $conn->prepare($query_tagihan)) {
        $stmt->bind_param('s', $id_pelanggan);
        $stmt->execute();
        $result = $stmt->get_result();

        // Mengambil data tagihan
        while ($row = $result->fetch_assoc()) {
            $data_tagihan[] = $row;
        }

        $stmt->close();
    }

    // Query untuk mengambil data dari tabel pelanggan berdasarkan id_pelanggan
    $query_pelanggan = "SELECT p.id_pelanggan, p.no_meter, p.nama, p.alamat, t.kode_tarif 
                        FROM pelanggan p 
                        JOIN tarif t ON p.id_tarif = t.id_tarif
                        WHERE p.id_pelanggan = ?";
    if ($stmt = $conn->prepare($query_pelanggan)) {
        $stmt->bind_param('s', $id_pelanggan);
        $stmt->execute();
        $result = $stmt->get_result();

        // Mengambil data pelanggan
        if ($row = $result->fetch_assoc()) {
            $data_pelanggan = $row;
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />

    <title>Thunder ElectriCity</title>

    <!-- Custom fonts for this template-->
    <link href="../Admin/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css" />
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet"
    />

    <!-- Custom styles for this template-->
    <link href="../Admin/css/sb-admin-2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.5.0/font/bootstrap-icons.min.css" />

    <!-- CSS untuk Sidebar Hitam -->
    <style>
    /* CSS untuk Sidebar Hitam */
    .sidebar {
        background-color: #000; /* Warna hitam untuk background sidebar */
        color: #fff; /* Warna teks putih untuk kontras dengan latar belakang hitam */
    }

    .sidebar .nav-item .nav-link {
        color: #fff; /* Warna teks putih untuk link */
    }

    .sidebar .nav-item .nav-link.active {
        background-color: #333; /* Warna latar belakang link aktif */
    }

    .sidebar .sidebar-brand {
        color: #fff; /* Warna teks untuk brand */
    }

    .sidebar .sidebar-brand img {
        max-height: 200px; /* Atur tinggi gambar brand jika diperlukan */
    }

    /* Tambahkan lebih banyak aturan CSS sesuai kebutuhan */
    </style>
</head>

<body id="page-top">
<!-- Page Wrapper -->
<div id="wrapper">
    <!-- Sidebar -->
    <ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar">
        <!-- Sidebar - Brand -->
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="utama.php">
            <img src="../Admin/img/thunder.png" alt="" style="max-height: 300px; padding-top:30px;">
        </a>

        <!-- Divider -->
        <hr class="sidebar-divider my-0" />

        <!-- Nav Item - Dashboard -->
        <li class="nav-item active">
            <a class="nav-link" href="utama.php">
                <i class="fas fa-fw fa-chart-line"></i>
                <span>Dashboard</span></a>
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider" />

        <!-- Heading -->
        <div class="sidebar-heading">Interface</div>

        <!-- Nav Item - Pages Collapse Menu -->
        <li class="nav-item">
            <a class="nav-link collapsed" href="kelolaPembayaran.php">
                <i class="fas fa-fw fa-database"></i>
                <span>Kelola Pembayaran</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link collapsed" href="riwayatPembayaran.php">
                <i class="fas fa-fw fa-database"></i>
                <span>Riwayat Pembayaran</span>
            </a>
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider d-none d-md-block" />

        <!-- Sidebar Toggler (Sidebar) -->
        <div class="text-center d-none d-md-inline">
            <button class="rounded-circle border-0" id="sidebarToggle"></button>
        </div>
    </ul>
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
        <!-- Main Content -->
        <div id="content">
            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                <!-- Sidebar Toggle (Topbar) -->
                <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                    <i class="fa fa-bars"></i>
                </button>

                <!-- Topbar Navbar -->
                <ul class="navbar-nav ml-auto">
                    <!-- Nav Item - User Information -->
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo $_SESSION['username']; ?></span>
                            <img class="img-profile rounded-circle" src="../Admin/img/undraw_profile.svg" alt="Profile Image">
                        </a>
                        <!-- Dropdown - User Information -->
                        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                Profile
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="logout.php">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                Logout
                            </a>
                        </div>
                    </li>
                </ul>
            </nav>
            <!-- End of Topbar -->

            <!-- Begin Page Content -->
            <div class="container-fluid">
                <!-- Page Heading -->
                <h1 class="h3 mb-4 text-gray-800">Kelola Pembayaran</h1>

                <!-- Form Pencarian -->
                <form method="post" action="">
                    <div class="form-group">
                        <label for="id_pelanggan">Pilih ID Pelanggan</label>
                        <select class="form-control" id="id_pelanggan" name="id_pelanggan" required>
                            <option value="">-- Pilih ID Pelanggan --</option>
                            <?php
                            // Ambil ID Pelanggan dan Nama dari tabel pelanggan
                            $query = "SELECT p.id_pelanggan, p.nama 
                                    FROM pelanggan p 
                                    JOIN tagihan t ON p.id_pelanggan = t.id_pelanggan 
                                    GROUP BY p.id_pelanggan, p.nama";
                            if ($stmt = $conn->prepare($query)) {
                                $stmt->execute();
                                $result = $stmt->get_result();
                                while ($row = $result->fetch_assoc()) {
                                    echo '<option value="' . htmlspecialchars($row['id_pelanggan']) . '">' . htmlspecialchars($row['id_pelanggan']) . ' - ' . htmlspecialchars($row['nama']) . '</option>';
                                }
                                $stmt->close();
                            }
                            ?>
                        </select>
                    </div>
                    <button type="submit" name="search" class="btn btn-primary mb-3">Cari Data</button>
                </form>

                <!-- Data Pelanggan -->
                <?php if (!empty($data_pelanggan)): ?>
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Data Pelanggan</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <tr>
                                    <th>ID Pelanggan</th>
                                    <td><?php echo htmlspecialchars($data_pelanggan['id_pelanggan']); ?></td>
                                </tr>
                                <tr>
                                    <th>Nomor Meter</th>
                                    <td><?php echo htmlspecialchars($data_pelanggan['no_meter']); ?></td>
                                </tr>
                                <tr>
                                    <th>Nama</th>
                                    <td><?php echo htmlspecialchars($data_pelanggan['nama']); ?></td>
                                </tr>
                                <tr>
                                    <th>Alamat</th>
                                    <td><?php echo htmlspecialchars($data_pelanggan['alamat']); ?></td>
                                </tr>
                                <tr>
                                    <th>Kode Tarif</th>
                                    <td><?php echo htmlspecialchars($data_pelanggan['kode_tarif']); ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Data Tagihan -->
                <?php if (!empty($data_tagihan)): ?>
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Data Tagihan</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>ID Tagihan</th>
                                        <th>Bulan</th>
                                        <th>Tahun</th>
                                        <th>Jumlah Meter</th>
                                        <th>Tarif per kWh</th>
                                        <th>Jumlah Bayar</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data_tagihan as $tagihan): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($tagihan['id_tagihan']); ?></td>
                                        <td><?php echo $nama_bulan[(int)$tagihan['bulan']]; ?></td>
                                        <td><?php echo htmlspecialchars($tagihan['tahun']); ?></td>
                                        <td><?php echo htmlspecialchars($tagihan['jumlah_meter']); ?></td>
                                        <td><?php echo htmlspecialchars($tagihan['tarif_perkwh']); ?></td>
                                        <td><?php echo htmlspecialchars($tagihan['jumlah_bayar']); ?></td>
                                        <td><?php echo htmlspecialchars($tagihan['status']); ?></td>
                                        <td><a href="#" class="btn btn-warning">Bayar</a></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <!-- /.container-fluid -->
        </div>
        <!-- End of Main Content -->
    </div>
    <!-- End of Content Wrapper -->
</div>
<!-- End of Page Wrapper -->

<!-- Bootstrap core JavaScript-->
<script src="../Admin/vendor/jquery/jquery.min.js"></script>
<script src="../Admin/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Core plugin JavaScript-->
<script src="../Admin/vendor/jquery-easing/jquery.easing.min.js"></script>

<!-- Custom scripts for all pages-->
<script src="../Admin/js/sb-admin-2.min.js"></script>


</body>
</html>
