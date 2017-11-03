<?php
    require_once 'sys/core/init.inc.php';

    if (isset($_SESSION['calDay']))
        $calDay = $_SESSION['calDay'];
    else {
        date_default_timezone_set('America/New_York');
        $calDay = date('Y-m-d H:i:s');
    }
    if (isset($_GET['change'])) {
        $change = $_GET['change'];
        if ($change == '+1month' || $change == '-1month' || $change == '+1year' || $change == '-1year')
            $calDay = date('Y-m-d H:i:s', strtotime($calDay . $change));
        elseif ($change == 'today') {
            date_default_timezone_set('America/New_York');
            $calDay = date('Y-m-d H:i:s');
        }
    }
    $_SESSION['calDay'] = $calDay;
    $cal = new Calendar($dbo, $calDay);
    $page_title = "Events Calendar";
    $css_files = array('style.css', 'admin.css', 'ajax.css');

    include_once 'assets/common/header.inc.php';
?>
<div id="content">
    <?php echo $cal->buildCalendar(); ?>
</div>

<?php
echo (isset($_SESSION['user'])) ? "Logged In!" : "Logged Out!";
echo "<br /> $calDay";
include_once 'assets/common/footer.inc.php'; 
?>
