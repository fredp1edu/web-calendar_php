<?php
declare(strict_types=1);
$status = session_status();
if ($status == PHP_SESSION_NONE)
    session_start();

$actions = array(
    'event_edit' => array(
        'object' => 'Calendar',
        'method' => 'processForm',
        'header' => 'Location: ../../'
    ),
    'user_login' => array(
        'object' => 'Admin',
        'method' => 'processLoginForm',
        'header' => 'Location: ../../'
    ),
    'user_logout' => array(
        'object' => 'Admin',
        'method' => 'processLogout',
        'header' => 'Location: ../../'
    )
);
if ($_POST['token'] == $_SESSION['token'] && isset($actions[$_POST['action']])) {
    $doAction = $actions[$_POST['action']];
} else {
    header("Location: ../../");
    exit;
}
require_once '../../sys/config/db_cred.inc.php';

foreach ($C as $name => $val) {
    define($name, $val);
}
$dbo = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$calObj = new $doAction['object']($dbo);
$act = $doAction['method'];
if (TRUE === $msg = $calObj->$act()) {      // $doAction['method'](); doesn't work directly -- WHY!!!
    header($doAction['header']);
    exit;
} else
    die ($msg);

function __autoload($class) {
    $filename = "../../sys/class/class." . $class . ".inc.php";
    if (file_exists($filename)) {
        include_once $filename;
    }
}    
?>
