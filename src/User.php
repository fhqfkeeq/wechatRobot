<?php

namespace WechatRobot;

/**
 * Created by PhpStorm.
 * User: zhaojipeng
 * Date: 17/9/29
 * Time: 14:36
 */
class User
{
    private $url = 'https://wx.qq.com/';
    private $BaseRequest = [];
    private $pass_ticket = '';

    public function __construct($BaseRequest, $pass_ticket)
    {
        $this->BaseRequest = $BaseRequest;
        $this->pass_ticket = $pass_ticket;
    }

    public function init()
    {
        $operation = 'cgi-bin/mmwebwx-bin/webwxinit';

        $params = [
            'r' => time(),
            'pass_ticket' => $this->pass_ticket
        ];

        $query = http_build_query($params);

        $params = [
            'BaseRequest' => $this->BaseRequest
        ];

        $url = $this->url . $operation . '?' . $query;

        $response = Http::http_post($url, $params);

        if ($response === false) {
            return false;
        }

        wlog(4, 'debug', $response);

        $data = json_decode($response, true);

        if ($data !== false && $data['BaseResponse']['Ret'] != 0) {
            return false;
        }

        return $data;
    }

    public function getContact()
    {
        $operation = 'cgi-bin/mmwebwx-bin/webwxgetcontact';
        $params = [
            'pass_ticket' => $this->pass_ticket,
            'r' => time(),
            'seq' => 0,
            'skey' => $this->BaseRequest['Skey'],
        ];

        $url = $this->url . $operation;

        $response = Http::http_get($url, $params);

        if ($response === false) {
            return false;
        }

        wlog(4, 'debug', $response);

        $data = json_decode($response, true);

        if ($data !== false && $data['BaseResponse']['Ret'] != 0) {
            return false;
        }

        return $data;
    }
}