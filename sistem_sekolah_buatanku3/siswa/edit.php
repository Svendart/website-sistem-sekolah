<?php 

require_once "../config/session.php";
require_login();

include "../config/koneksi.php";

$id = $_GET['id'];

$sql = "SELECT * FROM siswa WHERE id_siswa = '$id'";

$query = mysqli_query ($koneksi, $sql);

$data = mysqli_fetch_assoc ($query);
$alamat = preg_replace('/\s*[\r\n]+\s*/', ' ', trim($data['alamat']));


?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title> Edit Data Siswa </title>

    <link rel = "stylesheet" href = "../css/style.css">

</head>
<body>

<div class = "container">

    <h1> Edit Data Siswa </h1>

    <form action = "update.php" method = "POST" enctype="multipart/form-data">

        <input
            type = "hidden"
            name = "id_siswa"
            value = "<?= htmlspecialchars($data['id_siswa']); ?>"
        >

        <label> NIS </label>

        <input
            type = "text"
            name = "nis"
            value = "<?= htmlspecialchars($data['nis']); ?>"
            required
        >

        <label> Nama Siswa </label>

        <input
            type = "text"
            name = "nama_siswa"
            value = "<?= htmlspecialchars($data['nama_siswa']); ?>"
            required
        >

        <label> Jenis Kelamin </label>

        <select name = "jenis_kelamin" required>

                <option
                    value = "Laki-Laki"
                    <?= $data['jenis_kelamin'] == 'Laki-Laki' ? 'selected' : ''; ?>
                >
                    Laki-laki

                </option>

                <option
                    value = "Perempuan"
                    <?= $data['jenis_kelamin'] == 'Perempuan' ? 'selected' : ''; ?>
                >
                    Perempuan
                    
                </option>

        </select>

        <label> Kelas </label>
        <select name = "kelas" required>

                <option value = "10"
                <?= $data['kelas'] == '10' ? 'selected' : ''; ?>
                > 10

                </option>

                <option
                    value = "11"
                    <?= $data['kelas'] == '11' ? 'selected' : ''; ?>
                >
                    11
                    
                </option>

                <option
                    value = "12"
                    <?= $data['kelas'] == '12' ? 'selected' : ''; ?>
                >
                    12

                </option>

                <option value = "13" 
                    <?= $data['kelas'] == '13' ? 'selected' : ''; ?> 
                > 13
                        
                </option>
        </select>


        <label> Jurusan </label>

        <select name = "jurusan" required>

                <option
                    value = "PPLG"
                    <?= $data['jurusan'] == 'PPLG' ? 'selected' : ''; ?>
                >
                    PPLG

                </option>

                <option
                    value = "TJKT"
                    <?= $data['jurusan'] == 'TJKT' ? 'selected' : ''; ?>
                >
                    TJKT
                    
                </option>

                <option
                    value = "GEO"
                    <?= $data['jurusan'] == 'GEO' ? 'selected' : ''; ?>
                >
                    GEO

                </option>

                <option
                    value = "TO"
                    <?= $data['jurusan'] == 'TO' ? 'selected' : ''; ?>
                >
                    TO
                    
                </option>

                <option
                    value = "TM"
                    <?= $data['jurusan'] == 'TM' ? 'selected' : ''; ?>
                >
                    TM

                </option>

                <option
                    value = "TKP"
                    <?= $data['jurusan'] == 'TKP' ? 'selected' : ''; ?>
                >
                    TKP
                    
                </option>

                <option
                    value = "DPIB"
                    <?= $data['jurusan'] == 'DPIB' ? 'selected' : ''; ?>
                >
                    DPIB

                </option>

                <option
                    value = "TE"
                    <?= $data['jurusan'] == 'TE' ? 'selected' : ''; ?>
                >
                    TE
                    
                </option>

                <option
                    value = "TKL"
                    <?= $data['jurusan'] == 'TKL' ? 'selected' : ''; ?>
                >
                    TKL

                </option>

                <option
                    value = "TPFL"
                    <?= $data['jurusan'] == 'TPFL' ? 'selected' : ''; ?>
                >
                    TPFL
                    
                </option>

        </select>


        
        <label> Rombel </label>

        <select name = "rombel" required>

                <option 
                    value = "A"
                    <?= $data['rombel'] == 'A' ? 'selected' : ''; ?>
                    > A

                    </option>

                <option 
                    value = "B"
                    <?= $data['rombel'] == 'B' ? 'selected' : ''; ?>
                    > B

                    </option>

                <option 
                    value = "C"
                    <?= $data['rombel'] == 'C' ? 'selected' : ''; ?>
                    > C

                    </option>
                
                <option 
                    value = "D"
                    <?= $data['rombel'] == 'D' ? 'selected' : ''; ?>
                    > D

                    </option>

                <option 
                    value = " "
                    <?= $data['rombel'] == ' ' ? 'selected' : ''; ?>
                    >  

                </option>

        </select>



        <label> Alamat </label>
        <textarea name = "alamat"><?= htmlspecialchars($alamat); ?></textarea>
        
        <label> No HP </label>

        <input
            type = "number"
            name = "no_hp"
            value = "<?= htmlspecialchars($data['no_hp']); ?>"
        >

        <label> Foto Siswa </label>
        <?php 
            $fotoPath = $data['foto'] ?? '';
            $fotoAbsPath = __DIR__ . '/../../' . $fotoPath;
            $fotoWebPath = '../' . $fotoPath;
        ?>
        <?php if (!empty($fotoPath) && file_exists($fotoAbsPath)): ?>
            <div class="foto-preview">
                <img src="<?= htmlspecialchars($fotoWebPath) ?>" alt="Foto Siswa" style="max-width: 150px; max-height: 150px; border-radius: 8px; border: 1px solid #ddd;">
                <input type="hidden" name="foto_lama" value="<?= htmlspecialchars($fotoPath) ?>">
                <br><small style="color: #64757d;">Kosongkan jika tidak ingin mengubah foto.</small>
            </div>
        <?php else: ?>
            <p style="color: #94a3b8; font-size: 14px;">Belum ada foto.</p>
        <?php endif; ?>
        <input type="file" name="foto" accept="image/*">
        <small style="color: #64748b; font-size: 12px;">Format: JPG, PNG, WebP. Maksimal 2MB.</small>

            <a href = "index.php">
            Kembali
        </a>
        </button>

        <button type = "submit">
            Update Data
        </button>



    </form>

</div>

    
</body>
</html>

