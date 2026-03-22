<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

ini_set('display_errors', 0);
set_exception_handler(function($exception) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => $exception->getMessage()
    ]);
    exit;
});

require_once __DIR__ . '/../backend/config/Connection.php';
require_once __DIR__ . '/../backend/controllers/NutritionistController.php';
require_once __DIR__ . '/../backend/routes/NutritionistRouter.php';

$controller = new NutritionistController();
$router = new NutritionistRouter($controller);
$router->handleRequest();