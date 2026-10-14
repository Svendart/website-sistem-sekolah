<!-- Ini adalah awal PHP -->

<?php

require_once "../config/session.php";
require_login();

include "../config/koneksi.php";

// Ambil parameter filter
$cari = isset($_GET['cari']) && is_string($_GET['cari'])
    ? trim($_GET['cari'])
    : "";

$filterKelas = isset($_GET['kelas']) && is_string($_GET['kelas'])
    ? trim($_GET['kelas'])
    : "";

$filterJurusan = isset($_GET['jurusan']) && is_string($_GET['jurusan'])
    ? trim($_GET['jurusan'])
    : "";

// Bangun query WHERE dinamis
$whereClauses = [];
$params = [];

if ($cari !== "") {
    $cariQuery = mysqli_real_escape_string($koneksi, $cari);
    $whereClauses[] = "(nama_siswa LIKE '%$cariQuery%' OR nis LIKE '%$cariQuery%')";
}

if ($filterKelas !== "") {
    $kelasSafe = mysqli_real_escape_string($koneksi, $filterKelas);
    $whereClauses[] = "kelas = '$kelasSafe'";
}

if ($filterJurusan !== "") {
    $jurusanSafe = mysqli_real_escape_string($koneksi, $filterJurusan);
    $whereClauses[] = "jurusan = '$jurusanSafe'";
}

$sql = "SELECT * FROM siswa";
if (!empty($whereClauses)) {
    $sql .= " WHERE " . implode(" AND ", $whereClauses);
}
$sql .= " ORDER BY id_siswa DESC";

$query = mysqli_query($koneksi, $sql);

// Ambil distinct kelas & jurusan untuk dropdown filter (dari data yang ada)
$kelasList = [];
$jurusanList = [];

$resKelas = mysqli_query($koneksi, "SELECT DISTINCT kelas FROM siswa WHERE kelas IS NOT NULL AND kelas != '' ORDER BY kelas");
if ($resKelas) {
    while ($row = mysqli_fetch_assoc($resKelas)) {
        $kelasList[] = $row['kelas'];
    }
}

$resJurusan = mysqli_query($koneksi, "SELECT DISTINCT jurusan FROM siswa WHERE jurusan IS NOT NULL AND jurusan != '' ORDER BY jurusan");
if ($resJurusan) {
    while ($row = mysqli_fetch_assoc($resJurusan)) {
        $jurusanList[] = $row['jurusan'];
    }
}

// Master list dari form tambah (supaya semua opsi valid selalu tersedia di filter)
$masterKelas = ['10', '11', '12', '13'];
$masterJurusan = ['PPLG', 'TJKT', 'GEO', 'TO', 'TM', 'TKP', 'DPIB', 'TE', 'TKL', 'TPFL'];

// Gabungkan: master list + data DB (unique, urutkan)
$kelasList = array_unique(array_merge($masterKelas, $kelasList));
sort($kelasList, SORT_STRING);

$jurusanList = array_unique(array_merge($masterJurusan, $jurusanList));
sort($jurusanList, SORT_STRING);

?>

<!-- Ini adalah batas PHP -->

<!-- Ini adalah awal html -->

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>Data Siswa</title>

    <link rel = "stylesheet" href = "../css/style.css">

</head>

<body>

<div class = "container">

    <div class="judul-siswa">
        <h1> Data Siswa </h1>
        <a href="../dashboard.php" class="btn-kembali-dashboard">← Kembali ke Dashboard</a>
    </div>

    <br>
    <br>

    <div class="area-pencarian-siswa">
        <form class="pencarian-siswa" action="index.php" method="get">
            <div class="filter-row">
                <div class="filter-group">
                    <label>Cari Nama Siswa atau NIS</label>
                    <div class="kolom-cari">
                        <input
                            type="text"
                            name="cari"
                            value="<?= htmlspecialchars($cari, ENT_QUOTES, 'UTF-8'); ?>"
                            placeholder="Cari Berdasarkan Nama atau NIS..."
                        >
                    </div>
                </div>

                <div class="filter-group">
                    <label>Filter Kelas</label>
                    <select name="kelas" class="filter-select">
                        <option value="">-- Semua Kelas --</option>
                        <?php foreach ($kelasList as $kls): ?>
                            <option value="<?= htmlspecialchars($kls) ?>" <?= $filterKelas === $kls ? 'selected' : '' ?>>
                                <?= htmlspecialchars($kls) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Filter Jurusan</label>
                    <select name="jurusan" class="filter-select">
                        <option value="">-- Semua Jurusan --</option>
                        <?php foreach ($jurusanList as $jur): ?>
                            <option value="<?= htmlspecialchars($jur) ?>" <?= $filterJurusan === $jur ? 'selected' : '' ?>>
                                <?= htmlspecialchars($jur) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn-filter">Terapkan Filter</button>
                <a class="tombol-reset-cari" href="index.php">Reset Semua</a>
            </div>
        </form>

        <a class="tombol-tambah-siswa" href="tambah.php">+ Tambah Siswa</a>
    </div>

    <br> <br>

    <table>

        <thead>

        <tr>

            <th> No </th>
            <th> Foto </th>
            <th> NIS </th>
            <th> Nama </th>
            <th> Jenis Kelamin </th>
            <th> Kelas </th>
            <th> Jurusan </th>
            <th> Rombel </th>
            <th> Alamat </th>
            <th> No HP </th>
            <th> Aksi </th>

        </tr>

        </thead>

        <tbody>

<!-- html terpotong untuk php dulu-->

<!-- Ini adalah bagian loop PHP + combine HTML -->

        <?php

        $no = 1;

        while ($data = mysqli_fetch_assoc($query)):

        ?>

        <tr>

            <td> <?= $no++; ?> </td>
            <td>
                <?php 
                    $fotoPath = $data['foto'] ?? '';
                    $fotoAbsPath = __DIR__ . '/../../' . $fotoPath;
                    $fotoWebPath = '../' . $fotoPath;
                ?>
                <?php if (!empty($fotoPath) && file_exists($fotoAbsPath)): ?>
                    <img src="<?= htmlspecialchars($fotoWebPath) ?>" alt="Foto" style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px; border: 1px solid #ddd;">
                <?php else: ?>
                    <span style="color: #94a3b8;">—</span>
                <?php endif; ?>
            </td>
            <td> <?= htmlspecialchars ($data ['nis']); ?> </td>
            <td> <?= htmlspecialchars ($data ['nama_siswa']); ?> </td>
            <td> <?= htmlspecialchars ($data ['jenis_kelamin']); ?> </td>
            <td> <?= htmlspecialchars ($data ['kelas']); ?> </td>          
            <td> <?= htmlspecialchars ($data ['jurusan']); ?> </td>
            <td> <?= htmlspecialchars ($data ['rombel']); ?> </td>  
            <td> <?= htmlspecialchars ($data ['alamat']); ?> </td>
            <td> <?= htmlspecialchars ($data ['no_hp']); ?> </td>

            <td>
                <div class="aksi-siswa">
                    <a class="tombol-aksi tombol-edit" href = "edit.php?id=<?= urlencode($data['id_siswa']); ?>">
                        Edit
                    </a>


                    <a
                        class="tombol-aksi tombol-hapus"
                        href="#konfirmasi-hapus"
                        data-id="<?= htmlspecialchars($data['id_siswa'], ENT_QUOTES, 'UTF-8'); ?>"
                    >

                        Hapus
                    </a>
                </div>
            </td>
        
        </tr>

        <?php endwhile; ?>

        </tbody>

    </table>

    <div style="margin-top: 20px; padding: 15px; background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <a href="cetak.php" style="padding: 8px 16px; background: #198754; color: white; border-radius: 6px; text-decoration: none; font-weight: 500; font-size: 14px;">
                📄 Cetak
            </a>
            <a href="export_excel.php" style="padding: 8px 16px; background: #0d6efd; color: white; border-radius: 6px; text-decoration: none; font-size: 14px;">
                📊 Export Excel
            </a>
        </div>
    </div>

</div>

<dialog id="konfirmasi-hapus" class="dialog-konfirmasi-hapus">
    <p>Apakah Anda yakin ingin menghapus data siswa ini?</p>
    <form class="aksi-konfirmasi-hapus" action="hapus.php" method="post">
        <input type="hidden" name="id" class="id-siswa-hapus">
        <button type="submit" class="tombol-hapus tombol-konfirmasi-hapus">Ya, Hapus</button>
        <button type="button" class="tombol-batal-hapus">Batal</button>
    </form>
</dialog>

<script src="../js/script.js?v=2"></script>


    
</body>
</html>