<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

if( $USERID < 1){

    return apptongxin($SHUJU,415,-99,"no login",$YZTOKEN,$WY);
}

$USER = uid($USERID);

if($USER['level'] < 1){

    return apptongxin($SHUJU,200,-3,"不是代理",$YZTOKEN,$WY);
}

if($MOD == 'get'){
    /*获取数据*/




    /*小于1 多条数据*/

    $NUM = (int)(isset($_NPOST['num'])?$_NPOST['num']:10);
    $PAG = (int)(isset($_NPOST['pg'])?$_NPOST['pg']:1);

    if($NUM < 8){
        
        $NUM = 8;
    }

    if($NUM > 100){

        $NUM = 100;
    }

   
    $WHERE = array('shid' => $USERID ,'off' => 2 );

    $limit = listmit( $NUM , $PAG);

    $D =db('dingdan');

    $DATA = $D ->zhicha('id,uid,jjine,rejine,xingming,shouhuo,ip,atime')-> where($WHERE)->order('id desc') ->limit($limit) -> select();

    if($DATA){

        $SHUJU = $UUU = array();
        $LEVEL = logac('level');


        foreach( $DATA as $ONG ){

            if( !isset(  $UUU[$ONG['uid']] )){

                $u = uid($ONG['uid']);

                if($u) $UUU[$ONG['uid']] = $u['name'];
                else $UUU[$ONG['uid']] =  '未知';
            }

            $ddfc = array(
                'uid' => $ONG['uid'], /*充值的uid*/
                'id' => $ONG['id'],
                'name' => $UUU[$ONG['uid']],
                'fenyong' => $ONG['jjine'],
                'rejine' => $ONG['rejine'],
                'fenlv' => $ONG['xingming'] <= 0 ?0:$ONG['xingming']*100,
                'dengjin' => $LEVEL[(int)$ONG['shouhuo']],
                'ip' => $ONG['ip'],
                'atime' =>$ONG['atime']
            );

            $SHUJU[] = $ddfc;
        }

    }else{

         $CODE = -1;
    
    }

}else if($MOD == 'post'){
    /*新增数据*/

}else if($MOD == 'put'){
    /*修改数据*/

}else if($MOD == 'delete'){
    /*删除数据*/

}



return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);