<?php
class PublicAction extends Action
{
    // 系统写日志的函数
    public function writeLog($user, $log, $type = 1)
    {
        //img_log
        if (empty($user) || empty($log)) {return false;}
        $addt = time();
        $in   = "insert into img_log(log,`user`,`addtime`,`type`)values('$log','$user','$addt','$type');";
        $dd   = M("img_log");
        $dd->execute($in);
    }

    public function uploadToMeitu($filename, $path, $type = 'file')
    {
        if (strtolower(C("IMG_API")) == 'mftp') {
            $url  = 'http://www.mftp.info/upload.php';
            $data = array(
                'uploadimg' => '@' . realpath($path) . ";type=" . $type . ";filename=" . $filename,
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // curl_getinfo($ch);
            $json = curl_exec($ch);
            curl_close($ch);
            if (strstr($json, '外链地址') !== false) {
                $json = explode('外链地址 :', $json);
                $json = $json[1];
                $json = explode('<br', $json);
                $json = $json[0];
                $json = explode('value="', $json);
                $json = $json[1];
                $json = explode('"', $json);
                $url  = $json[0];
                return $url;
            } else {
                return false;
            }
        } elseif (strtolower(C("IMG_API")) == 'chuantu') {
            $url  = 'http://www.chuantu.biz/upload.php';
            $data = array(
                'uploadimg' => '@' . realpath($path) . ";type=" . $type . ";filename=" . $filename,
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // curl_getinfo($ch);
            $json = curl_exec($ch);
            curl_close($ch);
            //echo $json;die();
            if (strstr($json, '地址 :<input value="') !== false) {
                $json = explode('地址 :<input value="', $json);
                $json = $json[1];
                $json = explode('"', $json);
                $url  = $json[0];
                return $url;
            } else {
                return false;
            }

        } 
        // elseif (strtolower(C("IMG_API")) == 'meitu') {
        //     $url  = 'http://web.upload.meitu.com/image_upload.php';
        //     $data = array(
        //         'upload_file' => '@' . realpath($path) . ";type=" . $type . ";filename=" . $filename,
        //     );
        //     $ch = curl_init();
        //     curl_setopt($ch, CURLOPT_URL, $url);
        //     curl_setopt($ch, CURLOPT_POST, true);
        //     curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        //     curl_setopt($ch, CURLOPT_HEADER, false);
        //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //     // curl_getinfo($ch);
        //     $return_data = curl_exec($ch);
        //     curl_close($ch);
        //     $json = json_decode($return_data);
        //     if (isset($json->original_pic)) {
        //         $url = $json->original_pic;
        //         return $url;
        //     }
        //     return false;
        // }
         else {
            return $this->uploadToJeoshi($path);
        }

    }

    /**
     * 上传图片到jeoshi
     * @since  2018-01-08 11:48:55
     * @author Simon
     */
    public function uploadToJeoshi($file)
    {
        $content = file_get_contents($file);
        $ch      = curl_init();
        $post    = array();

//        curl_setopt($ch, CURLOPT_URL, 'http://www.cdn.com/Home/Index/imgCdn/');
        curl_setopt($ch, CURLOPT_URL, 'http://img.jeoshi.com/Home/Index/imgCdn');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $time);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $post['acc']  = 'JeoshiImgCdn';
        $post['pw']   = md5('JeoshiImgCdn');
        $post['Name'] = md5(time() . (mt_rand(0, 1000) * 0.0001)) . '.jpg';
        $post['File'] = $content;
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        $res = curl_exec($ch);
        curl_close($ch);
        $json = json_decode($res, true);
        return $json['Url'] ? str_replace(':80', '', $json['Url']) : false;
    }

    public function getALinkByPath($path)
    {
        $fname    = explode('/', $path);
        $fname    = $fname[count($fname) - 1];
        $basePath = dirname(dirname(dirname(__FILE__)));
        return $this->uploadToMeitu($fname, $basePath . $path);
        //R('Public/uploadToMeitu',array($fname,$basePath.$path));
    }

    // -1 已经评价过了
    // -10 图片不存在
    // -2 图片添加人 就是自己 不可以评论
    // -3 角色没有权限评论
    // -4 打分失败
    // 2 打分成功

    public function review($id, $sorce, $note)
    {
        $user = session('loginName');
        $dd   = M("img_review");
        $ss   = "select id from img_review where username='$user' and imgid='$id' limit 1";
        $ss   = $dd->query($ss);
        if (count($ss) == 1) {
            return -1; //您已经评价过了
        }

        $ss = "select adduser from images where id='$id' limit 1";
        $ss = $dd->query($ss);
        if (count($ss) == 0) {
            return -10; // 这个图片不存在
        }
        $adduser = $ss[0]['adduser'];

        if ($user == $adduser) {
            return -2; //这个图片添加人 就是自己 不可以评论
        }
        $accDB = M('ebay_user', null, 'DB_CONFIG2');
        $ss    = "select id from ebay_user where username='$user' and (truename 	like '%美工%' or truename like '%摄影%') limit 1";
        $rs    = $accDB->query($ss);

        if (count($rs) == 1) {
            return -3;
        }
        if (empty($note)) {
            $note = '';
        }
        $data['username'] = $user;
        $data['score']    = $sorce;
        $data['imgid']    = $id;
        $data['note']     = $note;
        $data['addtime']  = time();
        $rs               = $dd->add($data);
        //echo $dd->_sql();
        if ($rs) {
            return 2;
        }
        return -4; // 打分失败!

    }

    /*
    $expTitle     文件名
    $expCellName  excel 列名, 格式 array( array(dataindex,name),array(...),array(...)...);  dataindex $expTableData中的每一条数据的 索引
    $expTableData  数据  array(array(index1=>data1,index2=>data2,....),....) index?n 也就是 dataindex
     */
    public function exportExcel($expTitle, $expCellName, $expTableData, $foemat = array())
    {
        $xlsTitle = iconv('utf-8', 'gb2312', $expTitle); //文件名称
        $fileName = 'wiss-' . $expTitle . date('Y-m-d'); //or $xlsTitle 文件名称可根据自己情况设定
        $cellNum  = count($expCellName);
        $dataNum  = count($expTableData);
        vendor("PHPExcel.PHPExcel");
        $objPHPExcel = new PHPExcel();
        $cellName    = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ');
        for ($i = 0; $i < $cellNum; $i++) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i] . '1', $expCellName[$i][1]);
        }
        for ($i = 0; $i < $dataNum; $i++) {
            for ($j = 0; $j < $cellNum; $j++) {
                $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j] . ($i + 2), $expTableData[$i][$expCellName[$j][0]]);
            }
            $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setWidth(20);
            $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setWidth(30);
            $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setWidth(20);
            $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('F')->setWidth(10);
            $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('G')->setWidth(10);
            $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('H')->setWidth(80);
        }

        header('pragma:public');
        header('Content-type:application/vnd.ms-excel;charset=utf-8;name="' . $xlsTitle . '.xls"');
        header("Content-Disposition:attachment;filename=$fileName.xls"); //attachment新窗口打印inline本窗口打印
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    public function writeFile($file, $str)
    {
        $file  = dirname(dirname(dirname(__FILE__))) . '/log/' . $file;
        $index = strripos($file, '/');
        if (!file_exists($file) && strripos($file, '/') !== false) {
            $fileDir = substr($file, 0, $index);
            if (!file_exists($fileDir)) {
                mkdir($fileDir, 0777, true);
            }
        }
        file_put_contents($file, "\xEF\xBB\xBF" . $str, FILE_APPEND);
    }

}
