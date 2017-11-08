<?php

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
require_once '../../sys/class/class.db_connect.inc.php';
require_once '../../sys/class/class.params.inc.php';
require_once '../../sys/class/class.calendar.inc.php';
require_once '../../sys/class/class.event.inc.php';
require_once '../../sys/class/class.admin.inc.php';

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

/* declare(strict_types=1);  not supported with live version of PHP
    $status = session_status();
    if ($status == PHP_SESSION_NONE)
    function __autoload($class) {           autoload not working in live env. - figure out why
    $filename = "../../sys/class/class." . $class . ".inc.php";
    if (file_exists($filename)) {
        include_once $filename;
    }
}
*/

?>
