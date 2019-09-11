<?php

/**
 * 拍图服务层
 * Class PhotoService
 */
class PhotoService  {

    /**
     * 同步摄影图片给美工
     */
    public function  syncPhotoToDesign() {
        $photoList = D('Photo')->where(array('type' => 0, 'status' => 0))->select();

        foreach ($photoList as $key => $val) {
            $sku = $val['sku'];
            $dir = $val['path'];
            $files = '';
            $flag = false;
            if (is_dir($dir)) {
                if ($dh = opendir($dir)) {
                    while (($file = readdir($dh)) !== false) {
                        if ($file == '.' || $file == '..') continue;

                        if (!empty($val['path'])) {
                            $files .= trim($file).",";
                            $newDir = str_replace('A001', 'A002', $val['path']);
                            if (!is_dir($newDir)) {
                                mkdir($newDir,0777,true);
                            }
                            copy($val['path'].DIRECTORY_SEPARATOR.$file, $newDir.DIRECTORY_SEPARATOR.$file);
                            unlink($val['path'].DIRECTORY_SEPARATOR.$file);
                            $flag = true;
                        }  else {
                            $flag = false;
                        }
                    }

                    if ($flag) {
                        $files = rtrim($files, ',');
                        D('Photo')->where(array('id' => $val['id']))
                            ->save(array('content' => $files, 'status' => 2));
                    }
                }
            }
        }
    }

    /**
     * 推送待处理列表
     */
    public function pushPengingList($skuList, $shareName = 'A001') {
        $return = array();

        foreach ($skuList as $key => $sku) {
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $path = "E:".DIRECTORY_SEPARATOR.$shareName.DIRECTORY_SEPARATOR.date('Ymd').DIRECTORY_SEPARATOR.$sku;
            } else {
                $path = '/home/'.$shareName.'/'.date('Ymd').'/'.$sku;
            }

            $data = array(
                'sku'           => $sku,
                'type'          => 0,
                'path'          => $path,
                'user_id'       => 0,
                'create_time'   => date('Y-m-d H:i:s'),
                'update_time'   => date('Y-m-d H:i:s'),
            );
            $photoInfo = D('Photo')->where(array('sku' => $sku, 'type' => 0))->find();

            if (empty($photoInfo)) {
                if (!is_dir($path)) {
                    mkdir($path,0777,true);
                }
                if(D('Photo')->add($data)) {
                    $return[] = $sku;
                }
            }
        }

        return $return;
    }

    /**
     * 推送已完成（拍图）
     */
    public function pushFinishedList() {
        $photoModel = new PhotoModel();
        $skuList = $photoModel->where(array('type' => 0, 'status' => 2))->getField('sku', true);
        $result = curl_post(C('ERP_HTTP_URL').'/t.php?s=/Products/Api/pushFinishedPhotoList', array(
            'skuList' => json_encode($skuList)), 120);

        $result = json_decode($result, true);

        if ($result['data']) {
            foreach ($result['data'] as $sku) {
                $photoModel->where(array(
                    'type' => 0,
                    'status' => 2,
                    'sku' => $sku
                ))->save(array(
                    'status'        =>  3,
                    'update_time'   => date('Y-m-d H:i:s')
                ));
            }
        }

        return $result;
    }
}