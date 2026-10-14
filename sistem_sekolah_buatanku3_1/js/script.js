const dialogKonfirmasiHapus = document.querySelector("#konfirmasi-hapus");
const tombolBatalHapus = document.querySelector(".tombol-batal-hapus");
const idSiswaHapus = document.querySelector(".id-siswa-hapus");

document.querySelectorAll(".aksi-siswa .tombol-hapus").forEach((tombol) => {
    tombol.addEventListener("click", (event) => {
        event.preventDefault();
        idSiswaHapus.value = tombol.dataset.id;
        dialogKonfirmasiHapus.showModal();
    });
});

tombolBatalHapus.addEventListener("click", () => {
    dialogKonfirmasiHapus.close();
});