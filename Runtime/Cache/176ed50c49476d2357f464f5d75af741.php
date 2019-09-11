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
<div class="topmagins"></div>
<div id="form_submit_table" style="border: 1px solid #0070D8;padding:12px;">
<form class="pure-form" action="<?php echo U('System/imgconfig');?>" method="post">
    <div>
        1. 在这些图片类型中
        <div class="img_type_select">
            <ul>
        <?php
 foreach($Alltypes as $vvv){ $id=$vvv['id']; $typename=$vvv['typename']; if(in_array($id ,$imgtypes)){ $sed='checked="checked"'; }else{ $sed=''; } echo '<li><input class="checkedtype" '.$sed.' value="'.$id.'" type="checkbox" name="checkedtype[]"/>'.$typename.'</li>'; } ?>
            </ul>
        </div>
        当评论人数大于
        <input type="text" name="pingluncount" value="<?php echo ($data[0]['rev_count']); ?>" id="pingluncount"/> 并且平均得分低于等于
        <input type="text" name="danger_score" value="<?php echo ($data[0]['score']); ?>" id="danger_score"/> 时,需要重拍!
    </div>
<hr style="border:1px solid #999;margin:10px 0;">
    <input type="submit" name="submit" value="submit" class="pure-button pure-button-primary" onclick="return subchecke()">
</form>
    </div>
<script type="text/javascript" src="__PUBLIC__/js/jquery.js"></script>
<script type="text/javascript" src="__PUBLIC__/js/mytools.js"></script>
<script>
    funsTool=selectCheckbox;
    function subchecke(){
         var ctype='';
        $(".checkedtype").each(function(){
             var val=$(this).val();
             if($(this).prop("checked")){
                 ctype+=val;
             }
        });

        if(ctype==''){
            funsTool.showHTips('图片类型必须选择','#911',1200);
            return false;
        }

        var pingluncount= $.trim($("#pingluncount").val());


        if(pingluncount==''||isNaN(pingluncount)||parseInt(pingluncount)<=0){
            funsTool.showHTips('评论人数必须填写为正整数','#911',1400);
            return false;
        }

        var danger_score= $.trim($("#danger_score").val());


        if(danger_score==''||isNaN(danger_score)||parseFloat(danger_score)<=0){
            funsTool.showHTips('平均得分必须是小数或者整数','#911',1400);
            return false;
        }

        return true;
    }

    $(function(){
        <?php echo $result;?>
    });
</script>
</body>
</html>