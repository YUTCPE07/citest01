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
    }
    else {
        header('Location:/demo/action/index.php');
        exit();
    }

    $javascript = [
        'libs/jquery.tablesorter.min.js',
        'libs/jquery.tablesorter.pager.min.js',
        'libs/jquery.tablesorter.widgets.min.js',
        'retention.js'
    ];

    $url = SERVICE_BACK . 'getselect';
    $value = ['flag' => 'select', 'id' => 'brand_id', 'name' => 'name', 'table_name' => 'mi_brand', 'value' => $brand];
    $select = $ftn->postService($url, $value);

    $oTmp->assign('select_brand', htmlspecialchars_decode($select->value));
    $oTmp->assign('title_page', 'Retention');
    $oTmp->assign('content_file', 'report/retention.html');
    $oTmp->assign('javascript', $javascript);
    $oTmp->display('layout/template.html');