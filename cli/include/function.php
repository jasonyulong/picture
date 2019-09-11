<?php
function CopyImagesFromUrl($filePath){
    $PIC='http://192.168.1.72/';
    $xdroot='/uploads/'.date('YmdH').'/';
    $upload=dirname(dirname(dirname(__FILE__))).$xdroot;
    if(!is_dir($upload)){
        @mkdir($upload,0777,true);
    }
    $filePath=str_replace(' ','%20',$filePath);
    if(preg_match('/(\.jpg|\.jpeg|\.gif|\.png)$/i',$filePath,$m)){
        $filezname=$m[1];
        $newName=md5($filePath).$filezname;
    }else{
        return false;
    }
    //echo $PIC.$filePath."\n";
    //echo $upload.$newName."\n";
    if(copy($PIC.$filePath,$upload.$newName)){
        return $xdroot.$newName;
    }
    return false;
}

function mkThumbnail($src, $width = null, $height = null, $filename = null) {
    if (!isset($width) && !isset($height))
        return false;
    if (isset($width) && $width <= 0)
        return false;
    if (isset($height) && $height <= 0)
        return false;

    $size = getimagesize($src);
    if (!$size)
        return false;

    list($src_w, $src_h, $src_type) = $size;
    $src_mime = $size['mime'];
    switch($src_type) {
        case 1 :
            $img_type = 'gif';
            break;
        case 2 :
            $img_type = 'jpeg';
            break;
        case 3 :
            $img_type = 'png';
            break;
        case 15 :
            $img_type = 'wbmp';
            break;
        default :
            return false;
    }

    if (!isset($width))
        $width = $src_w * ($height / $src_h);
    if (!isset($height))
        $height = $src_h * ($width / $src_w);

    $imagecreatefunc = 'imagecreatefrom' . $img_type;
    $src_img = $imagecreatefunc($src);
    $dest_img = imagecreatetruecolor($width, $height);
    imagecopyresampled($dest_img, $src_img, 0, 0, 0, 0, $width, $height, $src_w, $src_h);

    $imagefunc = 'image' . $img_type;
    if ($filename) {
        if(!is_dir(dirname($filename))){
            mkdir(dirname($filename),0777,true);

        }
        $imagefunc($dest_img, $filename);
    } else {
        return false;
/*        header('Content-Type: ' . $src_mime);
        $imagefunc($dest_img);*/
    }
    imagedestroy($src_img);
    imagedestroy($dest_img);
    return true;
}

function debug($a){
    if(!empty($_SERVER['argv'])){
        print_r($a);
    }else{
        echo '<pre>',print_r($a,true),'</pre>';
    }
}


function setImageSorceAndReload($imgIDArr,$imgTypeIndexID,$conf){
    global $dbcon;
    // $k id
    // $v addtime
    $lowSorce=$conf['score'];
    $imgtypes=$conf['imgtypes'];
    $rev_count=$conf['rev_count'];

    foreach($imgIDArr as $k=>$v){
        $ss="select ROUND(AVG(score),2) as p from img_review where imgid='$k' and addtime<=$v limit 1";
        $ss=$dbcon->getResultArrayBySql($ss);

        $p=$ss[0]['p'];// 平均分
        echo $k.'====>'.$p."\n";

        $ss="select count(id) as cc from img_review where imgid='$k' and addtime<=$v limit 1";
        $ss=$dbcon->getResultArrayBySql($ss);
        $cc=$ss[0]['cc'];// 打分的人数

        $type=$imgTypeIndexID[$k];

        $up="update images set score='$p' where id=$k limit 1";
        if($dbcon->execute($up)){
            $dbcon->execute("update img_review set `status`=2 where imgid='$k' and addtime<=$v and `status`=1 ");
        }

        //是需要重拍的类型， 平均分低于设置 ，评论人超过设置
        if(in_array($type,$imgtypes)&&$p<=$lowSorce&&$cc>=$rev_count){
            $up="update images set `status`='2' where id=$k and `status`=1 limit 1";
            echo '需要重拍 id:'.$k."\n";
            $dbcon->execute($up);
        }

    }


}
