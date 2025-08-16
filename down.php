<?php

define('WYHEAD','');
define('WYPHP' , dirname(__FILE__).'/WangYa/');
define('WYTEMP', 'temp');
define("WYCON" , '');
define("WYDB"  , '');
define('WYNAME'  , '');

require WYPHP.'WangYa.php';

if (strpos($_SERVER['HTTP_USER_AGENT'],'MicroMessenger') !== false ) {     //微信内部浏览器
?>
<!DOCTYPE html>
<html>
    <head>
        <title>提示2</title>
        <meta charset="utf-8">
     <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no, width=device-width">
     <script>
    function backgame(){
       
        history.go(-1);
    }
    </script>
     </head>
     
    <body style="background:#000;height:100%;width:100%">
    <div style="background:#000;height:100%;width:100%" onclick="backgame();"><img src="/downtip.png" style="width:100%;padding-bottom:1000px;" /></div>
    </body>
</html>

<?php
    
}else{
    header('Location: /apkhongbao.apk');
    exit();
}  

?>