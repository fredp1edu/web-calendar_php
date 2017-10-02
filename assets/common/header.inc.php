<!DOCTYPE html >
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title><?php echo $page_title; ?></title>
    <?php foreach ($css_files as $css): ?>
        <link rel="stylesheet" type="text/css" href="assets/css/<?php echo $css; ?>" />
    <?php endforeach; ?>
</head>

<body>
