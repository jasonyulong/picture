<?php if (!defined('THINK_PATH')) exit();?><link rel="stylesheet" href="__PUBLIC__/css/pure-min.css">
<style>
    body{margin:0px;padding:0;}
</style>
<div style="background:#006DD2;height:40px;margin-bottom: 10px;"></div>
<form action="__URL__/loginCheck/" method="post" style="margin-top:10px;" class="pure-form">
    <div style="margin: 20px;">
        <input type="text" name="acc" placeholder="UserName" value="" autocomplete="off" />
        <input type="password" name="pwd"  placeholder="Password" autocomplete="off"/>
        <input type="submit" value="Login" class="pure-button pure-button-primary"/>
    </div>

</form>