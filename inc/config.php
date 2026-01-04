<?php

define("ENV", "DEV");
// define("ENV", "TEST");
// define("ENV", "PROD");

define("JWT_SECRET_KEY", "d2a7b0a1a56a566ca79fe9ba6c89706061281812e9a4933a33e968592b85f3ef");
define("SERVER_NAME", "api.myaerolife.com");

if (ENV == "DEV") {
  define("DB_HOST", "localhost");
  define("DB_USERNAME", "root");
  define("DB_PASSWORD", "");
  define("DB_DATABASE_NAME", "myaerolife_dashboard");
} else if (ENV == "TEST") {
  define("DB_HOST", "localhost");
  define("DB_USERNAME", "myaero2026_dashboard_uat");
  define("DB_PASSWORD", "6g?VyY9#");
  define("DB_DATABASE_NAME", "myaero2026_dashboard_uat");
} else if (ENV == "PROD") {
  define("DB_HOST", "localhost");
  define("DB_USERNAME", "myaero2026_dashboard");
  define("DB_PASSWORD", "*Up5-U@8d_e0wez@yoTr");
  define("DB_DATABASE_NAME", "myaero2026_dashboard");
}
