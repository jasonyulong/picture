<?php
error_reporting(0);
if(empty($_SERVER['argv'])){//禁止 web 服务运行
    echo '---not php-cli----';
    die();
}

include "include/dbconf.php";
include "include/dbmysqli.php";
include "include/function.php";


$dbcon=new DBMysqli($APPCONF);

$t=strtotime("-1 days");
$s="select id,oldpath,pic,path from images where path ='' and addtime>$t limit 5000";
echo $s."\n";
$s=$dbcon->getResultArrayBySql($s);
foreach($s as $vvv){
    $id=$vvv['id'];
    $oldpath=$vvv['oldpath'];
    $path=CopyImagesFromUrl($oldpath);
    if($path!==false){
        $up="update images set path='$path' where id='$id' limit 1";
        $dbcon->query($up);
        $src=dirname(dirname(__FILE__)).$path;
        $p=str_replace('/uploads/','',$path);
        $savepath=dirname(dirname(__FILE__)).'/pic/'.$p;
        if(mkThumbnail($src,180,180,$savepath)){
            $savepath=explode('/pic/',$savepath);
            $savepath=$savepath[1];
            $u="update images set pic='$savepath' where id='$id' limit 1";
            echo $savepath."\n";
            $dbcon->query($u);
        }
    }
}

/*
$ss="SELECT id,name,oldpath FROM `images` where LENGTH(name)<4 order by id desc limit 999";
echo $ss."\n";
$ss=$dbcon->getResultArrayBySql($ss);

foreach($ss as $vvv){
    $id=$vvv['id'];
    $oldpath=$vvv['oldpath'];
    $arr=explode('/',$oldpath);
    $name=$arr[count($arr)-2];
    echo $name."\n";
    $up="update images set name='$name' where id=$id limit 1;";
    $dbcon->execute($up);
}*/