<?php
$conn = new mysqli('localhost', 'root', '', 'garuda');

$sql = "CREATE TABLE IF NOT EXISTS `dashboard_notifications` (
  `id_notif` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `role` varchar(50) NOT NULL,
  `type` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `body` text DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `metadata` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_notif`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if ($conn->query($sql)) {
    echo "dashboard_notifications created.\n";
} else {
    echo "Error: " . $conn->error . "\n";
}
