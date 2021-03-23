<?php

require('env.php');
require('includes/PaykickstartAPI.php');

$response = [
    'success' => 0, 
    'message' => "",
    'data' => [],
];

$json = file_get_contents('php://input');
$data = json_decode($json, true);

$validMethods = ['license_status'];

if (empty($data['pkApiMethod'])) {
    $response['message'] = "No method supplied.";
    echo json_encode($response);
    exit;
}

if (empty($data['licenseKey'])) {
    $response['message'] = "No license key supplied.";
    echo json_encode($response);
    exit;
}

if (!in_array($data['pkApiMethod'], $validMethods)) {
    $response['message'] = "Invalid method {$data['pkApiMethod']}.";
    echo json_encode($response);
    exit;
}

$key = $data['licenseKey'];
$pk = new PaykickstartAPI($config['auth_token']);

switch($data['pkApiMethod']) {
    case 'license_status':
        echo json_encode($pk->get_status($key));
        break;
    default: 
        $response['message'] = 'Something went wrong';
        echo json_encode($response);
        break;
        
}


