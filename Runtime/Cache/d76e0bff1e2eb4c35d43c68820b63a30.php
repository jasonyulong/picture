<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <link rel="stylesheet" href="__PUBLIC__/css/pure-min.css">
    <link rel="stylesheet" href="__PUBLIC__/css/css.css">
    <link rel="stylesheet" href="__PUBLIC__/css/tools.css">
    <title>Picture</title>
</head>
<body>
    <?php
 $cpower=session('power'); ?>
    <div class="logininfobox">
        <div class="other_power">
            <div class="o_text">其他功能</div>
            <div id="top_menu">
                <div class="menu_arrow"></div>
                <div class="top_menu_content">
                    <div class="menu_ul">
                        <ul>
                            <li><a href="<?php echo U('Index/index');?>">首页</a></li>

                            <?php if(in_array('imgpower',$cpower)){ ?>
                            <li><a href="<?php echo U('System/power');?>">权限设置</a></li>
                            <?php } ?>

                            <?php if(in_array('imgtypeset',$cpower)){ ?>
                            <li><a href="<?php echo U('System/imgtype');?>">图片类型设置</a></li>
                            <?php } ?>

                             <li><a href="<?php echo U('Index/upload');?>">上传图片</a></li>

                            <?php if(in_array('imgtable',$cpower)){ ?>
                            <li><a href="<?php echo U('Table/index');?>">查看报表</a></li>
                            <?php } ?>

                            <li><a href="<?php echo U('Index/viewNeedReload');?>">需要重拍</a></li>

                            <li><a href="<?php echo U('Index/viewNeedReplace');?>">需要替换</a></li>

                            <?php if(in_array('imgconfig',$cpower)){ ?>
                            <li><a href="<?php echo U('System/imgconfig');?>">系统设置</a></li>
                            <?php } ?>

                        </ul>
                    </div>
                </div>
            </div>
        </div>



        <div class="indexsearchbox">
            <form class="pure-form" id="main_search_form" action="<?php echo U('Index/index');?>">
                <input type="text" value="<?php echo $key; ?>" name="pkeyword" class="pure-input-1-3" placeholder="请输入sku,多个用逗号或者空格隔开">
                <?php echo $indexhtml; ?>
                <input type="hidden" name="pageNow" id="pageNows" value=""/>
                <button type="submit" class="pure-button pure-button-primary">&nbsp;&nbsp;搜索&nbsp;&nbsp;</button>
            </form>
            <form id="downloadform" method="post" action="<?php echo U('Index/download');?>" target="_blank">
                <input type="hidden" id="idstr" name="idstr" value=""/>
            </form>
        </div>


        <div class="userlogininfo">
            <span id="userloginame"><?php echo session('loginName'); ?></span> <a href="<?php echo U('Login/logout');?>">退出!</a>
        </div>
        <div style="clear: both;"></div>
    </div>
    <div class="topmagins"></div>

<div style="font-family: '微软雅黑';font-weight:bold;font-size:20px;margin:10px;text-align: center;">
    批量上传
</div>
<div class="content">
    <form class="pure-form pure-form-stacked">
        <div class="drag" id="dragbox" ondrop = "dropFile(event)" ondragenter = "return bgChange(1)" ondragover = "return false">
            <div class="spn-img" id="spn-img"></div>
        </div>
        <div id="submitimgid">
            <?php echo ($htmls); ?>
            <?php echo ($html); ?>
            <?php  if($html==''){ ?>
                <h1 class="noimgtype">您无法上传图片!原因: 找不到授权给您的图片类型。请联系管理员</h1>
            <?php }else{ ?>
            <input type="button" id="submitimg" class="pure-button pure-button-primary" onclick="submitimgSend()" value="提交" />
            <?php } ?>
        </div>
    </form>
    <div style="font-family: '微软雅黑';font-weight:bold;font-size:16px;margin:6px;">
        <p>1,将图片用鼠标拖拽到虚线框中,数量不限。但需要考虑网速。</p>
        <p>2,等到缩略图全部显示之后,点击提交按钮!</p>
        <p>3,图片大小不能超过2M</p>
        <p>4,图片名称一定要包含SKU（允许的图片名:AG075A.jpg,AG075A (1).jpg,AG075A_800x800.jpg）</p>
    </div>
</div>
<script type="text/javascript" src="__PUBLIC__/js/jquery.js"></script>
<script type="text/javascript" src="__PUBLIC__/js/mytools.js"></script>
<script>
    var funsTool=selectCheckbox;
    var _MSG='';
    var _LEN=0;
    function bgChange(num){
        //console.log(num);
        if(num==2){
            $("#dragbox").css({"backgroundColor":"#fff"})
        }else{
            $("#dragbox").css({"backgroundColor":"#efefef"})
        }
        return false;
    }

    function submitimgSend(){
        var data=[];
        var namestr=[];
        var chaeckStr='';
        var STOP=false;
        var imgtype=$("#upload_img_type").val();
        var photoer=$("#upload_photoer").val();

        if(''==imgtype){
            alert("请选择图片类别!");
            return false;
        }

        if(photoer==''&&imgtype==99){
            alert("上传原图!摄影师不能为空!");
            return false;
        }

        $("#spn-img .boximg img").each(function(i){
            var filename=$(this).attr("title");
            var src=$(this).attr("src");
            if(src.length>2000000){
                STOP=true;
                alert("第"+(1+i)+"张图片的大小超过了限制!");
                return false;
            }

            chaeckStr+=filename+"*";
            namestr.push(filename);
            data.push(src);
        });
        if(STOP||data.length==0){
            return;
        }

        //========================== 同步
        var bool=true;
        var checkData='';
        $.ajax({
            type: "POST",
            url: "<?php echo U('Index/checkimgname');?>",
            data: "&str="+chaeckStr,
            async: false,
            success: function (msg) {
                msg = $.trim (msg);
                var arr=msg.split('@@@');
                if(arr[0]=='1'){
                    bool=false;
                    checkData=arr[1];
                }
            }
        });

        //========================== 同步  END
        funsTool.showModbox('提交图片',440,660,function(){});

        if(!bool){
            funsTool.insertModBox(checkData,true);
            return
        }

        //return ;
            // 循环 post
        var len=namestr.length;
        _LEN=len;

        for(var i=0;i<len;i++){
            var datai=data[i];
            var namestri=namestr[i];
            $.post(
                    '<?php echo U("Index/SaveImages");?>',
                    {"doaction":imgtype,"data":datai,"namestr":namestri,"photoer":photoer},
                    function(datas){
                        _MSG+=datas;
                        funsTool.insertModBox(_MSG,true);
                        _LEN--;
                        if(_LEN==0){
                            _MSG='';
                            $("#spn-img").html("");
                        }
                    }
            );
        }







    }


    var fileUploadPreview = function (aFile) {
        if (typeof FileReader == "undefined") {
            alert("浏览器不支持");return;
        }
        var i;
        for (i = 0; i < aFile.length; i++) {
            var tmp = aFile[i];
            var reader = new FileReader();//文件读取API
            reader.readAsDataURL(tmp);//将文件读取为url
            reader.onload = (function (f) {
                return function (e) {
                    var add ="<span class='boximg'><b class='delimg' onclick='delPrevEl(this);'>╳</b><img src=\""+e.target.result+"\" title=\""+f.name+"\"></span>";
                    $("#spn-img").append($(add));
                }
            })(tmp)
        }
    };

    var dropFile = function (e) {
        fileUploadPreview(e.dataTransfer.files);
        e.stopPropagation();
        e.preventDefault();
        bgChange(2);
    };

    function delPrevEl(that){
        $(that).parent(".boximg").remove();
    }

</script>

</body>
</html>