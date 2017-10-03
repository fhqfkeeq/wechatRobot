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

        if ($data['QRLogin.code'] != 200) {
            return false;
        }

        //调用请求
        $this->uuid = $data['QRLogin.uuid'];

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
        $qrcode_val = $this->url . 'l/' . $this->uuid;
        return QRcode::text($qrcode_val);
    }

    public function listenScan()
    {
        $data = $this->listenEvnet();

        if ($data['code'] != 201) {
            return false;
        } else {
            return true;
        }
    }

    public function listenClick()
    {
        $data = $this->listenEvnet();

        if ($data['code'] != 200) {
            return false;
        } else {
            return $data['redirect_uri'];
        }
    }

    public function getLoginInfo($url)
    {
        $url .= '&fun=new&version=v2';
        $data = http_get($url);
        echo $data . PHP_EOL;
        $info = XML2Array::createArray($data);

        if($info['error']['ret'] == 0){
            return $info['error']['pass_ticket'];
        }else{
            return false;
        }
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

        $url = $this->url . $operation;

        $response = http_listen($url, $params);

        if ($response === false) {
            return false;
        }

        $data = parsing_point($response);

        return $data;
    }
}