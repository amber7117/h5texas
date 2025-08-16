<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

$D  = db('fanglist');

if($MOD == 'get'){
    /*获取数据*/

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

        
        $WHERE = array();

        $limit = listmit( $NUM , $PAG);

        $YZTOKEN = token();

        sescc('token',$YZTOKEN,$UHA);

        if( isset($_NPOST['level']) && $_NPOST['level'] != '' && $_NPOST['level'] > -1 ){

            $WHERE['gameid'] = $_NPOST['level'];
        }

        if( isset($_NPOST['tuid']) && $_NPOST['tuid'] != '' ){

            $WHERE['userin LIKE'] = ','.$_NPOST['tuid'].',%';
        }

        if( isset($_NPOST['soso']) && $_NPOST['soso'] != '' ){

            $WHERE['fangid'] = $_NPOST['soso'];

        }

        /*一天都没有完成的直接关闭*/

        $times = time()-3600*5;

        $D ->where( array('off' => '0' ,'atime <'=>$times))-> update(array('stime' => time(),'off' =>2));

        $DATA = $D ->zhicha('id,gameid,fangid,uid,atime,off')-> where($WHERE) ->limit($limit)->order('id desc') -> select();

        if($DATA){

            

            $CODE = 1;
            $STAT = 200;
            $SHUJU['data'] = $DATA;

        }else{

            $CODE = -1;

        }

        $SHUJU['gameoff'] = logac('gameoff'); 
        $SHUJU['gamelist'] = Game_List();

    }else{

        /*读取一条数据*/
        $TOKEN = isset($_NPOST['ttoken'])?$_NPOST['ttoken']:"";

        if($TOKEN == '' || $sescc['token'] !=  $TOKEN){

            $YZTOKEN = token();
            sescc('token',$YZTOKEN,$UHA);
            return apptongxin($SHUJU,415,-1,'token错误',$YZTOKEN,$WY);

        }

        $DATA = $D ->where(array('id' => $ID ))-> find();

        $YZTOKEN = token();
        sescc('token',$YZTOKEN,$UHA);

        if(!$DATA ){

            return apptongxin($SHUJU,415,-1,'编辑ID错误',$YZTOKEN,$WY);
        }
        
        $DATA['neirong'] = $DATA['neirong'] != '' ? unserialize($DATA['neirong']):$DATA['neirong'];
        $SHUJU = $DATA;
    }



}else if($MOD == 'post'){
    /*新增数据*/

}else if($MOD == 'put'){
    /*修改数据*/

}else if($MOD == 'delete'){
    /*删除数据*/

    $ID = (int)(isset($_NPOST['id'])?$_NPOST['id']:0);

    if($ID < 1){

        $YZTOKEN = token();
        sescc('token',$YZTOKEN,$UHA);
        return apptongxin($SHUJU,404,-1,$MSG,$YZTOKEN,$WY);
    
    }

    $TOKEN = isset($_NPOST['ttoken'])?$_NPOST['ttoken']:"";

    if($TOKEN == '' || $sescc['token'] !=  $TOKEN){

        $YZTOKEN = token();
        sescc('token',$YZTOKEN,$UHA);
        return apptongxin($SHUJU,415,-1,'token错误',$YZTOKEN,$WY);

    }

    $DATAS = $D -> where( array('id' => $ID ) ) -> find();

    $YZTOKEN = token();
    sescc('token',$YZTOKEN,$UHA);

    if(!$DATAS){

        
        return apptongxin($SHUJU,404,-1,'数据不存在',$YZTOKEN,$WY);
    }

    if($DATAS['off'] == '0'){
        /*还在游戏中 通信游戏服务删除东西*/

            $SERVERIP = Game_Server($DATAS['gameid']);

            $IP = fenpeiip($DATAS['fangid'],$SERVERIP);

            if(!$IP){

                return apptongxin($SHUJU,415,-1,"游戏服务器通信失败",$YZTOKEN,$WY);
            }

            $usesuju = array(

                'y'=>'fangexit',
                'd' => $DATAS['fangid']
            );

            $ganeid = true;
            $fan = httpudp($usesuju,$IP['ip'],$IP['port']);

            if(!$fan || $fan['code'] == '-1')  $ganeid =  false;

            if(!$ganeid ){

                return apptongxin($SHUJU,415,-1,"游戏通信服务器数据失败",$YZTOKEN,$WY);
            }

            $Mem -> d($DATAS['gameid'] .'/'. $DATAS['fangid']);
            
            $uuu = explode(',',$DATAS['userin']);

            if( $uuu ){

                foreach($uuu as $gege){
                    if($gege == '')continue;

                    $USERONLINE = $Mem -> g( 'gameuid/'.$gege);

                    if( $USERONLINE ){

                        if( $USERONLINE['gid'] == $DATAS['gameid'] &&  $USERONLINE['fangid'] == $DATAS['fangid'] )

                        $Mem -> d( 'gameuid/'.$gege);
                    }
                }
            }

    }

    /*直接删除房间数据*/
    $fan = $D -> where(array('id' => $ID ))->delete();

    if( $fan ){

        $STAT = 200;
        $CODE = 1;
        
        $gamejiu = $D ->setbiao('gamejiu')->where(array( 'gameid' => $DATAS['gameid'] ,'fangid'=> $DATAS['fangid'] )) -> select();


        $D ->where(array( 'gameid' => $DATAS['gameid'] ,'fangid'=> $DATAS['fangid'] )) -> delete();

        adminlog($sescc['aid'], 4 , serialize( array( 'ac' => $AC , 'mo' => $MOD , 'id'=> $ID,'yuan'=> $DATAS,'gamejiu' => $gamejiu )));

    }else{
     
        return apptongxin($SHUJU,410,-1,"删除失败?",$YZTOKEN,$WY);
    }

}

return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);