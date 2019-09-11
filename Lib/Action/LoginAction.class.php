<?php
class LoginAction extends Action{
    public function index(){
        $this->display('login');
    }

    public function login(){

        $rs=$this->AllowIp();

        if($rs==false){
            echo '<meta charset="utf-8">系统检测到您的登陆地点异常!';
            return false;
        }

        $this->display();
    }

    public function logout(){
        session('loginName',null);
        session('loginPwd',null);
        $this->redirect('Login/login/');
    }

    public function loginCheck(){

        $rs=$this->AllowIp();

        if($rs==false){
            echo '<meta charset="utf-8">系统检测到您的登陆地点异常!';
            return false;
        }

        if($this->_post('acc') == "NUll" || trim($this->_post('acc')) == '' || $this->_post('pwd') == "NULL" || trim($this->_post('pwd')) == ''){
            echo 'pls input Name and Password';
            exit;
        }
        $db=M('user_power');
        $accDB = M('ebay_user',NULL,'DB_CONFIG2');
        $map['username'] = trim($this->_post('acc'));
        $map['password'] = md5(md5(trim($this->_post('pwd'))));
        $accArray = $accDB->where($map)->limit(1)->select();
       // debug($accArray);
        $accDB->close;
        if(count($accArray) != 1){
            echo 'Name or Password is wrong';
            exit;
        }

        $truename=$accArray[0]['truename'];

        // 写入lastlogin

        $ss="select id,username,power,count,download from user_power where username='".$accArray[0]['username']."' limit 1";
        $ss=$db->query($ss);
        //debug($db->getDbError());
        $data=array();
        $data['lastlogin']=time();
        $power='';
        $count=200;
        if(count($ss)==1&&is_array($ss)){
            $data['id']=$ss[0]['id'];
            $power=$ss[0]['power'];
            $count=$ss[0]['count'];
            $db->save($data);
        }else{
            $data['username']=$accArray[0]['username'];
           // debug($data);
            $db->add($data);
            //echo $db->_sql();
        }

        $power=explode(',',$power);

        session(array('name'=>'loginID','expire'=>12*3600));
        session(array('name'=>'loginName','expire'=>12*3600));
       //session(array('name'=>'loginPwd','expire'=>7200));
        session('loginID',$accArray[0]['id']);
        session('loginName',$accArray[0]['username']);
        session('power',$power);
        session('count',$count);
        session('truename',$truename);
        $this->redirect('Index/index/');
    }

    /**
     *测试人员谭 2018-03-23 21:20:55
     *说明: 允许的IP地址
     */
    private function AllowIp($ip=''){

        if($ip==''){
            $ip=get_client_ip();
        }
        $Arr=C('ALLOW_IP_ARRAY');

        //p($Arr);
        $UserIp=$this->splitIp($ip);

        //print_r($Arr);
        foreach($Arr as $ipItem){
            $listIp=$this->splitIp($ipItem);
            if($listIp=='0.0.'){ // 这个设置存在 就表示 所有的 都可以登陆
                return true;
            }

            if($listIp==$UserIp){
                return true;
            }
        }

        return true;

    }

    /**
     *测试人员谭 2018-03-23 21:26:43
     *说明: 只是分割钱两位
     */
    private function splitIp($ip){
        $spr=preg_split("/\d+\.\d+$/", $ip);
        return $spr[0];
    }
}