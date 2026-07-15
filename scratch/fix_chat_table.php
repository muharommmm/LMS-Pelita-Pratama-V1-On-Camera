<?php
$conn = new mysqli('localhost', 'root', '', 'garuda');

$conn->query("DROP TABLE IF EXISTS `chat_messages`");

$sql = "CREATE TABLE `chat_messages` (
  `id_pesan` int(11) NOT NULL AUTO_INCREMENT,
  `pengirim_id` int(11) NOT NULL,
  `pengirim_role` varchar(50) DEFAULT 'siswa',
  `penerima_id` int(11) NOT NULL,
  `pesan` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_pesan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if ($conn->query($sql)) {
    echo "chat_messages fixed.\n";
} else {
    echo "Error: " . $conn->error . "\n";
}
