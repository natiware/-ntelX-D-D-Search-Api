<?php
$did = $_GET['did'];

//GET API DATA IN JSON
//$data = json_decode(file_get_contents('php://input'), true);
//$did = $data['did'];

$bucket_list = array(
    'pastes', 'darknet.tor', 'darknet.i2p', 'whois', 'usenet', 
    'leaks.private.general', 'leaks.private.comb', 'leaks.logs', 
    'leaks.public.wikileaks', 'leaks.public.general', 'dumpster', 
    'documents.public.scihub'
);

$response = false;
foreach ($bucket_list as $bucket) {
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.intelx.io/file/read?type=1&systemid=' . $did . '&k=X-KEY&bucket=' . $bucket,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HEADER => true,
    ));

    $response = curl_exec($curl);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    if ($http_code == 200 && !empty($response)) {
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);

        if (!empty($body)) {
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $did . '.txt"');
            echo $body;
            exit;
        }
    }
}

header('Content-Type: application/json');
http_response_code(404);
echo json_encode(array('message' => 'No data'));
?>
