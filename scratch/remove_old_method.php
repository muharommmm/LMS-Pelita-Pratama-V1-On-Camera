<?php
// Script to remove the OLD obfuscated getRekapMateriSemester from line 7
// while keeping the new clean version at line 285+

$file = 'application/models/Kelas_model.php';
$content = file_get_contents($file);
$lines = explode("\n", $content);

// The old method is on line 7 (index 6) - the big obfuscated line
$line7 = $lines[6];

// Find the old method declaration in line 7
// It starts with: public function getRekapMateriSemester($id_kelas, $id_materi = null) {
$methodStart = 'public function getRekapMateriSemester($id_kelas, $id_materi = null) {';
$pos = strpos($line7, $methodStart);

if ($pos === false) {
    echo "OLD method not found in line 7. Checking all lines...\n";
    foreach ($lines as $i => $line) {
        $p = strpos($line, $methodStart);
        if ($p !== false) {
            echo "Found at line " . ($i+1) . ", position $p\n";
        }
    }
    exit(1);
}

echo "Found OLD method in line 7 at position $pos\n";

// We need to find where this method ends and the next method begins
// The pattern is: } public function nextMethod(...)
// We need to find the closing brace of getRekapMateriSemester followed by next public function

// Find the next "public function" after the start of getRekapMateriSemester
$searchFrom = $pos + strlen($methodStart);
$nextMethod = strpos($line7, '} public function ', $searchFrom);

if ($nextMethod === false) {
    echo "Could not find the end of the old method!\n";
    exit(1);
}

// The method body ends at $nextMethod + 1 (the closing brace)
$methodEnd = $nextMethod + 2; // Include "} " before "public function"

$oldMethod = substr($line7, $pos, $methodEnd - $pos);
echo "Old method length: " . strlen($oldMethod) . " chars\n";
echo "First 100 chars: " . substr($oldMethod, 0, 100) . "\n";
echo "Last 100 chars: " . substr($oldMethod, -100) . "\n";

// Remove the old method from line 7
$newLine7 = substr($line7, 0, $pos) . substr($line7, $methodEnd);
$lines[6] = $newLine7;

// Write back
$newContent = implode("\n", $lines);
file_put_contents($file, $newContent);

echo "\nDone! Old getRekapMateriSemester removed from line 7.\n";
echo "File size before: " . strlen($content) . "\n";
echo "File size after: " . strlen($newContent) . "\n";
