<?php

class ImageAction extends Action
{
    public function uploadImg() {
        //允许跨域
        header("Access-Control-Allow-Origin:*");
        if(!in_array($_SERVER['HTTP_ORIGIN'],C('ALLOW_HOST'))){
            #die('不允许访问!');
        }
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') die;
        //检测当前图片所属信息 sku、adduser
        if (empty($_POST['sku']) && empty($_POST['adduser'])) {
            die(json_encode(array('status' => 0, 'info' => 'sku丢失 或 不存在的用户')));
        }
        //上传图片名称是否符合规则
//        if (!$status = $this->checkImgName($_POST['name'])) {
//            die(json_encode(array('status' => 0, 'info' => '图片名称不符和规范')));
//        }
        //开始上传
        $info = $this->uploading();
        if (!$info) {
            die(json_encode(array('status' => 0, 'info' => '上传失败')));
        }
        //大图路径
        $imgPath = str_replace('./', '/', $info[0]['savepath']) . $info[0]['savename'];
        //缩略图路径
        $picPath = str_replace('./uploads/', '', $info[0]['savepath']) . $info[0]['savename'];
        //保存数据
        $saveImgData = array(
            'name' => $_POST['name'],
            'sku' => $_POST['sku'],
            'adduser' => $_POST['adduser'],
            'addtime' => time(),
            'type' => $_POST['type'],
            'pic' => $picPath,
            'path' => $imgPath,
            'type' => $_POST['file_type'],
            'pid' => 0,
            'status' => 1,
            'is_replace' => 1
        );
        $imgModel = new ImageModel();
        if ($id = $imgModel->add($saveImgData)) {
            die(json_encode(array('status' => 1, 'pic_id'=>$id,'info' => $id . '上传成功' . $_POST['sku'])));
        } else {
            die(json_encode(array('status' => 0, 'info' => $_POST['name'] . '上传失败')));
        }
    }

    /**
     * 开始上传效果图或原图
     * @return array|bool
     */
    public function uploading() {
        import('ORG.Net.UploadFile');
        $thumbpath = "./pic/" . date('YmdH') . '/';
        $imgpath = "./uploads/" . date('YmdH') . '/';
        if (!is_dir($thumbpath)) {
            mkdir($thumbpath,0777,true);
        }
        if (!is_dir($imgpath)) {
            mkdir($imgpath,0777,true);
        }
        $upload = new UploadFile();                                    // 实例化上传类
        $upload->thumb = true;
        $upload->thumbMaxWidth = '180';
        $upload->thumbMaxHeight = '180';
        $upload->maxSize = 2000000;                                // 设置附件上传大小
        $upload->allowExts = array('jpg', 'gif', 'png', 'jpeg');              // 设置附件上传类型
        $upload->saveRule = md5(time()) . rand(1111, 9999);
        $upload->thumbPath = $thumbpath;
        $upload->savePath = $imgpath;                               // 设置附件上传目录
        if (!$upload->upload()) {                                       // 上传错误提示错误信息
//            $this->error($upload->getErrorMsg());
            return false;
        } else {                                                         // 上传成功 获取上传文件信息
            $info = $upload->getUploadFileInfo();
            return $info;
        }
    }

    /**
     * 检测图片名是否符合规范
     */
    public function checkImgName($imgName) {
        $dbERP = M('ebay_user', NULL, 'DB_CONFIG2');
        $Ex = '/^([0-9A-Za-z\-]{5,15})(\.|\s|_|\(|（)/';
        if (preg_match($Ex, $imgName, $m)) {
            $sku = $m[1];
            $skuSql = "(select goods_sn from ebay_goods where goods_sn='$sku' limit 1)";
            $skuSql .= "union(select goods_sn from ebay_goods_audit where goods_sn='$sku' limit 1)";
            $skuSql .= "union(select goods_sn from ebay_productscombine where goods_sn='$sku' limit 1)";
            $res = $dbERP->query($skuSql);
            if (is_array($res) && count($res) == 0) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    public function deleteImg() {
        header("Access-Control-Allow-Origin:*");
        if(!in_array($_SERVER['HTTP_ORIGIN'],C('ALLOW_HOST'))){
            #die('不允许访问!');
        }
        $id = $_POST['id'];
        $imageModel = new ImageModel();
        $status = $imageModel->where('id=' . $id)->setField('status', 4);
        if (false === $status) {
            echo json_encode(array('status' => 0, 'info' => '删除失败'));
        } else {
            echo json_encode(array('status' => 1, 'info' => '删除成功'));
        }
    }

    // 获取原图缩略图
    public function getImgBySku() {
        $sku = $_REQUEST['sku'];
        $count = $_REQUEST['count'];
        if ($count > 20) {
            $count = 20;
        }
        if ($count <= 0) {
            $count = 1;
        }
        //        $sku = trim($sku);
//        $sku = preg_replace('/[^0-9A-Z\-]/', '', $sku);
        $dd = M('images');
        $ss = "select id,pic,path,adduser from images where sku='$sku' and status=1 and type='99' order by id desc limit $count";
        $ss = $dd->query($ss);
        $rs = array();
        for ($i = 0; $i < count($ss); $i++) {
            $rs[$i]['pic'] = C('PIC_URL') . '/pic/' . $ss[$i]['pic'];
            $rs[$i]['id'] = $ss[$i]['id'];
            $rs[$i]['adduser'] = $ss[$i]['adduser'];
            $size = getimagesize(dirname(dirname(dirname(__FILE__))) . '/' . $ss[$i]['path']);
            if (count($size) > 0 && is_array($size)) {
                $rs[$i]['wight'] = $size[0];
                $rs[$i]['height'] = $size[1];
            } else {
                $rs[$i]['wight'] = '600';
                $rs[$i]['height'] = '600';
            }
        }
        $data = array('data' => $rs, 'error' => 0);
        $str = json_encode($data);
        echo $str;
    }

    //获取效果图缩略图
    public function getEffectsImgBySku() {
        $sku = $_REQUEST['sku'];
        $count = $_REQUEST['count'];
        if ($count > 20) {
            $count = 20;
        }
        if ($count <= 0) {
            $count = 1;
        }
//        $sku = trim($sku);
//        $sku = preg_replace('/[^0-9A-Z\-]/', '', $sku);
        $dd = M('images');
        $ss = "select id,pic,path,adduser from images where sku='$sku' and status=1 and type='100' order by id desc limit $count";
        $ss = $dd->query($ss);
        $rs = array();
        for ($i = 0; $i < count($ss); $i++) {
            $rs[$i]['pic'] = C('PIC_URL') . '/pic/' . $ss[$i]['pic'];
            $rs[$i]['id'] = $ss[$i]['id'];
            $rs[$i]['adduser'] = $ss[$i]['adduser'];
            $size = getimagesize(dirname(dirname(dirname(__FILE__))) . '/' . $ss[$i]['path']);
            if (count($size) > 0 && is_array($size)) {
                $rs[$i]['wight'] = $size[0];
                $rs[$i]['height'] = $size[1];
            } else {
                $rs[$i]['wight'] = '600';
                $rs[$i]['height'] = '600';
            }
        }
        $data = array('data' => $rs, 'error' => 0);
        $str = json_encode($data);
        echo $str;
    }


    /**
     * 保存erp系统推送的图片
     */
    public function saveCustomAttrPicFromErp() {
        //图片根目录
        $CustomAttrPicPath = C('CUSTOM_ATTR_IMAGE_PATH');
        $path = '.'.$CustomAttrPicPath.$_POST['path'];
        $content = $_POST['content'];
        $name = $_POST['name'];
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        if (file_put_contents($path . $name, $content)) {
            die(json_encode(array('status' => '1')));
        } else {
            die(json_encode(array('status' => '0')));
        }
    }

    /**
     * 获取自定义属性图片
     */
    public function getCustomAttrImg(){
        $CustomAttrPicPath = C('CUSTOM_ATTR_IMAGE_PATH');
        $sku = $_GET['sku'];
        $attr_string = $_GET['attr_string'];
        $pic_type = $_GET['suffix'];
        $picPath = '.'.$CustomAttrPicPath.$sku[0] . $sku[1] . '/' . $sku . '/'.$sku . '-' . $attr_string.'.'.$pic_type;
        $picContent = file_get_contents($picPath);
        header('Content-type: image/'.$pic_type);
        echo $picContent;
    }


}