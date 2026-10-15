<?php
require 'koneksi.php';

$id = (int) ($_GET['id'] ?? 0);
$hasil = mysqli_query($conn,
    "SELECT * FROM showroom_mobil WHERE id = $id");
$mobil = mysqli_fetch_assoc($hasil);
if (!$mobil) {
    exit('Mobil tidak ditemukan. Kembali ke daftar mobil.');
}

$kode  = $mobil['kode_mobil'];
$nama  = $mobil['nama_mobil'];
$brand = $mobil['brand_mobil'];
$warna = $mobil['warna_mobil'];
$tipe  = $mobil['tipe_mobil'];
$harga = $mobil['harga_mobil'];
$pesan = '';

if (isset($_POST['simpan'])) {
    // Nilai mentah (untuk ditampilkan ulang di form)
    $kode  = trim($_POST['kode_mobil']);
    $nama  = trim($_POST['nama_mobil']);
    $brand = trim($_POST['brand_mobil']);
    $warna = trim($_POST['warna_mobil']);
    $tipe  = trim($_POST['tipe_mobil']);
    $harga = trim($_POST['harga_mobil']);

    // Nilai aman untuk dimasukkan ke SQL
    $kode_sql  = mysqli_real_escape_string($conn, $kode);
    $nama_sql  = mysqli_real_escape_string($conn, $nama);
    $brand_sql = mysqli_real_escape_string($conn, $brand);
    $warna_sql = mysqli_real_escape_string($conn, $warna);
    $tipe_sql  = mysqli_real_escape_string($conn, $tipe);

    if ($kode == '' || $nama == '' || $brand == '' ||
        $warna == '' || $tipe == '') {
        $pesan = 'Semua kolom wajib diisi.';
    } elseif (!is_numeric($harga) || $harga <= 0 || $harga > 99999999999) {
        $pesan = 'Harga harus berupa angka positif, tanpa Rp dan titik.';
    } else {
        $cek = mysqli_query($conn,
            "SELECT id FROM showroom_mobil
             WHERE kode_mobil = '$kode_sql' AND id <> $id");
        if (mysqli_num_rows($cek) > 0) {
            $pesan = 'Kode mobil sudah dipakai mobil lain.';
        } else {
            mysqli_query($conn,
                "UPDATE showroom_mobil
                 SET kode_mobil = '$kode_sql', nama_mobil = '$nama_sql',
                     brand_mobil = '$brand_sql', warna_mobil = '$warna_sql',
                     tipe_mobil = '$tipe_sql', harga_mobil = '$harga'
                 WHERE id = $id");
            header('Location: index.php?pesan=1');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit mobil - ShowRoom EAD</title>
    <link rel="stylesheet" href="style.css">
</head>
<body><main>
<p class="label">SHOWROOM EAD</p>
<h1>Edit mobil</h1>
<p><a href="index.php">Kembali ke daftar</a></p>
<?php if ($pesan != ''): ?>
    <div class="kesalahan"><?= htmlspecialchars($pesan) ?></div>
<?php endif; ?>
<form method="post">
    <label for="kode">Kode mobil</label>
    <input id="kode" name="kode_mobil" maxlength="20" value="<?= htmlspecialchars($kode) ?>">
    <label for="nama">Nama mobil</label>
    <input id="nama" name="nama_mobil" maxlength="100" value="<?= htmlspecialchars($nama) ?>">
    <label for="brand">Brand</label>
    <input id="brand" name="brand_mobil" maxlength="50"
           value="<?= htmlspecialchars($brand) ?>">
    <label for="warna">Warna</label>
    <input id="warna" name="warna_mobil" maxlength="30"
           value="<?= htmlspecialchars($warna) ?>">
    <label for="tipe">Tipe (contoh: MPV, SUV, Sedan)</label>
    <input id="tipe" name="tipe_mobil" maxlength="20" value="<?= htmlspecialchars($tipe) ?>">
    <label for="harga">Harga (angka saja, tanpa Rp dan titik)</label>
    <input id="harga" name="harga_mobil" value="<?= htmlspecialchars($harga) ?>">
    <button name="simpan">Simpan perubahan</button>
</form>
</main></body>
</html>