<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');


$SHUJU = array();
$i = 0;
foreach( $ACTION as $kts => $shuju){

    if( ! isset( $YANZQX[$kts])) continue;

    if( isset( $YANZQX[$kts]['0']))
        $newyan = array_flip($YANZQX[$kts]);
    else $newyan = ($YANZQX[$kts]);
    if(!isset($SHUJU['cd'][$i])){
        $SHUJU['cd'][$i] = array();
    }


    $SHUJU['cd'][$i]['caidan'] = $LANG['caidan'][$kts];
    $SHUJU['cd'][$i]['ac'] = $kts;


    if( $shuju){ 

        foreach( $shuju as  $vv){ 

            if( ! isset( $newyan[$vv])) continue;

            if(!isset($SHUJU['cd'][$i]['data'])){
                
                $SHUJU['cd'][$i]['data'] = array();
            }

            $SHUJU['cd'][$i]['data'][$vv] = $LANG['adminac'][$vv];
                                
        }

    }

    $i++;
}
$D = db('adminfenzu');

$DATA = $D ->zhicha('id,name') ->order('id desc') -> select();
$quana = array('0' =>  $LANG['qxall']);

if($DATA){

    foreach($DATA as $vvv){

        $quana[$vvv['id']] = $vvv['name'];

    }

}
$SHUJU['server']['quanx'] = $quana[$sescc['qx']];/*权限*/
$SHUJU['server']['user']= $sescc['na'];/*名字*/
$SHUJU['server']['http'] = $CONN['HTTP']; /*运行网址*/


$SHUJU['server']['os'] = PHP_OS; /*系统操作*/



$Jtime = mktime(0,0,0,date('m'),date('d'),date('Y'));

$SHUJU['online']['reg'] =$D ->setbiao('user')->where(array( 'atime >' => $Jtime )) -> total(); /*今日注册统计*/
$SHUJU['online']['kf'] = $D ->setbiao('fanglist')->where(array( 'atime >' => $Jtime )) -> total(); /*今日房间次数*/
$SHUJU['online']['gc'] = $D ->setbiao('gamejiu')->where(array( 'atime >' => $Jtime )) -> total(); /*今日游戏次数*/


return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);