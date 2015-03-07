<?php
/**
 * Database configuration
 * Author: Generaleye
 */

ob_start();
session_start();

if ($_SERVER["SERVER_NAME"]=="localhost") {
    define('DB_HOST', 'localhost');
    define('DB_USERNAME', 'root');
    define('DB_PASSWORD', 'root');
    define('DB_NAME', 'aide');
} elseif ($_SERVER["SERVER_NAME"]=="aide-generaleye.rhcloud.com") {
    define('DB_HOST', '127.10.196.130');
    define('DB_USERNAME', 'adminxAsHCnN');
    define('DB_PASSWORD', 'Z4ZNpUd9Vfwv');
    define('DB_NAME', 'aide');
}

define('SENDGRID_USERNAME', 'generaleye');
define('SENDGRID_PASSWORD', 'sendgrid_password');
define('SENDGRID_CC_EMAIL', 'odumuyiwaleye@gmail.com');
define('SENDGRID_FROM_EMAIL', 'developers@aide-generaleye.rhcloud.com');
define('SENDGRID_FROM_NAME', 'Aide Developers');

define('EBULK_USERNAME', 'odumuyiwaleye@yahoo.com');
define('EBULK_APIKEY', '084dbdea272be04f1ec62b41c37f2930ce90508d');
define('EBULK_FROM_NAME', 'Aide Dev');

define('GOOGLE_URL_KEY', 'AIzaSyDsIoSE8N4_r4t3A46s5_EYAY3NYVWpCKs');

define('GOOGLE_GCM_APIKEY', "AIzaSyAIwX-VBZ11Br3kQSNO2v26b_e9quekwxI");

define('REGISTRATION_SUCCESSFUL', 0);
define('REGISTRATION_FAILED', 1);
define('EMAIL_ALREADY_EXISTS', 2);
//define('USERNAME_ALREADY_EXISTS', 3);
?>
