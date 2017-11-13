<?php
require_once 'sys/core/init.inc.php';
$calDay = NULL;

if (isset($_GET['event_id'])) {
    $item = preg_replace('/[^0-9]/', '', $_GET['event_id']);
    $doAction = "displayEvent";
    if (empty($item)) {
        header("Location: ./");
        exit;
    } 
} 
elseif (isset($_GET['day_event'])) {
    $item = $_GET['day_event'];
    $sessDate = strtotime($_SESSION['calDay']);
    $year = date('Y', $sessDate);
    $month = date('m', $sessDate);
    $time = date('G:i:s', $sessDate);
    $sessDay = sprintf('%02d', $item);
    $calDay = "$year-$month-$sessDay $time";
    $_SESSION['calDay'] = $calDay;
    $doAction = "displayDayEvents";
}
elseif (isset($_GET['month_event'])) {
    $item = NULL;
    $calDay = $_SESSION['calDay'];
    $doAction = "displayMonthEvents";
}

else {
    header("Location: ./");
    exit;
}
$page_title = "View and Edit Page";
$css_files = array('style.css', 'admin.css', 'ajax.css');
include_once 'assets/common/header.inc.php';

$cal = new Calendar($dbo, $calDay);
?>
<div id="content">
    <?php echo $cal->$doAction($item); ?>
    <a href="./">&laquo; Back to the calendar</a>
</div>

<?php include_once 'assets/common/footer.inc.php'; ?>
