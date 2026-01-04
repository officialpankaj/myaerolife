<?php
class Logger
{
  private static $logDir = __DIR__ . '/../logs';

  private static function getLogFile()
  {
    if (!is_dir(self::$logDir)) {
      mkdir(self::$logDir, 0777, true);
    }
    return self::$logDir . '/php-error-' . date('Y-m-d') . '.log';
  }

  public static function logError($message)
  {
    $logFile = self::getLogFile();
    $time = date('Y-m-d H:i:s');
    error_log("[$time] ERROR: $message\n", 3, $logFile);
  }

  public static function logInfo($message)
  {
    $logFile = self::getLogFile();
    $time = date('Y-m-d H:i:s');
    error_log("[$time] INFO: $message\n", 3, $logFile);
  }
}


// Report all errors but don’t display them
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Log PHP errors
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
  Logger::logError("[$errno] $errstr in $errfile on line $errline");
  return true; // prevent PHP default handler
});

// Log uncaught exceptions
set_exception_handler(function ($exception) {
  Logger::logError("Uncaught Exception: " . $exception->getMessage() . " in " . $exception->getFile() . ":" . $exception->getLine());
});

// Log script shutdown errors (fatal)
register_shutdown_function(function () {
  $error = error_get_last();
  if ($error !== null) {
    Logger::logError("Shutdown error: {$error['message']} in {$error['file']} on line {$error['line']}");
  }
});
