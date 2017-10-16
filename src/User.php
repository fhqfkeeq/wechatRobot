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
    private $contactList = [];

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

        $data = json_decode($response, true);

        if ($data === false || $data['BaseResponse']['Ret'] != 0) {
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

        $data = json_decode($response, true);

        if ($data !== false && $data['BaseResponse']['Ret'] != 0) {
            return false;
        }

        $this->contactList = $data['MemberList'];

        return $data;
    }

    /**
     * 按username (PS:@8fef9c4fe0b272aee0ea74c65a81f928)这种获取用户信息
     * @param $username
     */
    public function batchGetContact($username)
    {
        $operation = 'cgi-bin/mmwebwx-bin/webwxbatchgetcontact';

        $query = [
            'type' => 'ex',
            'r' => time(),
            'pass_ticket' => $this->pass_ticket
        ];

        $params = [
            'BaseRequest' => $this->BaseRequest,
            'Count' => 1,
            'List' => [
                [
                    'UserName' => $username,
                    'EncryChatRoomId' => $username,
                ]
            ]
        ];

        $url = $this->url . $operation . '?' . http_build_query($query);

        $response = Http::http_post($url, $params);

    }

    public function searchByNickName($nickname)
    {
        $re = array_filter($this->contactList, function ($input) use ($nickname) {
            if(strpos($input['NickName'], $nickname) !== false){
                return true;
            }else{
                return false;
            }
        });

        foreach ($re as $item) {
            $returnData[] = [
                'nickname' => $item['NickName'],
                'username' => $item['UserName'],
            ];
        }

        return $returnData;
    }

    public function searchByUserName($username)
    {
        foreach ($this->contactList as $item){
            if($item['UserName'] == $username){
                return $item;
            }
        }
        return false;
    }
}