<?php
session_start(); // Memulai sesi untuk menyimpan informasi pengguna
include 'connect.php'; // Memastikan koneksi database sudah terhubung
require_once('tcpdf_6_2_13/tcpdf/tcpdf.php'); // Menyertakan library TCPDF untuk membuat PDF

// Mendapatkan ID pembelian dari URL
$id_pembelian = $_GET['id_pembelian'];

// Memeriksa apakah ID pembelian tidak nol
if ($id_pembelian != 0) {
    // Mengambil data pembelian dari database, termasuk metode pembayaran
    $sql = "SELECT pb.*, c.username, s.status, mp.nama_metode, 
            CASE 
                WHEN mp.nama_metode != 'COD' THEN b.nama_bank 
                ELSE '-' 
            END AS nama_bank, 
            CASE 
                WHEN mp.nama_metode != 'COD' THEN b.rekening 
                ELSE '-' 
            END AS rekening 
            FROM pembelian pb 
            JOIN customer c ON pb.id_customer = c.id_customer 
            JOIN status s ON pb.id_status = s.id_status
            JOIN metode_pembayaran mp ON pb.id_metode = mp.id_metode
            LEFT JOIN pembayaran p ON p.id_pembelian = pb.id_pembelian
            LEFT JOIN bank b ON p.id_bank = b.id_bank
            WHERE pb.id_pembelian = ?"; // Query untuk mengambil detail pembelian

    $stmt = $conn->prepare($sql); // Mempersiapkan statement SQL

    if (!$stmt) {
        echo "Error preparing statement: " . $conn->error; // Menampilkan error jika ada
        exit; // Menghentikan eksekusi jika terjadi kesalahan
    }

    $stmt->bind_param("i", $id_pembelian); // Mengikat parameter ID pembelian
    $stmt->execute(); // Mengeksekusi query
    $result = $stmt->get_result(); // Mendapatkan hasil dari query yang dieksekusi

    // Memeriksa apakah ada hasil dari query
    if ($result && $result->num_rows > 0) { // Mengecek apakah ada data yang ditemukan
        $notaData = $result->fetch_assoc(); // Mengambil data nota

        // Mengambil detail item pembelian
        $item_sql = "SELECT dp.*, p.nama_produk 
                    FROM detail_pembelian dp 
                    JOIN produk p ON dp.id_produk = p.id_produk 
                    WHERE dp.id_pembelian = ?"; // Query untuk mengambil detail item
        $item_stmt = $conn->prepare($item_sql); // Mempersiapkan statement SQL untuk item

        if (!$item_stmt) {
            echo "Error preparing item statement: " . $conn->error; // Menampilkan error jika ada
            exit; // Menghentikan eksekusi jika terjadi kesalahan
        }

        $item_stmt->bind_param("i", $id_pembelian); // Mengikat parameter ID pembelian
        $item_stmt->execute(); // Mengeksekusi query item
        $item_result = $item_stmt->get_result(); // Mendapatkan hasil untuk item

        // Membuat dokumen PDF baru
        $pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false); // Inisialisasi TCPDF
        $pdf->SetCreator(PDF_CREATOR); // Menentukan pembuat PDF
        $pdf->SetHeaderData(''); // Mengatur header PDF
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN)); // Mengatur font header
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA)); // Mengatur font footer
        $pdf->SetMargins(PDF_MARGIN_LEFT, '5', PDF_MARGIN_RIGHT); // Mengatur margin
        $pdf->SetAutoPageBreak(TRUE, 10); // Mengatur auto page break
        $pdf->SetFont('helvetica', '', 12); // Mengatur font untuk isi PDF
        $pdf->AddPage(); // Menambahkan halaman baru

        // Menyiapkan konten HTML dengan logo
        $logo = 'assets/logo-2.png'; // Menentukan path logo
        $content = '<style type="text/css">
            body {
                font-size: 12px;
                line-height: 1.5;
                font-family: "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;
                color: #000;
            }
            h2, h3 {
                text-align: center;
                color: #0056b3;
            }
            th {
                background-color: #f2f2f2;
                font-weight: bold;
            }
        </style>';

        // Menambahkan informasi pembelian
        $content .= '
        <p style="text-align: center;"><img src="' . $logo . '" alt="Logo" width="250"/></p></br>
        <table>
            <tr>
                <td><b>Username: ' . htmlspecialchars($notaData['username']) . '</b></td>
                <td align="left"><b>Tanggal: ' . date('d-m-Y', strtotime($notaData['tanggal_pembelian'])) . '</b></td>
            </tr>
            <tr>
                <td><b>Nama Penerima: ' . htmlspecialchars($notaData['nama_penerima']) . '</b></td>
                <td align="left"><b>No. Rekening: ' . htmlspecialchars($notaData['rekening']) . '</b></td>
            </tr>
            <tr>
                <td><b>Alamat Penerima: ' . htmlspecialchars($notaData['alamat_penerima']) . '</b></td>
                <td align="left"><b>Bank: ' . htmlspecialchars($notaData['nama_bank']) . '</b></td>
            </tr>
            <tr>
                <td><b>Telepon Penerima: ' . htmlspecialchars($notaData['telp_penerima']) . '</b></td>
                <td align="left"><b>Nama Metode: ' . htmlspecialchars($notaData['nama_metode']) . '</b></td>
            </tr>
        </table>
        </br>
        <h3>Detail Item Pembelian</h3>
        <table border="1" cellpadding="5">
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Quantity</th>
                <th>Total</th>
            </tr>';

        $total_belanja = 0; // Menginisialisasi total belanja
        $index = 1; // Menginisialisasi indeks untuk nomor item

        // Mengambil data setiap item dan menghitung subtotal
        while ($item = $item_result->fetch_assoc()) {
            $subtotal = $item['harga'] * $item['quantity']; // Menghitung subtotal
            $total_belanja += $subtotal; // Menambahkan subtotal ke total belanja
            $content .= '<tr>';
            $content .= '<td>' . $index . '</td>';
            $content .= '<td>' . htmlspecialchars($item['nama_produk']) . '</td>';
            $content .= '<td>' . $item['quantity'] . '</td>';
            $content .= '<td>Rp ' . number_format($subtotal, 0, ',', '.') . '</td>';
            $content .= '</tr>';
            $index++; // Menaikkan indeks
        }

        $content .= '</table>'; // Menutup tabel detail item
        $content .= '<h3>Total Belanja: Rp ' . number_format($total_belanja, 0, ',', '.') . '</h3>'; // Menampilkan total belanja

        // Menambahkan tanda tangan toko di kanan dengan garis bawah
        $content .= '<p style="margin-top: 50px; text-align: right;">Tanda Tangan</p>';
        $content .= '<p></p><p></p>';
        $content .= '<p style="margin-top: 50px; text-align: right;">MEDIC</p>';
        $content .= '<p style="border-bottom: 1px solid #000; width: 150px; margin: 0 auto;">&nbsp;</p>'; // Placeholder untuk tanda tangan

        // Mengoutput konten HTML ke PDF
        $pdf->writeHTML($content, true, false, true, false, '');

        // Menentukan pengaturan output
        $file_location = 'assets/nota/'; // Path untuk menyimpan file PDF

        // Memastikan folder sudah ada
        if (!file_exists($file_location)) {
            mkdir($file_location, 0777, true); // Membuat folder jika belum ada
        }

        $datetime = date('dmY_hms'); // Mengambil timestamp untuk nama file
        $file_name = "INV_" . $datetime . ".pdf"; // Menentukan nama file PDF
        ob_end_clean(); // Membersihkan output buffer

        // Memeriksa variabel ACTION untuk menentukan aksi yang diambil
        $action = isset($_GET['ACTION']) ? $_GET['ACTION'] : 'show'; // Mengambil aksi dari parameter GET
        if ($action == 'download') { // Jika aksi adalah download
            $pdf->Output($file_location . $file_name, 'F'); // Menyimpan file PDF ke folder
            echo json_encode(['status' => 'success', 'file' => $file_location . $file_name]); // Mengembalikan response JSON
            exit; // Menghentikan eksekusi
        } else {
            $pdf->Output($file_name, 'I'); // Menampilkan PDF di browser
        }
    }
}
?>