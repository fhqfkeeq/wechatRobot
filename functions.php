<?php
/**
 * Created by PhpStorm.
 * User: zhaojipeng
 * Date: 17/9/29
 * Time: 14:23
 */

function http_listen($url, $params)
{
    $response = \WechatRobot\Http::http_get($url, $params);
    if ($response !== false) {
        $data = parsing_point($response);
        wlog(4, 'debug', json_encode($data));
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

/**
 * 记录日志公用方法
 * @param $level
 * @param $log_type
 * @param string $logData
 * @return int
 */
function wlog($level = 4, $log_type, $logData = '')
{
    $log_level = [
        '1' => 'ERROR',
        '2' => 'WARNING',
        '3' => 'INFO',
        '4' => 'DEBUG',
    ];
    $log_path = './debug/' . $log_type . '_' . date('Ym') . '/'; //日志文件

    if (!is_dir($log_path)) {
        mkdir($log_path, 0755, true);
    }
    $log_file = $log_path . date('d') . '.log';
    return file_put_contents($log_file, $log_type . '|' . $log_level[$level] . '|' . date('Y-m-d H:i:s') . '|' . $logData . PHP_EOL, FILE_APPEND);
}