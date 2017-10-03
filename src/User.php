<?php

namespace WechatRobot;

use LSS\XML2Array;
use PHPQRCode\QRcode;

/**
 * Created by PhpStorm.
 * User: zhaojipeng
 * Date: 17/9/29
 * Time: 14:36
 */
class User
{
    private $url = 'https://wx.qq.com/';
    private $pass_ticket = '';

    public function __construct($pass_ticket = '')
    {
        $this->pass_ticket = $pass_ticket;
    }

    public function init()
    {
        $operation = 'cgi-bin/mmwebwx-bin/webwxinit';

        $params = [
            'r' => '604658324',
            'pass_ticket' => $this->pass_ticket
        ];

        $url = $this->url . $operation;

        $response = http_post($url, $params);

        if ($response === false) {
            return false;
        }

        $data = parsing_point($response);

        if ($data['QRLogin.code'] != 200) {
            return false;
        }

        //调用请求
        $this->uuid = $data['QRLogin.uuid'];

        return $this->uuid;
    }
}