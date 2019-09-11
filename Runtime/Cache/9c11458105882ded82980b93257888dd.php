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
<div id="searchresult">
    <style>
        table.pic_data_table tr:hover td{background:#F0EFEE ;}
        .crltd:hover{cursor: pointer;background: #0078e7 !important;}
    </style>
    <div>
        <form id="reupload_search_form" method="get" class="pure-form" action="<?php echo U('Index/viewNeedReload');?>">
            <input class="pure-input-1-3" type="text" placeholder="请输入sku,多个用逗号或者空格隔开" name="pkeyword" value="<?php echo ($key); ?>">
            <select name="uploader" id="uploader">
                <option value="">选择上传人</option>
                <?php foreach($uploaduser as $vvv){ if($vvv==$thisaddusr){ echo '<option selected="selected" value="'.$vvv.'">'.$vvv.'</option>'; }else{ echo '<option value="'.$vvv.'">'.$vvv.'</option>'; } } ?>
            </select>
            <select name="pstatus">
                <option value="0" <?php if($thisstaus == '0'){ echo "selected"; } ?> >--选择状态--</option>
                <option value="1" <?php if($thisstaus == '1'){ echo "selected"; } ?> >等待重新上传</option>
                <option value="2" <?php if($thisstaus == '2'){ echo "selected"; } ?> >重新上传完成</option>
            </select>
            <input id="pageNowreload" type="hidden" value="" name="pageNow">
            Page<select name="" class="sortClass">
            <option value="1" <?php if(isset($_GET['pageNow']) && $_GET['pageNow']=="1") echo 'selected'; ?>>1</option>
            <?php for($i=1;$i<$pageCount;$i++){ $pageCheck = (isset($_GET['pageNow']) && $_GET['pageNow']==$i+1) ? 'selected' : ''; echo '<option value="'.($i+1).'" '.$pageCheck.'>'.($i+1).'</option>'; } ?>
        </select>
            <input type="hidden" name="s" value="/Index/viewNeedReload"/>
            <input class="pure-button pure-button-primary" onclick="return true;" value="搜索" type="submit"/>
        </form>
        <div style="background:#ccc;margin: 4px;padding:4px;font-size: 12px;">
            本页：<?php echo count($data); ?>&nbsp;&nbsp; Total:<?php echo $totalcount;?>
        </div>
    </div>
    <div id="table_content">
        <table class="pure-table pure-table-bordered pic_data_table" width="90%">
            <thead>
            <tr>
                <td>编号</td>
                <td>SKU</td>
                <td>上传人员</td>
                <td>状态</td>
                <td>原图</td>
                <td>效果图</td>
                <td>总计</td>
            </tr>
            </thead>
            <tbody>
            <?php $ii=1; foreach($data as $dv){ $count = $dv['count']; $dsku = $dv['sku']; $dadd = $dv['adduser']; $dstuscn= $dv['statusCn']; $dstus = $dv['status']; $dyt = $dv['ytcount']; $xgt = $dv['xgcount']; echo '<tr><td>'.$ii.'</td>'; echo '<td><a href="javascript:void(0);" datausr="'.$dadd.'" datarel="'.$dstus.'" datasku="'.$dsku.'" onclick="getimgsinfo(this)">'.$dsku.'</a></td>'; echo '<td>'.$dadd.'</td>'; echo '<td>'.$dstuscn.'</td>'; echo '<td class="crltd" rel="99" rem="'.$dyt.'" datausr="'.$dadd.'" datasku="'.$dsku.'" onclick="toplreupload(this,'.$dstus.');">'.$dyt.'</td>'; echo '<td class="crltd" rel="100" rem="'.$xgt.'" datausr="'.$dadd.'" datasku="'.$dsku.'" onclick="toplreupload(this,'.$dstus.');">'.$xgt.'</td>'; echo '<td>'.$count.'</td>'; echo '</tr>'; $ii++; } ?>
            </tbody>
        </table>
    </div>
    <div style="clear: both"> </div>
    <input type="hidden" id="hideurl" value="__APP__/Index/plreuploadimgs"/>
    <input type="hidden" id="hiddenurl" value="__APP__/Index/showReuploadImgsInfo"/>
</div>
<script type="text/javascript" src="__PUBLIC__/js/jquery.js"></script>
<script type="text/javascript" src="__PUBLIC__/My97DatePicker/WdatePicker.js"></script>
<script type="text/javascript" src="__PUBLIC__/js/mytools.js"></script>
<script type="text/javascript">
    $(function(){
        // 其实一开始让我做分页我是拒绝的! 因为分页根本用不上
        $(".sortClass").change(function(){
            $("#pageNowreload").val($(this).val());
            $("#reupload_search_form").submit();
        });

        $("#main_search_form input,#main_search_form button").prop("disabled",true);
    });
    function showBigImg(id){
        ViewBigImgHandle.initBigImgHandle(id);
    }

    function getimgsinfo(that){
        var addusr = $.trim($(that).attr('datausr'));
        var sku    = $.trim($(that).attr('datasku'));
        var type   = $.trim($(that).attr('datarel'));
        var url = $('#hiddenurl').val();
        url += '/'+addusr+'/'+sku+'/'+type;
        window.open(url,'_blank');
    }

    function toplreupload(that,status){
        var url = $('#hideurl').val();
        var type= $.trim($(that).attr('rel'));
        var num = $.trim($(that).attr('rem'));
        var addusr = $.trim($(that).attr('datausr'));
        var sku    = $.trim($(that).attr('datasku'));
        if(status == 3){
            alert("当前SKU已经重传完成，请不要重复操作！");return false;
        }
        if(num == '0'){alert('当前没有图片需要重传！');return false;}
        url += '/'+type+'/'+addusr+'/'+sku;
        window.open(url,'_blank');
    }

    function viewReview(id){
        var num = 0;
        num = $('.review_info_box').length;
        if(num != 0){alert("已获取全部评论！");return false;}
        var bigwin=$("#bodyWindows").css('display');
        if(undefined==bigwin||''==bigwin){
            ViewBigImgHandle.initBigImgHandle(id);
        }
        var h1=$("#bodyWindows").height();
        var h2=$("#bodyWindows .viewImg_box").height();

        var H=h1-h2-110;
        console.log(h1,',',h2,',',H);
        $.post(
                '<?php echo U("Index/getimgreview");?>',
                {'id':id,'H':H},
                function(data){
                    $("#bodyWindows .viewImg_box").append($(data));
                }
        );

    }

    $(document).on('change', '.titlePics', function(){
        var id = $.trim($(this).attr('rel'));
        if(id == ''){alert("id丢失!");return false;}
        $('#form_'+id).submit();
    });

</script>
</body>
</html>