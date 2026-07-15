<?php
// Function definition logic for getNotifikasiTugasGuru
function getNotifikasiTugasGuru($db, $id_guru) {
    // 1. Get assignments (Tugas) that need grading
    // log_materi -> kelas_materi (jenis = 2, id_guru = $id_guru) -> master_siswa
    $sql_tugas = "
        SELECT 
            'tugas' as tipe,
            m.judul_materi as judul,
            s.nama as nama_siswa,
            l.log_time as waktu,
            l.id_log as id_referensi
        FROM log_materi l
        JOIN kelas_materi m ON l.id_materi = m.id_materi
        JOIN master_siswa s ON l.id_siswa = s.id_siswa
        WHERE m.id_guru = ? 
          AND m.jenis = 2 
          AND (l.nilai IS NULL OR l.nilai = '' OR l.nilai = '0')
    ";

    // 2. Get exams (Ujian Esai) that need grading
    // cbt_nilai -> cbt_jadwal -> cbt_bank_soal (bank_guru_id = $id_guru) -> master_siswa
    // We only care if there are essay questions and dikoreksi = 0
    $sql_ujian = "
        SELECT 
            'ujian' as tipe,
            b.bank_nama as judul,
            s.nama as nama_siswa,
            n.time_create as waktu,
            n.id_nilai as id_referensi
        FROM cbt_nilai n
        JOIN cbt_jadwal j ON n.id_jadwal = j.id_jadwal
        JOIN cbt_bank_soal b ON j.id_bank = b.id_bank
        JOIN master_siswa s ON n.id_siswa = s.id_siswa
        WHERE b.bank_guru_id = ?
          AND b.jml_esai > 0
          AND (n.dikoreksi = 0 OR n.dikoreksi IS NULL)
    ";

    // Combine and sort by waktu DESC, LIMIT 10
    $sql = "($sql_tugas) UNION ($sql_ujian) ORDER BY waktu DESC LIMIT 10";
    
    $stmt = $db->prepare($sql);
    $stmt->bind_param("ii", $id_guru, $id_guru);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

$conn = new mysqli('localhost', 'root', '', 'garuda'); // Assume default credentials
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$res = getNotifikasiTugasGuru($conn, 1); // Test with Guru ID 1
print_r($res);
$conn->close();
?>
