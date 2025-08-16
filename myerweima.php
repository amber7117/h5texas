<?php

define('WYHEAD','');
define('WYPHP' , dirname(__FILE__).'/WangYa/');
define('WYTEMP', 'temp');
define("WYCON" , '');
define("WYDB"  , '');
define('WYNAME'  , '');

require WYPHP.'WangYa.php';
$CONN = include WYPHP.'conn.php';

$UHA = md5($SESSIONID);

/*读取缓存*/
$_NSESSION = $sescc = sescc('','',$UHA);


// 图片一
$path_1 = pichttp($CONN['erweima']);   //背景

/*默认*/
$madaxiao = $CONN['erweima_dx'];
$erweima_zy = $CONN['erweima_zy'];
$erweima_sx = $CONN['erweima_sx'];
$gametype = '';




/**
 * $path_1 背景
 * $erweima_zy 二维码左右位置
 * $erweima_sx 二维码上下位置
 * $madaxiao 二维码大小
 */
if($_GET['gametype'] == 2){ /*niuniu*/
    $gametype = 'niuniu';
    $path_1 = pichttp($CONN['niu_erweima']);
    $erweima_zy = $CONN['niu_erweima_zy'];
    $erweima_sx = $CONN['niu_erweima_sx'];
    $madaxiao = $CONN['niu_erweima_dx'];

}elseif ($_GET['gametype'] == 4){ /*caifuji*/
    $gametype = 'caifuji';
    $path_1 = pichttp($CONN['cfj_erweima']);
    $erweima_zy = $CONN['cfj_erweima_zy'];
    $erweima_sx = $CONN['cfj_erweima_sx'];
    $madaxiao = $CONN['cfj_erweima_dx'];

}elseif (isset($_GET['gametype']) && trim($_GET['gametype']) == 'videolonghu'){
    $gametype = 'videolonghu';
    $path_1 = pichttp($CONN['lherweima']);
    $erweima_zy = $CONN['lherweima_zy'];
    $erweima_sx = $CONN['lherweima_sx'];
    $madaxiao = $CONN['lherweima_dx'];
}elseif (isset($_GET['gametype']) && trim($_GET['gametype']) == 'apkhongbao'){
    $gametype = 'apkhongbao';

    if(isset($_GET['type']) && $_GET['type'] == 'app'){
        $path_1 = pichttp($CONN['apkhberweima1']);   //背景
        $erweima_zy = $CONN['apkhberweima_zy1'];
        $erweima_sx = $CONN['apkhberweima_sx1'];
        $madaxiao = $CONN['apkhberweima_dx1'];
    }else{
        $path_1 = pichttp($CONN['apkhberweima']);   //背景
        $erweima_zy = $CONN['apkhberweima_zy'];
        $erweima_sx = $CONN['apkhberweima_sx'];
        $madaxiao = $CONN['apkhberweima_dx'];
    }
}elseif ( trim($_GET['gametype']) == 'lianhuanpao' ){
    $gametype = 'lianhuanpao';
    $path_1 = pichttp($CONN['lhp_erweima']);
    $erweima_zy = $CONN['lhp_erweima_zy'];
    $erweima_sx = $CONN['lhp_erweima_sx'];
    $madaxiao = $CONN['lhp_erweima_dx'];
}elseif ( trim($_GET['gametype']) == 'sghuachuan' ){
    $gametype = 'sghuachuan';
    $path_1 = pichttp($CONN['shc_erweima']);
    $erweima_zy = $CONN['shc_erweima_zy'];
    $erweima_sx = $CONN['shc_erweima_sx'];
    $madaxiao = $CONN['shc_erweima_dx'];
}elseif ( trim($_GET['gametype']) == 'dezhou' ){
    $gametype = 'dezhou';
    $path_1 = pichttp($CONN['dez_erweima']);
    $erweima_zy = $CONN['dez_erweima_zy'];
    $erweima_sx = $CONN['dez_erweima_sx'];
    $madaxiao = $CONN['dez_erweima_dx'];
}


// 图片二
if($gametype == 'apkhongbao'){
    $path_2 = 'http://127.0.0.1/ewm.php?madaxiao='.$madaxiao.'&data='.urlencode(str_replace('@',md5(time()),str_replace('/Tz/','/@/',$CONN['ewmyuming'])).'/download.php?tuid='.$sescc['uid']); 
}else{
    $path_2 = 'http://127.0.0.1/ewm.php?madaxiao='.$madaxiao.'&data='.urlencode(str_replace('@',md5(time()),str_replace('/Tz/','/@/',$CONN['maHTTP'])).'?tuid='.$sescc['uid'].'&gametype='.$gametype);
}



// 创建图片对象
$image_1 = imagecreatefrompng($path_1);

$image_2 = imagecreatefrompng($path_2);

// 合成图片
$fan = imagecopymerge($image_1, $image_2,$erweima_zy,$erweima_sx, 0, 0, imagesx($image_2), imagesy($image_2), 100);

$left = 0;
if(isset($_GET['type']) && $_GET['type'] == 'app'){
    $left = 225;
}else{
    $left = 550;
}

imagefttext($image_1,20,0,$left, 60 , $COLOR  , WYPHP.'Font/'.$ZITI.'.ttf' ,"ID:".$sescc['uid'], array('character_spacing' => 20));

$COLOR = imagecolorallocate($image_1, 255, 255, 255);
imagefttext($image_1,20,0,$left, 58 , $COLOR  , WYPHP.'Font/'.$ZITI.'.ttf' ,"ID:".$sescc['uid'], array('character_spacing' => 20));

header('Content-Type: image/png');
imagepng($image_1);



?>