<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

if( $USERID < 1){

    return apptongxin($SHUJU,415,-99,"no login",$YZTOKEN,$WY);
}

$USER = uid($USERID);


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

       
        $WHERE = array('tuid' => $USERID,'tuid1 OR'=> $USERID,'tuid2 OR'=> $USERID);

        $limit = listmit( $NUM , $PAG);

        $D =db('user');



        $DATA = $D ->zhicha('name,touxiang,atime,appid,uid,tuid,tuid1,tuid2,xingbie')-> where($WHERE)->order('uid desc') ->limit($limit) -> select();

        if($DATA){

            $CODE = 1;
            $STAT = 200;

            foreach($DATA as $k=>$v){

                $v['touxiang'] = touxiang($v['touxiang']);
                if($v['tuid']== $USERID){
                    $v['j'] = 1;
                }else if($v['tuid1']== $USERID){
                    $v['j'] = 2;
                }else if($v['tuid2']== $USERID){
                    $v['j'] = 3;
                }
                
                $DATA[$k] = $v;
            }

            $SHUJU = $DATA;

        }else{

            $CODE = -1;

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