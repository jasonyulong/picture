<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/15
 * Time: 11:57
 */

class ImageThumpService{

    CONST IMG_TYPE_SEP = '|';		#图片分隔标志

    CONST IMG_PATH = '/images/';	#图片默认路径
    CONST IMG_TYPE = 'jpg';			#图片默认类型
    CONST IMG_SIZE = '800*800';		#图片默认大小
    CONST IMG_QUA = 5;				#默认图片质量

    private $toSize;
    private $imgPath;				#图片地址
    private $imgInfo;				#图片信息
    private $percent;				#图片压缩比例
    private $imgRs;					#图片资源

    private $types;
    /**
     * 设置图片地址
     * @param 	string 	$imgPath
     * @author 	Rex
     * @since 	2017-09-08
     */
    public function setImgPath($imgPath,$imgSize) {
        $this->imgPath = $imgPath;
        $this->toSize = explode('*', $imgSize);
    }

    /**
     * 显示图片
     * @author 	Rex
     * @since 	2017-09-08
     */
    public function showImage() {
        $this->openImage();
        $this->thumpImage();
        return $this->showImageH();
    }

    private function showImageH() {
        $func = 'image'.$this->imgInfo['type'];
        ob_end_clean();
        ob_start();
        $func($this->imgRs);
        $ff=ob_get_contents();
        ob_end_clean();

        $img_type='jpg';
        switch($this->types[2]){//判读图片类型
            case 1:$img_type="gif";break;
            case 2:$img_type="jpg";break;
            case 3:$img_type="png";break;
        }
        $file_content=base64_encode($ff);//base64编码
        $imgbase64='data:image/'.$img_type.';base64,'.$file_content;//合成图片的base64编码
        // echo $imgbase64;
        //echo '<img src="'.$imgbase64.'"/>';
        return $imgbase64;
    }

    // 按照比例压缩图片
    private function thumpImage(){
        $newW = $this->imgInfo['width'] * $this->percent;
        $newH = $this->imgInfo['height'] * $this->percent;
        $imgThump = imagecreatetruecolor($newW, $newH);
        imagecopyresampled($imgThump, $this->imgRs, 0, 0, 0, 0, $newW, $newH, $this->imgInfo['width'], $this->imgInfo['height']);
        imagedestroy($this->imgRs);
        $this->imgRs = $imgThump;
    }

    /**
     * 打开图片
     * @author 	Rex
     * @since 	2017-09-08
     */
    private function openImage() {
        list($w,$h,$type,$attr) = getimagesize($this->imgPath);
        $this->imgInfo = array(
            'width'		=> $w,
            'height'	=> $h,
            'type'		=> image_type_to_extension($type,false),
            'attr'		=> $attr
        );

        $this->types=$type;

        $this->percent = $this->toSize[0] / $this->imgInfo['width'];
        $func = 'imagecreatefrom'.$this->imgInfo['type'];
        $this->imgRs = $func($this->imgPath);
    }

    public function __destruct() {
        @imagedestroy($this->imgRs);
    }
}

//$img=new ImageThumpService();
//$img->setImgPath('C:/Koala.jpg','600*600');
//$img->showImage();