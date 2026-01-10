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

  public function registerScan($employee_code, $doctor_code, $chemist_code, $quantity)
  {
    $result =  $this->update("INSERT INTO scans(employee_code, doctor_code, chemist_code, quantity, ip_address, created_at) VALUES(
      $employee_code, 
      $doctor_code, 
      '$chemist_code', 
      $quantity,
      '" . $_SERVER['REMOTE_ADDR'] . "',
      NOW()
    )");

    if ($result !== false) {
      return true;
    } else {
      return false;
    }
  }

  public function getAllScans()
  {
    $result =  $this->select("SELECT sc.*, e.state, e.zone, e.region, e.hq FROM scans sc LEFT JOIN employees e ON e.employee_code = sc.employee_code");

    if ($result !== false && isset($result->num_rows) && $result->num_rows > 0) {
      $data = array();
      while ($row = $result->fetch_assoc()) {
        $data[] = $row;
      }
      return $data;
    }
    return false;
  }

  public function getAllScansByState()
  {
    $result =  $this->select("SELECT e.state, COUNT(*) as count FROM scans sc LEFT JOIN employees e ON sc.employee_code = e.employee_code GROUP BY e.state");

    if ($result !== false && isset($result->num_rows) && $result->num_rows > 0) {
      $data = array();
      while ($row = $result->fetch_assoc()) {
        $data[] = $row;
      }
      return $data;
    }
    return false;
  }

  public function getAllScansCount()
  {
    $result =  $this->select("SELECT COUNT(*) as total FROM scans");

    if ($result !== false && isset($result->num_rows) && $result->num_rows > 0) {
      $row = $result->fetch_assoc();
      return $row['total'];
    }
    return 0;
  }
}
