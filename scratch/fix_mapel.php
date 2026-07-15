<?php
$conn = new mysqli('localhost', 'root', '', 'garuda');
$sql = "UPDATE ebooks SET mapel_id = 7 WHERE id_ebook IN (6,7,8,9,10)";
$conn->query($sql);
echo "Updated mapel_id for ebooks 6-10 to 7 (Bahasa Inggris).\n";
?>
