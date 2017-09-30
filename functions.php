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
function http_get($url, $params)
{
    $client = new \GuzzleHttp\Client();
    $response = $client->request('GET', $url, ['query' => $params]);

    if ($response->getStatusCode() == 200) {
        return $response->getBody();
    } else {
        return false;
    }
}

function http_listen($url, $params)
{
    $response = http_get($url, $params);
    if ($response !== false) {
        $data = parsing_point($response);
        if ($data['code'] == 408) {
            http_listen($url, $params);
        } elseif ($data['code'] == 200) {
            return $response;
        }
    }
}

function outputf($output = '', $pad_string = ' '){
    return str_pad($output, 50, $pad_string);
}

function parsing_point($data)
{
    $match_data = [];
    $regx = '/window.[\w]+.([\w.]*) = [\"]?([a-zA-z0-9=]*)?[\"]?;*/';
    $re = preg_match_all($regx, $data, $value, PREG_SET_ORDER);

    if ($re > 0) {
        foreach ($value as $item) {
            $match_data[$item[1]] = $item[2];
        }
    }

    return $match_data;
}