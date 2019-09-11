<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 16/10/13
 * Time: 下午4:45
 */

/**
 * CURL POST 请求
 * @param $url
 * @param array $params
 * @param $timeout
 * @return mixed
 */
function curl_post($url,array $params = array(),$timeout = 120){
    //初始化curl
    $ch = curl_init();
    //抓取指定网页
    curl_setopt($ch,CURLOPT_URL,$url);
    //设置header
    curl_setopt($ch, CURLOPT_HEADER, 0);
    //要求结果为字符串且输出到屏幕上
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    //post提交方式
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    //运行curl
    $result = curl_exec($ch);
    curl_close($ch);

    //输出结果
    return $result;
}

/**
 * CURL GET 请求
 * @param $url
 * @param int $timeout
 * @return mixed
 */
function curl_get($url,$timeout = 120){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_URL, $url);

    $result = curl_exec($curl);
    curl_close($curl);

    return $result;
}