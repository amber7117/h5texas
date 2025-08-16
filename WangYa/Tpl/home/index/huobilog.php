<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

if( $USERID < 1){

    return apptongxin($SHUJU,415,-99,"no login",$YZTOKEN,$WY);
}

$D  = db('huobilog');

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

        $DATA = $D -> where($WHERE) ->limit($limit)->order('id desc') -> select();

        if($DATA){

            $CODE = 1;
            $STAT = 200;

            $SHUJU['type'] = logac('huobilog');

            foreach ($DATA as $k=>$v){

                if( empty($v['data']) ) $DATA[$k]['data'] = $SHUJU['type'][$v['type']];

                $DATA[$k]['type'] = $SHUJU['type'][$v['type']];
                $DATA[$k]['atime'] = date("m-d H:i", $v['atime']);
            }

           
            $SHUJU['data'] = $DATA;


        }else{

            $CODE = -1;
            $SHUJU['data'] = [];
            $MSG = '没有更多数据';
        }

        if(!$SHUJU || $SHUJU == array()){
            return apptongxin($SHUJU,200,-1,"记录中暂无更多数据",$YZTOKEN,$WY);
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