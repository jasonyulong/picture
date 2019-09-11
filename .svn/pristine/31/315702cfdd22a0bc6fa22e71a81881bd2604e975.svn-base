
Array.prototype.FindVal=function(val){
    var len=this.length;
    for(var i=0;i<len;i++){
        if(this[i]==val){
            return i;
        }
    }
    return -1;
};

selectCheckbox=(function(){
    var isDrag=false;
    var Drag=false;
    var inputClassname='';
    var scrollbar=null;
    var obj=false;
    var No=false;
    var creatMask=function(){
        var mask='';
        if(!$("#bodyMask").css('display')){
            var str="<div id='bodyMask' style=\'display:none;height: 100%;width:100%;background:#000;opacity:.4;position: absolute;z-index:100;top:0;left:0;\'></div>";
            $("body").append($(str));
        }
        mask=$("#bodyMask");
        mask.height($(document).height()).width($(document).width());
        return mask;
    };

    var hideBodyMask=function(){
        //$("html").css('overflow','scroll');
        $("#bodyMask").hide();
        $("html").css({overflowY:'auto'})
    };

    var showBodyMask=function(){
        var mask=creatMask();
        mask.show();
        $("html").css({overflow:'hidden'})
    };

    //自动隐藏顶部的
    var showTips = function (bool, strs, mytimes) {
        var mytimes = mytimes || 1000;
        if ($ ("#mytips_show_hide_for_action").length > 0) {
            $ ("#mytips_show_hide_for_action h1").html (strs);
        } else {
            var str = "<div id='mytips_show_hide_for_action' style='position:fixed;top:-42px;left:50%;z-index:9999;background:#999;height: 40px;width:200px;border-radius: 0 0 5px 5px;margin-left: -100px;margin-top:0;'>";
            str += "<h1 style='font-size:13px;background:#fff;color:#393;border-radius: 0 0 5px 5px;position:absolute;margin-left:5px;margin-top:0;width:190px;max-width:190px;line-height:35px;text-align: center;height:35px;'>" + strs + "</h1>";
            str += "</div>";
            $ ("body").prepend (str);
        }
        var O = $ ("#mytips_show_hide_for_action");
        if (! bool) {
            $ ("#mytips_show_hide_for_action h1").css ("color", "#f00");
        } else {
            $ ("#mytips_show_hide_for_action h1").css ("color", "#393");
        }

        O.show ();
        O.animate ({"top": "0px"}, '800', function () {
            setTimeout (function () {
                $ ("#mytips_show_hide_for_action").animate ({"top": "-42px"}, '1000', function () {
                    $ ("#mytips_show_hide_for_action").hide ();
                });
            }, mytimes);
        });
    };

    //自动隐藏中间的
    var showHTips = function (str, color, times) {
        var times = times || 1800;
        creatshowTips ( str,color);
        $ ("#Tan_show_hide_tips").fadeIn ();
        setTimeout (function () {
            $ ("#Tan_show_hide_tips").fadeOut ();
        }, times);
    };

    var creatshowTips = function (str, color) {
        var h = 22, w = 600;
        var color = color ? color : "#606060";
        if (! $ ("#Tan_show_hide_tips").css ("display")) {  //不存在
            var divs = "<div id='Tan_show_hide_tips' style=\'display:none;line-height: " + h + "px;color:" + color + ";height: " + h + "px;width:" + w + "px;margin-left:-" + w / 2 + "px;margin-top:-" + h / 2 + "px'\'>" + str + "</div>"
            $ ("body").append ($ (divs));
        } else {
            $ ("#Tan_show_hide_tips").css ("color", color).html (str);
        }
    };

    var creatModbox=function(title,h,w){
        if(!$("#showModbox").css('display')){//不存在
            var str="<div id='showModbox' style='display:none;height:"+h+"px;width:"+w+"px;position:fixed;z-index:200;top:50%;left:50%;";
            str+="background:#fff;border:2px solid #999988;margin-left:-"+w/2+"px;margin-top:-"+h/2+"px'>";
            str+="<div class='Modboxtitlebox'><span class='Modboxtitle'>"+title+"</span><span class='btn_close' onclick='selectCheckbox.deleteModBox()'></span><div class='clear'></div></div>";
            str+="<div class='ModboxContent'>" +
            "<div id='ModboxContent' style='height:"+(h-30)+"px'><img class='loading' style='margin-top:"+(h-80)/2+"px;width:40px;height:40px;' src='./Public/img/1461729891255.gif'/></div>"
            "</div>";
            str+="</div>";
            $("body").append($(str));
        }
        return $("#showModbox");
    };

    var LoadingModBox=function(){
        var h=$("#ModboxContent").height();
        $("#ModboxContent").html("<img class='loading' style='margin-top:"+(h-80)/2+"px;width:40px;height:40px;' src='./Public/img/1461729891255.gif'/>");
    };

    var deleteModBox=function(){
        $("#ModboxContent").html("<img class='loading' src='./Public/img/1461729891255.gif'/>");
        $("#showModbox").remove();
        hideBodyMask();

    };
    var insertModBox=function(data,margin){
        margin=margin||false;
        if(margin){
            data='<div style="margin:15px;">'+data+'</div>'
        }
        $("#ModboxContent").html(data);
    };
    var showModbox=function(title,height,wight,callback){
        var box=creatModbox(title,height,wight);
        showBodyMask();
        box.show();
        callback();
    };







    var initObj=function(){
        if(inputClassname==''){
            var inputSave=$("input[type='checkbox']");
        }else{
            var inputSave=$("input[type='checkbox']."+inputClassname);
        }
        obj=inputSave;
    };

    var getposition=function(event){
        var event=event||window.event;
        if(!No){return ;}
        if(Drag){
            //console.log(event.clientX,event.clientY);
            if(Drag.length>30){
                isDrag=true;//拖动达到30个位置的时候方才判定为开始拖拽
                showBodyMask();
                if(obj===false){
                    initObj();
                }
                Drag[30][0]=event.clientX;
                Drag[30][1]=event.clientY;
            }else{
                Drag.push([event.clientX,event.clientY]);
            }
        }

        if(isDrag){
            var startX=Drag[0][0];
            var startY=Drag[0][1];
            var endX=Drag[Drag.length-1][0];
            var endY=Drag[Drag.length-1][1];

            creatSelectMask(startX,startY,endX,endY,'');//只是一个效果
            //实际的功能
            checkedInputinMask(startX,startY,endX,endY);

        }

    };

    var startDrag=function(){
        Drag=[];
        scrollbar=$(document).scrollTop();
        No=$("#laxuan").prop("checked");
        //console.log(scrollbar);
    };

    var creatSelectMask=function(x,y,xx,yy,point){
        var mask='';
        //console.log(x,y,xx,yy);
        if(!$("#checkboxSelectMask").css('display')){
            var str="<div id='checkboxSelectMask' style=\'height: 100%;width:100%;background:#003300;opacity:.3;position: absolute;z-index:1000;\'></div>";
            $("body").append($(str));
        }
        $("#checkboxSelectMask").show();
        mask=$("#checkboxSelectMask");

        var w=Math.abs(x-xx);
        var h=Math.abs(yy-y);
        var ofx=0;
        var ofy=0;
        if(x>=xx){ //x 向原点移动
            ofx=xx;
        }else{
            ofx=x;
        }

        if(y>=yy){
            ofy=yy;
        }else{
            ofy=y;
        }

        mask.height(h).width(w).offset({top:ofy+scrollbar,left:ofx});
    };

    var checkedInputinMask=function(x,y,xx,yy){
        var x1,y1,xx1,yy1; // 确定谁大谁小
        x1=x>xx?xx:x;
        xx1=x>xx?x:xx;
        y1=y>yy?yy:y;
        yy1=y>yy?y:yy;
        //console.log(obj);
        obj.each(function(){
            var l=$(this).offset().left;
            var t=$(this).offset().top-scrollbar;
            if(l>x1&&l<xx1&&t>y1&&t<yy1){
               // console.log(l,',',x1,',',l,',',xx1,',',t,',',y1,',',t,',',yy1);
                $(this).prop("checked",true);
            }
        });
    };

    var hideSelectMask=function(){
        $("#checkboxSelectMask").hide();
    };

    var fuckOver=function(){
        Drag=false;
        isDrag=false;
        obj=false;
        if(No){
            hideBodyMask();
            hideSelectMask();
        }
    };

    var inputClass=function(str){
        inputClassname=str;
    };
    //事件注册
    var addEventHandler= function(el, evType, fnHandler) {
        if (el.addEventListener) {
            el.addEventListener(evType, fnHandler, false);
        } else if (el.attachEvent) {//ie
            el.attachEvent("on" + evType, fnHandler);
        } else {
            el["on" + evType] = fnHandler;
        }
    };
    //移除事件
    var removeEventHandler=function(el, evType, fnHandler) {
        if (el.removeEventListener) {
            el.removeEventListener(evType, fnHandler, false);
        } else if (el.detachEvent) {
            el.detachEvent("on" + evType, fnHandler);
        } else {
            el["on" + evType] = null;
        }
    };

    var Bind=function(object, fun){
        return function(event) {
            return fun.call(object, (event || window.event));
        }
    };

    var Binds=function(object, fun) {
        return function() {
            return fun.apply(object, arguments);
        }
    };

    return {
        showBodyMask: showBodyMask,
        hideBodyMask: hideBodyMask,
        addEventHandler: addEventHandler,
        removeEventHandler: removeEventHandler,
        Bind: Bind,
        Binds: Binds,
        getposition: getposition,
        startDrag: startDrag,
        overDrag: fuckOver,
        inputClass: inputClass,
        showHTips: showHTips,
        showTips: showTips,
        deleteModBox:deleteModBox,
        showModbox:showModbox,
        insertModBox:insertModBox,
        LoadingModBox:LoadingModBox
    }
})();


ViewBigImgHandle=(function(){
    var prevID=null;// 上一张
    var nextID=null;// 下一张
    var thisid=null;// 这一张
    var queueID=[]; // 浏览数组
    var divClass='picimageBox';
    var win_w=0;
    var win_h=0;
    var win_left_w=0;
    var win_right_w=0;
    var win_marginleft=0;
    var picArr='';


    var initBigImgHandle=function(id){
         $("."+divClass).each(function(){
             var sid=$(this).attr("dataid");
             if(undefined!=sid){
                 queueID.push(sid);
             }
         });
         resetDate(id,false);
        // 本页的ID 队列搞定

        // 窗口尺寸数据
        var windowW=$(window).width();
        var windowH=$(window).height();

        win_h= windowH;
        win_w=parseInt(windowW*0.9);
        win_left_w=parseInt((win_w-100)*0.7);
        win_right_w=(win_w-100)*0.3;
        win_marginleft=(windowW-win_w)/2;


        selectCheckbox.showBodyMask();   //console.log(win_h,',',win_w,',',win_left_w);
       // alert($(window).height());
        creatwin_html();
        setImginfo(thisid);
        getBigimg(thisid);
    };

    var getminiPicHtml=function(id){
        var picArrs="";
        var clhover='';
        var sid=0;
        var marginleft=0;
        var thisindex=0;
        for(var i=0;i<queueID.length;i++){
            sid=queueID[i];
            if(sid==id){
                clhover='hoverthis';
                thisindex=i;
            }else{
                clhover='';
            }
            picArrs+="<a href='javascript:void(0)' id='minipicscroll_"+sid+"' class='"+clhover+"' onclick='ViewBigImgHandle.getthisbigpic(this)' dataid='"+sid+"'><img src='"+$("#minipic_"+sid).attr("src")+"'/></a>"
        }
        var longwight=54*(i+1);
        marginleft=-54*thisindex;
        var temp="<div class='minipiclongbox' style='width:"+longwight+"px;margin-left:"+marginleft+"px'>"+picArrs+"</div>";
        temp="<div style='width:"+(win_left_w-66)+"px;' class='minipicshortbox'>"+temp+"</div>";
        var leftlink="<div class='scroolpicleft' onclick='ViewBigImgHandle.minipicscroll(-1)'><a href='javascript:void(0)'></a></div>";
        var rightlink="<div class='scroolpicright' onclick='ViewBigImgHandle.minipicscroll(1)'><a href='javascript:void(0)'></a></div>";
        temp=leftlink+temp+rightlink;
        return temp;
    };


    var minipicscroll=function(type){
        var long=queueID.length*54;
        var mleftnow=getminipicLongboxMleft();
        var windowsWidth=parseInt((win_left_w-66)/54)*54;
        var setMleft=0;
        //console.log(mleftnow);
        if(type==-1){// 如果是向左边
            if(mleftnow>=windowsWidth){// 左边不够一屏的话
                setMleft=mleftnow-windowsWidth;
            }

        }else{
            setMleft=long-windowsWidth;
            if(long-mleftnow-2*windowsWidth>0){
                setMleft=mleftnow+windowsWidth;
            }
        }
        setminipicLongboxMleft(setMleft,mleftnow);
    };

    var getminipicLongboxMleft=function(){
        var mleftnow=$("#bodyWindows .minipiclongbox").css("marginLeft");
        mleftnow=parseInt(mleftnow.replace('px',''))*-1;
        return mleftnow;
    };

    var setminipicLongboxMleft=function(mleft,mleftnow){
        mleft=(-1*mleft)+"px";
        var speed=300;
        var cha=Math.abs(mleft-mleftnow)/(win_left_w-66);
        if(cha<0.8){
            speed=parseInt(speed*300);
        }
        $("#bodyWindows .minipiclongbox").animate({"marginLeft":mleft},speed);
    };

/*    var minipicscrollright=function(){

    };*/

    var getBigimg=function(id){
        var imghtml='';
        // 更新 上一个，下一个，这一个

        $.post(
            'index.php?s=/Index/getbase64Img',
            {"id":id},
            function(data){
                data=JSON.parse(data);
                if(undefined==data.error||data.error=='1'){// 失败
                    setThisbigImgError(data);
                }else{
                    var url=data.url;
                    setThisbigImg(url);
                }
            }
        );
        return imghtml;
    };

    var setThisbigImg=function(url){
        var img=new Image();
        //alert(url);
        img.src=url;
        img.onload=function(){
            showImgbox(this);
        };
        img.onerror=function(){
            var a={"msg":'载入出错!!!'};
            setThisbigImgError(a);
        }
    };

    var showImgbox=function(that){
        if($("#showMycheckImgbox").css('display')){
            var src=$("#showMycheckImgbox img").attr("src");//正在显示的图片
            $("#showMycheckImgbox b").unbind();
            $("#showMycheckImgbox").remove();
            if(src==that.src){
                return;
            }
        }
        var h=that.height,w=that.width;
        var maxh=(win_h-100);
        var maxw=win_left_w;

        if(w>maxw){h=maxw*h/w;w=maxw;}

        if(h>maxh){w=maxh*w/h;h=maxh;}
        var ban=(maxw-w)/2;
        var html= "<img src='" + that.src + "' style='margin-left:"+ban+"px;height:" + h + "px;width:" + w + "px'/>";
        $("#bodyWindows .bodyWindows_img").html(html);
        //alert(that.height+','+that.width);
    };

    var setThisbigImgError=function(data){
        if(undefined==data.msg){
            var msg='未知的错误!';
        }else{
            var msg=data.msg;
        }
        //bodyWindows_img
        var html="<div class='errorbox'>"+msg+"</div>";

        $("#bodyWindows .bodyWindows_img").html(html);
    };

    var setImginfo=function(id){
        var evid="#"+divClass+"_"+id;
         var tname=$(evid+" .image_text .image_name").text();
         var somelog=$(evid+" .image_text p.image_ohter").html();
         var note=$(evid+" .image_text p.image_my_note").html();
//        console.log(tname);
//        console.log(somelog);
//        console.log(evid);

        $("#bodyWindows p.viewImg_name").html(tname);
         $("#bodyWindows p.viewImg_long").html(somelog);
         $("#bodyWindows div.viewImg_note textarea.add_star_note").val(note);
         $("#bodyWindows div.viewImg_start").html($(evid+" .printstartbox").clone(true));

    };

    var creatwin_html=function(){
        //var mask='';
        if(!$("#bodyWindows").css('display')){

            var pnhtml=getPrevAndNextLink();
            var loadimg=getLoadingImg();
            var miniPicHtml=getminiPicHtml(thisid);

            var str="<div id='bodyWindows'>"
                +"<div style='float:left;background:#000;width:"+win_left_w+"px;margin:10px 0 0 50px;height:"+(win_h-20)+"px;' class='left'>"
                +"<div class='bodyWindows_img' style='height:"+(win_h-100)+"px;'>"
                +loadimg
                +"</div>"
                +"<div class='number_index'>"+(queueID.FindVal(thisid)+1)+"/"+queueID.length+"</div>"
                +"<div class='minipicbox'>"+miniPicHtml+"</div>"
                +"</div>"
                +pnhtml
                +"<div class='right' style='float:left;background:#f5f5f5;width:"+win_right_w+"px;margin:10px 0 0 0;height:"+(win_h-20)+"px;'>"
                +"<div onclick='ViewBigImgHandle.closeWindaow()' class='closebox' style='margin-left:"+(win_right_w-22)+"px;'>×</div>"
                +"<div class='viewImg_box'>"
                +"<p class='viewImg_name'></p>"
                +"<p class='viewImg_long'></p>"
                +"<div class='viewImg_start' dataid=''>分数，星星</div>"
                +"<div class='viewImg_note'><textarea class='add_star_note'  placeholder='请输入简短的备注' rows='3' cols='4' style='width:"+(win_right_w-48)+"px;'></textarea></div>"
                +"</div>"
                +"</div>"
                +"<div style='clear: both'></div>"
                +"</div>";
            $("body").append($(str));
        }
        mask=$("#bodyWindows");
        mask.height(win_h).width(win_w).css("marginLeft",win_marginleft+"px");
        mask.show();
        //return mask;
    };

    var creatWindows=function(){
        // windows 窗体 分为 一个大黑窗，左边是黑色的图片区域

    };




    var closeWindaow=function(){
        prevID=null;
        nextID=null;
        thisid=null;
        queueID=[];
        $("#bodyWindows").remove();
        selectCheckbox.hideBodyMask();
    };




    var getPrevAndNextLink=function(){
        var righthtml="<div class='showimgleftlink' dataid='"+prevID+"' onclick='ViewBigImgHandle.getPrevBigimg(this)'>"
            +"<a href='javascript:void(0)'>"
            +"</a></div>";

        var lefthtml="<div class='showimgrightlink' dataid='"+nextID+"' onclick='ViewBigImgHandle.getNextBigimg(this)'>"
            +"<a href='javascript:void(0)'>"
            +"</a></div>";
        var html="<div class='leftAndrightlingbox' style='position:absolute;margin:"+(win_h/2-70)+"px 0 0 50px;width:"+win_left_w+"px;height:100px;'>"+righthtml+lefthtml+"</div>";
        return html;
    };

    var getLoadingImg=function(){
        var html="<img class='showImgs' style='margin:"+(win_h/2-70)+"px 0 0 "+(win_left_w/2-50)+"px;' src='Public/img/loding.gif'/>";
        return html;
    };

    var getNextBigimg=function(that){
        var id=$(that).attr("dataid");
        if(''==id||isNaN(id)){
            alert("已经没有了哦!");
            return false;
        }
        // 拖动
        resetDate(id,1);
        resetDateAndgetImg();
        scrollHandle();// 是否需要卷动
    };

    var getPrevBigimg=function(that){
        var id=$(that).attr("dataid");
        if(''==id||isNaN(id)){
            alert("已经没有了哦!");
            return false;
        }
        // 拖动
        resetDate(id,1);
        resetDateAndgetImg();
        scrollHandle();// 是否需要卷动
    };

    var getthisbigpic=function(that){
        var classname=$(that).attr("class");
        if(classname!=''){ return;}// 现在就是了
        var id=$(that).attr("dataid");
        resetDate(id,1);
        resetDateAndgetImg();
        scrollHandle();// 是否需要卷动
    };

    // 是否需要卷动
    var scrollHandle=function(){
        // 是否处于端点
        //var long=queueID.length*54;// 总长度

       // var thisidindex=queueID.FindVal(thisid);// 当前的第几张

        var mleftnow=getminipicLongboxMleft();// 当前滚动的长度

        var windowsWidth=parseInt((win_left_w-66)/54)*54;// 当前的宽度可以容纳多少个

        var mmleft=$("#bodyWindows .minipicshortbox").offset().left;// 视窗的 绝对位置
        var minipicleft=$("#minipicscroll_"+thisid).offset().left; // 当前的pic 绝对位置
        //console.log((minipicleft-mmleft),windowsWidth);
        var setMleft=0;
        if(minipicleft>0&&minipicleft-mmleft<50){
            // 到了左边的第一个了 向左滚动
            if(mleftnow-windowsWidth+54>=0){
                setMleft=mleftnow-windowsWidth+54;
            }
            setminipicLongboxMleft(setMleft,mleftnow);
            return;
        }

        var cha=minipicleft-mmleft;


        if(minipicleft>0&&cha>0&&Math.abs(windowsWidth-54-cha)<50){
            // 到了最右边的那个了 向右滚动吧
            var long=queueID.length*54;// 总长度
            setMleft=long>windowsWidth?long-windowsWidth:0;// 默认的
            if(mleftnow+windowsWidth+(windowsWidth-54)<=long){
                setMleft=mleftnow+windowsWidth-54
            }
            setminipicLongboxMleft(setMleft,mleftnow);
            return;
        }
        //var windowsWidthCount2=parseInt(windowsWidthCount/2)*54; // 即将卷动的数量






    };

    var resetDateAndgetImg=function(){
        var html=getLoadingImg();
        $("#bodyWindows .bodyWindows_img img").before(html);
        setImginfo(thisid);
        getBigimg(thisid);
    };

    var resetDate=function(id,set){
        thisid=id;
        var thisidindex=queueID.FindVal(thisid);
        prevID=thisidindex>0?queueID[thisidindex-1]:null;
        nextID=thisidindex>=0&&(queueID.length-thisidindex)>1?queueID[thisidindex+1]:null;
/*        console.log(queueID);
        console.log('this:'+thisid);
        console.log('thisindex:'+thisidindex);
        console.log('left:'+prevID);
        console.log('right:'+nextID);*/
        if(set){
            $("#bodyWindows .showimgleftlink").attr("dataid",prevID);
            $("#bodyWindows .showimgrightlink").attr("dataid",nextID);
            $("#bodyWindows .number_index").html((queueID.FindVal(thisid)+1)+"/"+queueID.length);
            var minipicid="#minipicscroll_"+thisid;
            $(minipicid).parent().find("a").removeClass("hoverthis");
            $(minipicid).addClass("hoverthis");
        }
    };

    return {
        "initBigImgHandle":initBigImgHandle,
        "closeWindaow":closeWindaow,
        "getNextBigimg":getNextBigimg,
        "getPrevBigimg":getPrevBigimg,
        "getthisbigpic":getthisbigpic,
        "minipicscroll":minipicscroll
    }
})();