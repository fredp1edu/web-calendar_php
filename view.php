<?php
    if (isset($_GET['event_id'])) {
        $id = preg_replace('/[^0-9]/', '', $_GET['event_id']);
        if (empty($id)) {
            header("Location: ./");
            exit;
        } 
    } else {
        header("Location: ./");
        exit;
    }
    require_once 'sys/core/init.inc.php';

    $page_title = "View and Edit Page";
    $css_files = array('style.css');
    include_once 'assets/common/header.inc.php';

    $cal = new Calendar($dbo);
?>
<div id="content">
    <?php echo $cal->displayEvent($id); ?>
    <a href="./">&laquo; Back to the calendar</a>
</div>

<?php include_once 'assets/common/footer.inc.php'; ?>
