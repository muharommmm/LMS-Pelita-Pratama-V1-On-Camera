<?php
// Read the file
$file = 'application/models/Kelas_model.php';
$content = file_get_contents($file);

// The old obfuscated method starts with "public function getRekapMateriSemester"
// but encoded in hex. Let's search for the function name literally
$search = 'getRekapMateriSemester';

// Find all occurrences
$offset = 0;
$positions = [];
while (($pos = strpos($content, $search, $offset)) !== false) {
    // Get context around the position
    $start = max(0, $pos - 50);
    $context = substr($content, $start, 200);
    $line = substr_count($content, "\n", 0, $pos) + 1;
    $positions[] = ['pos' => $pos, 'line' => $line, 'context_start' => substr($content, $start, 100)];
    $offset = $pos + 1;
}

echo "Found " . count($positions) . " occurrences of '$search':\n\n";
foreach ($positions as $i => $p) {
    echo "Occurrence " . ($i+1) . " at position {$p['pos']} (line {$p['line']}):\n";
    echo "Context: " . json_encode(substr($content, max(0,$p['pos']-30), 80)) . "\n\n";
}

// Now let's also look for hex-encoded version
// "getRekapMateriSemester" in hex would be various patterns
// Let's search for the pattern in the obfuscated line
$line7 = '';
$lines = explode("\n", $content);
if (isset($lines[6])) {
    $line7 = $lines[6]; // 0-indexed, so line 7 = index 6
    echo "Line 7 length: " . strlen($line7) . "\n";
    
    // Search for the function declaration in line 7
    $funcPos = strpos($line7, 'getRekapMateriSemester');
    if ($funcPos !== false) {
        echo "Found in line 7 at position $funcPos\n";
        echo "Context: " . json_encode(substr($line7, max(0,$funcPos-50), 150)) . "\n";
    } else {
        echo "NOT found literally in line 7\n";
        // Maybe it's in decoded form somewhere
        // Let's eval the hex strings to see
    }
}

// Check line 285
if (isset($lines[284])) {
    echo "\nLine 285: " . trim(substr($lines[284], 0, 100)) . "\n";
}
