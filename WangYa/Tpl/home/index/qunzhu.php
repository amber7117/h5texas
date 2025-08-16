<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

if( $USERID < 1){

    return apptongxin($SHUJU,415,-99,"no login",$YZTOKEN,$WY);
}

$D  = db('user');
global $Mem;
if($MOD == 'get'){
    /*获取群成员列表*/

    $NAME = (int)(isset($_NPOST['name'])?$_NPOST['name']:'');
    $hash = 'joinrequest/'.$USERID;
    $REQUESTDATA = $Mem -> g($hash);
    if($NAME == ''){

        /*小于1 多条数据*/

        $DATA = $D -> zhicha('uid,name,huobi,jifen,touxiang,qunjihe') -> select();

        $SHUJU = array();
        if($DATA){
            foreach($DATA as $k=>$v){

                $DATA[$k]['touxiang'] = pichttp($v['touxiang']);

                $userqun = explode(';',$v['qunjihe']);

                $key = array_search($v['uid'],$REQUESTDATA);
                if($key !== null && $key !== false){
                    $DATA[$k]['request'] = 1;
                }else{
                    $DATA[$k]['request'] = 0;
                }
                $qunarr = array();
                foreach($userqun as $kk=>$vv){
                    $arr = explode(',',$vv);
                    $qunarr[] = $arr[0];
                }
                $DATA[$k]['huobi'] = huobi($v['uid'],$USERID);
                if(in_array($USERID,$qunarr) || ($key !== null && $key !== false)){

                    $SHUJU[] = $DATA[$k];
                }
                
            }

            if($SHUJU){
                $CODE = 1;
                $STAT = 200;

            }else return apptongxin($SHUJU,415,-1,'还没有群成员哟',$YZTOKEN,$WY);
            
        }else return apptongxin($SHUJU,415,-1,'数据错误',$YZTOKEN,$WY);

    }else{

        /*读取一条数据*/
        
        $WHERE = array();
        $WHERE['uid LIKE'] = '%'.$NAME.'%';
        $WHERE['qunjihe LIKE'] = '%'.$USERID.'%';

        $DATA = $D ->where($WHERE)-> select();
        
        if(!$DATA ){

            return apptongxin($SHUJU,415,-1,'查询名字错误',$YZTOKEN,$WY);
        }
        $SHUJU = array();
        foreach($DATA as $k=>$v){
            $userqun = explode(';',$v['qunjihe']);
            $qunarr = array();
            foreach($userqun as $kk=>$vv){
                $arr = explode(',',$vv);
                $qunarr[] = $arr[0];
            }
            $DATA[$k]['touxiang'] = pichttp($v['touxiang']);
            $key = array_search($v['uid'],$REQUESTDATA);
            if($key !== null && $key !== false){
                $DATA[$k]['request'] = 1;
                $SHUJU[] = $DATA[$k];
            }else{
                if(in_array($USERID,$qunarr)){
                    $DATA[$k]['request'] = 0;
                    $SHUJU[] = $DATA[$k];
                }
            }
        }
        
        $SHUJU['uid'] = $USERID;

    }

}else if($MOD == 'post'){
    /*请求成为群主*/

    $userdata = $D -> zhicha('uid,jifen,isqunzhu') ->where(array('uid'=>$USERID)) -> find();

    if($userdata['jifen'] < $CONN['qunzhuNeed']){
        return apptongxin($SHUJU,415,-1,'房卡不足',$YZTOKEN,$WY);
    }

    if($userdata['isqunzhu'] == 1){
        return apptongxin($SHUJU,415,-1,'已经是群主了',$YZTOKEN,$WY);
    }

    $fan = jiaqian($USERID,14,0,-$CONN['qunzhuNeed'],0,'成为群主消耗');

    if($fan){
        $back = $D ->where(array('uid'=>$USERID)) -> update(array('isqunzhu' => 1));
        if(!$back){
            
            return apptongxin($SHUJU,415,-1,'申请成为群主失败，请联系客服',$YZTOKEN,$WY);
            
        }else{
            $SHUJU = $fan['jifen'];
            return apptongxin($SHUJU,200,1,'申请成为群主成功',$YZTOKEN,$WY);
        }  

    }else return apptongxin($SHUJU,415,-1,'扣除房卡失败，请稍后重试',$YZTOKEN,$WY);

    
}else if($MOD == 'put'){
    /*群主上下分*/
    $userdata = $D ->where(array('uid'=>$USERID)) -> find();

    if($userdata['isqunzhu'] == 1){

        $fan = false;
        $requsetdata = $D -> zhicha('uid,tuid,qunjihe') ->where(array('uid'=>$_NPOST['uid'])) -> find();

        $userqun = explode(';',$requsetdata['qunjihe']);
        $qunarr = array();
        foreach($userqun as $kk=>$vv){
            $arr = explode(',',$vv);
            $qunarr[] = $arr[0];
        }

        if($qunarr && in_array($USERID,$qunarr)){

            if((int)$_NPOST['type'] == 1){  //积分
                if((int)$_NPOST['jine'] > $userdata['jifen']){

                    $fan = jiaqian($USERID,4,0,-$_NPOST['jine'],0,'给'.$_NPOST['uid'].'代充');

                    if($fan){
                        $fan = jiaqian($_NPOST['uid'],4,0,$_NPOST['jine'],0,$userdata['name'].'代充');

                    }else return apptongxin($SHUJU,415,-1,'操作失败，请稍后重试',$YZTOKEN,$WY);

                }else return apptongxin($SHUJU,415,-1,'房卡不足，请先充值',$YZTOKEN,$WY);
                
            }else if((int)$_NPOST['type'] == 2){
    
                $fan = qunzhujia($_NPOST['uid'],$USERID,4,$_NPOST['jine'],$userdata['name'].'代充');
    
            }

            if($fan){
                $huobi = 0;
                
                $SHUJU = array(
                    'uid'=>$_NPOST['uid'],
                    'huobi'=>huobi($_NPOST['uid'],$USERID),
                    'jifen'=>$fan['jifen'],
                );
             
                return apptongxin($SHUJU,200,1,'操作成功',$YZTOKEN,$WY);
    
            }else return apptongxin($SHUJU,415,-1,'操作失败，请稍后重试',$YZTOKEN,$WY);

        }else return apptongxin($SHUJU,415,-1,'该用户还不是你的群成员',$YZTOKEN,$WY);

    }else return apptongxin($SHUJU,415,-1,'你还不是群主',$YZTOKEN,$WY);

}else if($MOD == 'delete'){
    /*是否同意入群申请*/

    $hash = 'joinrequest/'.$USERID;
    $DATA = $Mem -> g($hash);
   
    if($DATA){
        if(isset($_NPOST['type']) && isset($_NPOST['requestuid'])){
            $key = array_search($_NPOST['requestuid'],$DATA);
            if($key !== null && $key !== false){
 
                if($_NPOST['type'] == 1){  //同意

                    $requsetdata = $D -> zhicha('uid,tuid,qunjihe') ->where(array('uid'=>$_NPOST['requestuid'])) -> find();

                    $qunarr = explode(';',$requsetdata['qunjihe']);
                    $qunarr[] = $USERID.',0';
                    $qunjihe = implode(';',$qunarr);

                    $fan = $D -> where(array('uid'=>$_NPOST['requestuid'])) -> update(array('qunjihe'=>$qunjihe));

                    if($fan){
                    
                        unset($DATA[$key]);
                        
                        $Mem -> s($hash,$DATA);

                        return apptongxin($SHUJU,200,1,'同意申请成功',$YZTOKEN,$WY);
                    }else return apptongxin($SHUJU,415,-1,'同意申请失败，请稍后重试',$YZTOKEN,$WY);
                }else{      //拒绝

                    unset($DATA[$key]);
                        
                    $Mem -> s($hash,$DATA);
                    return apptongxin($SHUJU,200,1,'拒绝申请成功',$YZTOKEN,$WY);
                }
            }            
        }else{
            $key = key($DATA);
            $requsetdata = $D -> zhicha('uid,tuid,name,huobi,jifen,touxiang') ->where(array('uid'=>$DATA[$key])) -> find();
            $requsetdata['touxiang'] = pichttp($requsetdata['touxiang']);
            $SHUJU = $requsetdata;
        }
    }
}



return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);