<?php
    ini_set('display_errors', 0);
    error_reporting(1);

    include '../../include/common.php';
    require '../../include/service/Functional.class.php';
    require_once '../../include/connect.php';

    $oTmp = new TemplateEngine();
    $ftn = new Functional();

    if ($_SESSION['UID'] != "" && $_REQUEST['act_logout'] == "") {
        if ($_SESSION['user_brand_id'] != '' && $_SESSION['user_brand_id'] != null && $_SESSION['user_brand_id'] > 0) {
            $brand = $_SESSION['user_brand_id'];
            $flag = false;
        }
        else {
            $flag = true;
            $brand = '';
        }

        $javascript = ['newmember.js'];
    }
    else {
        header('Location:/demo/action/index.php');
        exit();
    }

    $oTmp->assign('title_page', 'New Member');
    $oTmp->assign('chartactive', 'active');
    $oTmp->assign('javascript', $javascript);
    $oTmp->assign('content_file', 'report/newmember.html');
    $oTmp->display('layout/template.html');    