<?php
class FileUploader {
    private $target_dir; // Direktori tujuan untuk menyimpan file
    private $allowed_types; // Tipe file yang diperbolehkan

    /**
     * Konstruktor untuk menginisialisasi direktori dan tipe file yang diizinkan.
     *
     * @param string $target_dir Direktori tujuan untuk menyimpan file.
     * @param array $allowed_types Tipe file yang diperbolehkan (default: jpg, jpeg, png, gif).
     */
    public function __construct($target_dir, $allowed_types = ['jpg', 'jpeg', 'png', 'gif']) {
        $this->target_dir = rtrim($target_dir, '/') . '/'; // Menghilangkan trailing slash jika ada
        $this->allowed_types = $allowed_types;

        // Cek apakah folder target ada, jika tidak buat folder tersebut
        if (!file_exists($this->target_dir)) {
            mkdir($this->target_dir, 0777, true); // Buat folder dengan izin tulis
        }
    }

    /**
     * Menghasilkan nama file yang unik.
     *
     * @param string $fileName Nama file yang ingin dibuat unik.
     * @return string Nama file yang unik.
     */
    private function uniqueFileName($fileName) {
        $fileType = pathinfo($fileName, PATHINFO_EXTENSION); // Mendapatkan tipe file
        $baseName = pathinfo($fileName, PATHINFO_FILENAME); // Mendapatkan nama file tanpa ekstensi
        $newFileName = $fileName; // Inisialisasi nama file baru
        $counter = 1; // Mulai dengan angka 1

        // Cek apakah file sudah ada di direktori target
        while (file_exists($this->target_dir . $newFileName)) {
            // Jika ada, tambahkan angka di belakang nama file sebelum ekstensi
            $newFileName = $baseName . '-' . $counter . '.' . $fileType;
            $counter++; // Tingkatkan angka
        }

        return $newFileName; // Kembalikan nama file yang unik
    }

    /**
     * Mengunggah file ke direktori target.
     *
     * @param array $file Data file yang diunggah dari $_FILES.
     * @return string|false Nama file yang diunggah atau false jika gagal.
     */
    public function upload($file) {
        $fileName = $file['name']; // Mengambil nama file
        $tempName = $file['tmp_name']; // Mengambil lokasi sementara file

        // Cek dan buat nama file yang unik
        $fileName = $this->uniqueFileName($fileName);
        $target_file = $this->target_dir . $fileName; // Gabungkan path lengkap

        // Validasi upload file
        $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION)); // Mengambil tipe file

        if (in_array($fileType, $this->allowed_types)) { // Memeriksa apakah tipe file diperbolehkan
            if (move_uploaded_file($tempName, $target_file)) { // Mengupload file ke direktori tujuan
                return $fileName; // Kembalikan nama file jika berhasil
            }
        }

        return false; // Kembalikan false jika upload gagal
    }
}
