<?php
$status = session_status();
if ($status == PHP_SESSION_NONE)
    session_start();
if (!isset($_SESSION['token']))
    $_SESSION['token'] = sha1(uniqid(mt_rand(), TRUE));

require_once 'sys/config/db_cred.inc.php';

foreach ($C as $name => $val) {
    define($name, $val);
}
$dbo = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

function __autoload($class) {
    $filename = "sys/class/class." . $class . ".inc.php";
    if (file_exists($filename)) {
        include_once $filename;
    }
}
?>
