<?php

/* ********************************************************
 *  Last Update : 29 April 2021 Version 5.4.5 
 ***********************************************************/

define('YII_ENABLE_ERROR_HANDLER', false);
define('YII_ENABLE_EXCEPTION_HANDLER', false);
ini_set("display_errors",true);

require_once(dirname(__FILE__).'/yiiframework/yii.php');
$config=dirname(__FILE__).'/protected/config/main.php';

Yii::createWebApplication($config)->run();   