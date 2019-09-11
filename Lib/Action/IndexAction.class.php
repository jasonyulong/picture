<?php
class IndexAction extends Action {
    public function _initialize(){
        if(!session('?loginName') || !session('?truename')){
            $this->redirect('Login/login');
        }
    }


    public function index(){
        $dd=M("images");
        $dbERP=M('ebay_user',NULL,'DB_CONFIG2');
        $key=$this->_get('pkeyword');
        $img_type=(int)$this->_get('img_type');
        $pageNo = $this->_get('pageNow');
        $imgaddtime = $this->_get('imgaddtime');
        $imgid = (int)$this->_get('imgid');
        $pageNo=$pageNo?$pageNo:1;
        $keys=$key;
        $keys=str_replace('，',',',$keys);
        $keys=str_replace(' ',',',$keys);
        $keys=explode(',',$keys);

        $fild='id,pic,oldpath,`name`,score,type,sku';
        $sql='select '.$fild.' from images where status IN(1,3) and is_replace != 2 ';
        if($key!=''){
            $keysql="";
            $i=0;
            foreach($keys as $k){
                if($k==''){
                    continue;
                }
                $i++;
                if($i>100){
                    break;
                }
                $keysql.=" or name like '$k%' ";
            }

            $keysql=trim($keysql,' or');
            $sql.=" and ($keysql) ";
        }

        if($img_type>0){
            $sql.=" and `type`=$img_type ";
        }
        if($imgaddtime!=''){
            $sstart=strtotime($imgaddtime.' 00:00:00');
            $send=strtotime($imgaddtime.' 23:59:59');
            $sql.=" and (addtime BETWEEN $sstart and $send) ";
        }
        if($imgid>0){
            $sql.=" and id=$imgid ";
        }
        $count=session('count');
        $count=!empty($count)?$count:200;


        $sqlcount=str_replace($fild,'count(id) as cc',$sql);

        $sqlcount=$dd->query($sqlcount);//echo $dd->_sql();

        $cc=$sqlcount[0]['cc'];

        $limitCount=$count;
        if($limitCount*($pageNo-1) >= $cc){
            $pageNo = 1;
        }
        $limitStart = $limitCount * ($pageNo-1);
        $pageCount = $limitCount != 0 ? ceil($cc / $limitCount) : 0;

        $sql.=" order by id desc  limit $limitStart,$count ";
        $sql=$dd->query($sql);//echo $dd->_sql();

        $idstr='';
        $skustr='';
        $skuArr=array();
        foreach($sql as $vs){
            $idstr.=$vs['id'].',';
            $sku=$vs['sku'];
            if(!empty($sku)&&!array_key_exists($sku,$skuArr)){
                $skustr.="'".$sku."',";
                $skuArr[$sku]='';
            }
            $idstr.=$vs['sku'].',';
        }
        $user=session('loginName');
        $idstr=trim($idstr,',');
        $skustr=trim($skustr,',');

        $scoreSql="SELECT imgid,score,note FROM img_review WHERE username ='$user' AND imgid IN($idstr)";
        $scoreSql=$dd->query($scoreSql);//echo $dd->_sql();
        $scoreArr=array();
        foreach($scoreSql as $vvv){
            $scoreArr[$vvv['imgid']]=array($vvv['score'],$vvv['note']);
        }


        //关于 图片 sku name
        $len=count($skuArr);
        $skuArr=array();
        if($skustr!=''){
            $gname="(SELECT goods_sn,goods_name FROM ebay_goods WHERE goods_sn in($skustr))UNION
(SELECT goods_sn,goods_name FROM ebay_goods_audit WHERE goods_sn in($skustr))";
            //debug($skustr);
            $gnameArr=$dbERP->query($gname);
            foreach($gnameArr as $vvv){
                $goods_sn=$vvv['goods_sn'];
                $goods_name=$vvv['goods_name'];
                if(!array_key_exists($goods_sn,$skuArr)){
                    $skuArr[$goods_sn]=$goods_name;
                }
            }
        }

        if($len>count($skuArr)){//有SKU 没有获得品名  从组合中找
             $gnameCom="SELECT goods_sn,notes as goods_name FROM ebay_productscombine where goods_sn in($skustr)";
             $gnameCom=$dbERP->query($gnameCom);
            foreach($gnameCom as $vvv){
                $goods_sn=$vvv['goods_sn'];
                $goods_name=$vvv['goods_name'];
                if(!array_key_exists($goods_sn,$skuArr)){
                    $skuArr[$goods_sn]='[组合]'.$goods_name;
                }
            }
        }

        //debug($scoreArr);
        $ss="select typename,id from  img_type ";
        $ss=$dd->query($ss);
        $typenameArr=array();
        $html='<select id="indexsearch_img_type" name="img_type"><option value="">图片类别</option>';
        foreach($ss as $vv){
            $id=$vv['id'];
            $typename=$vv['typename'];
            if($img_type==$id){
                $slec=" selected='selected' ";
            }else{
                $slec="";
            }
            $html.='<option value="'.$id.'" '.$slec.'>'.$typename.'</option>';
            $typenameArr[$id]=$typename;
        }
        $html.='</select>';

        $html.='<input type="text" placeholder="上传时间" onclick="WdatePicker()" style="width: 100px;" name="imgaddtime" id="top_imgaddtime" value="'.$imgaddtime.'"/>';

        $jiaose=session('truename');
        if(strstr($jiaose,'美工')===false&&strstr($jiaose,'摄影')===false){
            // 2016 -05 -16 今日我评论了多少
            $tod=strtotime(date('Y-m-01 ').'00:00:00');
            $ss="select count(id) as cc from img_review where username='$user' and addtime >$tod  limit 1";
            $ss=$dd->query($ss);
            $reviewcount=$ss[0]['cc'];
        }else{
            $reviewcount='';
        }



        $this->assign('reviewcount',$reviewcount);
        $this->assign('indexhtml',$html);
        $this->assign('key',$key);
        $this->assign('data',$sql);
        $this->assign('scoreArr',$scoreArr);
        $this->assign('skuArr',$skuArr);
        $this->assign('typenameArr',$typenameArr);
        $this->assign('totalcount',number_format($cc));
        $this->assign('allCount',$cc);
        $this->assign('pageCount',$pageCount);
        $this->display();
    }


    public function addMyStar(){
        // 用户打分
        $star=$this->_post("star");
        $id=$this->_post("id");
        $note=$this->_post("note");
        $rs=R("Public/review",array($id,$star,$note));
        echo $rs;
    }

    public function BattchaddMyStar(){
        $star=(int)$this->_post("star");
        $note=$this->_post("note");
        $ids=trim($this->_post("id"),',');
        if(preg_match('/[^0-9,]/',$ids)){
            echo $this->printHtmlmsg("输入的参数--图片信息--有误!",1);
            exit;
        }
        if($star>5||$star<=0){
            echo $this->printHtmlmsg("输入的参数--分数--有误!",1);
            exit;
        }
        $ids=explode(',',$ids);
        $dd=M("images");
        foreach($ids as $id){
            $idss="select `name` from images where id='$id' limit 1";
            $idss=$dd->query($idss);
            $skuname=$idss[0]['name'];
            $rs=R("Public/review",array($id,$star,$note));
            $html=$this->statusCodeToHtml($rs,$skuname);
            echo $html;
        }

    }
    // 下载图片
    public function download(){
/*        if(session('loginName')!='测试人员谭'){
            echo '<meta charset="utf-8"><div style="color:#911">正在测试中!暂停使用!</div>';
            return;
        }*/
        $fileids=trim($_POST['idstr']);
        $fileids=preg_replace('/[^0-9,]/','',$fileids);
        $fileids=trim($fileids,',');
        //echo $fileids;
        if($fileids==''){
            echo '<meta charset="utf-8"><div style="color:#911">错误!请求下载的图片有误!</div>';
            return ;
        }
        // user

        $fileidArr=explode(',',$fileids);
        if(count($fileidArr)>100){
            echo '<meta charset="utf-8"><div style="color:#911">错误!一次最多下载100张图片!</div>';
            return ;
        }
        $user=session('loginName');

        // 检查 下载限制

        $limitArr=$this->getmydownloadlimit();
        $limit=$limitArr['limit'];
        if($limitArr['over']===true){
            //echo '<meta charset="utf-8"><div style="color:#911">错误!今天下载的图片已经超过了限制!!,您的限制是'.$limit.'张/天!</div>';
            //return ;
        }

        $isGo=$this->isMoreThenLimit($limitArr['ids'],$fileidArr,$limit);
        //debug($isGo);
        if(!is_array($isGo)&&$isGo<0){
            echo '<meta charset="utf-8"><div style="color:#911">错误!本次下载的图片已经超过了今天的限制'.(-1*$isGo).'张!!您的限制是'.$limit.'张/天!</div>';
            return ;
        }


        $basePath=dirname(dirname(dirname(__FILE__)));
        $savePath=$basePath.'/cache/'.md5($user.$fileids.time()).'.zip';
        $zip = new ZipArchive();
        if ($zip->open($savePath, ZipArchive::CREATE) === true) {
            $dd=M("images");
            $ss="select path,name,id from images where id in($fileids) limit 100";
            $ss=$dd->query($ss);
            $i=0;
            $log="$user 下载了 ";
            foreach($ss as $vv){
                $path=$vv['path'];
                $name=str_replace(' ','',$vv['name']);
                $id=$vv['id'];
                $getName=$this->getFileKname($path);
                $imgpath=  $basePath.$path;
                if(file_exists($imgpath)){
                    $i++;
                    //echo $imgpath.'<br>';
                    $zip->addFile($imgpath,$name.'_'.$id.$getName);
                    $log.=$name.',';
                }
            }
            $zip->close();
            $log.=' 一共'.$i.'张图片!';
            if($i>0){
                R("Public/writelog",array($user,$log));
                //ob_end_clean();
/*                header("Content-type:application/octet-stream");
                Header("Accept-Length: ".filesize($savePath));
                Header("Content-Disposition: attachment; filename=123.zip");
                echo file_get_contents($savePath);*/
                $savePath=explode('cache/',$savePath);
                echo '<a href="./cache/'.$savePath[1].'">download</a>';
                $this->saveDownloadtext($user,date('Ymd'),$isGo);// 保存今天下载的数量
                exit;
            }else{
                echo '<meta charset="utf-8"><div style="color:#911">错误!无文件可以压缩!</div>';
                return ;
            }
        }else{
            echo '<meta charset="utf-8"><div style="color:#911">错误!压缩包错误!</div>';
            return ;
        }

    }

    private function getmydownloadlimit(){
        $user=session('loginName');
        $dd=M("img_download",null,'DB_CONFIG1');
        $today=date('Ymd');
        $ss="select download from user_power where username='$user' limit 1";
        $ss=$dd->query($ss);
        $mydown=$ss[0]['download'];

        $ss="select imgids from img_download where user='$user' and date='$today' limit 1";
        $ss=$dd->query($ss);
        if(count($ss)==1&&is_array($ss)){
            $imgids=$ss[0]['imgids'];
        }else{
            $data=array();
            $data['user']=$user;
            $data['date']=$today;
            $data['imgids']='';
            $in="insert into img_download(`user`,`date`,`imgids`)values('$user','$today','')";
            $dd->execute($in);
            $imgids='';
        }
        $over=false;
        $imgids=trim($imgids,',');
        $arr=explode(',',$imgids);
        if(count($arr)>=$mydown){
            $over=true;
        }
        // 限制   imgid  完毕
        return array('limit'=>$mydown,'ids'=>$arr,'over'=>$over);
    }

    private function isMoreThenLimit($arr,$addarr,$limit){
        foreach($addarr as $v){
            if(!in_array($v,$arr)&&$v!=''){
                array_push($arr,$v);
            }
        }
        if(count($arr)<=$limit){
            return $arr; //合并之后的 语序下载
        }else{
            // 不可以下载了
            return $limit-count($arr);
        }
    }

    private function saveDownloadtext($user,$today,$arr){
        $dd=M("img_download");
        $data['user']=$user;
        $data['date']=$today;
        $data['imgids']=implode(',',$arr);

        $ss="select id from img_download where user='$user' and date='$today' limit 1";
        $ss=$dd->query($ss);
        if(is_array($ss)&&count($ss)==1){
            $data['id']=$ss[0]['id'];
        }
        $dd->save($data);
    }

    private function getFileKname($filepath){
        if(preg_match('/(\.jpg|\.jpeg|\.gif|\.png)$/i',$filepath,$m)){
            $filezname=$m[1];
            return $filezname;
        }else{
            return false;
        }
    }

    public function getbase64Img(){
        $dd=M("images");
        $key=$this->_post('id');

        $data=array(
            'error'=>0,
            'msg'=>'',
            'url'=>''
        );

        $ss="select path,name,id from images where id='$key' limit 1";
        $ss=$dd->query($ss);
        if(count($ss)==0||$ss[0]['path']==''){
            $data['msg']='图片不存在!或大图已经损坏';
            $data['error']=1;
        }else{
            $name=$ss[0]['name'];
            $p=dirname(dirname(dirname(__FILE__))).$ss[0]['path'];
            $type=getimagesize($p);//取得图片的大小，类型等
            $ss=file_get_contents($p);
            if(!$ss){
                $data['msg']='图片不存在!或大图已经损坏';
                $data['error']=1;
            }else{
                switch($type[2]){//判读图片类型
                    case 1:$img_type="gif";break;
                    case 2:$img_type="jpg";break;
                    case 3:$img_type="png";break;
                }
                $file_content=base64_encode($ss);//base64编码
                $img='data:image/'.$img_type.';base64,'.$file_content;//合成图片的base64编码
                $data['url']=$img;
                $user=session('loginName');
                $log=$user.'-查看了【'.$name.',id:'.$key.'】大图';
                R("Public/writelog",array($user,$log));
            }

        }
        $str=json_encode($data);
        echo $str;

    }

    public function upload(){
        $db=M("img_type");
        $dbERP=M('ebay_user',NULL,'DB_CONFIG2');
        $truename=session('truename');
        $uname=session('loginName');

        if(strstr($truename,'管理员')!==false){
            $ss="select typename,id from  img_type ";
        }elseif(strstr($truename,'销售')!==false){
            $ss="select typename,id from  img_type where typename like '%业务%'";
        }else{
            $ss="select typename,id from  img_type where createuser like '%,$uname,%' ";
        }



        $ss=$db->query($ss);
        //debug($ss);

        $photoer="select username from ebay_user where truename like '%摄影%'";
        $photoer=$dbERP->query($photoer);

        if(count($photoer)>=1){
            $htmls='<select id="upload_photoer"><option value="">请选择这些图片的摄影师</option>';
            foreach($photoer as $vv){
                $uname=$vv['username'];
                $htmls.='<option value="'.$uname.'">'.$uname.'</option>';
            }
            $htmls.='</select>';
        }else{
            $htmls='<span style="color:#911"><b>无法显示摄影师!请设置ERP摄影师的分组带有\'摄影\'</b></span><input type="hidden" id="upload_photoer"/>';
        }

        if(count($ss)>=1){
            $html='<select id="upload_img_type"><option value="">请选择图片类别</option>';
            foreach($ss as $vv){
                $id=$vv['id'];
                $typename=$vv['typename'];
                $html.='<option value="'.$id.'">'.$typename.'</option>';
            }
            $html.='</select>';
        }else{
            $html='';
        }
        $this->assign('html',$html);
        $this->assign('htmls',$htmls);
        $this->display();
    }

    public function SaveImages(){
        $db=M("images");
        //$dbERP=M('ebay_user',NULL,'DB_CONFIG2');
        $truename=session('truename');
        $uname=session('loginName');

        if(strstr($truename,'管理员')!==false){
            $ss="select typename,id from  img_type ";
        }elseif(strstr($truename,'销售')!==false){
            $ss="select typename,id from  img_type where typename like '%业务%'";
        }else{
            $ss="select typename,id from  img_type where createuser like '%,$uname,%' ";
        }

        $ss=$db->query($ss);
        $imgtype=(int)$this->_post("doaction");
        $allowID=array();
        foreach($ss as $v){
            $allowID[$v['id']]=$v['typename'];
        }

        if(!array_key_exists($imgtype,$allowID)){
            echo '<div>严重错误!您无权上传【'.$allowID[$imgtype].'】类型的图片</div>';
            exit;
        }

        $data=$_POST['data'];
        $photoer=$_POST['photoer'];
        $isReupload = isset($_POST['isReupload'])?$_POST['isReupload']:0;//是否重传

        $namestr=$_POST['namestr'];
        if(!$isReupload) {
          if (empty($photoer) && $imgtype == 99) {
            echo "<div style='margin: 10px;color:#911;'>" . $namestr . ' 上传失败,原图的上传必须要选择摄影师!</div>';
            die();
          }
        }

        if (empty($data) || empty($namestr)) {
          echo "<div style='margin: 10px;color:#911;'>" . $namestr . ' 上传失败,上传过程中资源丢失!</div>';
          die();
        }

        if (!preg_match('/\.(jpg|gif|png)/i', $namestr)) {
          echo '<span style="color:#f30">图片' . $namestr . '上传格式有误!仅限jpg,png,gif</span><br><br>';
          die();
        }
        $Ex = $this->getPatts();
        if (preg_match($Ex, $namestr, $m)) {
          $sku = $m[1];
        } else {
          echo '<div style="color:#911">图片名不合规范!图片名:' . $namestr . '</div><br>';
          exit;
        }


        $bigpath = $this->createbigimg($data, $namestr);
        if (!$bigpath) {
          echo '<div style="color:#911">图片保存失败!图片名:' . $namestr . '</div><br>';
          exit;
        }

        $pic = str_replace('/uploads/', '/pic/', $bigpath);

        if (!$this->mkThumbnail($bigpath, 180, 180, $pic)) {
          echo '<div style="color:#911">mini图片保存失败!图片名:' . $namestr . '</div><br>';
          exit;
        }

        $user = session('loginName');
        $addtime = time();
        $base = dirname(dirname(dirname(__FILE__)));
        $pic = str_replace($base . '/pic/', '', $pic);
        $path = str_replace($base, '', $bigpath);
        $data = array();
        $namestr = preg_replace('/\.(jpg|gif|png)/i', '', $namestr);
        if($isReupload){
          //搜索该上传人员 该SKU需要重传的图片
          $imgaddusr = trim($_POST['imgaddusr']);
          $imgaddsku = trim($_POST['sku']);
          if (!strstr($namestr,$imgaddsku)) {
            echo '<span style="color:#f30">图片名' . $namestr . '不含有上传的SKU</span><br><br>';
            die();
          }
          if($imgaddusr == ''){
            echo '<div style="color:#911">图片上传人员为空!</div><br>';
            exit;
          }
          $getoneimg = $db->where("adduser = '$imgaddusr' and sku = '$imgaddsku' and type='$imgtype' and status = '2' ")->select();
          if(count($getoneimg) == 0){//如果没有需要重传的 则当前图片用做新图
            $getphotoer = $db->where("adduser = '$imgaddusr' and sku = '$imgaddsku' and type='$imgtype' and status = '3' ")->find();
            $data['name'] = $namestr;
            $data['sku'] = $sku;
            $data['pic'] = $pic;
            $data['path'] = $path;
            $data['addtime'] = $addtime;
            $data['adduser'] = $user;
            $data['type'] = $imgtype;
            $data['photoer'] = $getphotoer['photoer'];
            $data['status']  = 1;
            if ($db->add($data)) {
              echo '<div style="color:#191">图片【' . $namestr . '】保存成功!</div><br>';
            } else {
              echo '<div style="color:#911">图片【' . $namestr . '】保存失败!</div><br>';
            }
          }else{
            $oldid = $getoneimg[0]['id'];
            $data  = array();//上传的新图数据
            $data['name']    = $namestr;
            $data['sku']     = $imgaddsku;
            $data['pic']     = $pic;
            $data['path']    = $path;
            $data['photoer'] = $getoneimg[0]['photoer'];
            $data['addtime'] = $addtime;
            $data['adduser'] = $user;
            $data['type']    = $imgtype;
            $data['pid']     = $oldid;
            $data['status']  = 1;
            $upsql = "update images set status = '3' where id = '$oldid' ";
            if ($db->add($data)) {
              echo '<div style="color:#191">图片【' . $namestr . '】保存成功!</div><br>';
              $db->query($upsql);
            } else {
              echo '<div style="color:#911">图片【' . $namestr . '】保存失败!</div><br>';
            }
          }
        }else{
          $data['name'] = $namestr;
          $data['sku'] = $sku;
          $data['pic'] = $pic;
          $data['path'] = $path;
          $data['addtime'] = $addtime;
          $data['adduser'] = $user;
          $data['type'] = $imgtype;
          $data['photoer'] = $photoer;
          if ($db->add($data)) {
            echo '<div style="color:#191">图片【' . $namestr . '】保存成功!</div><br>';
          } else {
            echo '<div style="color:#911">图片【' . $namestr . '】保存失败!</div><br>';
          }
        }

    }

    /*替换图片*/
    public function SaveReplaceImages(){
        $db=M("images");
        //$dbERP=M('ebay_user',NULL,'DB_CONFIG2');
        $truename=session('truename');
        $uname=session('loginName');

        if(strstr($truename,'管理员')!==false){
            $ss="select typename,id from  img_type ";
        }elseif(strstr($truename,'销售')!==false){
            $ss="select typename,id from  img_type where typename like '%业务%'";
        }else{
            $ss="select typename,id from  img_type where createuser like '%,$uname,%' ";
        }

        $ss=$db->query($ss);
        $imgtype=(int)$this->_post("doaction");
        $allowID=array();
        foreach($ss as $v){
            $allowID[$v['id']]=$v['typename'];
        }

        if(!array_key_exists($imgtype,$allowID)){
            echo '<div>严重错误!您无权上传【'.$allowID[$imgtype].'】类型的图片</div>';
            exit;
        }

        $data=$_POST['data'];

        $namestr=$_POST['namestr'];

        if (empty($data) || empty($namestr)) {
          echo "<div style='margin: 10px;color:#911;'>" . $namestr . ' 上传失败,上传过程中资源丢失!</div>';
          die();
        }

        if (!preg_match('/\.(jpg|gif|png)/i', $namestr)) {
          echo '<span style="color:#f30">图片' . $namestr . '上传格式有误!仅限jpg,png,gif</span><br><br>';
          die();
        }
        $Ex = $this->getPatts();
        if (preg_match($Ex, $namestr, $m)) {
          $sku = $m[1];
        } else {
          echo '<div style="color:#911">图片名不合规范!图片名:' . $namestr . '</div><br>';
          exit;
        }


        $bigpath = $this->createbigimg($data, $namestr);
        if (!$bigpath) {
          echo '<div style="color:#911">图片保存失败!图片名:' . $namestr . '</div><br>';
          exit;
        }

        $pic = str_replace('/uploads/', '/pic/', $bigpath);

        if (!$this->mkThumbnail($bigpath, 180, 180, $pic)) {
          echo '<div style="color:#911">mini图片保存失败!图片名:' . $namestr . '</div><br>';
          exit;
        }

        $user = session('loginName');
        $addtime = time();
        $base = dirname(dirname(dirname(__FILE__)));
        $pic = str_replace($base . '/pic/', '', $pic);
        $path = str_replace($base, '', $bigpath);
        $data = array();
        $namestr = preg_replace('/\.(jpg|gif|png)/i', '', $namestr);
          //搜索该上传人员 该SKU需要替换的图片
        $imgaddusr = trim($_POST['imgaddusr']);
        $imgaddsku = trim($_POST['sku']);
        if (!strstr($namestr,$imgaddsku)) {
          echo '<span style="color:#f30">图片名' . $namestr . '不含有上传的SKU</span><br><br>';
          die();
        }
        if($imgaddusr == ''){
          echo '<div style="color:#911">图片上传人员为空!</div><br>';
          exit;
        }
        $getoneimg = $db->where("adduser = '$imgaddusr' and sku = '$imgaddsku' and type='$imgtype' and is_replace = '2' ")->select();
        if(count($getoneimg) == 0){//如果没有需要替换的 则当前图片用做新图
          $getphotoer = $db->where("adduser = '$imgaddusr' and sku = '$imgaddsku' and type='$imgtype' and is_replace = '3' ")->find();
          $data['name'] = $namestr;
          $data['sku'] = $sku;
          $data['pic'] = $pic;
          $data['path'] = $path;
          $data['addtime'] = $addtime;
          $data['adduser'] = $user;
          $data['type'] = $imgtype;
          $data['photoer'] = $getphotoer['photoer'];
          if ($db->add($data)) {
            echo '<div style="color:#191">图片【' . $namestr . '】保存成功!</div><br>';
          } else {
            echo '<div style="color:#911">图片【' . $namestr . '】保存失败!</div><br>';
          }
        }else{
          $oldid = $getoneimg[0]['id'];
          $upsql = "update images set is_replace = '3',pic = '$pic',path = '$path',addtime = '$addtime',adduser = '$user' where id = '$oldid' ";
          if ($db->query($upsql)) {
            echo '<div style="color:#191">图片【' . $namestr . '】保存成功!</div><br>';
          } else {
            echo '<div style="color:#911">图片【' . $namestr . '】保存失败!</div><br>';
          }
        }
    }

    function getPatts(){
        $ex='/^([0-9A-Za-z\-_]{5,15})(\.|\s|_|\(|（)/';
        return $ex;
    }

    private function createbigimg($source,$namestr){

        $skuArr=explode('.',$namestr);

        $fomart=".".$skuArr[1];
        $fdata=explode("base64,",$source);

        $fdata=$fdata[1];

        $filename=md5($source).rand(1111,9999).$fomart;

        $upload=dirname(dirname(dirname(__FILE__))).'/uploads/'.date('YmdH');
        if(!is_dir($upload)){
            @mkdir($upload);
        }

        $filenamesave=$upload.'/'.$filename;
        //echo $filenamesave.'<br>';
        $rs=file_put_contents($filenamesave,base64_decode($fdata));

        if($rs){

            return $filenamesave;
        }

        return false;

    }

    private function mkThumbnail($src, $width = null, $height = null, $filename = null){
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
                @mkdir(dirname($filename));
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

    public function checkimgname(){
        //$db=M("img_type");
        $dbERP=M('ebay_user',NULL,'DB_CONFIG2');

        $str=trim($this->_post("str"),'*');

        if($str==''){
            return '<div style="color:#911">没有接收到文件名!无法检测!</div>';
        }

        $error='0';
        $rstr='';

        $str=explode('*',$str);
        $i=0;
        //匹配SKU
        $Ex=$this->getPatts();
        foreach($str as $v){
            //提取SKU
            $i++;
            if(preg_match($Ex,$v,$m)){
                $sku=$m[1];
                //查询是否是ERP系统存在的SKU
                $skuSql="(select goods_sn from ebay_goods where goods_sn = '$sku' limit 1)";
                $skuSql.="union(select goods_sn from ebay_goods_audit where goods_sn = '$sku' limit 1)";
                $skuSql.="union(select goods_sn from ebay_productscombine where goods_sn = '$sku' limit 1)";
                $skusql=$dbERP->query($skuSql);
                if(is_array($skusql)&&count($skusql)==0){
                    $rstr.='<div style="color:#911">第'.$i.'张图片名不合规范,识别不到SKU【'.$sku.'】图片名:'.$v.'</div><br>';
                    $error=1;
                }
            }else{
                $rstr.='<div style="color:#911">第'.$i.'张图片名不合规范!图片名:'.$v.'</div><br>';
                $error=1;
            }
        }
        echo $error.'@@@'.$rstr;
    }

    public function deleteImages(){
        $cpower=session("power");
        if(!in_array('imgdelete',$cpower)){
            echo 'Unauthorized....';
            return ;
        }

        $str=trim($this->_post('str'));
        $str=trim($str,',');
        if(preg_match('/[^0-9,]/',$str)){
            echo '<div style="color:#911">非法的提交</div>';
            return ;
        }
        $db=M("images");
        $str=explode(',',$str);
        $user=session('loginName');

        foreach($str as $vv){
            $id=$vv;
            if(empty($id)){ continue;}
            $ss="select * from images where id='$id' limit 1";
            $ss=$db->query($ss);
            $name=$ss[0]['name'];
            $id=$ss[0]['id'];
            //判断是否为重传的图
            $pid = $ss[0]['pid'];
            $data=array();
            $data['id']=$id;
            $data['status']=4;
            if($db->save($data)){
                $log=$user." 删除了图片---【".$name.'，id:'.$id.'】'.date('Y-m-d H:i:s');
                if($pid > 0){
                  $data['id'] = $pid;
                  $data['status'] = 2;
                  $db->save($data);
                }
                R("Public/writelog",array($user,$log));
                echo '<div style="color:#191">图片'.$name.' 删除成功!</div>';
            }else{
                echo '<div style="color:#191">图片'.$name.' 删除失败!</div>';
            }

        }


    }

    /*标记替换图片*/
    public function markImages(){
        $cpower=session("power");
        if(!in_array('imgreplace',$cpower)){
            echo 'Unauthorized....';
            return ;
        }

        $str=trim($this->_post('str'));
        $str=trim($str,',');
        if(preg_match('/[^0-9,]/',$str)){
            echo '<div style="color:#911">非法的提交</div>';
            return ;
        }
        $db=M("images");
        $str=explode(',',$str);

        foreach($str as $vv){
            $id=$vv;
            if(empty($id)){ continue;}
            $ss="select * from images where id='$id' limit 1";
            $ss=$db->query($ss);
            $name=$ss[0]['name'];
            $id=$ss[0]['id'];
            $data=array();
            $data['id']=$id;
            $data['is_replace']=2;
            if($db->save($data)){
                echo '<div style="color:#191">图片'.$name.' 标记成功!</div>';
            }else{
                echo '<div style="color:#191">图片'.$name.' 标记失败!</div>';
            }
        }
    }

    /*取消标记图片*/
    public function cancelImages(){
    $cpower=session("power");
    if(!in_array('imgreplace',$cpower)){
      echo 'Unauthorized....';
      return ;
    }

    $str=trim($this->_post('str'));
    $str=trim($str,',');
    if(preg_match('/[^0-9,]/',$str)){
      echo '<div style="color:#911">非法的提交</div>';
      return ;
    }
    $db=M("images");
    $str=explode(',',$str);

    foreach($str as $vv){
      $id=$vv;
      if(empty($id)){ continue;}
      $ss="select * from images where id='$id' limit 1";
      $ss=$db->query($ss);
      $name=$ss[0]['name'];
      $id=$ss[0]['id'];
      $data=array();
      $data['id']=$id;
      $data['is_replace']=1;
      if($db->save($data)){
        echo '<div style="color:#191">图片'.$name.' 取消标记成功!</div>';
      }else{
        echo '<div style="color:#191">图片'.$name.' 取消标记失败!</div>';
      }
    }
  }

    /*显示替换图片详情*/
    public function showReplaceImgsInfo(){
      $dd=M("images");
      $dbERP=M('ebay_user',NULL,'DB_CONFIG2');
      $pageNo = $this->_get('pageNow');
      $pageNo=$pageNo?$pageNo:1;
      $where = '';
      if(isset($_GET['submit'])){
        $addusr  = $_GET['addusr'];
        $sku     = $_GET['sku'];
        $imgtype = $_GET['img_type'];
        $type    = $_GET['status'];
        if($imgtype){
          $where .= " and type = '$imgtype' ";
        }
      }else {
        $addusr = $_GET['_URL_'][2];
        $sku    = $_GET['_URL_'][3];
        $type   = $_GET['_URL_'][4];
      }
      $fild='id,pic,oldpath,`name`,score,type,sku';
      $sql='select '.$fild.' from images where sku = "'.$sku.'" and is_replace = '.$type.' '.$where.' and status != 4 and adduser = "'.$addusr.'"';
      $count=session('count');
      $count=!empty($count)?$count:200;

      $sqlcount=str_replace($fild,'count(id) as cc',$sql);

      $sqlcount=$dd->query($sqlcount);//echo $dd->_sql();

      $cc=$sqlcount[0]['cc'];

      $limitCount=$count;
      if($limitCount*($pageNo-1) >= $cc){
        $pageNo = 1;
      }
      $limitStart = $limitCount * ($pageNo-1);
      $pageCount = $limitCount != 0 ? ceil($cc / $limitCount) : 0;

      $sql.=" order by id desc  limit $limitStart,$count ";
      $sql=$dd->query($sql);//echo $dd->_sql();

      $idstr='';
      $skustr='';
      $skuArr=array();
      foreach($sql as $vs){
        $idstr.=$vs['id'].',';
        $sku=$vs['sku'];
        if(!empty($sku)&&!array_key_exists($sku,$skuArr)){
          $skustr.="'".$sku."',";
          $skuArr[$sku]='';
        }
        $idstr.=$vs['sku'].',';
      }
      $user=session('loginName');
      $idstr=trim($idstr,',');
      $skustr=trim($skustr,',');
      $cpower=session("power");

      $scoreSql="SELECT imgid,score,note FROM img_review WHERE username ='$user' AND imgid IN($idstr)";
      $scoreSql=$dd->query($scoreSql);//echo $dd->_sql();
      $scoreArr=array();
      foreach($scoreSql as $vvv){
        $scoreArr[$vvv['imgid']]=array($vvv['score'],$vvv['note']);
      }


      //关于 图片 sku name
      $len=count($skuArr);
      $skuArr=array();
      if($skustr!=''){
        $gname="(SELECT goods_sn,goods_name FROM ebay_goods WHERE goods_sn in($skustr))UNION
(SEL  ECT goods_sn,goods_name FROM ebay_goods_audit WHERE goods_sn in($skustr))";
        //debug($skustr);
        $gnameArr=$dbERP->query($gname);
        foreach($gnameArr as $vvv){
          $goods_sn=$vvv['goods_sn'];
          $goods_name=$vvv['goods_name'];
          if(!array_key_exists($goods_sn,$skuArr)){
            $skuArr[$goods_sn]=$goods_name;
          }
        }
      }

      if($len>count($skuArr)){//有SKU 没有获得品名  从组合中找
        $gnameCom="SELECT goods_sn,notes as goods_name FROM ebay_productscombine where goods_sn in($skustr)";
        $gnameCom=$dbERP->query($gnameCom);
        foreach($gnameCom as $vvv){
          $goods_sn=$vvv['goods_sn'];
          $goods_name=$vvv['goods_name'];
          if(!array_key_exists($goods_sn,$skuArr)){
            $skuArr[$goods_sn]='[组合]'.$goods_name;
          }
        }
      }

      //debug($scoreArr);
      $ss="select typename,id from  img_type ";
      $ss=$dd->query($ss);
      $typenameArr=array();
      $html='<select id="indexsearch_img_type" name="img_type"><option value="">图片类别</option>';
      foreach($ss as $vv){
        $id=$vv['id'];
        $typename=$vv['typename'];
        if($imgtype==$id){
          $slec=" selected='selected' ";
        }else{
          $slec="";
        }
        $html.='<option value="'.$id.'" '.$slec.'>'.$typename.'</option>';
        $typenameArr[$id]=$typename;
      }
      $html.='</select>';

      $this->assign('inhtml',$html);
      $this->assign('inaddusr',$addusr);
      $this->assign('insku',$sku);
      $this->assign('intype',$type);
      $this->assign('data',$sql);
      $this->assign('cpower',$cpower);
      $this->assign('scoreArr',$scoreArr);
      $this->assign('skuArr',$skuArr);
      $this->assign('typenameArr',$typenameArr);
      $this->assign('totalcount',number_format($cc));
      $this->assign('allCount',$cc);
      $this->assign('pageCount',$pageCount);
      $this->display();
    }

    /*显示重传图片详情*/
    public function showReuploadImgsInfo(){
      $dd=M("images");
      $dbERP=M('ebay_user',NULL,'DB_CONFIG2');
      $pageNo = $this->_get('pageNow');
      $img_type = $this->_get('pstatus');
      $pageNo=$pageNo?$pageNo:1;
      $where = '';
      if(isset($_GET['submit'])){
        $addusr  = $_GET['addusr'];
        $sku     = $_GET['sku'];
        $imgtype = $_GET['img_type'];
        $type    = $_GET['status'];
        if($imgtype){
          $where .= " and type = '$imgtype' ";
        }
      }else {
        $addusr = $_GET['_URL_'][2];
        $sku    = $_GET['_URL_'][3];
        $type   = $_GET['_URL_'][4];
      }
      $fild='id,pic,oldpath,`name`,score,type,sku';
      $sql='select '.$fild.' from images where sku = "'.$sku.'" and status = '.$type.' '.$where.' and adduser = "'.$addusr.'"';
      $count=session('count');
      $count=!empty($count)?$count:200;

      $sqlcount=str_replace($fild,'count(id) as cc',$sql);

      $sqlcount=$dd->query($sqlcount);//echo $dd->_sql();

      $cc=$sqlcount[0]['cc'];

      $limitCount=$count;
      if($limitCount*($pageNo-1) >= $cc){
        $pageNo = 1;
      }
      $limitStart = $limitCount * ($pageNo-1);
      $pageCount = $limitCount != 0 ? ceil($cc / $limitCount) : 0;

      $sql.=" order by id desc  limit $limitStart,$count ";
      $sql=$dd->query($sql);//echo $dd->_sql();

      $idstr='';
      $skustr='';
      $skuArr=array();
      foreach($sql as $vs){
        $idstr.=$vs['id'].',';
        $sku=$vs['sku'];
        if(!empty($sku)&&!array_key_exists($sku,$skuArr)){
          $skustr.="'".$sku."',";
          $skuArr[$sku]='';
        }
        $idstr.=$vs['sku'].',';
      }
      $user=session('loginName');
      $idstr=trim($idstr,',');
      $skustr=trim($skustr,',');

      $scoreSql="SELECT imgid,score,note FROM img_review WHERE username ='$user' AND imgid IN($idstr)";
      $scoreSql=$dd->query($scoreSql);//echo $dd->_sql();
      $scoreArr=array();
      foreach($scoreSql as $vvv){
        $scoreArr[$vvv['imgid']]=array($vvv['score'],$vvv['note']);
      }


      //关于 图片 sku name
      $len=count($skuArr);
      $skuArr=array();
      if($skustr!=''){
        $gname="(SELECT goods_sn,goods_name FROM ebay_goods WHERE goods_sn in($skustr))UNION
(SEL  ECT goods_sn,goods_name FROM ebay_goods_audit WHERE goods_sn in($skustr))";
        //debug($skustr);
        $gnameArr=$dbERP->query($gname);
        foreach($gnameArr as $vvv){
          $goods_sn=$vvv['goods_sn'];
          $goods_name=$vvv['goods_name'];
          if(!array_key_exists($goods_sn,$skuArr)){
            $skuArr[$goods_sn]=$goods_name;
          }
        }
      }

      if($len>count($skuArr)){//有SKU 没有获得品名  从组合中找
        $gnameCom="SELECT goods_sn,notes as goods_name FROM ebay_productscombine where goods_sn in($skustr)";
        $gnameCom=$dbERP->query($gnameCom);
        foreach($gnameCom as $vvv){
          $goods_sn=$vvv['goods_sn'];
          $goods_name=$vvv['goods_name'];
          if(!array_key_exists($goods_sn,$skuArr)){
            $skuArr[$goods_sn]='[组合]'.$goods_name;
          }
        }
      }

      //debug($scoreArr);
      $ss="select typename,id from  img_type ";
      $ss=$dd->query($ss);
      $typenameArr=array();
      $html='<select id="indexsearch_img_type" name="img_type"><option value="">图片类别</option>';
      foreach($ss as $vv){
        $id=$vv['id'];
        if($id == '101'){continue;}
        $typename=$vv['typename'];
        if($imgtype==$id){
          $slec=" selected='selected' ";
        }else{
          $slec="";
        }
        $html.='<option value="'.$id.'" '.$slec.'>'.$typename.'</option>';
        $typenameArr[$id]=$typename;
      }
      $html.='</select>';

      $this->assign('inhtml',$html);
      $this->assign('inaddusr',$addusr);
      $this->assign('insku',$sku);
      $this->assign('intype',$type);
      $this->assign('data',$sql);
      $this->assign('scoreArr',$scoreArr);
      $this->assign('skuArr',$skuArr);
      $this->assign('typenameArr',$typenameArr);
      $this->assign('totalcount',number_format($cc));
      $this->assign('allCount',$cc);
      $this->assign('pageCount',$pageCount);
      $this->display();
    }

    private function printHtmlmsg($str,$type){
        $color='';
        switch($type){
            case 1:$color='color:#911';break;
            case 2:$color='color:#191';break;
            case 3:$color='font-weight:bold;';break;
        }
        return '<div style="'.$color.'">'.$str.'</div>';
    }

    private function statusCodeToHtml($code,$strHead=''){
        $str='';
        $type=0;

        switch($code){
            case -1:$str='您已经打过分了';$type=1;break;
            case -10:$str='图片不存在！';$type=1;break;
            case -3:$str='您的角色没有权限评论!';$type=1;break;
            case -2:$str='不能给自己的图片打分！';$type=1;break;
            case -4:$str='打分失败!DB 错误！';$type=1;break;
            case 2:$str='打分成功!';$type=2;break;
        }

        return $this->printHtmlmsg($strHead.$str,$type);
    }

    public function ToLink(){
        $bill=$this->_get('bill');
        $bill=trim($bill,',');
        //echo $bill;
        if(preg_match('/[^0-9,]/',$bill)){
            echo "<div style='color:#911'>您提交了非法的数据!</div>";exit;
        }
        $db=M("images");
        $ss="select * from images where id in($bill) limit 200";
        $ss=$db->query($ss);
        $data=array();
        $user=session('loginName');
        $log="$user 下载了【图片链接】 ";
        $i=0;
        foreach($ss as $vvv){
            $url=R('Public/getALinkByPath',array($vvv['path']));
            $imgname=$vvv['name'];
            $sku=$vvv['sku'];
            if($url!=''&&$url!=false){
                $log.=$imgname.'_'.$vvv['id'].',';
                $i++;
            }
            $data[]=array($sku,$imgname,$url);
        }
        $log.=' 一共'.$i.'张图片!';
        R("Public/writelog",array($user,$log));
        $expCellName=array(
            array(0,'SKU'),
            array(1,'图片名'),
            array(2,'公网链接')
        );

        R('Public/exportExcel',array('获取图片链接',$expCellName,$data));
    }

    public function getimgreview(){
        $dd=M("images",NULL,'DB_CONFIG1');
        $cpower=session("power");
        if(!in_array('view_content',$cpower)){
            echo '<div style="color:#911">没有权限 查看评论!</div>';
            return ;
        }

        $viewUser=false;
        if(in_array('view_er',$cpower)){
            $viewUser=true;
        }
        $id=(int)$this->_post('id');
        $H=(int)$this->_post('H');
        //view_er
        $ss="select username,score,imgid,addtime,status,note from img_review where imgid=$id order by id desc";
        $ss=$dd->query($ss);
        $html='<div class="review_info_box" style="height:'.$H.'px"><div class="review_info_boxs">';
        foreach($ss as $vv){
            $username=$viewUser?$vv['username']:'****';
            $addtime=date('Y-m-d H:i',$vv['addtime']);
            $status=$vv['status'];
            $score=$vv['score'];
            $note=$vv['note'];
            $note=$note==''?'<div class="emptynote">没有评论内容</div>':$note;
            $statusStr=$status==1?'审核中':'已审核';
            $html.='<div class="rrview_hover"><div class="review_info_box_title"><span>'.$username.'<span> <i>'.$addtime.'</i> <b>['.$statusStr.']</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>'.$score.'分</b> </div>';
            $html.='<div class="review_info_box_contect"><p>'.$note.'</p></div></div>';
        }
        if(count($ss)==0){
            $html.='<div class="emptynote">图片暂时没有评论</div>';
        }
        $html.='</div></div>';
        echo $html;
    }

    public function viewNeedReload(){
        $dd=M("images",NULL,'DB_CONFIG1');
        $dbERP=M('ebay_user',NULL,'DB_CONFIG2');
        $key=$this->_get('pkeyword');
        $pageNo = (int)$this->_get('pageNow');
        $adduser=trim($this->_get('uploader'));
        $pstatus=(int)$this->_get('pstatus');//0= 等待重拍+已经重拍， 1=等待重拍  2=已经重拍
        $pageNo=$pageNo<=0?1:$pageNo;
        $keys=$key;
        $keys=str_replace('，',',',$keys);
        $keys=str_replace(' ',',',$keys);
        $keys=explode(',',$keys);
        $where=" where id>0 ";
        if($pstatus==0){
            $where.=" and status in(2,3) ";
        }elseif($pstatus==1){
            $where.=" and status =2 ";
        }else{
            $where.=" and status =3 ";
        }
        //获取上传人员S
        $uploaduser=S("uploaduser");
        if(empty($uploaduser)){
          $ss="select adduser from images where status in(1,2,3) group by adduser";
          $ss=$dd->query($ss);
          $uploaduser=array();
          foreach($ss as $vvv){
            $uploaduser[]=$vvv['adduser'];
          }
          S("uploaduser",$uploaduser,3600);
        }
        //获取上传人员E
        $fild='id,sku,adduser,status,type';
        $sql='select '.$fild.' from images '.$where;
        if($key!=''){
            $keysql="";
            $i=0;
            foreach($keys as $k){
                if($k==''){
                    continue;
                }
                $i++;
                if($i>100){
                    break;
                }
                $keysql.=" or name like '$k%' ";
            }

            $keysql=trim($keysql,' or');
            $sql.=" and ($keysql) ";
        }
        if($adduser!=''){
          $sql.=" and adduser = '$adduser' ";
        }

        $count=session('count');
        $count=!empty($count)?$count:200;

        $sqlcount=str_replace($fild,'count(id) as cc',$sql);

        $sqlcount=$dd->query($sqlcount);#echo $dd->_sql();

        $cc=count($sqlcount);

        $limitCount=$count;
        if($limitCount*($pageNo-1) >= $cc){
            $pageNo = 1;
        }

        $limitStart = $limitCount * ($pageNo-1);
        $pageCount = $limitCount != 0 ? ceil($cc / $limitCount) : 0;
        $sql.=" order by sku  limit $limitStart,$count ";#echo $sql;
        $sql=$dd->query($sql);//echo $dd->_sql();

        $dataarr = array();
        $statusarr=array(
          '1'=>'正常',
          '2'=>'等待重传',
          '3'=>'已经重传'
        );
        foreach($sql as $sqlv){
          $vsku     = $sqlv['sku'];
          $vadduser = $sqlv['adduser'];
          $vstatus  = $sqlv['status'];
          $vtype    = $sqlv['type'];
          $vvs      = $vstatus;
          $vstatus  = $statusarr[$vstatus];
          $uniquekey= $vsku.$adduser.$vvs;
          if(!array_key_exists($uniquekey,$dataarr)){
            $ytcount = $vtype == '99'?1:0;
            $xgcount = $vtype == '100'?1:0;
            $dataarr[$uniquekey]= array(
              'count'=>1,
              'ytcount'=>$ytcount,
              'xgcount'=>$xgcount,
              'sku'=>$vsku,
              'adduser'=>$vadduser,
              'statusCn'=>$vstatus,
              'status'=>$vvs
            );
          }else{
            $dataarr[$uniquekey]['count']++;
            if($vtype == '99'){
              $dataarr[$uniquekey]['ytcount']++;
            }
            if($vtype == '100'){
              $dataarr[$uniquekey]['xgcount']++;
            }
          }
        }

        $this->assign('key',$key);
        $this->assign('data',$dataarr);
        $this->assign('thisstaus',$pstatus);
        $this->assign('thisaddusr',$adduser);
        $this->assign('totalcount',number_format($cc));
        $this->assign('allCount',$cc);
        $this->assign('uploaduser',$uploaduser);
        $this->assign('pageCount',$pageCount);
        $this->display('viewNeedReload');
    }

    public function viewNeedReplace(){
        $dd=M("images",NULL,'DB_CONFIG1');
        $dbERP=M('ebay_user',NULL,'DB_CONFIG2');
        $key=$this->_get('pkeyword');
        $pageNo = (int)$this->_get('pageNow');
        $adduser=trim($this->_get('uploader'));
        $pstatus=(int)$this->_get('pstatus');//0= 等待替换+已经替换， 1=等待替换  2=已经替换
        $pageNo=$pageNo<=0?1:$pageNo;
        $keys=$key;
        $keys=str_replace('，',',',$keys);
        $keys=str_replace(' ',',',$keys);
        $keys=explode(',',$keys);
        $where=" where id>0 and status != 4 ";
        if($pstatus==0){
            $where.=" and is_replace in(2,3) ";
        }elseif($pstatus==1){
            $where.=" and is_replace =2 ";
        }else{
            $where.=" and is_replace =3 ";
        }
        //获取上传人员S
        $uploaduser=S("uploaduser");
        if(empty($uploaduser)){
          $ss="select adduser from images where is_replace in(1,2,3) group by adduser";
          $ss=$dd->query($ss);
          $uploaduser=array();
          foreach($ss as $vvv){
            $uploaduser[]=$vvv['adduser'];
          }
          S("uploaduser",$uploaduser,3600);
        }
        //获取上传人员E
        $fild='id,sku,adduser,is_replace,type';
        $sql='select '.$fild.' from images '.$where;
        if($key!=''){
            $keysql="";
            $i=0;
            foreach($keys as $k){
                if($k==''){
                    continue;
                }
                $i++;
                if($i>100){
                    break;
                }
                $keysql.=" or name like '$k%' ";
            }

            $keysql=trim($keysql,' or');
            $sql.=" and ($keysql) ";
        }
        if($adduser!=''){
          $sql.=" and adduser = '$adduser' ";
        }

        $count=session('count');
        $count=!empty($count)?$count:200;

        $sqlcount=str_replace($fild,'count(id) as cc',$sql);

        $sqlcount=$dd->query($sqlcount);#echo $dd->_sql();

        $cc=count($sqlcount);

        $limitCount=$count;
        if($limitCount*($pageNo-1) >= $cc){
            $pageNo = 1;
        }

        $limitStart = $limitCount * ($pageNo-1);
        $pageCount = $limitCount != 0 ? ceil($cc / $limitCount) : 0;
        $sql.=" order by sku  limit $limitStart,$count ";#echo $sql;
        $sql=$dd->query($sql);//echo $dd->_sql();

        $dataarr = array();
        $replacearr=array(
          '1'=>'正常',
          '2'=>'等待替换',
          '3'=>'已经替换'
        );
        foreach($sql as $sqlv){
          $vsku       = $sqlv['sku'];
          $vadduser   = $sqlv['adduser'];
          $visreplace = $sqlv['is_replace'];
          $vtype      = $sqlv['type'];
          $vvs        = $visreplace;
          $visreplace = $replacearr[$visreplace];
          $uniquekey  = $vsku.$adduser.$vvs;
          if(!array_key_exists($uniquekey,$dataarr)){
            $ytcount = $vtype == '99'?1:0;
            $xgcount = $vtype == '100'?1:0;
            $ywcount = $vtype == '101'?1:0;
            $dataarr[$uniquekey]= array(
              'count'=>1,
              'ytcount'=>$ytcount,
              'xgcount'=>$xgcount,
              'ywcount'=>$ywcount,
              'sku'=>$vsku,
              'adduser'=>$vadduser,
              'isreplaceCn'=>$visreplace,
              'isreplace'=>$vvs
            );
          }else{
            $dataarr[$uniquekey]['count']++;
            if($vtype == '99'){
              $dataarr[$uniquekey]['ytcount']++;
            }
            if($vtype == '100'){
              $dataarr[$uniquekey]['xgcount']++;
            }
            if($vtype == '101'){
              $dataarr[$uniquekey]['ywcount']++;
            }
          }
        }

        $this->assign('key',$key);
        $this->assign('data',$dataarr);
        $this->assign('thisstaus',$pstatus);
        $this->assign('thisaddusr',$adduser);
        $this->assign('totalcount',number_format($cc));
        $this->assign('allCount',$cc);
        $this->assign('uploaduser',$uploaduser);
        $this->assign('pageCount',$pageCount);
        $this->display('viewNeedReplace');
    }

    public function plreuploadimgs(){
      $sku     = $_GET['_URL_'][4];
      $adduser = $_GET['_URL_'][3];
      $imgtype = $_GET['_URL_'][2];
      $this->assign('imgtype',$imgtype);
      $this->assign('imgsku',$sku);
      $this->assign('imgaddusr',$adduser);
      $this->display();
    }

    public function plreplaceimgs(){
      $sku     = $_GET['_URL_'][4];
      $adduser = $_GET['_URL_'][3];
      $imgtype = $_GET['_URL_'][2];
      $this->assign('imgtype',$imgtype);
      $this->assign('imgsku',$sku);
      $this->assign('imgaddusr',$adduser);
      $this->display();
    }

    public function reuploadimg()
    {
      if ($_POST['hiddenid'] == '') {
        die("图片id丢失!");
      }
      $id   = $_POST['hiddenid'];
      $imgt =M('images',NULL,'DB_CONFIG1');
      $dbERP=M('ebay_user',NULL,'DB_CONFIG2');
      $rstr = '';
      if($_FILES['titlePics']['name'] != ''){
        $str = $_FILES['titlePics']['name'];
        if($str==''){
          echo '<div style="color:#911">没有接收到文件名!无法检测!</div>';die;
        }
        $Ex=$this->getPatts();
          //提取SKU
          if(preg_match($Ex,$str,$m)){
            $sku=$m[1];
            $skuSql="(select goods_sn from ebay_goods where goods_sn='$sku' limit 1)";
            $skuSql.="union(select goods_sn from ebay_goods_audit where goods_sn='$sku' limit 1)";
            $skuSql.="union(select goods_sn from ebay_productscombine where goods_sn='$sku' limit 1)";
            $skusql=$dbERP->query($skuSql);
            if(is_array($skusql)&&count($skusql)==0){
              $rstr.='<div style="color:#911">图片名不合规范,识别不到SKU【'.$sku.'】图片名:'.$str.'</div><br>';
              echo $rstr;die;
            }
          }else{
            $rstr.='<div style="color:#911">图片名不合规范!图片名:'.$str.'</div><br>';
            echo $rstr;die;
          }
          $uploadInfo    = $this->uploadImg();
          $imagepath     = str_replace('./','/',$uploadInfo[0]['savepath']).$uploadInfo[0]['savename'];//大图路径
          $picpath       = str_replace('./uploads/','',$uploadInfo[0]['savepath']).$uploadInfo[0]['savename'];//缩略图路径
          //获取原数据
          $oldimg = $imgt->where("id='$id'")->find();
          $time = time();
          $data = array();//上传的新图数据
          $data['name'] = $oldimg['name'];
          $data['sku']  = $oldimg['sku'];
          $data['pic']  = $picpath;
          $data['path'] = $imagepath;
          $data['photoer']=$oldimg['photoer'];
          $data['addtime'] = $time;
          $data['adduser'] = $oldimg['adduser'];
          $data['type'] = $oldimg['type'];
          $data['pid']  = $id;
          $data['status'] = 1;
          //判断是否已经重传过
          $checkup = $imgt->where("pid='$id'")->select();
          if(count($checkup) == 0) {
            if ($imgt->add($data)) {
              //更新原数据的状态
              $upsql = "update images set status = '3' where id = '$id' ";
              if ($imgt->execute($upsql)) {
                echo "<script>alert('上传成功')</script>";
              } else {
                echo "<script>alert('上传失败')</script>";
              }
            }
          }
      }
      $this->viewNeedReload();
    }

  public function uploadImg(){
    import('ORG.Net.UploadFile');
    $thumbpath = "./pic/".date('YmdH').'/';
    $imgpath   = "./uploads/".date('YmdH').'/';
    if(!is_dir($thumbpath)){
      @mkdir($thumbpath);
    }
    if(!is_dir($imgpath)){
      @mkdir($imgpath);
    }
    $upload = new UploadFile();                                    // 实例化上传类
    $upload->thumb = true;
    $upload->thumbMaxWidth  = '180';
    $upload->thumbMaxHeight = '180';
    $upload->maxSize    = 2000000 ;                                // 设置附件上传大小
    $upload->allowExts  = array('jpg', 'gif', 'png');              // 设置附件上传类型
    $upload->saveRule   =  md5(time()).rand(1111,9999);
    $upload->thumbPath  =  $thumbpath;
    $upload->savePath   =  $imgpath;                               // 设置附件上传目录
    if(!$upload->upload()) {                                       // 上传错误提示错误信息
      $this->error($upload->getErrorMsg());
    }else{                                                         // 上传成功 获取上传文件信息
      $info =  $upload->getUploadFileInfo();
    }
    return $info;
  }

}