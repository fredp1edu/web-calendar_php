<?php
declare(strict_types=1);

$status = session_status();
if ($status == PHP_SESSION_NONE)
    session_start();

require_once '../../sys/config/db_cred.inc.php';

foreach ($C as $name => $val) {
    define($name, $val);
}
$actions = array(
    'event_view' => array(
        'object' => 'Calendar',
        'method' => 'displayEvent'
    ),
    'edit_event' => array(
        'object' => 'Calendar',
        'method' => 'displayForm'
    ),
    'event_edit' => array(
        'object' => 'Calendar',
        'method' => 'processForm'
    )
);
if (isset($actions[$_POST['action']])) {
    $doAction = $actions[$_POST['action']];
    $dbo = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $calObj = new $doAction['object']($dbo);
    if (isset($_POST['event_id']))
        $id = (int)$_POST['event_id'];
    else
        $id = NULL;
}
$act = $doAction['method'];
echo $calObj->$act($id);      // directly referencing $doAction['method'] never works here.

function __autoload($class) {
    $filename = "../../sys/class/class." . $class . ".inc.php";
    if (file_exists($filename)) {
        include_once $filename;
    }
}  
?>
