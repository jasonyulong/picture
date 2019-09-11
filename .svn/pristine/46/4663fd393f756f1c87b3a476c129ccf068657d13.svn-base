<?php
/*
error_reporting(0);
if(empty($_SERVER['argv'])){//禁止 web 服务运行
    echo '---not php-cli----';
    die();
}

include "include/dbconf.php";
include "include/dbmysqli.php";
include "include/function.php";


$dbcon=new DBMysqli($APPCONF);

$s="select id,pic,path from images where pic =''";
$s=$dbcon->getResultArrayBySql($s);
foreach($s as $vvv){
    $path=$vvv['path'];
    $id=$vvv['id'];
    //echo $path."\r\n";
    $u="update images set path='' where id='$id' limit 1";
    $dbcon->query($u);
    $ii=dirname(dirname(__FILE__)).'/'.$path;
    echo $path."\r\n";
    unlink($ii);
}