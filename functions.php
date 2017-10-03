<?php
/**
 * Created by PhpStorm.
 * User: zhaojipeng
 * Date: 17/9/29
 * Time: 14:23
 */

/**
 * @param $url
 * @param $params
 * @return bool|\Psr\Http\Message\StreamInterface
 */
function http_get($url, $params = [])
{
    $client = new \GuzzleHttp\Client(['cookies' => true]);

    if (empty($params) === false) {
        $response = $client->request('GET', $url, ['query' => $params]);
    } else {
        $response = $client->request('GET', $url);
    }

    if ($response->getStatusCode() == 200) {
        return $response->getBody()->getContents();
    } else {
        return false;
    }
}

function http_post($url, $params = [])
{
    $client = new \GuzzleHttp\Client();

    $response = $client->request('POST', $url, ['json' => $params]);

    if ($response->getStatusCode() == 200) {
        return $response->getBody()->getContents();
    } else {
        return false;
    }
}

function http_listen($url, $params)
{
    $response = http_get($url, $params);
    if ($response !== false) {
        echo $response . PHP_EOL;
        $data = parsing_point($response);
        print_r($data);
        echo PHP_EOL;
        if ($data['code'] == 408) {
            http_listen($url, $params);
        } elseif ($data['code'] == 201 || $data['code'] == 200) {
            return $response;
        }
    }
}

function parsing_point($data)
{
    $match_data = [];
    $regx = '/window.([\w.]*)[\s]?=[\s]?[\"\']?([a-zA-z0-9=:\/\.\-\?@\&]*)?[\"\']?;*/';
    $re = preg_match_all($regx, trim($data), $value, PREG_SET_ORDER);

    if ($re > 0) {
        foreach ($value as $item) {
            $match_data[$item[1]] = $item[2];
        }
    }

    return $match_data;
}