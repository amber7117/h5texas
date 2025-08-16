<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

$D = db('tixiandingdan');

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

        if( isset($_NPOST['uid']) && $_NPOST['uid'] != '' ){

            $WHERE['uid'] = (int)$_NPOST['uid'];

        }

        if( isset($_NPOST['type']) && $_NPOST['type'] != '' && $_NPOST['type'] > -1 ){

            $WHERE['type'] = $_NPOST['type'] + 1;
        }

        if( isset($_NPOST['state']) && $_NPOST['state'] != '' && $_NPOST['state'] > -1 ){

            $WHERE['state'] = (int)$_NPOST['state'] - 1;
        }

        tixianguoqi();

        $DATA = $D -> where($WHERE) ->limit($limit) ->order('time desc') -> select();

        if($DATA){

            foreach($DATA as $k=>$v){

                $DATA[$k]['ttime'] = date('Y-m-d H:i:s',substr($v['time'],0,10));

                if((int)$v['state'] == -1){

                    $DATA[$k]['state'] = '提现失败';

                }else if((int)$v['state'] == 0){

                    $DATA[$k]['state'] = '生成订单';

                }else if((int)$v['state'] == 1){

                    $DATA[$k]['state'] = '提现成功';

                }else if((int)$v['state'] == 2){

                    $DATA[$k]['state'] = '审核中';

                }else if((int)$v['state'] == 3){

                    $DATA[$k]['state'] = '订单过期';

                }
            }
            $CODE = 1;
            $STAT = 200;
            $SHUJU['data'] = $DATA;

        }else{

            $CODE = -1;

        }

        /*货币*/

        $SHUJU['bizhong'] = array($CONN['huobi'],$CONN['yongjin']);

        $SHUJU['tixianstate'] = array('提现失败','生成订单','提现成功','审核中','订单过期');   //0	生成订单	1:提现成功   -1:提现失败   2:审核中	 3:订单过期


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