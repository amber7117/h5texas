<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

$D  = db('huobilog');

if($MOD == 'get'){
    /*获取数据*/

    $ID = (int)(isset($_NPOST['id'])?$_NPOST['id']:0);

    if($ID < 1){


        $WHERE = array();


        $DATA = [];

        $CODE = 1;
        $STAT = 200;

        $Jtime = mktime(0,0,0,date('m'),date('d'),date('Y'));

        $type = logac('huobilog');

        for ($i=0;$i<count($type);$i++){

            if( (isset($_NPOST['tuid']) && $_NPOST['tuid'] != '') || (isset($_NPOST['jtime']) && $_NPOST['jtime'] != '') || (isset($_NPOST['ktime']) && $_NPOST['ktime'] != '') ){

                $sql = "select SUM(jine) as num from ay_huobilog WHERE `type` = ".$i;

                if( isset($_NPOST['ktime']) && $_NPOST['ktime'] != '' ){

                    $WHERE['atime >='] = strtotime($_NPOST['ktime']);
                    $sql .= " and atime >= ".strtotime($_NPOST['ktime']);
                }

                if( isset($_NPOST['jtime']) && $_NPOST['jtime'] != '' ){

                    $WHERE['atime <='] = strtotime($_NPOST['jtime']);
                    $sql .= " and atime <= ".strtotime($_NPOST['jtime']);

                }

                if( isset($_NPOST['tuid']) && $_NPOST['tuid'] != '' ){

                    $sql .= " and uid = ".$_NPOST['tuid'];

                }


            }else{

                $sql = "select SUM(jine) as num from ay_huobilog WHERE `type` = ".$i;

            }
            $jine = $D -> query($sql,'other');
            $num = isset($jine['num'])?$jine['num']:0;

            $DATA[$i] = $num;

        }

        $SHUJU['data'] = [$DATA];
        $SHUJU['type'] = $type;



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