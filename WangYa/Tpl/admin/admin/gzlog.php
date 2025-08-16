<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

$D  = db('gzlog');


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

        if( isset($_NPOST['gz_uid']) && $_NPOST['gz_uid'] != '' ){

            $WHERE['gz_uid'] = $_NPOST['gz_uid'];
        }


        $DATA = $D -> where($WHERE) ->limit($limit)->order('gz_time desc') -> select();


        if($DATA){

            $CODE = 1;
            $STAT = 200;

            foreach ($DATA as $k => $v ){

                $DATA[$k]['gz_time'] = date('Y-m-d H:i:s',$v['gz_time']);

            }

            $Mdata = array();

            $MUID = array();

            foreach($DATA as $shuju){


                $chunam  = '未知';

                if(!isset($MUID[$shuju['gz_uid']])){

                    $uuuu = uid($shuju['gz_uid']);

                    if( $uuuu ){

                        $chunam  =$MUID[$shuju['gz_uid']] = $uuuu['name'];

                    }else{

                        $chunam  =$MUID[$shuju['gz_uid']] = '未知';
                    }
                }else{

                    $chunam  =$MUID[$shuju['gz_uid']];
                }

                $shuju['uname'] = $chunam;
                $Mdata[] = $shuju;

            }

            $SHUJU['data'] = $Mdata;

            if($WHERE){

                $where =  $D -> wherezuhe( $WHERE );
                $fanhui = $D -> qurey("select sum(gz_jine) num from `".$D->biao().'` '.$where);
                $SHUJU['jine'] =(float)$fanhui['num'];

            }



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