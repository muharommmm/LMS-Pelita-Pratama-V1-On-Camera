<?php
$conn = new mysqli('localhost', 'root', '', 'garuda');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "CREATE TABLE IF NOT EXISTS `chat_messages` (
  `id_chat` int(11) NOT NULL AUTO_INCREMENT,
  `pengirim_id` int(11) NOT NULL,
  `penerima_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_chat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if ($conn->query($sql) === TRUE) {
    echo "Table chat_messages created successfully\n";
} else {
    echo "Error creating table: " . $conn->error . "\n";
}

$conn->close();
?>
