<?php

use PHPMailer\PHPMailer\Exception;

require_once PROJECT_ROOT_PATH . "/Model/Database.php";

class AdminModel extends Database
{
  public function checkEmployee($id = null)
  {
    if ($id !== null) {
      $result =  $this->select("SELECT id FROM employees WHERE employee_code = '" . $id . "' AND active = '" . EMPLOYEE_STATUS['ACTIVE'] . "' LIMIT 1");
      if ($result !== false && isset($result->num_rows) && $result->num_rows > 0) {
        return true;
      } else {
        return false;
      }
    }

    return false;
  }

  public function validateLogin($employee_code, $password)
  {
    $result =  $this->select("SELECT * FROM employees WHERE employee_code = '$employee_code' and binary password = '$password' AND active = '" . EMPLOYEE_STATUS['ACTIVE'] . "'");
    if ($result !== false && isset($result->num_rows) && $result->num_rows > 0) {
      $result = $result->fetch_assoc();

      return $result;
    } else {
      return false;
    }
  }

  public function getEmployeeById($employee_code)
  {
    $result =  $this->select("SELECT * FROM employees WHERE employee_code = '$employee_code' AND active = '" . EMPLOYEE_STATUS['ACTIVE'] . "'");
    if ($result !== false && isset($result->num_rows) && $result->num_rows > 0) {
      $result = $result->fetch_assoc();

      return $result;
    } else {
      return false;
    }
  }

  public function getEmployeesList()
  {
    $result =  $this->select("SELECT id, employee_code, role, state, zone, region, hq, chairname, created_at, updated_at FROM employees WHERE role= '" . USER_ROLES['HERO'] . "' AND active = '" . EMPLOYEE_STATUS['ACTIVE'] . "'");

    if ($result !== false && isset($result->num_rows) && $result->num_rows > 0) {
      $data = array();
      while ($row = $result->fetch_assoc()) {
        $data[] = $row;
      }
      return $data;
    }
    return false;
  }

  public function getDoctorsList($employee_code)
  {
    $whereCondition = "";

    if ($employee_code) {
      $whereCondition .= " AND employee_code= '$employee_code'";
    }

    $result =  $this->select("SELECT * FROM doctors WHERE true $whereCondition");

    if ($result !== false && isset($result->num_rows) && $result->num_rows > 0) {
      $data = array();
      while ($row = $result->fetch_assoc()) {
        $data[] = $row;
      }
      return $data;
    }
    return false;
  }

  public function getChemistsList($employee_code)
  {
    $whereCondition = "";

    if ($employee_code) {
      $whereCondition .= " AND employee_code= '$employee_code'";
    }

    $result =  $this->select("SELECT * FROM chemists WHERE true $whereCondition");

    if ($result !== false && isset($result->num_rows) && $result->num_rows > 0) {
      $data = array();
      while ($row = $result->fetch_assoc()) {
        $data[] = $row;
      }
      return $data;
    }
    return false;
  }

  public function registerScan($employee_code, $doctor_code, $doctor_name, $chemist_code, $chemist_name, $quantity, $launch_status)
  {
    $result =  $this->update("INSERT INTO scans(employee_code, doctor_code, doctor_name, chemist_code, chemist_name, quantity, launch_status, ip_address, created_at) VALUES(
      $employee_code, 
      " . ($doctor_code === null ? "NULL" : "$doctor_code") . ", 
      " . ($doctor_name === null ? "NULL" : "'$doctor_name'") . ",
      " . ($chemist_code === null ? "NULL" : "'$chemist_code'") . ", 
      " . ($chemist_name === null ? "NULL" : "'$chemist_name'") . ",
      $quantity,
      '$launch_status', 
      '" . $_SERVER['REMOTE_ADDR'] . "',
      NOW()
    )");

    if ($result !== false) {
      return true;
    } else {
      return false;
    }
  }

  public function getAllScans($userdata)
  {
    $result =  $this->select("SELECT sc.*, COALESCE(sc.doctor_code, sc.doctor_name) AS doctor, COALESCE(sc.chemist_code, sc.chemist_name) AS chemist, e.state, e.zone, e.region, e.hq FROM scans sc LEFT JOIN employees e ON e.employee_code = sc.employee_code WHERE e.region = '" . $userdata->region . "'");

    if ($result !== false && isset($result->num_rows) && $result->num_rows > 0) {
      $data = array();
      while ($row = $result->fetch_assoc()) {
        $data[] = $row;
      }
      return $data;
    }
    return false;
  }

  public function getAllScansByState($userdata)
  {
    $result = $this->select("
        SELECT 
          e.state, COALESCE(SUM(sc.quantity), 0) AS pob_count, COUNT(DISTINCT doctor_code) as doctor_count 
        FROM scans sc LEFT JOIN employees e 
          ON sc.employee_code = e.employee_code 
        GROUP BY e.state 
        ORDER BY e.state DESC
    ");


    if ($result !== false && isset($result->num_rows) && $result->num_rows > 0) {
      $data = array();
      while ($row = $result->fetch_assoc()) {
        $data[] = $row;
      }
      return $data;
    }
    return false;
  }

  public function getAllPOBCount()
  {
    $result =  $this->select("SELECT SUM(quantity) as total FROM scans");

    if ($result !== false && isset($result->num_rows) && $result->num_rows > 0) {
      $row = $result->fetch_assoc();
      return $row['total'];
    }
    return 0;
  }

  public function getAllDoctorsCount()
  {
    $result =  $this->select("SELECT COUNT(DISTINCT doctor_code) as total FROM scans");

    if ($result !== false && isset($result->num_rows) && $result->num_rows > 0) {
      $row = $result->fetch_assoc();
      return $row['total'];
    }
    return 0;
  }

  public function getAllRegionsCount()
  {
    $result =  $this->select("SELECT COUNT(DISTINCT region) as total FROM employees");

    if ($result !== false && isset($result->num_rows) && $result->num_rows > 0) {
      $row = $result->fetch_assoc();
      return $row['total'];
    }
    return 0;
  }
}
