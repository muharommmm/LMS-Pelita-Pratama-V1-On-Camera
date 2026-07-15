<?php
// Simulasikan get_ebooks_for_student
$conn = new mysqli('localhost', 'root', '', 'garuda');
$class_id = 2; // misal kelas 8 NON REGULER
$user_id = 56; // misal id_siswa/user_id

$sql = "SELECT ebooks.* FROM ebooks
        LEFT JOIN master_kelas ON master_kelas.id_kelas = ebooks.class_id
        WHERE (ebooks.class_id = '' OR ebooks.class_id IS NULL OR FIND_IN_SET('$class_id', ebooks.class_id) > 0)";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "Found Ebook ID: " . $row["id_ebook"]. " - Class_id in DB: " . $row["class_id"] . "\n";
    }
} else {
    echo "No ebooks found for class $class_id\n";
}
?>
