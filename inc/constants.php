<?php

define('USER_ROLES', [
  'HERO' => '0',
  'MANAGER' => '1'
]);

define('EMPLOYEE_STATUS', [
  'INACTIVE' => '0',
  'ACTIVE' => '1'
]);

define('EXPORT_COLUMN_NAMES', [
  'zone' => 'Zone',
  'region' => 'Region',
  'state' => 'State',
  'hq' => 'HQ',
  'employee_code' => 'Employee Code',
  'doctor' => 'Doctor Code/Name',
  'chemist' => 'Chemist Code/Name',
  'quantity' => 'Quantity',
  'launch_status' => 'Launch Status',
  'ip_address' => 'IP Address',
  'created_at' => 'Timestamp'
]);

define('LAUNCH_STATUS_NAMES', [
  '0' => 'Launched Today',
  '1' => 'Relaunching with input'
]);
