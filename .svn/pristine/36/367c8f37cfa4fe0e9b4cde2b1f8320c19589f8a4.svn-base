<?php
if(empty($_SERVER['argv'])){//禁止 web 服务运行
    echo '---not php-cli----';
   // die();
}

error_reporting(0);
date_default_timezone_set ("Asia/Chongqing");
include "include/dbconf.php";
include "include/dbmysqli.php";
include "include/simple_html_dom.php";

$dbcon=new DBMysqli($APPCONF);
/*$ss="desc images;";
$ss=$dbcon->getResultArrayBySql($ss);
print_r($ss);*/
echo '<meta charset="utf-8"/>';
$Main="http://192.168.1.72/";
$webdir="2016年4月图片/".$_SERVER['argv'][1]."/";
$i=0;
$array=array();

$base=$Main.$webdir;
//echo $base;exit;
function getFilebylink($webpath){
    global $Main,$dbcon;
    $html = file_get_html($webpath);
    $filelog=dirname(dirname(__FILE__)).'/cache/'.date('YmdH').'.txt';
    foreach($html->find('a') as $element){
        $href=$element->href;
        $plaintext=$element->plaintext;
        //debug($plaintext);
        //debug($href);
        if(preg_match('/\/$/',$href)&&'Parent Directory'!=trim($plaintext)){
            getFilebylink($webpath.$href);
        }else{
            if('Parent Directory'!=trim($plaintext)){
                //debug($webpath.$href);
                $oldpath=str_replace($Main,'',$webpath.$href);

                $ss="select id from images where oldpath='$oldpath' limit 1";
                $ss=$dbcon->getResultArrayBySql($ss);
                if(count($ss)==1){
                    continue;
                }
                $fnameArr=explode('.',$href);
                if(count($fnameArr)==1){
                    continue;
                }

                $fnamed=$fnameArr[count($fnameArr)-1];//最后一个
                if(!array_key_exists(strtolower($fnamed),array('jpg'=>1,'jpeg'=>1,'png'=>1,'gif'=>1))){
                    continue;
                }
                $fname=str_replace('.'.$fnamed,'',$href);
                $fname=str_replace('%20','',$fname);
                $t=time();
                $in="insert into images(`oldpath`,`name`,`addtime`)values('$oldpath','$fname','$t');";
                //debug($in);
                if(!$dbcon->query($in)){
                    echo $dbcon->getError();
                    writeFile($filelog,$in."\r\n");
                }
            }

        }
    }

}

function debug($arr){
    echo '<pre>';
    print_r($arr);
    echo '</pre>';
}
function writeFile($file,$str){
    $index=strripos($file,'/');
    if(!file_exists($file)&&strripos($file,'/')!==false){
        $fileDir=substr($file,0,$index);
        if(!file_exists($fileDir)){
            mkdir($fileDir,0777,true);
        }
    }
    file_put_contents($file, $str,FILE_APPEND);
}


getFilebylink($base);



