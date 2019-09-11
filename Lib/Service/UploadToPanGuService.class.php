<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/20
 * Time: 15:32
 */
class UploadToPanGuService
{
    private $appkey  = "fe131d7f5a6b38b23cc967316c13dae2c2dbe667a77eae52509b630bdf2152099950f160a844b222f6c44671626e8369";
    private $baseUrl = "https://sales.datacaciques.com/api/v1";
    private $signatureUrl;
    private $associateUrl;

    public function __construct()
    {
        $this->setBaseUrl();
        $this->getSignature();
    }

    /**
     * @-create at 2017/7/20 by 朱诗萌
     * @-comment 上传图片至盘古,并自定义文件夹名称
     */
    public function uploadToPanGuWithDirectory($data,$directory){
        $signature = $this->getSignature();
        $this->uploadImageToPanGu($data);
        $data = array_filter($data,function($param){
            return !empty($param['path'])?true:false;
        });
        $files = array_map(function($param)use($signature){
            $return = [];
            $return['name'] = $param['name'];
            $path_arr = explode('/',$param['path']);
            $return['uri'] = str_replace('${filename}',$path_arr[3],$signature['Key']);
            return $return;
        },$data);
        $data = array(
            'rootDir'=>'ebay',
            'path'=>$directory,
            'files'=>$files,
//       'isAutoMatchSku'=>true
            'isAutoMatchSku'=>false //是否自动匹配sku
        );
        $result = json_decode($this->curl_post_json($this->associateUrl,$data),true);
        return $result;
    }

    public function uploadImageToPanGu($data){
        $signature = $this->getSignature();
        foreach($data as $v){
            $path = substr($v['path'],1,strlen($v['path'])-1);
            $file_info = [
                'Filename'=>'',
                'name'=>'',
                'chunk'=>0,
                'chunks'=>1,
                'acl'=>'public-read',
                'Content-Type'=>'',
                'success_action_redirect'=>303,
                'Filename'=>'',
                'key'=>$signature['Key'],
                'AWSAccessKeyId'=>$signature['AWSAccessKeyId'],
                'Policy'=>$signature['Policy'],
                'signature'=>$signature['Signature'],
                'file'=>'@'.realpath($path)
            ];
            $this->curl_post($signature['UploadUrl'],$file_info);
        }
    }

    /**
     * @-create at 2017/7/20 by 朱诗萌
     * @-comment 获取签名信息
     */
    protected function getSignature(){
        if(empty($this->signature)){
            $result = json_decode($this->curl_post($this->signatureUrl),true);
            if($result['code'] != 0){
                throw new Exception('签名信息获取错误');
            }else{
                $this->signature = $result['data'];
            }
        }
        return $this->signature;
    }

    protected function setBaseUrl(){
        $this->signatureUrl = $this->baseUrl.'/photo/signature?api_key='.$this->appkey;
        $this->associateUrl = $this->baseUrl.'/photo/associate?api_key='.$this->appkey;
    }

    public function curl_post_json($url,array $data){
        $headers = array();
        $headers[0] = "Content-Type: application/json";
        $headers[1] = "charset: UTF-8";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true );
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if(C('IS_OPEN_PROXY')){
            curl_setopt($ch, CURLOPT_PROXY, C('PROXY_HOST')); //代理服务器地址
            curl_setopt($ch, CURLOPT_PROXYPORT, C('PROXY_PORT')); //代理服务器端口
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP); //使用http代理模式
        }

        $return_data = curl_exec($ch);
        curl_close($ch);
        return $return_data;
    }

    private function curl_post($url,array $params = array(),$timeout = 120){
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

        if(C('IS_OPEN_PROXY')){
            curl_setopt($ch, CURLOPT_PROXY, C('PROXY_HOST')); //代理服务器地址
            curl_setopt($ch, CURLOPT_PROXYPORT, C('PROXY_PORT')); //代理服务器端口
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP); //使用http代理模式
        }

        //运行curl
        $result = curl_exec($ch);
        curl_close($ch);

        //输出结果
        return $result;
    }
}