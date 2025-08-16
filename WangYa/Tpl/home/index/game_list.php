<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');


$D = db('gamelist');

if($MOD == 'get'){


    $DATA = $D ->order('gl_rank desc') -> select();

    if($DATA){

        $CODE = 1;
        $STAT = 200;

        foreach ($DATA as $k => $v ){
            if(!empty($v['gl_imgurl'])){
                $DATA[$k]['gl_imgurl'] = 'http://'.$_SERVER['HTTP_HOST'].$v['gl_imgurl'];
                if(empty($v['gl_gameurl'])){
                    $DATA[$k]['gl_gameurl'] = '#';
                }
            }
            if(!empty($v['gl_tupian'])){
                $DATA[$k]['gl_tupian'] = 'http://'.$_SERVER['HTTP_HOST'].$v['gl_tupian'];
            }
        }

        $SHUJU['data'] = $DATA;

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