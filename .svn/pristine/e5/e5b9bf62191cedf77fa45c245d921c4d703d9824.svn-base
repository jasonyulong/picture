<?php
error_reporting(0);
if(empty($_SERVER['argv'])){//禁止 web 服务运行
    echo '---not php-cli----<br>';
    echo '<meta charset="utf-8"/>';
    die();
}
ini_set('memory_limit','500M');
include "include/dbconf.php";
include "include/dbmysqli.php";
include "include/function.php";


$dbcon=new DBMysqli($APPCONF);
$dbconERP=new DBMysqli($ERPCONF);

//print_r($dbconERP);

function getSKUFromERPAndSet($name,$id,$oldpath){

    if($name==''){return ;}
    global $dbcon,$dbconERP;
    $str='';
    if(preg_match('/(240-[A-Za-z0-9]{5,10})/',$name,$m)){ // 240-CG0048ACO
        $str=$m[1];
    }elseif(preg_match('/([A-Za-z0-9]{5,10}\-[A-Z0-9])/',$name,$m)){ /// SP9001A-1  或者 SP9001A-A
        $str=$m[1];
    }elseif(preg_match('/([A-Za-z0-9]{5,10})/',$name,$m)){
        $str=$m[1];
    }else{
        //echo 'Error:'.$name.'======'.$oldpath.'<br>';
        //$str=getSKUfromOldpath($oldpath,$id);
    }

    if($str==''){
        return false;
    }
    //echo $str.'============='.$name.'<br>';

    $ss="(SELECT goods_sn FROM ebay_goods WHERE goods_sn='$str')
UNION
(SELECT goods_sn FROM ebay_goods_audit WHERE goods_sn='$str')
LIMIT 1";

    $ss=$dbconERP->getResultArrayBySql($ss);
    if(count($ss)==1){
        $up="update images set sku ='$str' where id=$id limit 1";
        $dbcon->execute($up);
    }else{
        $str1=preg_replace('/\-\d$/','',$str);
        if($str1!=$str){
            $up="update images set sku ='$str1' where id=$id limit 1";
            $dbcon->execute($up);
        }else{
            echo $str1.'===='.$id.'===='.$name.'===='.$oldpath."\n<br>";
        }
    }

}

function getSKUfromOldpath($oldpath,$id){
    global $dbcon,$dbconERP;
    $str='';
    if(preg_match('/(240-[A-Za-z0-9]{5,10})/',$oldpath,$m)){ // 240-CG0048ACO
        $str=$m[1];
    }elseif(preg_match('/([A-Za-z0-9]{5,10}\-[A-Z0-9])/',$oldpath,$m)){ /// SP9001A-1  或者 SP9001A-A
        $str=$m[1];
    }elseif(preg_match('/([A-Za-z0-9]{5,10})/',$oldpath,$m)){
        $str=$m[1];
    }else{
        echo 'Paths:'.$oldpath.'<br>';
        return '';
    }
    if($str!=''){
        $up="update images set name ='$str' where id=$id limit 1";
        $dbcon->execute($up);
    }
    return $str;
}

$ss="SELECT id,`name`,oldpath FROM `images` WHERE sku = ''";
//echo $ss;

$ss=$dbcon->getResultArrayBySql($ss);
//debug($ss);

foreach($ss as $vv){
    $name=$vv['name'];
    $id=$vv['id'];
    $oldpath=$vv['oldpath'];
    getSKUFromERPAndSet($name,$id,$oldpath);
}

$dbcon->close();
$dbconERP->close();

