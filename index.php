<?php
    require_once 'sys/core/init.inc.php';

    $cal = new Calendar($dbo, "2018-01-01 12:00:00");
    $page_title = "Events Calendar";
    $css_files = array('style.css');

    include_once 'assets/common/header.inc.php';
?>
<div id="content">
    <?php echo $cal->buildCalendar(); ?>
</div>

<?php include_once 'assets/common/footer.inc.php'; ?>
