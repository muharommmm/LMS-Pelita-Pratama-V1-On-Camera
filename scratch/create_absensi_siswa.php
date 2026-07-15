<?php
$conn = new mysqli('localhost', 'root', '', 'garuda');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "CREATE TABLE IF NOT EXISTS `absensi_siswa` (
  `id_absensi` int(11) NOT NULL AUTO_INCREMENT,
  `class_id` int(11) DEFAULT NULL,
  `mapel_id` int(11) DEFAULT NULL,
  `tutor_id_input` int(11) DEFAULT NULL,
  `tp_id` int(11) DEFAULT NULL,
  `smt_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `jenis_kegiatan` varchar(100) DEFAULT NULL,
  `method` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_absensi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if ($conn->query($sql) === TRUE) {
    echo "Table absensi_siswa created successfully\n";
} else {
    echo "Error creating table: " . $conn->error . "\n";
}

$conn->close();
?>
