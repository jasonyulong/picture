<?php
error_reporting(0);
if(empty($_SERVER['argv'])){//禁止 web 服务运行
    echo '---not php-cli----<br>';
    echo '<meta charset="utf-8"/>';
    die();
}
date_default_timezone_set ("Asia/Chongqing");
ini_set('memory_limit','500M');
include "include/dbconf.php";
include "include/dbmysqli.php";
include "include/function.php";

$dbcon=new DBMysqli($APPCONF);
$dbconERP=new DBMysqli($ERPCONF);

// 更新 平均分
//1 拥有超级评论员的权限
$ss="select username from user_power where super_review = 1 ;";
$ss=$dbcon->getResultArrayBySql($ss);
$SuperUser=array();
foreach($ss as $v){
    $SuperUser[$v]=0;
}


// 读取配置 Start
$ss='select * from img_config limit 1';
$ss=$dbcon->getResultArrayBySql($ss);
$config_score=(int)$ss[0]['score'];
$imgtypes=trim($ss[0]['imgtypes'],',');
$config_imgtypes=explode(',',$imgtypes);
$config_rev_count=(int)$ss[0]['rev_count'];

$conf=array('score'=>$config_score,'imgtypes'=>$config_imgtypes,'rev_count'=>$config_rev_count);

//读取配置 end

if(count($config_imgtypes)==0||$config_rev_count==0||$config_score==0){
    echo '配置有误!!!!!!!!!';
    die();
}

$imgTypeIndexID=array();
$imgIDArr=array();
$times=strtotime('-100 days');
$ss="select a.score,a.username,a.imgid,a.addtime,b.type from img_review a join images b on a.imgid=b.id where a.addtime> $times and a.status=1";
//debug($ss);die();
$ss=$dbcon->getResultArrayBySql($ss);

foreach($ss as $v){
    $imgid=$v['imgid'];
    $username=$v['username'];
    $score=$v['score'];
    $addtime=$v['addtime'];
    $type=$v['type'];
    // 分数=1  并且 是超级评论员  并且 是需要重传的图片 !
    if($score==1&&array_key_exists($username,$SuperUser)&&in_array($type,$config_imgtypes)){
        // 超级评论员打了一星！ 你惨了
        $up="update images set `status`=2 where id ='$imgid' and `status`=1 limit 1";
        $dbcon->execute($up);
        echo '超级评论员-需要重拍 id:'.$imgid."\n";
        //continue;
    }

    $imgTypeIndexID[$imgid]=$type;
    // 极端情况 查询之后 马上 又有新的评论
    if(!isset($imgIDArr[$imgid])||(int)$imgIDArr[$imgid]<$addtime){
        $imgIDArr[$imgid]=$addtime;
    }

}

//============================= 平均分的玩法



setImageSorceAndReload($imgIDArr,$imgTypeIndexID,$conf);








$dbcon->close();
$dbconERP->close();