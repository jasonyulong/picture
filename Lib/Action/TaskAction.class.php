<?php

/**
 * 任务执行
 * Class TaskAction
 */
class TaskAction extends Action {

    public function _initialize(){
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '1000M');
    }

    /**
     * 同步图片信息
     */
    public function syncPhoto() {
        import("@.Service.PhotoService");
        $service = new PhotoService();
        $service->syncPhotoToDesign();

        die('success');
    }

    /**
     * 同步待拍图列表
     */
    public function pushPengingList() {

        import("@.Service.PhotoService");
        $skuList = $this->_post('skuList');
        $skuList = json_decode(htmlspecialchars_decode($skuList));
        $service = new PhotoService();

        try {
            $returnSkuList = $service->pushPengingList($skuList);
            die(json_encode(array(
                'status' => 1,
                'msg' => '操作成功',
                'data' => $returnSkuList
            )));
        } catch (Exception $e) {
            die(json_encode(array(
                'status' => 0,
                'msg' => '操作失败',
                'data' => $skuList
            )));
        }
    }

    /**
     * 推送拍图已完成
     */
    public function pushFinishedList() {
        import("@.Service.PhotoService");
        $service = new PhotoService();
        $result = $service->pushFinishedList();

        die($result['msg']);
    }



}