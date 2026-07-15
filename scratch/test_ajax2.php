<?php
$postdata = http_build_query(
    array(
        'id_kelas' => '1', // example class ID
        'id_kjm' => '2', // example materi ID
        'label' => 'Materi'
    )
);

$opts = array('http' =>
    array(
        'method'  => 'POST',
        'header'  => "Content-Type: application/x-www-form-urlencoded\r\n" .
                     "Cookie: ci_session=test\r\n", // mock cookie? might need auth
        'content' => $postdata,
        'ignore_errors' => true // to capture 500 error body
    )
);

$context  = stream_context_create($opts);
$result = file_get_contents('http://localhost/garuda_cbt/kelasstatus/loadstatus', false, $context);

echo "HTTP Response:\n";
echo $http_response_header[0] . "\n";
echo "Body:\n";
echo $result;
