<?php
session_start(); // Memulai sesi

// Cek apakah ada parameter konfirmasi
if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
    // Hapus semua variabel sesi
    session_unset();

    // Hancurkan sesi
    session_destroy();

    // Redirect ke halaman login
    header("Location: index.php");
    exit();
} else {
    echo '<script>
            var confirmLogout = confirm("Apakah Anda yakin ingin logout?");
            if (confirmLogout) {
                window.location.href = "logout.php?confirm=yes";
            } else {
                window.location.href = "utama.php"; // Halaman dashboard atau halaman utama Anda
            }
          </script>';
}
?>
