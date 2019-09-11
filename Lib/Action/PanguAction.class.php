<?php
 class PanguAction extends Action{
   private $appkey  = "fe131d7f5a6b38b23cc967316c13dae2f85d537e16c2ca425243db72527bb3b390bf62fdbe49ef5d42a8e17c15f78ccf";
   private $baseUrl = "https://erp.datacaciques.com/api/v1";

   public function uploadimgs(){
     $imgs   = M('images',NULL,'DB_CONFIG1');
     if(IS_POST&&$_POST['str'] != '') {
       $upname = $_POST['upname'];
       $ids = trim($_POST['str'],',');
       $imgarr = $imgs->query("select sku,path,name from images where id IN($ids)");
       if(count($imgarr) == 0){
         echo "<font style='color:#911'>数据丢失！</font>";die();
       }
       $signature = $this->getsignature();

       if ($signature['code'] == 0) {
         $data = $signature['data'];
       }

       foreach($imgarr as $vpic){
         if(empty($vpic)){continue;}
         $this->upload_file($data,$vpic);
       }
       $result = json_decode($this->bound_files($imgarr,$data,$upname),true);

       if(isset($result['code']) &&$result['code'] == 0){
         echo "<font style='color:#191'>操作完成！</font>";die();
       }
     }
   }

   public function getsignature(){
     $url = $this->baseUrl.'/photo/signature?api_key='.$this->appkey;
     return $result = json_decode($this->curlPost($url),true);
   }

   public function curlPost($url,$data=array()){
     $connection = curl_init();
     curl_setopt($connection, CURLOPT_URL, $url);
     curl_setopt($connection, CURLOPT_POST, 1);
     curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
     curl_setopt($connection, CURLOPT_POSTFIELDS, http_build_query($data));
     curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
     curl_setopt($connection, CURLOPT_CONNECTTIMEOUT, 30);
     curl_setopt($connection, CURLOPT_TIMEOUT, 90);
     $response = curl_exec($connection);
     curl_close($connection);
     return $response;
   }

   public function upload_file($sdata,$path){
     $path = substr($path['path'],1,strlen($path['path'])-1);
     $url=$sdata['UploadUrl'];
     $data = array(
       'Filename'=>'',
       'name'=>'',
       'chunk'=>0,
       'chunks'=>1,
       'acl'=>'public-read',
       'Content-Type'=>'',
       'success_action_redirect'=>303,
       'Filename'=>'',
       'key'=>$sdata['Key'],
       'AWSAccessKeyId'=>$sdata['AWSAccessKeyId'],
       'Policy'=>$sdata['Policy'],
       'signature'=>$sdata['Signature'],
       'file'=>'@'.realpath($path)
     );
     $ch = curl_init();
     curl_setopt($ch, CURLOPT_URL, $url);
     curl_setopt($ch, CURLOPT_POST, true );
     curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
     curl_setopt($ch, CURLOPT_HEADER, false);
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
     $return_data = curl_exec($ch);
//       if(strstr($_SESSION['truename'],'程序员')||strstr($_SESSION['loginName'],'测试人员谭')){
//           $this->debug($data);
//           $this->debug($return_data);
//       }

     curl_close($ch);
     return $return_data;
   }

   public function bound_files($imgarr,$sdata,$upname){
     $files=array();
     $key = $sdata['Key'];
     foreach($imgarr as $iv){
       $pic = $iv['path'];
       $name= $iv['name'];
       $picarr=explode('/',$pic);
       $pic = $picarr[3];
       $files[]=array(
         'uri'=>str_replace('${filename}',$pic,$key),
         'name'=>$name,
       );
     }

//     $sku = $iv['sku'];
     $url=$this->baseUrl.'/photo/associate?api_key='.$this->appkey;
     $data = array(
       'rootDir'=>'ebay',
       'path'=>$upname,
       'files'=>$files,
//       'isAutoMatchSku'=>true
       'isAutoMatchSku'=>false
     );
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
     $return_data = curl_exec($ch);
     if(strstr($_SESSION['truename'],'程序员')||strstr($_SESSION['loginName'],'测试人员谭')){
       $this->debug($data);
       $this->debug($return_data);
     }
     curl_close($ch);
     return $return_data;
   }

   public function debug($data){
     echo "<pre>";
     print_r($data);
     echo "</pre>";
   }
 }