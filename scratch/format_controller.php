<?php
$content = file_get_contents('application/controllers/Kelasnilai.php');

// Simple formatting: add newlines after semicolons and braces
$formatted = str_replace(
    ['{', '}', ';'],
    ["{\n", "}\n", ";\n"],
    $content
);

file_put_contents('scratch/formatted_controller.php', $formatted);
echo "Formatted controller written to scratch/formatted_controller.php\n";
