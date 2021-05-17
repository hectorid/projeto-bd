<?php
namespace App;

const ENABLE_JSON_OUTPUT = true;

if (ENABLE_JSON_OUTPUT)
    header('Content-Type: application/json; charset=utf-8');

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);


// START DEBUG OUTPUT CAPTURE
ob_start();

require_once '../autoload.php';
require_once '../functions.php';

$method = strtolower($_SERVER['REQUEST_METHOD']);
var_dump($method);

// Get the reousrce by remove the "/api" prefix from the URI
$resource = str_replace('/api', '', $_SERVER['REQUEST_URI']);

$input_data = file_get_contents('php://input');
var_dump($input_data);
$input_json = json_decode($input_data, true);
var_dump($input_json);


$match = null;
// Check if the resource path matches any of the routes
foreach (API_ROUTES as $route) {
    $path_regex = pathToRegex($route['path']);

    if (preg_match($path_regex, $resource)) {
        $match = $route;
        break;
    }
}

var_dump($match);

$success = false;
$output_data = [];
if (!$match)
    $http_response_code = 404; // Not found
else if (!in_array($method, $match['methods']))
    $http_response_code = 405; // Method Not Allowed
else {
    $class = "App\\controllers\\{$match['controller']}Controller";
    var_dump("{$class}::{$method}");

    if (!method_exists($class, $method)) {
        $http_response_code = 501; // Not Implemented
    }
    else  {
        // get the parameters from the URI
        $params = getParamsFromResource($match['path'], $resource);
        var_dump($params);

        $args = [$params];
        if (in_array($method, [POST, PUT]))
            $args[] = $input_json;

        // Call the controller method
        $output_data = call_user_func_array([$class, $method], $args);
        var_dump($output_data);

        if (empty($output_data))
            $http_response_code = 500; // Internal Server Error
        else {
            $http_response_code = 200; // OK
            $success = true;
        }
    }
}

// END DEBUG OUTPUT CAPTURE
$debug_output = ob_get_clean();

if (!ENABLE_JSON_OUTPUT)
    echo $debug_output;
else {
    $response = [
        'success' => $success,
        'data' => $output_data,
        'debug_output' => $debug_output
    ];
    if (DEBUG_MODE)
        $response['debug_output'] = $debug_output;

    http_response_code($http_response_code);
    echo json_encode($response);
}

