<?php    
    require_once 'sys/core/init.inc.php';

    if (!isset($_SESSION['user'])) {
        header("Location: ./");
        exit;
    }
    $page_title = "Add/Edit Page";
    $css_files = array('style.css', 'admin.css');
    include_once 'assets/common/header.inc.php';

    $calDay = $_SESSION['calDay'];
    $cal = new Calendar($dbo, $calDay);

/* declare(strict_types=1); unsupported in production env. using old version of PHP  */
?>
<div id="content">
    <?php echo $cal->displayForm(); ?>
</div>

<?php include_once 'assets/common/footer.inc.php'; ?>
