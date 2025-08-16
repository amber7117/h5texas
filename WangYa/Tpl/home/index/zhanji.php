<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

if( $USERID < 1){

    return apptongxin($SHUJU,415,-99,"no login",$YZTOKEN,$WY);
}

$D  = db('fanglist');
global $Mem;
if($MOD == 'get'){
    /*获取数据*/

    $DATA = $D -> where(array( 'userin OLK' => '%'.$USERID.'%')) -> order('atime desc') -> limit(50) -> select();

    if($DATA){

        $ARR = array();
        $kk = 0;
        foreach($DATA as $k=>$v){
 
            $neirong = $v['neirong'];

            eval('$neirong='.$neirong.';');

            $shuying = $neirong['AllTongji'];

            if(count($shuying) < 2) continue;
            $ARR[$kk] = array();
            $ARR[$kk]['fangid'] = $v['fangid'];
            $ARR[$kk]['atime'] = date('Y-m-d H:i:s',$v['atime']);


            $ARR[$kk]['neirong'] = array();
            foreach($shuying as $uid=>$vv){
                $ARR[$kk]['neirong'][$uid] = array();
                $userdata = uid($uid);
                $ARR[$kk]['neirong'][$uid]['name'] = $userdata['name']?$userdata['name']:$userdata['zhanghao'];
                $ARR[$kk]['neirong'][$uid]['shuying'] = $vv;
            }
            $kk ++;

        }

        $SHUJU = $ARR;

    }else return apptongxin($SHUJU,415,-1,'还没有数据哟',$YZTOKEN,$WY);


}else if($MOD == 'post'){
    /* 群主开房查询 */
    $userdata = $D -> setbiao('user') -> zhicha('uid,jifen,isqunzhu') ->where(array('uid'=>$USERID)) -> find();
    
    if((int)$userdata['isqunzhu'] == 1){

        $DATA = $D -> setbiao('fanglist') -> where(array( 'fangzhu' => $USERID)) -> order('atime desc') -> select();

        if($DATA){

            foreach($DATA as $k=>$v){
                $DATA[$k]['atime'] = date('Y-m-d H:i:s',$v['atime']);
                if($v['off'] == 0){
                    $DATA[$k]['off'] = '正常';
                }else if($v['off'] == 1){
                    $DATA[$k]['off'] = '已结束';
                }else if($v['off'] == 2){
                    $DATA[$k]['off'] = '已过期';
                }
            }
            $SHUJU = $DATA;
        }else return apptongxin($SHUJU,415,-1,'还没有数据哟',$YZTOKEN,$WY);

    }else return apptongxin($SHUJU,415,-1,'还不是群主',$YZTOKEN,$WY);

}else if($MOD == 'put'){

    if(isset($_POST['gametype']) && ($_POST['gametype'] == 'hongbao' || $_POST['gametype'] == 'apkhongbao')){

        /*赠送好友*/
        $YZHost = 'weiyi/put'.md5($USERID);
        $cuzai = $Mem ->g($YZHost);

        if($cuzai){

            $Mem ->s($YZHost,1,1);
            return apptongxin(array(),415,$CODE,'请不要重复提交',$YZTOKEN,$WY);
        }

        $Mem ->s($YZHost,1,1);

        if(!isset($_POST['uid']) || !isset($_POST['jine'])){

            return apptongxin(array(),415,$CODE,'提交数据错误',$YZTOKEN,$WY);

        }

        $FUID = (int)$_POST['uid'];
        $JINE = (float)$_POST['jine'];

        if($JINE <= 0){

            return apptongxin(array(),415,$CODE,'提交金额错误',$YZTOKEN,$WY);

        }

        if($FUID == $USERID){

            return apptongxin(array(),415,$CODE,'不能赠送给自己',$YZTOKEN,$WY);

        }

        $SFX = round($JINE*$CONN['apkgivesxf'],2);

        $MYDATA = uid($USERID,1);
        $FDATA = uid($FUID,1);

        if(!$FDATA){
            return apptongxin(array(),415,$CODE,'找不到好友信息',$YZTOKEN,$WY);
        }

        if($MYDATA['huobi'] < ($JINE + $SFX)){

            return apptongxin(array(),415,$CODE,'金币不足，请充值后再赠送好友',$YZTOKEN,$WY);

        }
        $fan = jiaqian($USERID,26,0,0,-($JINE + $SFX),'赠送'.$FDATA['name'].'了'.$JINE.'金币','',0,0);

        if($fan){

            $SHUJU = array(
                'huobi' => $fan['huobi'],
            );

            $back = jiaqian($FUID,26,0,0,$JINE,$MYDATA['name'].'赠送给你'.$JINE.'金币','',0,0);

            if($back){

                return apptongxin($SHUJU,200,1,'成功赠送给'.$FDATA['name'].'了'.$JINE.'金币',$YZTOKEN,$WY);

            }else return apptongxin($SHUJU,415,$CODE,'好友添加金币失败，请联系客服',$YZTOKEN,$WY);

        }else return apptongxin(array(),415,$CODE,'扣除金币失败，请稍后重试',$YZTOKEN,$WY);

    }else{

        /* 房主强行解散房间 */

        $HASH = 'gameuid/'.$USERID;

        $GAMEID = $Mem -> g( 'fangid/'.((int)$_POST['roomid']));

        if(!$GAMEID){
        
            return apptongxin($SHUJU ,415,-1,'房间号不存在',$YZTOKEN,$WY);
        }

        $GAMEIDIP = Game_Server( $GAMEID );

        if( !$GAMEIDIP ){

            return apptongxin($SHUJU ,415,-1,'还没有这个游戏',$YZTOKEN,$WY);

        }

        $IP = fenpeiip($_POST['roomid'],$GAMEIDIP);

        if(!$IP){

            return apptongxin($SHUJU ,415,-1,'没有服务器通信地址',$YZTOKEN,$WY);
        }

        $usesuju = array('y'=>'fangexit','d' => (int)$_POST['roomid']);
        $fan = httpudp($usesuju,$IP['ip'],  $IP['port'] );

        if(!$fan || $fan['code'] == '-1')  $ganeid =  false;

        $data = $Mem -> g($HASH);   //删除房主房间号缓存
        
        if( !$ganeid )
        {  
            return apptongxin($SHUJU ,200,1,'房间已结束',$YZTOKEN,$WY);
        }

        if($fan['code'] == 2){
            return apptongxin($SHUJU ,200,1,'房间已结束',$YZTOKEN,$WY);
        }
    }
    
}else if($MOD == 'delete'){

    if(isset($_POST['gametype']) && ($_POST['gametype'] == 'hongbao' || $_POST['gametype'] == 'apkhongbao')){
        /*金币赠送记录*/
        $SHUJU = array();
        $data = db('huobilog') -> where(array('uid' => $USERID,'type' => 26))->limit(20)->order('atime desc') -> select();
        if($data){

            foreach($data as $k=>$v){

                $data[$k]['atime'] = date('Y-m-d | H:i:s',$v['atime']);
            }

            $SHUJU = $data;
        }

    }else{

        /*移除成员*/

        $requsetdata = $D -> setbiao('user') -> zhicha('uid,tuid,qunjihe') ->where(array('uid'=>$_NPOST['removetuid'])) -> find();

        $userqun = explode(';',$requsetdata['qunjihe']);
        $qunarr = array();
        foreach($userqun as $kk=>$vv){
            $arr = explode(',',$vv);
            $qunarr[] = $arr;
        }
        
        foreach($qunarr as $k=>$arr){
            if((int)$arr[0] == (int)$USERID){
                unset($qunarr[$k]);
            }else{
                $qunarr[$k] = implode(',',$arr);
            }
        }
        $qunjihe = implode(';',$qunarr);

        $fan = $D -> where(array('uid'=>$_NPOST['removetuid'])) -> update(array('qunjihe'=>$qunjihe));

        if($fan){

            return apptongxin($SHUJU,200,1,'移除成功',$YZTOKEN,$WY);

        }else return apptongxin($SHUJU,415,-1,'移除失败，请稍后重试',$YZTOKEN,$WY);
    }
    

}



return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);