<?php

/**
 * Created by PhpStorm.
 * User: zhaojipeng
 * Date: 17/9/29
 * Time: 14:36
 */
class Login
{
    private $url = 'https://login.weixin.qq.com/';
    private $uuid = '';

    public function getUUID()
    {
        $params = [
            'appid' => 'wx782c26e4c19acffb',
            'redirect_uri' => 'https://wx.qq.com/cgi-bin/mmwebwx-bin/webwxnewloginpage',
            'fun' => 'new',
            'lang' => 'en_US',
            '_' => '1452859503801'
        ];

        $operation = 'jslogin';

        //调用请求
        $this->uuid = get();

        return $this->uuid;
    }

    public function getQrCode()
    {
        $operation = 'qrcode';
        $url = $this->url . $operation . '/' . $uuid;

        $qrCode = get($url);

        return $qrCode;
    }

    public function listenScan($uuid)
    {
        $operation = 'cgi-bin/mmwebwx-bin/login';

        $params = [
            'loginicon' => 'true',
            'uuid' => $uuid,
            'tip' => '0',
            'r' => '-1160587432',
            '_' => '1452859503803',
        ];
    }

    public function listenClick()
    {

    }
}