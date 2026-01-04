<?php

require_once PROJECT_ROOT_PATH . "./Model/Database.php";

ini_set('memory_limit', '512M');

class ExcelExporter extends Database
{
  public static function export(array $data, array $headerMap, string $filename = 'export.xlsx', string $sheetName = 'Sheet 1', array $bucketNames = [])
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

  public function exportV2(array $data, array $headerMap, string $filename = 'export.xlsx', $bucketNames = [])
  {
    if (empty($data)) {
      die("No data to export");
    }

    $columns = array_keys($headerMap);
    $headerRow = array_values($headerMap);

    $writer = new XLSXWriter();
    $writer->writeSheetRow('Claims', $headerRow);

    $rowStyle = [
      'font' => 'Calibri',
      'font-size' => 11,
      // No border, no wrap_text, no fill for data rows
      'halign' => 'left',
      'valign' => 'center'
    ];

    foreach ($data as $claim) {
      $writer->writeSheetRow('Claims', $this->formatClaimRow($claim, $columns, $bucketNames), $rowStyle);
    }

    // Output file
    $filename = 'claims_export_' . date('Y-m-d_H-i-s') . '.xlsx';
    header('Content-disposition: attachment; filename="' . $filename . '"');
    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header('Content-Transfer-Encoding: binary');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    $writer->writeToStdOut();
    exit;
  }

  private function formatClaimRow(array $row, array $columns, array $bucketNames): array
  {
    $ordered = [];

    foreach ($columns as $col) {
      $value = $row[$col] ?? '';

      switch ($col) {
        case 'claim_type':
          $value = CLAIM_TYPES[$value] ?? $value;
          break;

        case 'hospitalized':
        case 'download_legal':
        case 'not_interested':
          $value = YES_NO_OPTIONS_NAMES[$value] ?? $value;
          break;

        case 'rejection_type':
          if (isset($row['claim_type']) && in_array($row['claim_type'], ['health_insurance', 'health_insurance_critical_illness'])) {
            $value = HEALTH_INSURANCE_REJECTION_REMARKS[$value] ?? $value;
          } else {
            $value = REJECTION_REMARKS[$value] ?? $value;
          }
          break;

        case 'mr_rejection_type':
          $value = REJECTION_REMARKS[$value] ?? $value;
          break;

        case 'prioritized':
        case 'download_legal':
          $value = YES_NO_OPTIONS_NAMES[$value] ?? $value;
          break;

        case 'complaint_type':
        case 'hospitalization_complaint_type':
          $decoded = json_decode($value, true);
          if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $value = implode(', ', array_map(fn($d) => DISPUTE_TYPES[$d['value']] ?? $d['label'], $decoded));
          } else {
            $value = '';
          }
          break;

        case 'case_acceptance_type':
          $value = CASE_ACCEPTANCE_TYPES[$value] ?? $value;
          break;

        case 'submission_type':
          $value = CASE_SUBMISSION_TYPES[$value] ?? $value;
          break;

        case 'service_agreement':
          $value = SERVICE_AGREEMENT_TYPE_NAMES[$value] ?? $value;
          break;

        case 'admin_approval':
        case 'doctor_approval':
          $value = APPROVAL_STATUS_NAMES[$value] ?? $value;
          break;

        case 'bucket':
          $value = $bucketNames[$value] ?? $value;
          break;

        case 'moved_at':
          $value = getDaysSinceMoved($value);
          break;

        case 'mr_submission_mode':
          $value = MR_SUBMISSION_MODE[$value] ?? $value;
          break;

        case 'mr_company_reply_status':
          $value = MR_COMPANY_REPLY_STATUS[$value] ?? $value;
          break;

        default:
          // No transformation needed
          break;
      }

      $ordered[] = $value;
    }

    return $ordered;
  }
}
