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
<div class="topmagins" style="height:20px;"></div>
<div style="text-indent:2em;height:35px;border-bottom:2px solid #0071DA;">正在编辑 <b style="color:#C90000"><?php echo ($username); ?></b> 权限</div>
<?php echo ($saveRs); ?>
<div id="table_content" style="margin-top: 20px;">
    <form action="<?php echo U('System/modauserpower');?>&id=<?php echo ($userid); ?>" id="submyform" class="pure-form pure-form-aligned" method="post">
        <div class="power_box">
            <input type="button" value="提交" onclick="submypower()" class="pure-button pure-button-primary"/>
            <ul class="powersul">
                <li class="expart"><input <?php if(in_array('imgtypeset',$ypower)){echo 'checked="checked"';}?> type="checkbox" value="imgtypeset" name="power[]"/>图片类别设置</li>
                <li class="expart"><input <?php if(in_array('imgdelete',$ypower)){echo 'checked="checked"';}?> type="checkbox" value="imgdelete" name="power[]"/>删除图片</li>
                <li class="expart"><input <?php if(in_array('imgreload',$ypower)){echo 'checked="checked"';}?> type="checkbox" value="imgreload" name="power[]"/>重传图片</li>
                <li class="expart"><input <?php if(in_array('imgreplace',$ypower)){echo 'checked="checked"';}?> type="checkbox" value="imgreplace" name="power[]"/>替换图片</li>
                <li class="expart"><input <?php if(in_array('imgtable',$ypower)){echo 'checked="checked"';}?> type="checkbox" value="imgtable" name="power[]"/>报表</li>
                <li class="expart"><input <?php if(in_array('uploadimgs',$ypower)){echo 'checked="checked"';}?> type="checkbox" value="uploadimgs" name="power[]"/>上传图片到盘古</li>
                <li class="expart"><input <?php if(in_array('imgpower',$ypower)){echo 'checked="checked"';}?> type="checkbox" value="imgpower" name="power[]"/>权限设置</li>
                <li class="expart"><input <?php if(in_array('view_content',$ypower)){echo 'checked="checked"';}?> type="checkbox" value="view_content" name="power[]"/>查看评论内容</li>
                <li class="expart"><input <?php if(in_array('view_er',$ypower)){echo 'checked="checked"';}?> type="checkbox" value="view_er" name="power[]"/>查看评论人</li>
                <li class="expart"><input <?php if(in_array('imgconfig',$ypower)){echo 'checked="checked"';}?> type="checkbox" value="imgconfig" name="power[]"/>系统配置</li>
                <li class="expart"><input <?php if($super_review=='1'){echo 'checked="checked"';}?> type="checkbox" value="1" name="super_review"/>一星否决权(勾选此权限的人,如果给图片打出了1星,图片将不按照平均分处理,直接重拍)</li>
            </ul>
            每页显示图片:<input type="text" name="count" class="" value="<?php echo ($count); ?>" style="width:80px;"/><br>
            每日下载限制:<input type="text" name="download" class="" value="<?php echo ($download); ?>" style="width:80px;"/>
            <input type="hidden" value="sub" name="sub"/>
            <input type="hidden" value="<?php echo ($userid); ?>" name="id"/>
            <div class="clear"></div>
        </div>
    </form>
</div>
<script type="text/javascript" src="__PUBLIC__/js/jquery.js"></script>
<script type="text/javascript" src="__PUBLIC__/js/mytools.js"></script>
<script>
    function submypower(){
        $("#submyform").submit();
    }
</script>
</body>
</html>