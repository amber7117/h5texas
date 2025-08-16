<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

$ZHANGHAO = isset($_NPOST['zhanghao'])? $_NPOST['zhanghao'] : '';
$PASS = isset($_NPOST['pass'])? $_NPOST['pass'] : '';
$VCODE = isset($_NPOST['vcode'])? $_NPOST['vcode'] : '';

$canshu = array(
    'zhanghao#len#2-30',
    'pass#len#6-30',
    'vcode#len#4',
);

$FAN = yzpost( $canshu ,$_NPOST);



if($FAN['code'] == '0'){

    return apptongxin($SHUJU,415,-3,$LANG[$FAN['biao']].' [ '.$FAN['msg'].' ] '.$LANG['cuowu'] ,$YZTOKEN,$WY);
}

if($VCODE != $sescc['code']){

    return apptongxin($SHUJU,415,-1,$LANG['vcode'].$LANG['cuowu'] ,$YZTOKEN,$WY);

}

$D = db('admin');

$DATA = $D ->where(array('name' => $ZHANGHAO))-> find();

if(! $DATA ){

    return apptongxin($SHUJU,415,-1,$LANG['zhanghao'].$LANG['cuowu'] ,$YZTOKEN,$WY);
}

if($DATA['off'] < 1){

    return apptongxin($SHUJU,415,-1,$LANG['jinyong'],$YZTOKEN,$WY);
}

if($DATA['pass'] != mima($PASS)){

    return apptongxin($SHUJU,415,-1,$LANG['passcw'],$YZTOKEN,$WY);
}

sescc(array('aid'=>$DATA['id'],'qx' => $DATA['type'],'na' => $DATA['name'],'ip' => IP() ,'yzip' => $DATA['yanzhengip'] ),'',$UHA);

$Mem ->s('adminip/'.$DATA['id'], IP() );

adminlog($DATA['id'],0);

return apptongxin($SHUJU,200,1,'',$YZTOKEN,$WY);
