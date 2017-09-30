<?php

namespace WechatRobot;

use PHPQRCode\QRcode;
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

        $url = $this->url . $operation;

        $response = http_get($url, $params);

        if ($response === false) {
            return false;
        }

        $data = parsing_point($response);

        if ($data['code'] != 200) {
            return false;
        }

        //调用请求
        $this->uuid = $data['uuid'];

        return $this->uuid;
    }

    public function getQrCode()
    {
        $operation = 'qrcode';
        $url = $this->url . $operation . '/' . $this->uuid;

        $qrCode = get($url);

        return $qrCode;
    }

    public function getQrCodeByCli()
    {
        $qrcode_val = $this->url.'l/'.$this->uuid;
        return QRcode::text($qrcode_val);
    }

    public function listenScan()
    {
        $this->listenEvnet();
    }

    public function listenClick()
    {
        $this->listenEvnet();
    }

    private function listenEvnet()
    {
        set_time_limit(0);
        $operation = 'cgi-bin/mmwebwx-bin/login';

        $params = [
            'loginicon' => 'true',
            'uuid' => $this->uuid,
            'tip' => '0',
            'r' => '-1160587432',
            '_' => '1452859503803',
        ];
    }
}