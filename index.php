#!/usr/bin/env php
<?php
/**
 * Created by PhpStorm.
 * User: zhaojipeng
 * Date: 17/9/29
 * Time: 14:06
 */

//引入composer
require ('./vendor/autoload.php');
require ('./functions.php');

$app = new \Symfony\Component\Console\Application();
$app->add(new \WechatRobot\Console());
$app->run();