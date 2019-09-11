<?php
class ApiAction extends Action{

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


    // 给其他文件访问的API接口
    public function searchsku(){
        //$sku=$this->_post('sku');
        //$count=(int)$this->_post('count');

        $sku = $_REQUEST['sku'];
        $count = $_REQUEST['count'];

        if($count>20){
            $count=20;
        }
        if($count<=0){
            $count=1;
        }
        $sku=trim($sku);
        $sku=preg_replace('/[^0-9A-Z\-]/','',$sku);
        $dd=M('images');
        $ss="select id,pic,path from images where (name like '$sku%' or sku like '$sku%') and status=1 order by id desc limit $count";
        $ss=$dd->query($ss);
        $rs=array();
        for($i=0;$i<count($ss);$i++){
            $rs[$i]['pic']=C('PIC_URL').'/pic/'.$ss[$i]['pic'];
            $rs[$i]['path']=C('PIC_URL').$ss[$i]['path'];
            $rs[$i]['id']=$ss[$i]['id'];
            $size=getimagesize(dirname(dirname(dirname(__FILE__))).'/'.$ss[$i]['path']);
            if(count($size)>0&&is_array($size)){
                $rs[$i]['wight']=$size[0];
                $rs[$i]['height']=$size[1];
            }else{
                $rs[$i]['wight']='600';
                $rs[$i]['height']='600';
            }

        }
        $data=array('data'=>$rs,'error'=>0);
        $str=json_encode($data);
        echo $str;
    }

    // 给速卖通刊登访问的API接口
    public function searchskuForSmt(){
        $sku=$this->_post('sku');
        $count=(int)$this->_post('count');
        if($count>20){
            $count=20;
        }
        if($count<=0){
            $count=1;
        }
        $sku=trim($sku);
        $sku=preg_replace('/[^0-9A-Z\-]/','',$sku);
        $dd=M('images');
        $ss="select id,pic,path from images where (name like '$sku%' or sku like '$sku%') and status=1 order by id desc limit $count";
        $ss=$dd->query($ss);
        $rs=array();
        for($i=0;$i<count($ss);$i++){
            $rs[$i]['pic']=C('PIC_URL').$ss[$i]['path'];
            $rs[$i]['id']=$ss[$i]['id'];
            $rs[$i]['wight']='800';
            $rs[$i]['height']='800';

        }
        $data=array('data'=>$rs,'error'=>0);
        $str=json_encode($data);
        echo $str;
    }

    public function getLinkByid(){
        $fileids=trim($_POST['idstr']);
        $fileids=preg_replace('/[^0-9,]/','',$fileids);
        $fileids=trim($fileids,',');
        $basePath=dirname(dirname(dirname(__FILE__)));
        $dd=M("images");
        $ss="select path,name,id from images where id in($fileids) limit 100";
        $ss=$dd->query($ss);
        $rs=array();
        foreach($ss as $vv){
            $path=$vv['path'];
            $fname=explode('/',$path);
            $fname=$fname[count($fname)-1];
            $url=R('Public/uploadToMeitu',array($fname,$basePath.$path));
            if($url!=false){
                $rs[]=$url;
            }else{
                $rs[]='';
            }
        }
        $data=array('data'=>$rs,'error'=>0);
        $str=json_encode($data);
        echo $str;
    }

    /**
    *测试人员谭 2017-09-5 11:26:39
    *说明: 看大图
    */
    public function getBase64Img(){
        import("@.Service.ImageThumpService");
        $dd=M("images");
        $key = $this->_post('id');
        $username = $this->_post('username');

        $file='viewimg/'.date('Ymd').'.txt';

        $log=$username.'--调用了大图【 $key 】---'.date('Y-m-d H:i:s')."\r\n";

        R("Public/writeFile",array($file,$log));

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
           // $name=$ss[0]['name'];
            $p=dirname(dirname(dirname(__FILE__))).$ss[0]['path'];
/*            $type=getimagesize($p);//取得图片的大小，类型等

            $w     = $type[0];
            $h     = $type[1];
            $types = $type[2];
            $attr  = $type[3];

            $ss=file_get_contents($p);

            if(!$ss){
                $data['msg']='图片不存在!或大图已经损坏';
                $data['error']=1;
            }else{
                $img_type='jpg';
                switch($type[2]){//判读图片类型
                    case 1:$img_type="gif";break;
                    case 2:$img_type="jpg";break;
                    case 3:$img_type="png";break;
                }
                $file_content=base64_encode($ss);//base64编码
                $img='data:image/'.$img_type.';base64,'.$file_content;//合成图片的base64编码
                $data['url']=$img;
            }*/
            //这里要压缩一下图片大小 START
            $imgService=new ImageThumpService();
            $imgService->setImgPath($p,'600*600');
            $data['url']=$imgService->showImage();
            //$img;
        }
        $str=json_encode($data);
        echo $str;
    }


}