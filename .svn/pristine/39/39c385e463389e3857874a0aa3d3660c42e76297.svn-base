<?php

class TableAction extends Action
{
    public function _initialize() {
        if (!session('?loginName') || !session('?truename')) {
            $this->redirect('Login/login');
        }
    }

    public function index() {
        $dd = M("images");
        $key = $this->_get('pkeyword');
        $img_type = (int)$this->_get('img_type');
        $start = $this->_post('start');
        $end = $this->_post('end');
        $sub = $this->_post('sub');
        $uploader = $this->_post('uploader');
        if (empty($start)) {
            $start = date('Y-m-d');
        }
        if (empty($end)) {
            $end = date('Y-m-d');
        }
        //$ss="select count(id) as ";
        $uploaduser = S("uploaduser");
        if (empty($uploaduser)) {
            $ss = "select adduser from images where status in(1,2,3) group by adduser";
            $ss = $dd->query($ss);
            $uploaduser = array();
            foreach ($ss as $vvv) {
                $uploaduser[] = $vvv['adduser'];
            }
            S("uploaduser", $uploaduser, 3600);
        }
        $starts = strtotime($start . " 00:00:00");
        $ends = strtotime($end . " 23:59:59");
        $data = array();
        $uploaduserResult = array();
        $uploaduserFor = $uploaduser;
        if ($uploader != '') {
            $uploaduserFor = array($uploader);
        }
        if ($sub == 1) {
            foreach ($uploaduserFor as $uname) {
                $ss = "select count(id) as cc,`type` from images where addtime>=$starts and addtime<=$ends and adduser='$uname' and status in(1,2,3) group by type";
                $ss = $dd->query($ss);
                //debug($ss);
                if (count($ss) == 1 && $ss[0]['cc'] == 0) {
                    continue;
                }
                if (count($ss) == 0) {
                    continue;
                }
                $ccArr = array();
                foreach ($ss as $vv) {
                    $ccArr[$vv['type']] = $vv['cc'];
                }
                $data[$uname] = $ccArr;
                $uploaduserResult[] = $uname;
            }
        }
        $ss = "select id,typename from img_type";
        $ss = $dd->query($ss);
        $imgTypeArr = array();
        foreach ($ss as $vv) {
            $imgTypeArr[$vv['id']] = $vv['typename'];
        }
        // $imgTypeArr[0]='未知的类型';
        $this->assign('uploaduser', $uploaduser);
        $this->assign('imgTypeArr', $imgTypeArr);
        $this->assign('data', $data);
        $this->assign('uploaduserResult', $uploaduserResult);
        //$this->assign('typenameArr',$typenameArr);
        //$this->assign('totalcount',number_format($cc));
        //$this->assign('allCount',$cc);
        $this->assign('start', $start);
        $this->assign('end', $end);
        $this->display();
    }

    public function tihuan() {
        $dd = M("images");
        $key = $this->_get('pkeyword');
        $img_type = (int)$this->_get('img_type');
        $start = $this->_post('start');
        $end = $this->_post('end');
        $sub = $this->_post('sub');
        $uploader = $this->_post('uploader');
        if (empty($start)) {
            $start = date('Y-m-d');
        }
        if (empty($end)) {
            $end = date('Y-m-d');
        }
        //$ss="select count(id) as ";
        $uploaduser = S("uploaduser");
        if (empty($uploaduser)) {
            $ss = "select adduser from images where status in(1,2,3) group by adduser";
            $ss = $dd->query($ss);
            $uploaduser = array();
            foreach ($ss as $vvv) {
                $uploaduser[] = $vvv['adduser'];
            }
            S("uploaduser", $uploaduser, 3600);
        }
        $starts = strtotime($start . " 00:00:00");
        $ends = strtotime($end . " 23:59:59");
        $data = array();
        $uploaduserResult = array();
        $uploaduserFor = $uploaduser;
        if ($uploader != '') {
            $uploaduserFor = array($uploader);
        }
        if ($sub == 1) {
            foreach ($uploaduserFor as $uname) {
                $ss = "select count(id) as cc,`type` from images where addtime>=$starts and addtime<=$ends and adduser='$uname' and is_replace = 3 group by type";
                $ss = $dd->query($ss);
                //debug($ss);
                if (count($ss) == 1 && $ss[0]['cc'] == 0) {
                    continue;
                }
                if (count($ss) == 0) {
                    continue;
                }
                $ccArr = array();
                foreach ($ss as $vv) {
                    $ccArr[$vv['type']] = $vv['cc'];
                }
                $data[$uname] = $ccArr;
                $uploaduserResult[] = $uname;
            }
        }
        $ss = "select id,typename from img_type";
        $ss = $dd->query($ss);
        $imgTypeArr = array();
        foreach ($ss as $vv) {
            if ($vv['typename'] == '业务自处理') {
                continue;
            }
            $imgTypeArr[$vv['id']] = $vv['typename'];
        }
        // $imgTypeArr[0]='未知的类型';
        $this->assign('uploaduser', $uploaduser);
        $this->assign('imgTypeArr', $imgTypeArr);
        $this->assign('data', $data);
        $this->assign('uploaduserResult', $uploaduserResult);
        //$this->assign('typenameArr',$typenameArr);
        //$this->assign('totalcount',number_format($cc));
        //$this->assign('allCount',$cc);
        $this->assign('start', $start);
        $this->assign('end', $end);
        $this->display();
    }

    public function review() {
        $db = M("img_review");
        $start = $this->_post('start');
        $end = $this->_post('end');
        $sub = $this->_post('sub');
        $pluser = $this->_post('pluser');
        $userjiaose = $this->getAllUserIndexName();
        $plusers = $this->getPluser($db, $userjiaose);
        if (empty($start)) {
            $start = date('Y-m-d');
        }
        if (empty($end)) {
            $end = date('Y-m-d');
        }
        $starts = strtotime($start . ' 00:00:00');
        $ends = strtotime($end . ' 23:59:59');
        $arr = array();
        if ($pluser == '' && $sub != '') {
            $ss = "select count(id) as cc,username from  img_review where addtime between $starts and $ends group by username";
            $arr = $db->query($ss);
        }
        if ($pluser != '' && $sub != '') {
            $arr = array();
            for (; $starts <= $ends; $starts += 86400) {
                $myend = $starts + 86400;
                $ss = "select count(id) as cc,username from  img_review where  addtime >=$starts and addtime <$myend  and username ='$pluser' limit 1";
                $ss = $db->query($ss);
                $cc = $ss[0]['cc'];
                $uname = $ss[0]['username'];
                $arr[] = array('cc' => $cc, 'username' => $uname, 'date' => date('Y-m-d', $starts));
            }
        }
        //debug($arr);
        $this->assign('data', $arr);
        $this->assign('plusers', $plusers);
        $this->assign('userjiaose', $userjiaose);
        $this->assign('start', $start);
        $this->assign('end', $end);
        $this->display();
    }

    public function sheying() {
        $dbERP = M('ebay_user', NULL, 'DB_CONFIG2');
        $db = M('images', NULL, 'DB_CONFIG1');
        $start = $this->_post('start');
        $end = $this->_post('end');
        $sub = $this->_post('sub');
        $sheyingshi = $this->_post('sheyingshi');
        $photoer = "select username from ebay_user where truename like '%摄影%'";
        $photoers = $dbERP->query($photoer);
        $dataStar = array();
        $dataReLoad = array();
        if (empty($start)) {
            $start = date('Y-m-d');
        }
        if (empty($end)) {
            $end = date('Y-m-d');
        }
        $starts = strtotime($start . ' 00:00:00');
        $ends = strtotime($end . ' 23:59:59');
        $total = 0;
        if ($sheyingshi != '') {
            $dataStar = array(0, 0, 0, 0, 0, 0);
            $dataReLoad = array(
                'needreload' => 0,
                'reloaded' => 0,
            );
            $ss = "select id,status,score from images where addtime>=$starts and addtime<=$ends and status in(1,2,3) and pid=0 and photoer='$sheyingshi' ";
            $ss = $db->query($ss);
            foreach ($ss as $vs) {
                $id = $vs['id'];
                $status = $vs['status'];
                $score = $vs['score'];
                $index = $this->getStarindex($score);
                $dataStar[$index] += 1;
                if ($status >= 2) {
                    $dataReLoad['needreload'] += 1; //需要重新传
                }
                if ($status == 3) {
                    $dataReLoad['reloaded'] += 1; // 已经重新传
                }
            }
            $dataReLoad['wreload'] = $dataReLoad['needreload'] - $dataReLoad['reloaded'];
            $total = count($ss);
        }
        $this->assign('total', $total);
        $this->assign('photoers', $photoers);
        $this->assign('dataStar', $dataStar);
        $this->assign('dataReLoad', $dataReLoad);
        $this->assign('start', $start);
        $this->assign('end', $end);
        $this->display();
    }

    public function meigong() {
        $dbERP = M('ebay_user', NULL, 'DB_CONFIG2');
        $db = M('images', NULL, 'DB_CONFIG1');
        $start = $this->_post('start');
        $end = $this->_post('end');
        $sub = $this->_post('sub');
        $meigong = $this->_post('meigong');
        $meigongs = "select username from ebay_user where truename like '%美工%'";
        $meigongs = $dbERP->query($meigongs);
        if (empty($start)) {
            $start = date('Y-m-d');
        }
        if (empty($end)) {
            $end = date('Y-m-d');
        }
        $starts = strtotime($start . ' 00:00:00');
        $ends = strtotime($end . ' 23:59:59');
        $ss = "select id,typename from  img_type";
        $ss = $db->query($ss);
        $typeArr = array();
        foreach ($ss as $vv) {
            $typeArr[$vv['id']] = $vv['typename'];
        }
        $total = array();
        if ($meigong != '') {
            $dataStar = array();
            $dataReLoad = array();
            $ss = "select id,status,score,type from images where addtime>=$starts and addtime<=$ends and status in(1,2,3) and pid=0 and adduser='$meigong' ";
            $ss = $db->query($ss);
            foreach ($ss as $vs) {
                $id = $vs['id'];
                $status = $vs['status'];
                $score = $vs['score'];
                $type = $vs['type'];
                $index = $this->getStarindex($score);
                if (!isset($dataStar[$type])) {
                    $dataStar[$type] = array(0, 0, 0, 0, 0, 0);
                }
                if (!isset($dataReLoad[$type])) {
                    $dataReLoad[$type] = array(
                        'needreload' => 0,
                        'reloaded' => 0,
                    );
                }
                if (!isset($total[$type])) {
                    $total[$type] = 0;
                }
                $dataStar[$type][$index] += 1;
                if ($status >= 2) {
                    $dataReLoad[$type]['needreload'] += 1; //需要重新传
                }
                if ($status == 3) {
                    $dataReLoad[$type]['reloaded'] += 1; // 已经重新传
                }
                $total[$type] += 1;
            }
            //$dataReLoad['wreload']=$dataReLoad['needreload']-$dataReLoad['reloaded'];
        }
        //// debug($dataReLoad);
        /// debug($dataStar);
        $this->assign('typeArr', $typeArr);
        $this->assign('dataReLoad', $dataReLoad);
        $this->assign('dataStar', $dataStar);
        $this->assign('total', $total);
        $this->assign('meigongs', $meigongs);
        $this->assign('start', $start);
        $this->assign('end', $end);
        $this->display();
    }

    /**
     * 统计图片上传的款数
     */
    public function getStatistic() {
        $start = $_POST['start'];
        $end = $_POST['end'];
        if (empty($start)) {
            $start = date('Y-m-d');
        }
        if (empty($end)) {
            $end = date('Y-m-d');
        }
        $start_time = strtotime($start . ' 00:00:00');
        $end_time = strtotime($end . ' 23:59:59');
        $meigong = $_POST['meigong'];
        $map['addtime'][] = array('egt', $start_time);
        $map['addtime'][] = array('elt', $end_time);
//        if (!empty($meigong)) {
//            $map['adduser'] = $meigong;
//        } else {
//            $map['adduser'] = array('in', $meigongs);
//        }
        $type = $_REQUEST['type']?$_REQUEST['type']:0;
        if(!$type){
            //所有美工
            $user_list = M('ebay_user', NULL, 'DB_CONFIG2')->where(array('truename' => array('like', '%美工%')))->getField('username', true);
        }else{
            //所有摄影
            $user_list = M('ebay_user', NULL, 'DB_CONFIG2')->where(array('truename' => array('like', '%摄影%')))->getField('username', true);
        }

        $map['status'] = array('in','1,2,3');
        $imgModel = new ImageModel();
        $list = array();
        foreach($user_list as $v){
            if(!$type){
                $map['adduser'] = $v;
            }else{
                $map['photoer'] = $v;
            }
            $data = $imgModel
                ->where($map)
                ->group('sku')
                ->field('sku,count(sku) as sku_pic_qty')
//                ->order('count(sku) desc')
                ->select();
            $models = $imgModel
                ->where($map)
                ->group('sku')
                ->having('count(sku)>=10')
                ->select();
            if(!empty($data)){
                $list[$v]['list'] = $data;
                $list[$v]['model_count'] = count($models);
            }
        }
        $this->assign('type',$type);
        $this->assign('list',$list);
        $this->assign('start', $start);
        $this->assign('end', $end);
        $this->assign('meigong', $meigong);
//        $this->assign('meigongs', $meigongs);
        $this->display();
    }

    public function readSkuPicDetail(){
        $sku = $_REQUEST['sku'];
        $start = $_REQUEST['start'];
        $end = $_REQUEST['end'];
        $start_time = strtotime($start . ' 00:00:00');
        $end_time = strtotime($end . ' 23:59:59');
        $map['addtime'][] = array('egt', $start_time);
        $map['addtime'][] = array('elt', $end_time);
        $map['sku'] = $sku;
        if($_REQUEST['type']){
            $map['photoer'] = $_REQUEST['adduser'];
        }else{
            $map['adduser'] = $_REQUEST['adduser'];
        }
        $map['status'] = array('in','1,2,3');
        $imgModel = new ImageModel();
        $list = $imgModel->where($map)->field('addtime,pic,path')->select();
        $this->assign('list',$list);
        $this->display();
    }

    private function getStarindex($score) {
        if ($score >= 1 && $score < 2) {
            return 1;
        }
        if ($score >= 2 && $score < 3) {
            return 2;
        }
        if ($score >= 3 && $score < 4) {
            return 3;
        }
        if ($score >= 4 && $score < 5) {
            return 4;
        }
        if ($score >= 5) {
            return 5;
        }
    }

    private function getAllUserIndexName() {
        $dbERP = M('ebay_user', NULL, 'DB_CONFIG2');
        $ss = "select id,username,truename from ebay_user order by py";
        $rs = $dbERP->query($ss);
        $arr = array();
        foreach ($rs as $vvv) {
            $arr[$vvv['username']] = $vvv['truename'];
        }
        return $arr;
    }

    private function getPluser($db, $alluser) {
        $ss = "select username from img_review group by username ";
        $ss = $db->query($ss);
        $pjuser = array();
        foreach ($ss as $vvv) {
            $pjuser[$vvv['username']] = 1;
        }
        $rsArr = array();
        foreach ($alluser as $k => $v) {
            if (array_key_exists($k, $pjuser)) {
                $rsArr[] = $k;
            }
        }
        return $rsArr;
    }

    private function getSheyingshi() {
    }
}