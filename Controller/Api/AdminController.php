<?php

class AdminController extends BaseController
{
  public function login_callback()
  {
    $response = array(
      'code' => 404
    );

    if (strtoupper($_SERVER["REQUEST_METHOD"]) == "POST") {
      // Takes raw data from the request
      $json = file_get_contents('php://input');

      // Converts it into a PHP object
      $data = json_decode($json);

      $employee_code  = (isset($data->employee_code)) ? $data->employee_code : "NULL";
      $password  = (isset($data->password)) ? $data->password : "NULL";
      $remeberme  = (isset($data->remeberme)) ? $data->remeberme : "NULL";

      $adminModel = new AdminModel();
      $jwtAuthentication = new JWTAuthentication();
      if ($adminModel->checkEmployee($employee_code)) {
        if (($userdata = $adminModel->validateLogin($employee_code, $password)) !== false) {
          $response['code'] = 200;
          $response['status'] = 'success';
          $response['message'] = 'Login Successfully!';
          $response['userdata'] = $userdata;
          $response['accesstoken'] = $jwtAuthentication->encode($userdata, $remeberme);
          header("HTTP/1.1 200 Ok");
        } else {
          $response['code'] = 400;
          $response['status'] = 'fail';
          $response['message'] = "Incorrect Password. Please try again.";
          header("HTTP/1.1 200 Ok");
        }
      } else {
        $response['code'] = 404;
        $response['status'] = 'fail';
        $response['message'] = "Couldn't find your Employee Code";
        header("HTTP/1.1 200 Ok");
      }

      $this->sendOutput($response);
    } else {
      $response['code'] = 405;
      $response['status'] = 'error';
      $response['message'] = 'Method Not Allowed! Only POST requests are allowed';
      header("HTTP/1.1 405 Method Not Allowed");
      $this->sendOutput($response);
    }
  }

  public function verify_token_callback($userdata)
  {
    $response = array(
      'code' => 404
    );
    if (strtoupper($_SERVER["REQUEST_METHOD"]) == "GET") {
      $adminModel = new AdminModel();

      $data = $adminModel->getEmployeeById($userdata->employee_code);

      $response['userdata'] = $data;
      $response['code'] = 200;
      $response['status'] = 'success';
      $response['message'] = 'Token Verified';
      header("HTTP/1.1 200 Ok");
      $this->sendOutput($response);
    } else {
      $response['code'] = 405;
      $response['status'] = 'error';
      $response['message'] = 'Method Not Allowed! Only GET requests are allowed';
      header("HTTP/1.1 405 Method Not Allowed");
      $this->sendOutput($response);
    }
  }

  public function employees_callback()
  {
    $response = array(
      'code' => 404
    );

    if (strtoupper($_SERVER["REQUEST_METHOD"]) == "GET") {
      $adminModel = new AdminModel();
      if (($data = $adminModel->getEmployeesList()) !== false) {
        $response['code'] = 200;
        $response['status'] = 'success';
        $response['data'] = $data;
        $response['message'] = 'Data Found';
        header("HTTP/1.1 200 OK");
      } else {
        $response['code'] = 404;
        $response['status'] = 'fail';
        $response['message'] = 'No Data Found';
        header("HTTP/1.1 404 Not Found");
      }

      $this->sendOutput($response);
    } else {
      $response['code'] = 405;
      $response['status'] = 'error';
      $response['message'] = 'Method Not Allowed! Only GET requests are allowed';
      header("HTTP/1.1 405 Method Not Allowed");
      $this->sendOutput($response);
    }
  }

  public function doctors_callback($userdata)
  {
    $response = array(
      'code' => 404
    );

    if (strtoupper($_SERVER["REQUEST_METHOD"]) == "GET") {
      $adminModel = new AdminModel();
      if (($data = $adminModel->getDoctorsList($userdata->employee_code)) !== false) {
        $response['code'] = 200;
        $response['status'] = 'success';
        $response['data'] = $data;
        $response['message'] = 'Data Found';
        header("HTTP/1.1 200 OK");
      } else {
        $response['code'] = 404;
        $response['status'] = 'fail';
        $response['message'] = 'No Data Found';
        header("HTTP/1.1 404 Not Found");
      }

      $this->sendOutput($response);
    } else {
      $response['code'] = 405;
      $response['status'] = 'error';
      $response['message'] = 'Method Not Allowed! Only GET requests are allowed';
      header("HTTP/1.1 405 Method Not Allowed");
      $this->sendOutput($response);
    }
  }

  public function chemists_callback($userdata)
  {
    $response = array(
      'code' => 404
    );

    if (strtoupper($_SERVER["REQUEST_METHOD"]) == "GET") {
      $adminModel = new AdminModel();
      if (($data = $adminModel->getChemistsList($userdata->employee_code)) !== false) {
        $response['code'] = 200;
        $response['status'] = 'success';
        $response['data'] = $data;
        $response['message'] = 'Data Found';
        header("HTTP/1.1 200 OK");
      } else {
        $response['code'] = 404;
        $response['status'] = 'fail';
        $response['message'] = 'No Data Found';
        header("HTTP/1.1 404 Not Found");
      }

      $this->sendOutput($response);
    } else {
      $response['code'] = 405;
      $response['status'] = 'error';
      $response['message'] = 'Method Not Allowed! Only GET requests are allowed';
      header("HTTP/1.1 405 Method Not Allowed");
      $this->sendOutput($response);
    }
  }

  public function register_scan_callback($userdata)
  {
    $response = array(
      'code' => 404
    );

    if (strtoupper($_SERVER["REQUEST_METHOD"]) == "POST") {
      // Takes raw data from the request
      $json = file_get_contents('php://input');

      // Converts it into a PHP object
      $data = json_decode($json);

      $doctor_code  = (isset($data->doctor_code)) && $data->doctor_code !== "" ? $data->doctor_code : null;
      $doctor_name  = (isset($data->doctor_name)) && $data->doctor_name !== "" ? $data->doctor_name : null;
      $chemist_code  = (isset($data->chemist_code)) && $data->chemist_code !== "" ? $data->chemist_code : null;
      $chemist_name  = (isset($data->chemist_name)) && $data->chemist_name !== "" ? $data->chemist_name : null;
      $quantity  = (isset($data->quantity)) ? $data->quantity : null;
      $launch_status  = (isset($data->launch_status)) ? $data->launch_status : null;

      $adminModel = new AdminModel();
      if (($adminModel->registerScan($userdata->employee_code, $doctor_code, $doctor_name, $chemist_code, $chemist_name, $quantity, $launch_status)) !== false) {
        $response['code'] = 200;
        $response['status'] = 'success';
        $response['message'] = 'Scan Registered Successfully';
        header("HTTP/1.1 200 OK");
      } else {
        $response['code'] = 404;
        $response['status'] = 'fail';
        $response['message'] = 'Failed to Register Scan';
        header("HTTP/1.1 404 Not Found");
      }

      $this->sendOutput($response);
    } else {
      $response['code'] = 405;
      $response['status'] = 'error';
      $response['message'] = 'Method Not Allowed! Only POST requests are allowed';
      header("HTTP/1.1 405 Method Not Allowed");
      $this->sendOutput($response);
    }
  }

  public function all_scans_callback($userdata)
  {
    $response = array(
      'code' => 404
    );

    if (strtoupper($_SERVER["REQUEST_METHOD"]) == "GET") {
      $adminModel = new AdminModel();
      if (($data = $adminModel->getAllScans($userdata)) !== false) {
        $excelExporter = new ExcelExporter();
        $excelExporter->export($data, EXPORT_COLUMN_NAMES, 'all_scans.xlsx', 'All Scans');
      } else {
        $response['code'] = 404;
        $response['status'] = 'fail';
        $response['message'] = 'No Data Found';
        header("HTTP/1.1 404 Not Found");
      }

      $this->sendOutput($response);
    } else {
      $response['code'] = 405;
      $response['status'] = 'error';
      $response['message'] = 'Method Not Allowed! Only GET requests are allowed';
      header("HTTP/1.1 405 Method Not Allowed");
      $this->sendOutput($response);
    }
  }

  public function all_scans_by_state_callback($userdata)
  {
    $response = array(
      'code' => 404
    );

    if (strtoupper($_SERVER["REQUEST_METHOD"]) == "GET") {
      $adminModel = new AdminModel();
      if (($data = $adminModel->getAllScansByState($userdata)) !== false) {
        $totalPOBCollected = $adminModel->getAllPOBCount($userdata);
        $totalDoctors = $adminModel->getAllDoctorsCount($userdata);
        $response['code'] = 200;
        $response['status'] = 'success';
        $response['data'] = $data;
        $response['total_pob'] = $totalPOBCollected;
        $response['total_doctors'] = $totalDoctors;
        $response['message'] = 'Data Found';
        header("HTTP/1.1 200 OK");
      } else {
        $response['code'] = 404;
        $response['status'] = 'fail';
        $response['message'] = 'No Data Found';
        header("HTTP/1.1 404 Not Found");
      }

      $this->sendOutput($response);
    } else {
      $response['code'] = 405;
      $response['status'] = 'error';
      $response['message'] = 'Method Not Allowed! Only GET requests are allowed';
      header("HTTP/1.1 405 Method Not Allowed");
      $this->sendOutput($response);
    }
  }
}
