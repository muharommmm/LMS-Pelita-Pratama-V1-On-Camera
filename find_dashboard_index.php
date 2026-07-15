<?php
$content = file_get_contents('c:\\xampp\\htdocs\\garuda_cbt\\application\\controllers\\Dashboard.php');

$pos = stripos($content, 'public function index');
if ($pos !== false) {
    // Find matching braces
    $brace_count = 0;
    $start = strpos($content, '{', $pos);
    $end = $start;
    $len = strlen($content);
    for ($i = $start; $i < $len; $i++) {
        if ($content[$i] == '{') {
            $brace_count++;
        } elseif ($content[$i] == '}') {
            $brace_count--;
            if ($brace_count == 0) {
                $end = $i;
                break;
            }
        }
    }
    
    $buffer = substr($content, $pos, $end - $pos + 1);
    
    // Beautify the buffer
    $buffer = str_replace('goto ', "\ngoto ", $buffer);
    $buffer = preg_replace('/([a-zA-Z0-9_]+:)/', "\n$1\n", $buffer);
    $buffer = str_replace(';', ";\n", $buffer);
    $buffer = str_replace('{', "{\n", $buffer);
    $buffer = str_replace('}', "}\n", $buffer);
    
    echo $buffer;
} else {
    echo "Not found 'index' in Dashboard.php\n";
}
