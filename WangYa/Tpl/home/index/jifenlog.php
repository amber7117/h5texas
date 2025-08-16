<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

if( $USERID < 1){

    return apptongxin($SHUJU,415,-99,"no login",$YZTOKEN,$WY);
}

$D  = db('jifenlog');

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

        
        $WHERE = array('uid' => $USERID);

        $limit = listmit( $NUM , $PAG);


       



        $DATA = $D -> where($WHERE) ->limit($limit)->order('id desc') -> select();

        if($DATA){

            $CODE = 1;
            $STAT = 200;
            
            $Mdata = array();

            $MUID = array();


            $SHUJU['data'] = $DATA;
            $SHUJU['type'] = logac('jifenlog');

        }else{

            $CODE = -1;
        }

    }else{

        /*读取一条数据*/
    
    
    
    }



}else if($MOD == 'post'){
    /*新增数据*/

}else if($MOD == 'put'){
    /*修改数据*/

}else if($MOD == 'delete'){
    /*删除数据*/

}



return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);