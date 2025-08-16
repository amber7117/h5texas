<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

$D = db('dingdan');

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

            $WHERE['paytype'] = $_NPOST['level'];
        }

        if( isset($_NPOST['tuid']) && $_NPOST['tuid'] != '' ){

            $WHERE['uid'] = $_NPOST['tuid'];
            $WHERE['shid OR'] = $_NPOST['tuid'];
        }

        if( isset($_NPOST['soso']) && $_NPOST['soso'] != '' ){

            $WHERE['orderid'] = $_NPOST['soso'];
            $WHERE['tongyiid OR'] = $_NPOST['soso'];
            $WHERE['xiorderid OR'] = $_NPOST['soso'];
            
        }
        dingguoqi();



        $DATA = $D ->zhicha('id,orderid,uid,shid,payjine,atime,off,ip,paytype,bizhong')-> where($WHERE) ->limit($limit) ->order('id desc') -> select();

        if($DATA){

            $CODE = 1;
            $STAT = 200;
            $SHUJU['data'] = $DATA;

        }else{

            $CODE = -1;

        }

        /*货币*/

        $SHUJU['bizhong'] = array($CONN['jine'],$CONN['jifen'],$CONN['huobi']);
        $SHUJU['payoff'] = logac('payoff');
        $SHUJU['paytype'] = xitongpay( -1 );




    }else{

        /*读取一条数据*/

        $TOKEN = isset($_NPOST['ttoken'])?$_NPOST['ttoken']:"";

        if($TOKEN == '' || $sescc['token'] !=  $TOKEN){

            $YZTOKEN = token();
            sescc('token',$YZTOKEN,$UHA);
            return apptongxin($SHUJU,415,-1,'token错误',$YZTOKEN,$WY);

        }

        $SHUJU = $D ->where(array('id' => $ID ))-> find();

        if(!$SHUJU ){

            return apptongxin($SHUJU,415,-1,'编辑ID错误',$YZTOKEN,$WY);
        }

        $uidd = uid($SHUJU['uid']);

        if($uidd){

            $SHUJU['name'] = $uidd['name'];
            $SHUJU['shouji'] = $uidd['shouji'];
            $SHUJU['zhanghao'] = $uidd['zhanghao'];
        
        }else{
            
            $SHUJU['name'] = '未知';
            $SHUJU['shouji'] = '未知';
            $SHUJU['zhanghao'] = '帐号';
        }


        $uidd = uid($SHUJU['shid']);

        if($uidd){

            $SHUJU['sname'] = $uidd['name'];
            $SHUJU['sshouji'] = $uidd['shouji'];
            $SHUJU['szhanghao'] = $uidd['zhanghao'];
        
        }else{
            
            $SHUJU['sname'] = '未知';
            $SHUJU['shouji'] = '未知';
            $SHUJU['zhanghao'] = '帐号';
        }


        


    }



}else if($MOD == 'post'){
    /*新增数据*/

}else if($MOD == 'put'){
    /*修改数据*/

}else if($MOD == 'delete'){
    /*删除数据*/

}



return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);