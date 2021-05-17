<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

// START DEBUG OUTPUT CAPTURE
ob_start();

require_once '../autoload.php';

use App\controllers\UserController;

$success = false;

$input_data = file_get_contents('php://input');
$input_json = json_decode($input_data, true);

// Get parameters
$user = $input_json['user'] ?? '';
$password = $input_json['password'] ?? '';

$user_id = UserController::validate_login($user, $password);
if ($user_id > 0) {
    session_start();
    $_SESSION['user_id'] = $user_id;

    $success = true;
}

// END DEBUG OUTPUT CAPTURE
$debug_output = ob_get_clean();

echo json_encode([
    'success' => $success,
    'debug_output' => $debug_output
]);