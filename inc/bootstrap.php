<?php
define("PROJECT_ROOT_PATH", __DIR__ . "/../");

// include main configuration file
require_once PROJECT_ROOT_PATH . "/inc/config.php";

// include the utility and constants
require_once PROJECT_ROOT_PATH . "/inc/constants.php";
require_once PROJECT_ROOT_PATH . "/inc/helpers.php";

// include the base controller file
require_once PROJECT_ROOT_PATH . "/Controller/Api/BaseController.php";

// include the model file
require_once PROJECT_ROOT_PATH . "/Model/AdminModel.php";

// include PHP Mailer files
require PROJECT_ROOT_PATH . "./inc/php-jwt/src/JWT.php";
require PROJECT_ROOT_PATH . "./inc/php-jwt/src/Key.php";
require PROJECT_ROOT_PATH . "./inc/php-jwt/src/JWTExceptionWithPayloadInterface.php";
require PROJECT_ROOT_PATH . "./inc/php-jwt/src/ExpiredException.php";

// include PHP xlsxwriter files
require PROJECT_ROOT_PATH . "./inc/xlsxwriter/xlsxwriter.class.php";
