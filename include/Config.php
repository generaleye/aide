<?php
/**
 * Database configuration
 * Author: Generaleye
 */

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

define('REGISTRATION_SUCCESSFUL', 0);
define('REGISTRATION_FAILED', 1);
define('EMAIL_ALREADY_EXISTS', 2);
//define('USERNAME_ALREADY_EXISTS', 3);
?>
