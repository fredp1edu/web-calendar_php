<?php
    require_once 'sys/core/init.inc.php';

    $cal = new Calendar($dbo);
    $page_title = "Events Calendar";
    $css_files = array('style.css', 'admin.css', 'ajax.css');

    include_once 'assets/common/header.inc.php';
?>
<div id="content">
    <?php echo $cal->buildCalendar(); ?>
</div>

<?php
echo (isset($_SESSION['user'])) ? "Logged In!" : "Logged Out!";
include_once 'assets/common/footer.inc.php'; 
?>
