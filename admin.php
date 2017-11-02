<?php
    declare(strict_types=1);
    require_once 'sys/core/init.inc.php';
    if (!isset($_SESSION['user'])) {
        header("Location: ./");
        exit;
    }

    $page_title = "Add/Edit Page";
    $css_files = array('style.css', 'admin.css');
    include_once 'assets/common/header.inc.php';

    $cal = new Calendar($dbo);
?>
<div id="content">
    <?php echo $cal->displayForm(); ?>
</div>

<?php include_once 'assets/common/footer.inc.php'; ?>
