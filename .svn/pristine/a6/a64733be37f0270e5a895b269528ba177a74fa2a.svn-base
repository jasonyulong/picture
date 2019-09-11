<?php
class SystemAction extends Action{

    public function imgtype(){
        // 展示图片type 上传权限
        $cpower=session("power");
        if(!in_array('imgtypeset',$cpower)){
            echo 'unauthorized....';
            return ;
        }
        $db=M("img_type");
        $dbERP = M('ebay_user',NULL,'DB_CONFIG2');
        $ss="select * from img_type";
        $types=$db->query($ss);

        $ss="select truename from ebay_user group by truename";
        $truenameArr=$dbERP->query($ss);

        $this->assign('truenameArr',$truenameArr);
        $this->assign('types',$types);

        $this->display();

    }

    public function searchUsername(){
        $wd=trim($this->_post('wd'));
        $id=(int)$this->_post('id');
        $dbERP = M('ebay_user',NULL,'DB_CONFIG2');
        //$db=M("img_type");
        if($wd==''){
            echo '<div style="color:#911">错误的提交!</div>';return;
        }
        $ss="select username as uname from ebay_user where username like '%$wd%' or truename like '%$wd%'";
        $ss=$dbERP->query($ss);
        $html='<div class="userbox">';
        foreach($ss as $v){
            $html.='<div class="oneuser">'.$v['uname'].'<span class="'.$id.'" onclick="addUsers(\''.$v['uname'].'\',this)">+</span></div>';
        }
        $html.='<div style="clear:both"></div>';
        echo $html;
    }

    public function getsearchhtml(){
        $id=(int)$this->_post('id');
        if($id==0){
            echo '<div style="color:#911">参数错误!</div>';exit;
        }

        $db=M("img_type");
        $ss="select * from img_type where id=$id limit 1";
        $types=$db->query($ss);
        $user=trim($types[0]['createuser'],',');
        $user=explode(',',$user);
        if($user[0]==''){
            $user=array();
        }
        $html="<div class='userbox'>";
        foreach($user as $vv){
            $html.='<div class="oneuser">'.$vv.'<b class="'.$id.'" onclick="deleteuser(\''.$vv.'\',this)">╳</b></div>';
        }
        $html.="<div style='clear:both;'></div></div><div class='searchuserbox'>";
        $html.="<div class='searchinputbox'><input placeholder='输入人员角色或者姓,名' class='pure-input-1-3' type='text' value=''/><input class='".$id."' type='button' value='搜索' onclick='searchuser(this)'/></div>";
        $html.="<div class='searchresultbox'>搜索结果展示</div>";
        $html.="</div>";
        echo $html;

    }

    private function modImgTypeuser($add=1){
        ///writeFile
        $db=M("img_type");
        $id=(int)$this->_post('id');
        $username=trim($this->_post('user'));
        if($id==0){
            echo '-10';exit;
        }
        if($username==''){
            echo '-10';exit;
        }
        $ss="select * from img_type where id=$id limit 1";
        $types=$db->query($ss);
        if(count($types)!=1){
            echo '-9';exit;
        }
        $typename=$types[0]['typename'];
        $olduser=$types[0]['createuser'];
        $user=trim($types[0]['createuser'],',');
        $user=explode(',',$user);
        $str=$this->moduser($user,$username,$add);
        //echo $str;
        $data=array();
        $data['id']=$id;
        $data['createuser']=$str;
        $file='imgtype/'.date('Ymd').'.txt';
        $handleuser=session('loginName');
        if($db->save($data)!==false){
            $log=$handleuser."--修改【 $typename 】上传人员： ".trim($str,',').' 修改前是:'.trim($olduser,',').'---'.date('Y-m-d H:i:s')."\r\n";
            R("Public/writeFile",array($file,$log));
            echo 2;
        }else{
            echo -2;
        }
    }

    public function addImgTypeuser(){
        $cpower=session("power");
        if(!in_array('imgtypeset',$cpower)){
            echo 'unauthorized....';
            return ;
        }
        $this->modImgTypeuser(1);
    }

    public function delImgTypeuser(){
        $cpower=session("power");
        if(!in_array('imgtypeset',$cpower)){
            echo 'unauthorized....';
            return ;
        }
        $this->modImgTypeuser(-1);
    }

    private function moduser($arr,$username,$add=1){
        $arrs=array();
        foreach($arr as $v){
            if($v!=''){
                $arrs[]=$v;
            }
        }
        if($add>0){
            array_push($arrs,$username);
        }else{
            unset($arrs[array_search($username,$arrs)]);
        }
        $str=','.trim(implode(',',$arrs),',').',';
        return $str;
    }

    public function power(){
        $cpower=session("power");
        if(!in_array('imgpower',$cpower)){
            echo 'unauthorized....';
            return ;
        }

        $db=M("img_type");
        $dbERP = M('ebay_user',NULL,'DB_CONFIG2');

        $username=$this->_post("username");

        $user=session('loginName');
        if($user!='vipadmin'){
            $not=" and  username!='vipadmin' ";
        }else{
            $not='';
        }
        $v3ss="select username,truename from ebay_user where id>0 $not ";
        if($username!=''){
            $v3ss.=" and(username like '%$username%' or truename like '%$username%')";
        }
        $sa=$dbERP->query($v3ss);
        //echo $dbERP->getDbError();
        $arrTrname=array();
        $namestr='';
        foreach($sa as $vvv){
            $arrTrname[$vvv['username']]=$vvv['truename'];
            $namestr.="'".$vvv['username']."',";
        }
        $namestr=trim($namestr,',');

        if($namestr==''){
            $namestr="''";
        }

        $ss="select id,`username` from  user_power where username in($namestr) order by `username`";
        $ss=$db->query($ss);
        $this->assign('userArr',$ss);
        $this->assign('arrTname',$arrTrname);
        $this->display();
    }

    public  function modauserpower(){
        $id=(int)$this->_get("id");
        $db=M("user_power");

        $sub=$this->_post("sub");

        if($sub=='sub'){
            $cpower=$this->_post("power");
            //debug($power);
            $count=(int)$this->_post("count");
            $super_review=(int)$this->_post("super_review");
            $download=(int)$this->_post("download");
            $ss="select `username` from user_power where id='$id' limit 1";
            $ss=$db->query($ss);
            $moduser=$ss[0]['username'];
            $power=implode(',',$cpower);
            $data=array();
            $data['id']=$id;
            $data['power']=$power;
            $data['count']=$count;
            $data['super_review']=$super_review;
            $data['download']=$download;
            if($db->save($data)!==false){
                $handleuser=session('loginName');
                $file='power/'.date('Ymd').'.txt';
                $log=$handleuser."--修改【 $moduser 】的权限 【".$power.'】---'.date('Y-m-d H:i:s')."\r\n";
                R("Public/writeFile",array($file,$log));
                $saveRs='<div style="color:#191">保存权限成功</div>';
            }else{
                $saveRs='<div style="color:#911">保存权限失败</div>';
            }
            //echo $db->_sql();

        }

        $ss="select id,username,power,count,super_review,download from user_power where id=$id limit 1";
        $ss=$db->query($ss);
        $power=$ss[0]['power'];
        $count=$ss[0]['count'];
        $username=$ss[0]['username'];
        $userid=$ss[0]['id'];
        $super_review=$ss[0]['super_review'];
        $download=$ss[0]['download'];

        $ypower=explode(',',$power);// debug($cpower);
        $this->assign('ypower',$ypower);
        $this->assign('count',$count);
        $this->assign('username',$username);
        $this->assign('super_review',$super_review);
        $this->assign('download',$download);
        $this->assign('userid',$userid);
        $this->assign('saveRs',$saveRs);
        $this->display();
    }

    public function imgconfig(){
        $cpower=session("power");
        if(!in_array('imgconfig',$cpower)){
            echo 'unauthorized....';
            return ;
        }
        $db=M("img_config");
        $result='';
        // START ======================================  处理提交问题
        $submit=$this->_post('submit');
        if($submit=='submit'){
           // debug($_POST);
            $checkedtype=$this->_post('checkedtype');
            $pingluncount=(int)$this->_post('pingluncount');
            $danger_score=$this->_post('danger_score');
            $checkedtypestr=implode(',',$checkedtype);
            $ss="select id from img_config limit 1";
            $ss=$db->query($ss);
            if(count($ss)==1&&is_array($ss)){
                //debug($ss);
                $up="update img_config set score='$danger_score',rev_count='$pingluncount',imgtypes='$checkedtypestr' limit 1";
            }else{
                $up="insert into img_config(`score`,`rev_count`,`imgtypes`)values('$danger_score','$pingluncount','$checkedtypestr')";
            }

            $rs=$db->query($up);
            if($rs!==false){
                $result="funsTool.showHTips('修改成功!','#191',1400);";
            }else{
                $result="funsTool.showHTips('修改失败,可能您没有修改什么','#911',1400);";
            }
            //debug($db->_sql());
        }

        //END =========================  处理提交问题



        $ss="select * from img_config limit 1";
        $ss=$db->query($ss);

        $imgtypes=$ss[0]['imgtypes'];
        $imgtypes=explode(',',$imgtypes);

        $sst="select id,typename from img_type  order by id ";
        $sst=$db->query($sst);

        $this->assign('Alltypes',$sst);
        $this->assign('data',$ss);
        $this->assign('result',$result);
        $this->assign('imgtypes',$imgtypes);
        $this->display();
    }
}