<?php

namespace WechatRobot;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Middleware;

/**
 * Created by PhpStorm.
 * User: zhaojipeng
 * Date: 17/10/3
 * Time: 16:31
 */
class Http
{
    static $obj = null;
    static $jar = null;
    static $user_agent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.113 Safari/537.36';
    static $debug = false;

    public static function get_client()
    {
        if(is_null(self::$obj) === true){
            self::$obj = new Client(['cookies' => true]);
            self::$jar = new CookieJar();
            self::$jar->clear();
        }

        return self::$obj;
    }

    /**
     * @param $url
     * @param $params
     * @return bool|\Psr\Http\Message\StreamInterface
     */
    public static function http_get($url, $params = [])
    {

        $obj = self::get_client();

        if (empty($params) === false) {
            $response = $obj->request('GET', $url, [
                'query' => $params,
                'cookies' => self::$jar,
                'headers' => [
                    'User-Agent' => self::$user_agent
                ],
                'debug' => self::$debug
            ]);
        } else {
            $response = $obj->request('GET', $url, [
                'cookies' => self::$jar,
                'headers' => [
                    'User-Agent' => self::$user_agent
                ],
                'debug' => self::$debug
            ]);
        }

        if ($response->getStatusCode() == 200) {
            return $response->getBody()->getContents();
        } else {
            return false;
        }
    }

    public static function http_post($url, $params = [])
    {
        $obj = self::get_client();

        $response = $obj->request('POST', $url, [
            'json' => $params,
            'cookies' => self::$jar,
            'headers' => [
                'User-Agent' => self::$user_agent
            ],
            'debug' => self::$debug
        ]);

        if ($response->getStatusCode() == 200) {
            return $response->getBody()->getContents();
        } else {
            return false;
        }
    }
}