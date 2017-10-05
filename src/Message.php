<?php
/**
 * Created by PhpStorm.
 * User: zhaojipeng
 * Date: 17/10/4
 * Time: 11:12
 */

namespace WechatRobot;


class Message
{
    private $url = 'https://wx.qq.com/';
    private $BaseRequest = [];
    private $pass_ticket = '';
    private $syncKey = [];

    public function __construct($BaseRequest, $pass_ticket, $syncKey)
    {
        $this->BaseRequest = $BaseRequest;
        $this->pass_ticket = $pass_ticket;
        $this->syncKey = $syncKey;
    }

    public function syncCheck()
    {
        Http::setCookieFormAllDomain();
        $url = 'https://webpush.weixin.qq.com/';
        $operation = 'cgi-bin/mmwebwx-bin/synccheck';
        $params = [
            'r' => time() . '336',
            'skey' => $this->BaseRequest['Skey'],
            'sid' => $this->BaseRequest['Sid'],
            'uin' => $this->BaseRequest['Uin'],
            'deviceid' => $this->BaseRequest['DeviceID'],
            'synckey' => $this->syncKey2String(),
            '_' => time() . '201'
        ];

        $response = http_listen($url . $operation, $params, function ($response) {
            $regx = '/window.([\w.]*)[\s]?=[\s]?[\"\']?([a-zA-z0-9=:\/\.\-\?@\&\{\}\"\,]*)?[\"\']?;*/';
            $json = parsing_point($response, $regx);

            $regx = '/[\"\']?([\w.]*):[\"\']?([\w.]*)[\"\',]?/';
            $response = parsing_point($json['synccheck'], $regx);

            if ($response['retcode'] == 0 && $response['selector'] == 0) {
                return false;
            } else {
                return true;
            }
        });

        return $response;
    }

    public function messageSync()
    {
        $operation = 'cgi-bin/mmwebwx-bin/webwxsync';

        $query = [
            'sid' => $this->BaseRequest['Sid'],
            'skey' => $this->BaseRequest['Skey'],
            'pass_ticket' => $this->pass_ticket
        ];

        $params = [
            'BaseRequest' => $this->BaseRequest,
            'SyncKey' => $this->syncKey,
            'rr' => '-' . time()
        ];

        $url = $this->url . $operation . '?' . http_build_query($query);

        $data = Http::http_post($url, $params);

        $data = json_decode($data, true);

        if ($data['BaseResponse']['Ret'] == 0) {
            $this->syncKey = $data['SyncKey'];
        }
    }

    public function sendMessage($from, $to)
    {
        $operation = 'cgi-bin/mmwebwx-bin/webwxsendmsg';

        $query = '?pass_ticket=' . $this->pass_ticket;

        $clientMsgId = $localID = microtime(true)*10000 . rand(111,999);

        $params = [
            'BaseRequest' => $this->BaseRequest,
            'Msg' => [
                'Type' => 1,
                'Content' => '啦啦啦～',
                'FromUserName' => $from,
                'ToUserName' => $to,
                'LocalID' => $localID,
                'ClientMsgId' => $clientMsgId,
            ],
        ];

        $url = $this->url . $operation . $query;

        $response = Http::http_post($url, $params);
    }

    private function syncKey2String()
    {
        foreach ($this->syncKey['List'] as $item) {
            $tmp[] = $item['Key'] . '_' . $item['Val'];
        }

        return implode('|', $tmp);
    }
}