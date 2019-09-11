
FollowApp=(function(){

    var url='';

    var unfollow=[];

    var shopfollowersHeight=0;
    var lastUpdateTime=0;
    var lastUnfollow=0;

    var accountid=0;

    var errorCount=0;

    var allowErrorCount=80;

    var autoRun=0;
    var pointError=10;
    var itemArr = [];
    var itemArrIndex = 0;
    var csrf='';
    var init =function(){
        url=location.href;

        // 这个是 那啥我的关注页面
        var arr=url.match(/\/shop\/(\d+)\//);
        if(arr){
            var id=arr[1];
            accountid=id;
            initHtmlBox(id);
        }


        var type = url.match(/sortBy=([a-z]{5})/)[1];
        if(type){
            var csrfArr = url.match(/token=(.*?)&/);
            if(!csrfArr){
                var src = "/shop/"+accountid+"/following/";
                $("#main").append('<iframe src='+src+' id="myiframe"></iframe>');
            }else{
                csrf = csrfArr[1];
            }
            var autoArr=url.match(/autoRun=(\d+)/);
            if(autoArr){
                autoRun = autoArr[1];
                if(autoRun<10){
                    if(type == 'ctime'){
                        PointCTime();
                    }else{
                        PointSales();
                    }
                }
            }
        }
    };


    var initHtmlBox=function(id){
        var html="";
        html+="<div style='padding:3px;border-radius:4px;position:fixed;z-index:9999;width:60pt;height:60pt;background:#1AB394;top:200px;left:0px;color:#fff;'>";
        html+="<a href='/shop/"+id+"/following/'>我关注的人</a><br>";
        html+="<a id='FollowAppUnFollow' onclick='FollowApp.StartUnFollow()' href='javascript:'>全部取消</a><br>";
        html+="<a id='FollowAppAllFollow'  onclick='FollowApp.StartFollow()' href='javascript:'>全部关注</a><br>";
        html+="<a id='PointCTime' onclick='FollowApp.PointCTime()' href='javascript:'>最新点赞</a><br>";
        html+="<a id='PointSales' onclick='FollowApp.PointSales()' href='javascript:'>热销点赞</a><br>";
        html+="</div>";

        $("body").append(html);
    };


    var initDebugBox=function(){
        if($("#FollowAppDebugBox").length>0){
            return false;
        }
        var html="";
        html+="<div id='FollowAppDebugBoxScroll' style='padding:10px;border-radius:10px;position:fixed;z-index:9999;width:100%;height:100pt;overflow-y:scroll;overflow-x:hidden;background:#000;opacity:0.8;top:20px;color:#fff;'>";
        html+="<div id='FollowAppDebugBox' style='margin: 10px;'></div>";
        html+="</div>";

        $("body").append(html);

    };


    var StartUnFollow = function () {
        if (! /\/\d+\/following\//.test (url)) {
            alert ("无法操作！检测到当前界面不是 关注中啊!");
            return false;

        }

        initDebugBox();

        errorCount=0;

        FollowAppInter=setInterval(function(){
            listenHeight();
            scrollToEnd();
        },3000)

    };

    var StartFollow=function () {
        if (! /\/\d+\/followers\//.test (url)) {
            alert ("无法操作！检测到当前界面不是 别人家的粉丝列表!");
            return false;
        }
        errorCount=0;
        initDebugBox();


        // 开始愉快地关注吧！
        FollowEdAppInter=setInterval(function(){
            listenHeight(2);
            scrollToEnd();
        },4000);

    };


    var listenHeight=function(isUnfollows){

        isUnfollows=isUnfollows||1;
        var h=parseInt($("#shop-followers").height());

        if(h > shopfollowersHeight){
            shopfollowersHeight=h;
            console.log('发现了更新-------------');
            console.log(shopfollowersHeight);
            console.log(lastUpdateTime);
            lastUpdateTime= parseInt( Date.parse(new Date()) /1000) ;
            if(isUnfollows==1){
                unfollows();
            }else{
                tofollows();
            }
        }
    };



    // 取消粉丝
    var unfollows =function(){

        var Jqs=$("#shop-followers ul.follower-list li");
        var len=Jqs.length;

        if(len <=0 ){
            return false;
        }

        var token = window.csrf;

        var domain=getDomain();

        for(var i=lastUnfollow; i<len; i++){
            var userid= $(Jqs.eq(i)).attr('data-follower-shop-id');
            $("#FollowAppDebugBox").append('取关:'+userid+"<br>");
            $.ajax({
                type:"POST",
                url:domain+"/buyer/unfollow/shop/"+userid+"/",
                data:"csrfmiddlewaretoken="+token,
                //async: false,
                success: function (data) {
                    console.log(data);
                    //console.log(data)
                    if(data.success!=true){
                        errorCount++;
                    }

                    if(errorCount>allowErrorCount){
                        clearInterval(FollowAppInter);
                        alert("错误次数太多，请切换账号试试!");
                        return false;
                    }
                }
            },'json');
        }

        lastUnfollow=len;
        scrollDebugEnd();

    };


    // 关注粉丝
    var tofollows =function(){

        var Jqs=$("#shop-followers ul.follower-list li");
        var len=Jqs.length;

        if(len <=0 ){
            return false;
        }
        var domain=getDomain();
        var token = window.csrf;
        var stops=false;
        for(var i=lastUnfollow; i<len; i++){



            if(stops){
                break;
            }

            var userid= $(Jqs.eq(i)).attr('data-follower-shop-id');
          //  console.log($.trim($(Jqs.eq(i)).find('div.btn-follow').eq(0).html()));

            if('关注中'== $.trim($(Jqs.eq(i)).find('div.btn-follow').eq(0).html())){
                $("#FollowAppDebugBox").append('<span style="color:#911">已经是关注中:'+userid+"<br></span>");
                continue;
            }
            $("#FollowAppDebugBox").append('不停地关注中:'+userid+"<br>");
            $.ajax({
                type:"POST",
                url:domain+"/buyer/follow/shop/"+userid+"/",
                data:"csrfmiddlewaretoken="+token,
                async:false,
                success: function (data) {
                    console.log(data);
                },
                error: function(xhr, exception){
                    errorCount++;
                    if(errorCount>allowErrorCount){
                        clearInterval(FollowEdAppInter);
                        alert("错误次数太多，请切换账号试试!");
                        stops=true;
                        return false;
                    }
                }
            },'json');


        }

        lastUnfollow=len;
        scrollDebugEnd();

    };

    var getDomain=function(){
      var UrlArr=url.split('/shop/');
       return UrlArr[0];
    };

    var getTime=function(){
        var date = new Date();
        return date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + date.getDate()+" "
            +date.getHours()+":"+date.getMinutes()+":"+date.getSeconds();
    };

    var scrollToEnd=function(){//滚动到底部
        var h = $(document).height()-$(window).height();
        $(document).scrollTop(h+20);
    };


    var scrollDebugEnd=function(){
        var h=$("#FollowAppDebugBox").height();
        $("#FollowAppDebugBoxScroll").scrollTop(h);
    };


    var PointCTime= function(){
        if (!url.match(/ctime/)) {
            alert ("无法操作！检测到当前界面不是 别人家的最新页面!");
            return false;
        }
        errorCount=0;
        initDebugBox();
        setTimeout(function(){
            listenPage('ctime');
        },3000)

    };
    var PointSales= function(){
        if (!url.match(/sales/)) {
            alert ("无法操作！检测到当前界面不是 别人家的最新页面!");
            return false;
        }
        errorCount=0;
        initDebugBox();
        setTimeout(function(){
            listenPage('sales');
        },3000)
    };
    var listenPage=function(type){
        if(!csrf){
            csrf = document.getElementById("myiframe").contentWindow.csrf;
        }
        if(!csrf){
            alert ("无法操作！请页面完成加载之后再点击!");
            return false;
        }
        var page=parseInt($(".shopee-page-controller").find(".shopee-button-solid").html());
        console.log('开始第'+page+'页的点赞-----------');
        console.log(page);
        console.log(lastUpdateTime);
        lastUpdateTime= parseInt( Date.parse(new Date()) /1000) ;
        toPoint(page,type);

    };
    var toPoint =function(page,type){
        var Jqs=$(".shop-search-result-view div.row div.shop-search-result-view__item");
        var len=Jqs.length;
        if(len <=0 ){
            return false;
        }
        var domain=getPageDomain();
        var apidomain=getDomain();
        for(var i=0; i<len; i++){
            var title= $(Jqs.eq(i)).find('a').attr('title');
            var item = $(Jqs.eq(i)).find('a').attr('href').split (".").pop();
            var dianstr = $.trim($(Jqs.eq(i)).find('div.shopee-item-card__btn-likes').eq(0).find('svg').attr("class"));
            var strArr=dianstr.match(/solid/);
            if(strArr){
                $("#FollowAppDebugBox").append('<span style="color:#911">已点过赞:'+title+"<br></span>");
                continue;
            }
            $("#FollowAppDebugBox").append('不停的点赞中:'+title+"<br>");
            itemArr.unshift(item);
        }
        itemArr = getRandomArrayElements(itemArr,Math.ceil(len/3*2));
        var itemArrlen = itemArr.length;
        toPointReq(apidomain,itemArr,itemArrlen-1,page,domain,type);
    };

    //随机取数组
    var getRandomArrayElements=function(arr, count) {
        var shuffled = arr.slice(0), i = arr.length, min = i - count, temp, index;
        while (i-- > min) {
            index = Math.floor((i + 1) * Math.random());
            temp = shuffled[index];
            shuffled[index] = shuffled[i];
            shuffled[i] = temp;
        }
        return shuffled.slice(min);
    };


    var toPointReq = function(apidomain,itemArr,itemArrlen,page,domain,type) {
        if(errorCount>pointError){
            clearInterval(FollowEdAppInter);
            alert("错误次数太多，请切换账号试试!");
            return false;
        }
        if(itemArrIndex > itemArrlen){
            autoRun++;
            var pageurl = domain+"/search?page="+page+"&sortBy="+type+"&token="+csrf+"&autoRun="+autoRun;
            window.location.href=pageurl;
            scrollDebugEnd();
            return false;
        }
        var item = itemArr[itemArrIndex];
        itemArrIndex++;
        fetch(apidomain + "/api/v0/buyer/like/shop/" + accountid + "/item/" + item + "/", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "x-csrftoken":csrf
            },
            body: JSON.stringify({})
        }).then(function (res) {
            if (res.status === 200) {
                console.log(res.json());
            } else {
                errorCount++;
                console.log(res.json());
            }
        }).then(function (data) {
            toPointReq(apidomain,itemArr,itemArrlen,page,domain,type);
        }).catch(function (err) {
            errorCount++;
            toPointReq(apidomain,itemArr,itemArrlen,page,domain,type);
        });
    };

    var getPageDomain=function(){
        var UrlArr=url.split('/search?');
        return UrlArr[0];
    };

    return {
        "init":init,
        "StartUnFollow":StartUnFollow,
        "StartFollow":StartFollow,
        "PointCTime":PointCTime,
        "PointSales":PointSales
    }
})();


$(function(){
    FollowApp.init();
});
