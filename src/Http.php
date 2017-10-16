<?php

namespace WechatRobot;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;
use GuzzleHttp\Middleware;
use GuzzleHttp\RequestOptions;

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
        if (is_null(self::$obj) === true) {
            self::$obj = new Client(['cookies' => true]);
            self::$jar = new CookieJar();
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
            $contents = $response->getBody()->getContents();
            wlog(4, 'debug', 'url:' . $url . '|response:' . $contents);

            return $contents;
        } else {
            return false;
        }
    }

    public static function http_post($url, $params = [])
    {
        $obj = self::get_client();

        wlog(4, 'debug', 'url:' . $url . '|request:' . json_encode($params, JSON_UNESCAPED_UNICODE));

        $response = $obj->request('POST', $url, [
            'body' => json_encode($params, JSON_UNESCAPED_UNICODE),
            'cookies' => self::$jar,
            'headers' => [
                'User-Agent' => self::$user_agent,
                'Content-Type' => 'application/json',
            ],
            'debug' => self::$debug
        ]);

        if ($response->getStatusCode() == 200) {
            $contents = $response->getBody()->getContents();
            wlog(4, 'debug', 'url:' . $url . '|response:' . $contents);

            return $contents;
        } else {
            return false;
        }
    }

    //解决webpush.weixin.qq.com域名无法获取到cookie的问题
    public static function setCookieFormAllDomain()
    {
        $cookie = self::$jar->toArray();

        foreach ($cookie as $item) {
            $item['Domain'] = 'webpush.weixin.qq.com';
            $setCookie = new SetCookie($item);
            self::$jar->setCookie($setCookie);
        }
    }
}