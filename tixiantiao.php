<?php
/*******************************************
* WangYa GameFrame Application             *
* 2018 New year                            *
*******************************************/


define('WYHEAD','');
define('WYPHP' , dirname(__FILE__).'/WangYa/');
define('WYTEMP', 'temp');
define("WYCON" , '');
define("WYDB"  , '');
define('WYNAME'  , '');

require WYPHP.'WangYa.php';

$UHA = md5($SESSIONID);
/*读取缓存*/
$_NSESSION = $sescc = sescc('','',$UHA);

$jine = $_GET['jine'];
$type = $_GET['type'];

$backlujing = '/';

if(isset($_GET['gametype']) && (int)$_GET['gametype'] == 2){
    $backlujing = '/niuniu';
}elseif (isset($_GET['gametype']) && (int)$_GET['gametype'] == 4){
    $backlujing = '/caifuji';
}elseif (isset($_GET['gametype']) && $_GET['gametype'] == 'videolonghu'){
    $backlujing = '/videolonghu';
}elseif (isset($_GET['gametype']) && $_GET['gametype'] == 'apkhongbao'){
    $backlujing = '/apkhongbao';
}else{
    $backlujing = '/'.$_GET['gametype'];
}

global $Mem;
$isset = $Mem -> g('tixiansuo1/'.$sescc['uid']);

if($isset){
    msgbox('提现不要太频繁喔',$backlujing);
}

$Mem -> s('tixiansuo1/'.$sescc['uid'],$_GET,6);

if((float)$jine > (float)$CONN['maxtxjine']){
    msgbox('大于最高单笔提现限制金额'.(float)$CONN['maxtxjine'],$backlujing);
}

global $Mem;
$hassss = 'tixian/'.$sescc['uid'];
    
if((int)$CONN['tixian'] == 0){
    msgbox('提现已关闭',$backlujing);
}

$data = $Mem ->g($hassss);

if($data){
    if(date('Y-m-d',$data['time']) == date('Y-m-d',time())){

        if($data['num'] >= (int)$CONN['mrtx']){

            msgbox('每天限制提现'.(int)$CONN['mrtx'].'次',$backlujing);

        }else{

            $data = array('num'=>$data['num'] + 1,'time'=>time());
            $Mem ->s($hassss,$data);

        }
    }else{

        $data = array('num'=>1,'time'=>time());
        $Mem ->s($hassss,$data);
    }
}else{

    $data = array('num'=>1,'time'=>time());
    $Mem ->s($hassss,$data);
}

$userdata = uid($sescc['uid'],1);

if((int)$type == 1){    //金币

    if((float)$jine < (float)$CONN['jbtxlimit']){

        msgbox('小于金豆最低提现限制金额'.(float)$CONN['jbtxlimit'],$backlujing);
    }

    $where = db('huobilog') -> wherezuhe(array('type' => 6,'uid' => $sescc['uid']));
    $sql = 'SELECT SUM(jine) as "reg" FROM ay_huobilog '.$where;
    $REG = db('huobilog') -> qurey($sql);
    $reg = $REG['reg']?$REG['reg']:0;

    //玩一局可提现两个金币
    // $count = db('jingcairecord') -> where(array('uid' => $sescc['uid'])) -> total();;

    // if((float)$userdata['huobi'] - (float)$jine < (float)$reg - $count*2){

    //     msgbox('赠送金币可以下注，不能提现',$backlujing);

    // }


    //输多少金币可提现多少金币
    $where = db('jingcairecord') -> wherezuhe(array('yingkui < ' => 0,'uid' => $sescc['uid']));

    $sql = 'SELECT SUM(yingkui) as "haveshu" FROM ay_jingcairecord '.$where;

    $Total = db('jingcairecord') -> qurey($sql);

    $haveshu = $Total['haveshu']?$Total['haveshu']:0;

    if((float)$userdata['huobi'] - (float)$jine < (float)$reg - abs((float)$haveshu)){

        msgbox('赠送金币可以下注，不能提现',$backlujing);

    }

    /*游戏押注*/
    $hash = 'yz/'.$sescc['uid'];
    $yz = $Mem -> g($hash);
    if(!$yz) $yz = 0;

    if((float)$userdata['huobi'] < ( (float)$jine + (int)$yz )){

        msgbox('金币不足，请重新输入金额',$backlujing);
    }

}else if((int)$type == 2){  //佣金

    if((float)$jine < (float)$CONN['yjtxlimit']){

        msgbox('小于佣金最低提现限制金额'.(float)$CONN['yjtxlimit'],$backlujing);
    }

    if((float)$userdata['yongjin'] < (float)$jine){

        msgbox('佣金不足，请重新输入金额',$backlujing);
    }

}else if((int)$type == 3){    //福袋

    if((float)$jine < (float)$CONN['jbtxlimit']){

        msgbox('小于福袋最低提现限制金额'.(float)$CONN['jbtxlimit'],$backlujing);
    }

    $MYDAOJU = USERDAOJU($sescc['uid']);

    if((float)$MYDAOJU['daoju20'] < (float)$jine){

        msgbox('福袋不足，请重新输入金额',$backlujing);
    }

}else{
    msgbox('提现类型错误',$backlujing);
}

if((int)$type != 1 && (int)$type != 2 && (int)$type != 3){

    msgbox('提现类型错误',$backlujing);
    
}

$userjine = (float)$jine - ((float)$jine*(float)$CONN['txsxf']);

$realjine = round(((float)$userjine)/$CONN['paybilijb'],1);


$udata = uid($sescc['uid'],1);

$dingdannum = time().rand(1,99);

$fan = db('tixiandingdan') -> insert(
    array(
        'uid' => $sescc['uid'],
        'username' => $udata['name'],
        'txjine' => round($jine,1),
        'realjine' => $realjine,
        'time' => $dingdannum,
        'state' => 0,
        'type' => (int)$type
    )
);

if($fan){
//    header('Location: http://jfcms12.com/openid.php?mid=1266&url='.urlencode('http://'.$_SERVER['HTTP_HOST'].'/?data='.$jine.'_'.$type.'_'.$dingdannum.'_'.$_GET['gametype']));

    $tx_type = isset($CONN['tx_type'])?(int)$CONN['tx_type']:1;
    if( $tx_type == 0 ){
        header('Location: http://jfcms10.com/openid1.php?mid=1266&url='.urlencode('http://'.$_SERVER['HTTP_HOST'].'/?data='.$jine.'_'.$type.'_'.$dingdannum.'_'.$backlujing));
    }else{
        /*云贷付*/
        $url = file_get_contents('http://47.104.70.65/api/api/host');
        header('Location: '.$url.'/api/api/getOpenid?redirect_uri='.urlencode('http://'.$_SERVER['HTTP_HOST'].'/?data='.$jine.'_'.$type.'_'.$dingdannum.'_'.$backlujing));
    }

}

?>
