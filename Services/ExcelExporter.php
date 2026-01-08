<?php

require_once PROJECT_ROOT_PATH . "./Model/Database.php";

ini_set('memory_limit', '512M');

class ExcelExporter extends Database
{
  public static function export(array $data, array $headerMap, string $filename = 'export.xlsx', string $sheetName = 'Sheet 1')
  {
    if (empty($data)) {
      die("No data to export");
    }

    $columns = array_keys($headerMap);         // e.g., ['request_date', 'reference_enquiryid', ...]
    $headerRow = array_values($headerMap);

    $writer = new XLSXWriter();
    $writer->writeSheetRow($sheetName, $headerRow); // Add header with readable labels

    // Write each row according to headerMap order
    foreach ($data as $row) {
      $orderedRow = [];
      foreach ($columns as $col) {
        $value = $row[$col] ?? '';

        $orderedRow[] = $value;
      }
      $writer->writeSheetRow($sheetName, $orderedRow);
    }

    // Send as download
    header('Content-disposition: attachment; filename="' . $filename . '"');
    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header('Content-Transfer-Encoding: binary');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    $writer->writeToStdOut();
    exit;
  }
}
