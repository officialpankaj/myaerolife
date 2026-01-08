<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");

require __DIR__ . "/inc/bootstrap.php";
require PROJECT_ROOT_PATH . "/Services/JWTAuthentication.php";
require PROJECT_ROOT_PATH . "/Services/ExcelExporter.php";
require PROJECT_ROOT_PATH . "/Services/Logger.php";

require PROJECT_ROOT_PATH . "/Controller/Api/AdminController.php";

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

// Logging the endpoints requested
if (ENV === "PROD") {
  $requestBody = file_get_contents('php://input');
  Logger::logInfo("Request: " . $_SERVER['REQUEST_METHOD'] . " " . $_SERVER['REQUEST_URI']);
  Logger::logInfo("Request Body: " . $requestBody);
}

if (isset($uri[1])) {
  $admincontroller   = new AdminController();
  $jwtAuthentication = new JWTAuthentication();

  switch ($uri[1]) {

    case 'admin':
      switch ($uri[2]) {
        case 'login':
          $admincontroller->login_callback();
          break;
        case 'verify-token':
          $userData = $jwtAuthentication->validate();
          $admincontroller->verify_token_callback($userData);
          break;
        case 'employees':
          $userData = $jwtAuthentication->validate();
          $admincontroller->employees_callback($userData);
          break;
        case 'doctors':
          $userData = $jwtAuthentication->validate();
          $admincontroller->doctors_callback($userData);
          break;
        case 'chemists':
          $userData = $jwtAuthentication->validate();
          $admincontroller->chemists_callback($userData);
          break;
        case 'register-scan':
          $userData = $jwtAuthentication->validate();
          $admincontroller->register_scan_callback($userData);
          break;
        case 'all-scans':
          $userData = $jwtAuthentication->validate();
          $admincontroller->all_scans_callback($userData);
          break;
        case 'all-scans-by-state':
          $userData = $jwtAuthentication->validate();
          $admincontroller->all_scans_by_state_callback($userData);
          break;
        default:
          header("HTTP/1.1 404 Not Found");
          $response = array();
          $response['code'] = 404;
          $response['status'] = 'error';
          $response['message'] = 'Request URL Not Found';
          header("Content-Type: application/json");
          echo json_encode($response);
          exit();
      }
      // no break

    default:
      header("HTTP/1.1 404 Not Found");
      $response = array();
      $response['code'] = 404;
      $response['status'] = 'error';
      $response['message'] = 'Request URL Not Found';
      header("Content-Type: application/json");
      echo json_encode($response);
      exit();
  }
}
