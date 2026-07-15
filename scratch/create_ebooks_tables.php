<?php
$conn = new mysqli('localhost', 'root', '', 'garuda');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql_ebooks = "CREATE TABLE IF NOT EXISTS `ebooks` (
  `id_ebook` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `class_id` int(11) DEFAULT NULL,
  `mapel_id` int(11) DEFAULT NULL,
  `ekstra_id` int(11) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_ebook`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if ($conn->query($sql_ebooks) === TRUE) {
    echo "Table ebooks created successfully\n";
} else {
    echo "Error creating ebooks table: " . $conn->error . "\n";
}

$sql_history = "CREATE TABLE IF NOT EXISTS `ebook_reading_history` (
  `id_history` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `ebook_id` int(11) NOT NULL,
  `last_page` int(11) NOT NULL DEFAULT 1,
  `total_pages` int(11) NOT NULL DEFAULT 1,
  `last_accessed` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_history`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if ($conn->query($sql_history) === TRUE) {
    echo "Table ebook_reading_history created successfully\n";
} else {
    echo "Error creating ebook_reading_history table: " . $conn->error . "\n";
}

$conn->close();
?>
