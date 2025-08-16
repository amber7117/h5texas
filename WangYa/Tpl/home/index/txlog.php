<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

//$USERID = $_GET['uid'];

if( $USERID < 1){

    return apptongxin($SHUJU,415,-99,"no login",$YZTOKEN,$WY);
}

$D  = db('tixiandingdan');

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

        $WHERE = array('uid' => $USERID);

        $DATA = $D -> where($WHERE) ->limit($limit)->order('time desc') -> select();

        if($DATA){

            $CODE = 1;
            $STAT = 200;


            foreach ($DATA as $k=>$v){

                if((int)$v['state'] == -1){

                    $str = '提现失败';

                }else if((int)$v['state'] == 0){

                    $str = '生成订单';

                }else if((int)$v['state'] == 1){

                    $str = '提现成功';

                }else if((int)$v['state'] == 2){

                    $str = '审核中';

                }else if((int)$v['state'] == 3){

                    $str = '订单过期';

                }

                if($v['type'] == 1){
                    $DATA[$k]['type'] = '金币:'.$str;

                }else{
                    $DATA[$k]['type'] = '佣金:'.$str;
                }

                $DATA[$k]['time'] = date("m-d h:i", $v['time']);
                $DATA[$k]['atime'] = date("Y-m-d h:i:s", $v['time']);
            }

            $SHUJU['data'] = $DATA;

        }else{

            $CODE = -1;
            $MSG = '没有更多数据';
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