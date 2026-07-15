<?php
// Script to query ebooks and check class_id mapping
$conn = new mysqli('localhost', 'root', '', 'garuda');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id_ebook, title, class_id, mapel_id FROM ebooks";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "ID: " . $row["id_ebook"]. " - Title: " . $row["title"]. " - Class: " . $row["class_id"]. " - Mapel: " . $row["mapel_id"] . "\n";
    }
} else {
    echo "0 results";
}
$conn->close();
?>
