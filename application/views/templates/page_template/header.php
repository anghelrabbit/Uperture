<!DOCTYPE html>
<html ng-app="myApp">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Favicon-->
        <!--<link rel="shortcut icon" type="image/x-icon" href="data:image;base64,<?php echo $this->session->userdata('complogo'); ?>" />-->
        
        <link rel="icon" type="image/png" sizes="32x32" href="assets/images/logo.png">
        <title><?= $page_title ?></title>
        <?php foreach ($css as $data) { ?>
            <link href="<?= $data ?>" rel="stylesheet"/>
        <?php } ?>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
        <link href="assets/vendors/fontawesome-free-5.14.0-web/css/all.min.css" rel="stylesheet" type="text/css"/>

    </head>
    <body class="hold-transition skin-blue sidebar-mini" >
        <div class="wrapper myoverlay" >
