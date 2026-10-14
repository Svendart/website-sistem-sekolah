<?php
require_once "../config/session.php";
require_login();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Data Siswa</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

<div class="container">
    <h1>Tambah Data Siswa</h1>

    <?php if (isset($_GET['pesan']) && $_GET['pesan'] === 'nis_duplikat'): ?>
        <p role="alert">NIS sudah terdaftar. Silakan gunakan NIS yang berbeda.</p>
    <?php elseif (isset($_GET['pesan']) && $_GET['pesan'] === 'gagal'): ?>
        <p role="alert">Data siswa gagal disimpan. Silakan coba lagi.</p>
    <?php endif; ?>

    <form action="simpan.php" method="POST" enctype="multipart/form-data">

    <br>
        <label>NIS</label> 
        <input type="text" name="nis" required>

    <br>
        <label>Nama Siswa</label> 
        <input type="text" name="nama_siswa" required>

    <br>

        <label>Jenis Kelamin</label>
            <select name="jenis_kelamin" required>
            <option value="">-- Pilih --</option>
            <option value="Laki-Laki">Laki-laki</option>
            <option value="Perempuan">Perempuan</option>
            </select>

    <br>

        <label>Kelas</label>
        <select name = "kelas" required>
            <option value = "">-- Pilih -- </option>
            <option value = "10"> 10 </option>
            <option value = "11"> 11 </option>
            <option value = "12"> 12 </option>
            <option value = "13"> 13 </option>
        </select>
    

        <label>Jurusan</label>
         <select name = "jurusan" placeholder= "Contoh: PPLG" value="" required>
            <option value = "">-- Pilih -- </option>
            <option value = "PPLG"> PPLG </option>
            <option value = "TJKT"> TJKT </option>
            <option value = "GEO"> GEO </option>
            <option value = "TO"> TO </option>
            <option value = "TM"> TM </option>
            <option value = "TKP"> TKP </option>
            <option value = "DPIB"> DPIB </option>
            <option value = "TE"> TE </option>
            <option value = "TKL"> TKL </option>
            <option value = "TPFL"> TPFL </option>
        </select>


        <br>

        <label>Rombel</label>
        <select name = "rombel" required>
            <option value = "">-- Pilih ' ' bila kelasmu hanya 1-- </option>
            <option value = "A"> A </option>
            <option value = "B"> B </option>
            <option value = "C"> C </option>
            <option value = "D"> D </option>
            <option value = " " >  </option>
        </select>
    
        <br>


    <br>

        <label>Alamat</label> 
        <textarea name="alamat"></textarea>

    <br>

        <label>No. HP</label> 
        <input type="number" name="no_hp">

    <br>

        <label>Foto Siswa</label>
        <input type="file" name="foto" accept="image/*">
        <small style="color: #64748b; font-size: 12px;">Format: JPG, PNG, WebP. Maksimal 2MB.</small>

    <br>

       <a href="index.php">Kembali</a>

        <button type="submit">Simpan</button> 





    </form>

</div>

</body>
</html>




<!--
        <label>Jenis Kelamin</label>
            <select name="jenis_kelamin" required>
            <option value="">-- Pilih --</option>
            <option value="Laki-Laki">Laki-laki</option>
            <option value="Perempuan">Perempuan</option>
            </select>
        <label>Kelas</label>
        <select name = "kelas" required>
            <option value = "">-- Pilih -- </option>
            <option value = "10"> 10 </option>
            <option value = "11"> 11 </option>
            <option value = "12"> 12 </option>
            <option value = "13"> 13 </option>
        </select>
        <label>Jurusan</label>
         <select name = "jurusan" placeholder= "Contoh: PPLG" value="" required>
            <option value = "">-- Pilih -- </option>
            <option value = "PPLG"> PPLG </option>
            <option value = "TJKT"> PPLG </option>
            <option value = "GEO"> PPLG </option>
            <option value = "TO"> PPLG </option>
            <option value = "TM"> PPLG </option>
            <option value = "TKP"> PPLG </option>
            <option value = "DPIB"> PPLG </option>
            <option value = "TE"> PPLG </option>
            <option value = "TKL"> PPLG </option>
            <option value = "TPFL"> PPLG </option>
        </select>
-->
