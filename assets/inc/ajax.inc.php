<?php
session_start();
require_once '../../sys/config/db_cred.inc.php';
require_once '../../sys/class/class.db_connect.inc.php';
require_once '../../sys/class/class.params.inc.php';
require_once '../../sys/class/class.calendar.inc.php';
require_once '../../sys/class/class.event.inc.php';

foreach ($C as $name => $val) {
    define($name, $val);
}
$actions = array(
    'event_view' => array(
        'object' => 'Calendar',
        'method' => 'displayEvent'
    ),
    'day_view'   => array(
        'object' => 'Calendar',
        'method' => 'displayDayEvents'
    ),
    'month_view'   => array(
        'object' => 'Calendar',
        'method' => 'displayMonthEvents'
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
    if (!isset($_SESSION['started'])) {         //hopefully this will prevent repeated clicks from running the process repeatedly
        $_SESSION['started'] = 1;
        $doAction = $actions[$_POST['action']];
        $dbo = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        $calObj = new $doAction['object']($dbo);
        if (isset($_POST['event_id']))
            $item = (int) $_POST['event_id'];
        elseif (isset($_POST['day_event']))
            $item = (int) $_POST['day_event'];
        else
            $item = NULL;
        $act = $doAction['method'];  // directly referencing $doAction['method'] never works here.
        echo $calObj->$act($item);
        unset($_SESSION['started']);
    }
}
/* declare(strict_types=1);  not supported in production env 
$status = session_status();
if ($status == PHP_SESSION_NONE)
    session_start();
function __autoload($class) {                autoload didn't work in live environment - need to look into that.
    $filename = "../../sys/class/class." . $class . ".inc.php";
    if (file_exists($filename)) {
        include_once $filename;
    }    
*/
?>
