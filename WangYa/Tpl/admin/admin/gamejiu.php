<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

$D  = db('gamejiu');

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

      

        if( isset($_NPOST['soso']) && $_NPOST['soso'] != '' ){

            $WHERE['fangid'] = $_NPOST['soso'];

        }

        /*一天都没有完成的直接关闭*/

       

        $DATA = $D ->zhicha('id,gameid,fangid,qishu,atime')-> where($WHERE) ->limit($limit)->order('id desc') -> select();

        if($DATA){

            

            $CODE = 1;
            $STAT = 200;
            $SHUJU['data'] = $DATA;

        }else{

            $CODE = -1;

        }
        
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

}

return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);