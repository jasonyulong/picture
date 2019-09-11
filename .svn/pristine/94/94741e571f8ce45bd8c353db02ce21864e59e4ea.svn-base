<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/19
 * Time: 17:59
 */
class ImgApiAction extends Action
{
    // 原图:99 效果图:100 业务自处理:101 所有图片:all
    protected $extType = array(
        '99',
        '100',
        '101',
        'all'
    );

    /**
     * @-create at 2017/7/19 by 朱诗萌
     * @-comment 根据单个sku获取图片
     */
    public function getImgBySku()
    {
        $request = $_GET;
        if (empty($request['sku']) || empty($request['type'])) {
            $this->response(0, 'sku 和 type 必须');
            die;
        }
        $image = new ImageModel();
        $map['sku'] = $request['sku'];
        if ($request['type'] != 'all') {
            $map['type'] = $request['type'];
        }
        $list = $image->where($map)->select();
        if (!empty($list)) {
            $this->response(1, '获取成功', $list);
        } else {
            $this->response(0, '暂无图片');
        }
    }

    /**
     * @-create at 2017/7/19 by 朱诗萌
     * @-comment 根据多个sku获取图片
     */
    public function getImgBySkuList()
    {
        $request = $_GET;
        if (empty($request['sku']) || empty($request['type'])) {
            $this->response(0, 'sku 和 type 必须');
            die;
        }
        $request['sku'] = trim($request['sku'], ',');
        if (false != strrpos($request['sku'],',')) {
            $request['sku'] = explode(',', $request['sku']);
        }else{
            $request['sku'] = array($request['sku']);
        }
        $image = new ImageModel();

        /**
        *测试人员谭 2018-04-13 11:32:06
        *说明: 这里有一个 优化空间 如果是 sku 存在包含关系，可以干掉长的 留下短的 未实现
        */


        $map['sku'] = array('like', array_map(function ($param) {
            return $param . '%';
        }, $request['sku']), 'OR');


        if ($request['type'] != 'all') {
            $map['type'] = $request['type'];
        }
        $map['status'] = array('in',array(1,3));
        //$map['is_replace'] = array('neq',2);
        $map['is_replace'] = array('eq',1); // 谭： 目前表里面只有1 2

        $list = $image->where($map)->limit(50)->select();

        //echo $image->_sql().'ssss';die();

        if (!empty($list)) {
            foreach($list as $k=>$v){
                list($width,$height) = getimagesize('.'.$v['path']);
                $list[$k]['height'] = $height;
                $list[$k]['width'] = $width;
            }
            $this->response(1, '获取成功', $list);
        } else {
            $this->response(0, '暂无图片');
        }
    }

    /**
     * @-create at 2017/7/19 by 朱诗萌
     * @-comment 根据图片id上传图片到盘古
     */
    public function uploadImgToPanGu()
    {
        $request = $_POST;
        $ids = $request['ids'];
        $directory = $request['directory'];
        if(empty($ids)){
            $this->response(0,'没有获取到图片id');
        }
        if(empty($directory)){
            $this->response(0,'没有获取到指定目录名');
        }
        import("@.Service.UploadToPanGuService");
        $service = new UploadToPanGuService();
        $image = new ImageModel();
        $ids = trim($ids,',');
        if(strrpos($ids,',')){
            $ids = explode(',',$ids);
        }
        $map['id'] = array('in',$ids);
        $list = $image->where($map)->select();
        $result = $service->uploadToPanGuWithDirectory($list,$directory);
        if($result['code'] == 0){
            $this->response(1,'上传成功');
        }else{
            $this->response(0,'上传失败');
        }
    }

    public function response($status, $msg, array $data=[], $type = 'json')
    {
        $result['status'] = $status;
        $result['msg'] = $msg;
        if (false == $status) {
            $result['data'] = array();
            echo json_encode($result,JSON_UNESCAPED_UNICODE);
        } else {
            $result['data'] = $data;
            echo json_encode($result,JSON_UNESCAPED_UNICODE);
        }
    }
}