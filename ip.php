<?php
echo $_SERVER['REMOTE_ADDR'];die();
?>
<script>
    function IsPC(){
        var userAgentInfo = navigator.userAgent;
        var Agents = new Array("Android", "iPhone", "SymbianOS", "Windows Phone", "iPad", "iPod");
        var len=Agents.length;
        var flag = true,v=0;
        for(;v<len;v++){
            if (userAgentInfo.indexOf(Agents[v]) > 0) { flag = false; break; }
        }
        return flag;
    }
    var ispc=IsPC();
    //lert(ispc);
    if(ispc){
        location.href='./erp/login.php';
    }else{
        location.href="./erp/Mobile/login.mob.php";
    }
</script>
</body>
</html>
