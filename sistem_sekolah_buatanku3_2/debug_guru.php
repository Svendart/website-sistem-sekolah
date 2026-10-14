<?php
require_once 'config/koneksi.php';
$res = mysqli_query($koneksi, 'DESCRIBE guru');
while ($row = mysqli_fetch_assoc($res)) {
    echo $row['Field'] . ' (' . $row['Type'] . ') ' . ($row['Key'] ? '[' . $row['Key'] . ']' : '') . "\n";
}