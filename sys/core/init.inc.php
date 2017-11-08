<?php
session_start();
if (!isset($_SESSION['token']))
    $_SESSION['token'] = sha1(uniqid(mt_rand(), TRUE));

require_once 'sys/config/db_cred.inc.php';
require_once 'sys/class/class.db_connect.inc.php';
require_once 'sys/class/class.params.inc.php';
require_once 'sys/class/class.calendar.inc.php';
require_once 'sys/class/class.event.inc.php';

foreach ($C as $name => $val) {
    define($name, $val);
}
$dbo = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

/*
$status = session_status();
if ($status == PHP_SESSION_NONE)
function __autoload($class) {
    $filename = "sys/class/class." . $class . ".inc.php";
    if (file_exists($filename)) {
        include_once $filename;
    }
}
*/
?>
