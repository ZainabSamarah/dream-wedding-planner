<?php
require_once 'config.php';

header('Content-Type: application/json');

$response = [
    'loggedin' => false,
    'firstName' => '',
    'email' => ''
];

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
    $response['loggedin'] = true;
    $response['firstName'] = $_SESSION['first_name'] ?? '';
    $response['email'] = $_SESSION['email'] ?? '';
    $response['role'] = $_SESSION['role'] ?? 'user';
}

echo json_encode($response);
?>