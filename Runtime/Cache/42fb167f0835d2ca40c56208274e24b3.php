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
<style>
    .pldiv{display: inline-block;width:50px;height:12px;line-height: 12px;font-size: 12px;padding-top:2px;position: absolute;left:120px;}
    #to_top{border-radius: 20px;width:30px; height:40px; padding:20px; font:14px/20px arial; text-align:center;  background:#b2d6ff; position:absolute; cursor:pointer; color:#fff}
</style>
    <div id="searchresult">
        <div>
            <input type="button" style="font-family: '微软雅黑';font-size:12px;" class="pure-button pure-button-active" dataid="1" onclick="checkAll(this)" value="全选"/>
            <input type="button" style="font-family: '微软雅黑';font-size:12px;" class="pure-button pure-button-active" dataid="0" onclick="Unselected()" value="反选"/>
            <input type="button" style="font-family: '微软雅黑';font-size:12px;" class="pure-button pure-button-active" onclick="downloadimag(this)" value="下载"/>
            <input type="button" style="font-family: '微软雅黑';font-size:12px;" class="pure-button pure-button-active" onclick="TOLink(this)" value="转为链接"/>
            <input type="button" style="font-family: '微软雅黑';font-size:12px;" class="pure-button pure-button-active" onclick="battchAddStar(this)" value="批量评分"/>
            <?php if(in_array('imgdelete',$cpower)){ ?>
            <input type="button" style="font-family: '微软雅黑';font-size:12px;" class="pure-button pure-button-active" onclick="deleteImg(this)" value="删除图片"/>
            <?php } ?>
            <?php if(in_array('imgreplace',$cpower)){ ?>
            <input type="button" style="font-family: '微软雅黑';font-size:12px;" class="pure-button pure-button-active" onclick="markImg(this)" value="标记替换"/>
            <?php } ?>
            <?php if(in_array('uploadimgs',$cpower)){ ?>
            <input type="button" style="font-family: '微软雅黑';font-size:12px;" class="pure-button pure-button-active" onclick="uploadtopg(this)" value="上传到盘古"/>
            <?php } ?>
            Page<select name="pageNow" class="sortClass">
            <option value="1" <?php if(isset($_GET['pageNow']) && $_GET['pageNow']=="1") echo 'selected'; ?>>1</option>
            <?php for($i=1;$i<$pageCount;$i++){ $pageCheck = (isset($_GET['pageNow']) && $_GET['pageNow']==$i+1) ? 'selected' : ''; echo '<option value="'.($i+1).'" '.$pageCheck.'>'.($i+1).'</option>'; } ?>
        </select>

            <span style="margin-left:10px;font-size:13px;">数量:<?php echo count($data); ?> &nbsp;&nbsp;Total:<?php echo ($totalcount); ?></span>
            &nbsp;&nbsp;
            <span style="color:#777;font-size:12px;">
            <?php
 if($reviewcount!==''){ echo '我本月评论：'.$reviewcount; } ?>
            </span>
            <!--<input type="checkbox" id="laxuan"/> 开启拉选图片-->

        </div>
        <?php foreach($data as $vvv){ $id=$vvv['id']; $pic='./pic/'.$vvv['pic']; $skuname=$vvv['name']; $sku=$vvv['sku']; $goods_name=$skuArr[$sku]; $type=$vvv['type']; $oldpath=$vvv['oldpath']; if($oldpath=='0'){ $oldpath=''; } $score=number_format($vvv['score'],1); $myscore=$scoreArr[$id][0]; $mynote=$scoreArr[$id][1]; $oldpath=$typenameArr[$type]; ?>
            <div class="picimageBox" dataid="<?php echo ($id); ?>" id="picimageBox_<?php echo ($id); ?>">
                <div class="printstartbox" dataid="<?php echo ($id); ?>">
                    <div class="printstartbox_son" data_score="<?php echo ($myscore); ?>">
                        <div class="star_<?php echo ($id); ?>_1 startbox <?php if($myscore>=1){echo 'hovers';} ?>" dataid="star_<?php echo ($id); ?>_1"></div>
                        <div class="star_<?php echo ($id); ?>_2 startbox <?php if($myscore>=2){echo 'hovers';} ?>" dataid="star_<?php echo ($id); ?>_2"></div>
                        <div class="star_<?php echo ($id); ?>_3 startbox <?php if($myscore>=3){echo 'hovers';} ?>" dataid="star_<?php echo ($id); ?>_3"></div>
                        <div class="star_<?php echo ($id); ?>_4 startbox <?php if($myscore>=4){echo 'hovers';} ?>" dataid="star_<?php echo ($id); ?>_4"></div>
                        <div class="star_<?php echo ($id); ?>_5 startbox <?php if($myscore>=5){echo 'hovers';} ?>" dataid="star_<?php echo ($id); ?>_5"></div>
                        <div style="clear: both"> </div>
                    </div>
                    <div class="pldiv"><a href="javascript:void(0)" onclick="viewReview(<?php echo ($id); ?>)">查看评价</a></div>
                    <div class="score_number" style="margin-left:0 !important;"><?php echo ($score); ?></div>
                    <div style="clear: both"> </div>
                </div>
                <img src="<?php echo $pic; ?>" class="minipic" id="minipic_<?php echo ($id); ?>" onclick="showBigImg(<?php echo ($id); ?>,this)"/>
                <div class="image_text">
                    <p class="image_name">
                        <input type="checkbox" class="imglist"  value="<?php echo $id; ?>" data="<?php echo $sku?>">
                        <span class="imgnames"><?php echo $skuname; ?>&nbsp;<i><?php echo $oldpath; ?></i></span>
                    </p>
                    <p class="image_ohter"><?php echo $goods_name; ?></p>
                    <p class="image_my_note"><?php echo $mynote; ?></p>
                </div>
            </div>

        <?php } ?>
        <div style="clear:both;"></div>
    </div>
<br /><br /><br /><br /><br /><div id="to_top">返回顶部</div>
<script type="text/javascript" src="__PUBLIC__/js/jquery.js"></script>
<script type="text/javascript" src="__PUBLIC__/My97DatePicker/WdatePicker.js"></script>
<script type="text/javascript" src="__PUBLIC__/js/mytools.js"></script>

<script type="text/javascript" src="__PUBLIC__/plugins/layer/layer.js"></script>
<script type="text/javascript">
    (function(){
        //funsTool.showBodyMask();
/*
        var Global=selectCheckbox;
        Global.inputClass('imglist');
        Global.addEventHandler(window,'mousedown',Global.startDrag);
        Global.addEventHandler(window,'mousemove',Global.getposition);
        Global.addEventHandler(window,'mouseup',Global.overDrag);


*/
        var oTop = document.getElementById("to_top");
        var screenw = document.documentElement.clientWidth || document.body.clientWidth;
        var screenh = document.documentElement.clientHeight || document.body.clientHeight;
        oTop.style.left = screenw - oTop.offsetWidth +"px";
        oTop.style.top = screenh - oTop.offsetHeight + "px";
        window.onscroll = function(){
            var scrolltop = document.documentElement.scrollTop || document.body.scrollTop;
            oTop.style.top = screenh - oTop.offsetHeight + scrolltop +"px";
        }
        oTop.onclick = function(){
            document.documentElement.scrollTop = document.body.scrollTop =0;
        }

    })();

    $(function(){
        //=================================================================================
        // 星星
        //=================================================================================
        $(".printstartbox_son .startbox").on("mouseover",function(){
            //console.info("ons");
            if(!handleScore($(this).parent("div"))){ return;}
            var id=$(this).attr("dataid");
            var arr=id.split('_');
            var indexid=arr[2];
            var idStr=arr[0]+'_'+arr[1]+'_';
            addAndRemoveStarClasss(idStr,indexid,1);
            //console.log(idindex);
        }).on("click",function(){

            if(!handleScore($(this).parent())){ return;}
            //alert("ss");
            var id=$(this).attr("dataid");
            var arr=id.split('_');
            var imgid=arr[1];
            var Star=arr[2];
            var notearea = $('.add_star_note');
            if(notearea.length == 0&&(Star == '1'||Star == '2'||Star == '3')){
                alert("评价低分请点击图片写明备注再评价!");return false;
            }

            if(!alertbox(Star)){
                return false;
            }
            //var note='';
            var note=$(this).parent().parent().parent().next().find('textarea.add_star_note').val();
            if(undefined!=note&&note!=''){
                if(note.length>100){
                    alert("备注不超过100字,当前"+note.length+"字");
                    return false;
                }
            }
            if(note == ''&&notearea.length == 1&&(Star == '1'||Star == '2'||Star == '3')){
                alert("评价低分请写明备注再评价!");return false;
            }
            $(this).parent().attr("data_score",Star);
            //开始打分
            //console.log('ffddd');
            $.post(
                    '<?php echo U("Index/addMyStar");?>',
                    {'id':imgid,'star':Star,'note':note},
                    function(data){
                        return addMyStarResult(data);
                    }
            );
            return false;
            //
            //selectCheckbox.showHTips(false,"搞什么飞机",1200);
        });

        $(".printstartbox_son").on("mouseout",function(){
            if(!handleScore(this)){ return;}
            var id=$(this).children("div.startbox").eq(0).attr("dataid");
            var arr=id.split('_');
            var idStr=arr[0]+'_'+arr[1]+'_';
            addAndRemoveStarClasss(idStr,0,2);
        });

        //=================================================================================
        // 星星
        //=================================================================================

        $("div.image_text").on("click",function(event){
            var event=event||window.event;
            if(event.target.tagName.toLocaleLowerCase()=='input'){
                return ;
            }
            var bool=!$(this).find("input").prop("checked");
            $(this).find("input").prop("checked",bool);
            viewCheckedImg($(this).find("input").val(),bool);
        });

        //选中的效果
        $("input.imglist").each(function(){
            //picimageBox_
            var id='';
            if($(this).prop("checked")){
                id=$(this).val();
                viewCheckedImg(id,true);
            }
        });

        // 其实一开始让我做分页我是拒绝的! 因为分页根本用不上
        $(".sortClass").change(function(){
            $("#pageNows").val($(this).val());
            $("#main_search_form").submit();
        });

    });

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

    function viewCheckedImg(id,type){
        if(!type){
            $("#picimageBox_"+id).removeClass("hovers");
        }else{
            $("#picimageBox_"+id).addClass("hovers");
        }
    }

    function changeHovers(that){
        var classname=$(that).attr("class");
        if(classname.indexOf("hovers")!=-1){
            $(that).removeClass("hovers");
        }else{
            $(that).addClass("hovers");
        }
    }

    function battchAddStar(){
        // 去掉拉选模式 否则就不好了
        //$("#laxuan").prop("checked",false);
        var str='';
        var html='';
        $("input.imglist").each(function(){
            if($(this).prop("checked")){
                var id=$(this).val();
                str+=','+id;
                html+="<div class='battch_picbox hovers' saveid='"+id+"' onclick='changeHovers(this)'><img class='battch_pic' src='"+$("#minipic_"+id).attr("src")+"'/></div>";
            }
        });

        if(str==''){
            alert("您没有选中!");
            return false;
        }
        html="<div class='selectStarbox'>"
        +"<form class='pure-form pure-form-stacked'>"
        +"<div class='pure-g'>"
        +"<div class='pure-u-1 pure-u-md-1-3' style='width:170px;'>"
        +"<select class='selectStar' class='pure-input-1-2'><option value=''>--请选择分数--</option>"
        +"<option value='1'>1 星</option>"
        +"<option value='2'>2 星</option>"
        +"<option value='3'>3 星</option>"
        +"<option value='4'>4 星</option>"
        +"<option value='5'>5 星</option></select></div>"
        +"<div class='pure-u-1 pure-u-md-1-3 pure-u-8-24' style='width:56%;margin-top:4px;'>"
        +'<textarea class="add_star_note" style="width:300px;height:34px;display:inline-block;" cols="1" rows="1" placeholder="请输入简短的备注" ></textarea>'
        +"</div>"
        +"<div class='pure-u-1 pure-u-md-1-3 pure-u-8-24' style='width:13%;margin-top:4px;'>"
        +"<input  class='pure-button pure-button-primary' type='button' onclick='submitBattchStar(this)' value='提交'/>"
        +"</div>"
        +"</div>"
        +"</form>"
        +"</div>"
        +html;

        selectCheckbox.showModbox('批量打分(点击可以取消选择)',400,600,function(){});
        selectCheckbox.insertModBox(html,1)

    }

    function submitBattchStar(that){
        var val=$(that).parent().parent().find(".selectStar").val();
        if(''==val){
            selectCheckbox.showTips(false,"您需要选择分数!",1100);
            return false;
        }
        var note=$("#ModboxContent .add_star_note").val();
        if(note.length>100){
            selectCheckbox.showTips(false,"备注不超过100字,当前"+note.length+"字",1100);
            return false;
        }
        if((val == '1'||val=='2'||val=='3')&&note == ''){
            selectCheckbox.showTips(false,"评价低分请写明原因！",1100);
            return false;
        }
        var id='';
        $("#ModboxContent .battch_picbox.hovers").each(function(){
            id+=$(this).attr("saveid")+",";
        });
        //console.log(id);
        if(id==''){
            selectCheckbox.showTips(false,"您需要选择图片!",1100);
            return false;
        }

        if(!alertbox(val,'ALL')){
            return false;
        }
        selectCheckbox.LoadingModBox();
        //post
        $.post(
                '<?php echo U("Index/BattchaddMyStar");?>',
                {'id':id,'star':val,'note':note},
                function(data){
                    selectCheckbox.insertModBox(data,1);
                }
        );
        return false;
    }

    function alertbox(star,type){
        var str='';
        switch(star){
            case '1':str='(T_T)';break;
            case '2':str='(=_=)';break;
            case '3':str='(-_-)';break;
            case '4':str='(^_^)';break;
            case '5':str='(^o^)';break;
        }
        var some=type==undefined?"张":"些";

        str = str + "您将给这" + some + "图评" + star + "分,不可修改,您确定么?";

        return confirm(str);
    }
    function addMyStarResult(data){
        if(data==-1){
            selectCheckbox.showTips(false,"您已经打过分了",1200); return;
        }
        if(data==-10){
            selectCheckbox.showTips(false,"图片不存在！",1200); return;
        }
        if(data==-3){
            selectCheckbox.showTips(false,"您的角色没有权限评论!",1200); return;
        }
        if(data==-2){
            selectCheckbox.showTips(false,"不能给自己的图片打分！",1200); return;
        }
        if(data==-4){
            selectCheckbox.showTips(false,"打分失败!DB 错误！",1200); return;
        }
        if(data==2){
            selectCheckbox.showTips(true,"打分成功!",800); return;
        }
        selectCheckbox.showTips(false,"打分失败:未知错误",1200);
    }

    function handleScore(that){
        if($(that).attr("data_score")==''){
            return true;
        }
        return false;
    }

    function addAndRemoveStarClasss(idstr,id,type){
        for(var i=1;i<=5;i++){
            $("."+idstr+i).removeClass("hovers");
            //console.log("#"+idstr+i);
        }

        if(type==2){ return;}
        for(i=0;i<=id;i++){
            $("."+idstr+i).addClass("hovers");
        }
    }

    function  checkAll(that){
        var bool=$(that).attr("dataid")==0?0:1;
        $("input.imglist").prop("checked",!!bool);
        $(that).attr("dataid",(1-bool));

        $("input.imglist").each(function(){
            var id=$(this).val();
            viewCheckedImg(id,!!bool);
        })
    }
    function Unselected(){
        $("input.imglist").each(function(){
            var bool=!$(this).prop("checked");
            $(this).prop("checked",bool);
            viewCheckedImg($(this).val(),bool);
        })
    }

    function downloadimag(){
        var str='';
        $("input.imglist").each(function(){
            if($(this).prop("checked")){
                str+=','+$(this).val();
            }
        });

        if(str==''){
            alert("您没有选中!");
            return false;
        }
        $("#idstr").val(str);
        $("#downloadform").submit();

    }

    function TOLink(){
        var str='';
        $("input.imglist").each(function(){
            if($(this).prop("checked")){
                str+=','+$(this).val();
            }
        });

        if(str==''){
            alert("您没有选中!");
            return false;
        }
        var url="<?php echo U('Index/ToLink');?>"+"&bill="+str;
        window.open(url,"_blank");
    }

    function showBigImg(id,that){

        ViewBigImgHandle.initBigImgHandle(id);
    }

    function deleteImg(that){
        var str='';
        var i=0;
        var names='';
        var ss = '';
        $("input.imglist").each(function(){
            if($(this).prop("checked")){
                var id=$(this).val();
                str+=','+id;
                ss+=$("#picimageBox_"+id+" span.imgnames").html();
                ss = ss.replace(/<[^>]+>/g,"");
                ss = ss.replace('&nbsp;',"");
                names+=ss+"\n";
                i++;
            }
        });

        if(str==''){
            alert("您没有选中!");
            return false;
        }

        if(!confirm("您确定删除以下图片? \n"+names)){
            return false;
        }

        selectCheckbox.showModbox('删除图片',400,600,function(){});
        $.post(
                '<?php echo U("Index/deleteImages");?>',
                {'str':str},
                function(data){
                    selectCheckbox.insertModBox(data,1);
                }
        );
    }

    function markImg(that){
        var str='';
        var i=0;
        var names='';
        var ss = '';
        $("input.imglist").each(function(){
            if($(this).prop("checked")){
                var id=$(this).val();
                str+=','+id;

                ss+=$("#picimageBox_"+id+" span.imgnames").html();
                ss = ss.replace(/<[^>]+>/g,"");
                ss = ss.replace('&nbsp;',"");
                names+=ss+"\n";
                i++;
            }
        });

        if(str==''){
            alert("您没有选中!");
            return false;
        }

        if(!confirm("您确定标记替换以下图片? \n"+names)){
            return false;
        }

        selectCheckbox.showModbox('标记替换图片',400,600,function(){});
        $.post(
                '<?php echo U("Index/markImages");?>',
                {'str':str},
                function(data){
                    selectCheckbox.insertModBox(data,1);
                }
        );
    }

    function uploadtopg(that){
        var names='';
        $("div.hovers").each(function(){
            names += $(this).attr('dataid')+',';
        });
        if(names==''){
            alert("您没有选中!");
            return false;
        }
        var sku = $("div.hovers input" ).eq(0).attr('data');

//        var sku = $('input' ).attr('name');
        if(!confirm("您确定上传所选图片? ")){
            return false;
        }

        // layer 弹出输入框
        // 默认值为sku

        layer.prompt({
            formType: 3,
            value: sku,
            title: '请输入自定义文件夹名'
        }, function(value, index){
            if(!value || value == '') {
                layer.msg( '请输入正确的值！' );
                return false;
            }else{
                layer.close(index);
                selectCheckbox.showModbox('上传图片',400,600,function(){});
                $.post(
                        '<?php echo U("Pangu/uploadimgs");?>',
                        {'str':names,'upname':value},
                        function(data){
                            selectCheckbox.insertModBox(data,1);
                        }
                );
            }

        });

    }

</script>
</body>
</html>