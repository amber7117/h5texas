<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');


$D  = db('fanglist');

if($MOD == 'get'){
    /*获取数据*/

    if( $USERID < 1){

        return apptongxin($SHUJU,415,-99,"no login",$YZTOKEN,$WY);
    }


    $ID = (int)(isset($_NPOST['id'])?$_NPOST['id']:0);

    if($ID < 1){

        /*小于1 多条数据*/

        $NUM = (int)(isset($_NPOST['num'])?$_NPOST['num']:10);
        $PAG = (int)(isset($_NPOST['pg'])?$_NPOST['pg']:1);

        if($NUM < 8){
            
            $NUM = 8;
        }

        if($NUM > 100){

            $NUM = 100;
        }

        $WHERE = array('userin LIKE' => '%,'.$USERID.',%');

        $limit = listmit( $NUM , $PAG);




        $DATA = $D ->zhicha('id,gameid,fangid,atime,zqishu,qishu,off,userin')-> where($WHERE) ->limit($limit)->order('id desc') -> select();

        if($DATA){

            $CODE = 1;
            $STAT = 200;
            $SHUJU['data'] = $DATA;
            $SHUJU['game'] = Game_List(1);
         

        }else{

            $CODE = -1;
        }

        $YZTOKEN = token();
        sescc('token',$YZTOKEN,$UHA);




    }else{

        /*读取一条数据*/

        $YZHost = 'weiyi/'.md5('get'.$USERID);
        $cuzai = $Mem ->g($YZHost);

        if($cuzai){

            return apptongxin(array(),415,$CODE,'请不要重复提交',$YZTOKEN,$WY);
        }

        $Mem ->s($YZHost,1,1);

        $TOKEN = isset($_NPOST['ttoken'])?$_NPOST['ttoken']:"";

        if($TOKEN == '' || $sescc['token'] !=  $TOKEN){

            $YZTOKEN = token();
            sescc('token',$YZTOKEN,$UHA);
            return apptongxin($SHUJU,415,-1,'token错误',$YZTOKEN,$WY);

        }

        $YZTOKEN = token();
        sescc('token',$YZTOKEN,$UHA);

        $DATA = $D ->where(array('id' => $ID))-> find();

        if(!$DATA ){

            return apptongxin($SHUJU,415,-1,'查询ID错误',$YZTOKEN,$WY);
        }


       
        

        if( strpos( $DATA['userin'] , ','.$USERID.',' ) === false){
        
            return apptongxin($SHUJU,415,-1,'非法查询,你没有游戏',$YZTOKEN,$WY);

        }

        $gameid = $DATA['gameid'];

        $fangid = $DATA['fangid'];

        $FJJLU = $D -> setbiao('gamejiu') ->where(array('gameid' => $gameid, 'fangid' => $fangid)) ->select();

        if(!$FJJLU){


            return apptongxin($SHUJU,415,-1,'房间记录不存在',$YZTOKEN,$WY);
        
        }

        $SSSS = array();

        foreach($FJJLU as $kk){


            $gggg = unserialize($kk['neirong']);


            $GAMESS = array(
                'userinfo' => $gggg['Auserinfo'],
                'tongji' =>  $gggg['Atongji'],
                'alltongji' =>  $gggg['Aalltongji'],
            );

            $USs = array(
                
                'dju' => $gggg['Atongji'][$USERID],
                'zju' => $gggg['Aalltongji'][$USERID]
            
            );
            $SSSS[] = array(

                'qishu' => $kk['qishu'],
                'main' => $USs,
                'games' => $GAMESS
                
                
            
            );
        
        
        }

        $SHUJU['data'] = $SSSS;
        $SHUJU['uid'] = $USERID;

    }



}else if($MOD == 'post'){

    if( $USERID < 1){

        return apptongxin($SHUJU,415,-99,"no login",$YZTOKEN,$WY);
    }
    /*获取群列表*/

    // $YZHost = 'weiyi/put'.md5($USERID);
    // $cuzai = $Mem ->g($YZHost);

    // if($cuzai){

    //     $Mem ->s($YZHost,1,1);
    //     return apptongxin(array(),415,$CODE,'请不要重复提交',$YZTOKEN,$WY);
    // }

    // $Mem ->s($YZHost,1,1);

    $USER = uid($USERID,1);
    
    $qunzu = explode(',',$USER['hbqun']);
    $QUNARR = array();

    foreach($qunzu as $k=>$v){
        if($v != '' && $v !== false && $v != null){
            
            $arr = array();

            $leisetArr = logac('leiset',1);

            $allset = array();
            $beiset = array();
            foreach($leisetArr as $vv){

                $detail = explode('_',$vv);

                if($detail){
                    
                    $allset[$detail[0]] = $detail[0];
                    $beiset[$detail[0]] = $detail[2];
                }
            }

            sort($allset);
            $baoname = '';

            foreach($allset as $vv){
                $baoname .= $vv.'-';
            }

            $baoname = rtrim($baoname,'-');

            if((int)$v == 1){       //系统随机群

                $arr = array(
                    'qunname' => $CONN['apkhbminmailei'].'-'.$CONN['apkhbmaxmailei'].'固定'.$baoname.'包('.current($allset).'包'.current($beiset).'倍)',
                    'roomid' => $v,
                    'gameid' => 'apkhongbaoJ'
                );

            }else if((int)$v == 2){     //系统福利群

                $arr = array(
                    'qunname' => str_replace('_','-',$CONN['apkfltime']).'整点福利(可提前在房间内等待)',
                    'roomid' => $v,
                    'gameid' => 'apkhongbaoJ'
                );

            }else if((int)$v == 3){     //系统牛牛群

                $arr = array(
                    'qunname' => '牛牛群固定5包',
                    'roomid' => $v,
                    'gameid' => 'apkhongbaoJ'
                );

            }else{      //玩家创建群

                $gamedata = db('gamejiu') -> where(array('fangid' => $v,'gameid' => 'apkhongbao')) -> find();

                $gamedata['neirong'] = $gamedata['neirong'] != '' ? unserialize($gamedata['neirong']):$gamedata['neirong'];

                $GAME = $gamedata['neirong'];

                if(!$GAME) continue;

                $fangzhu = uid($GAME['isfguan']);
                $roomname = $fangzhu['name'].((int)$GAME['roomtype'] == 2?'群主免死':'').$baoname.'包('.current($allset).'包'.current($beiset).'倍)'.implode('-',$GAME['Afabaojine']).'房:'.$v;

                $arr = array(
                    'qunname' => $roomname,
                    'roomid' => $v,
                    'gameid' => $gamedata['gameid']
                );
            }

            $QUNARR[] = $arr;
            
        }
    }

    $SHUJU = $QUNARR;

}else if($MOD == 'put'){
    
    if( $USERID < 1){

        return apptongxin($SHUJU,415,-99,"no login",$YZTOKEN,$WY);
    }
    
    $fan = daili_count($USERID,$CONN['tuiji'],'apkhongbao');
 
    if(!$fan){
        return apptongxin(array(),415,$CODE,'数据错误',$YZTOKEN,$WY);
    }

    $SHUJU = array();

    $SHUJU['daili'] = $fan;

    $where = db('huobilog') -> wherezuhe(array('type' => 18,'uid' => $USERID));

    $sql = 'SELECT SUM(jine) as "TotalGet" FROM ay_huobilog '.$where;

    $Total = db('huobilog') -> qurey($sql);

    $SHUJU['allyongjin'] = $Total['TotalGet']?$Total['TotalGet']:0;

    $todayStart = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
    $todayEnd = mktime(23, 59, 59, date('m'), date('d'), date('Y'));

    $beginYesterday=mktime(0,0,0,date('m'),date('d')-1,date('Y'));
    $endYesterday=mktime(0,0,0,date('m'),date('d'),date('Y'))-1;

    $where = db('huobilog') -> wherezuhe(array('atime >' => $todayStart,'atime <=' => $todayEnd,'type'=>18,'uid'=>$USERID));

    $sql = 'SELECT SUM(jine) as "todayGet" FROM ay_huobilog '.$where;

    $todayTotal = db('huobilog') -> qurey($sql);

    $SHUJU['todayGet'] = $todayTotal['todayGet']?$todayTotal['todayGet']:0;

    $where = db('huobilog') -> wherezuhe(array('atime >' => $beginYesterday,'atime <=' => $endYesterday,'type'=>18,'uid'=>$USERID));
    $sql = 'SELECT SUM(jine) as "yesterdayGet" FROM ay_huobilog '.$where;

    $yesterdayTotal = db('huobilog') -> qurey($sql);
    $SHUJU['yesterdayGet'] = $yesterdayTotal['yesterdayGet']?$yesterdayTotal['yesterdayGet']:0;

    $SHUJU['canget'] = CanGet($SHUJU['yesterdayGet'],$USERID);
    
    $USERDATA = db('user') -> where(array('uid' => $USERID)) -> find();

    if(!$USERDATA){
        return apptongxin(array(),415,$CODE,'找不到用户数据',$YZTOKEN,$WY);
    }

    $SHUJU['yongjin'] = $USERDATA['yongjin'];

    $SHUJU['huobi'] = $USERDATA['huobi'];

}else if($MOD == 'delete'){
    /*删除数据*/
    $SHUJU = array();

    $SHUJU['apkhblogo'] = pichttp($CONN['apkhblogo']);

}



return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);