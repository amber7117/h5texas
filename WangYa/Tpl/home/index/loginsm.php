<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');


if( $MOD == 'get' ){

    $CODE = isset($_NPOST['ttoken'])?$_NPOST['ttoken']:'';
     
    if(strlen($CODE) == 32 ){

        $HASH = 'kjdenglu/'.mima( $CODE );

        $wode = $Mem -> g($HASH);

        if( $USERID > 0){

            /*用户绑定数据*/
            $Mem -> s( $HASH.'G',$SESSIONID , 120 );
        }

        if( $wode ){

            /*存在值的时候*/
           
            if( $wode > 0 ){

                sescc(array('uid' => $wode,'ip' => IP() ),'',$UHA);
                $STAT = 200;
                $CODE = 2;

            }else{
            
                $CODE  = -1;
            }

            $Mem -> d($HASH);
                
        }else{

            /*等待验证*/
            $CODE = 1;
        }


    }else{
    

        return apptongxin(array(),415,-1,'CODE错误',$YZTOKEN,$WY);
    }


}else if($MOD == 'post'){
    /*新增数据*/

}else if($MOD == 'put'){
    /*修改数据*/

}else if($MOD == 'delete'){
    /*删除数据*/

}



return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);