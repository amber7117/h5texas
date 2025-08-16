<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

if( $USERID < 1){

   
    return apptongxin(array(),415,-1,'没登陆',$YZTOKEN,$WY);
}

if( $MOD == 'get' ){

    $CODE = isset($_NPOST['ttoken'])?$_NPOST['ttoken']:'';


    $YZHost = 'weiyi/'.md5('get'.$USERID);

    $cuzai = $Mem ->g($YZHost);

    if($cuzai){

        return apptongxin(array(),415,$CODE,'请不要重复提交',$YZTOKEN,$WY);
    }

    $Mem ->s($YZHost,1,1);

    if($CODE  != '' && strlen($CODE) > 5){


        $DD = db('dingdan');
        $wode = $DD ->zhicha('orderid,id,uid,off')->where(array( 'orderid' => $CODE ))-> find();

        if( $wode ){

            /*存在值的时候*/

            if($wode['uid'] != $USERID){
            
                return apptongxin(array(),415,-1,'CODE错误',$YZTOKEN,$WY);

            }else if($wode['off'] == 2){

                $CODE = 2;
            
            }else if($wode['off'] == 3){

                $CODE = -1;
            
            
            }else if($wode['off'] < 2){

                $CODE = 1;
            
            
            }
            

        }else{

            return apptongxin(array(),415,-1,'CODE错误',$YZTOKEN,$WY);
        }


    }else{
    

        return apptongxin(array(),415,-1,'CODE错误',$YZTOKEN,$WY);
    }


}else if($MOD == 'post'){
    /*新增数据*/

}else if($MOD == 'put'){
    /*修改数据*/
    $ID = isset($_NPOST['id'])?$_NPOST['id']:'';

    $YZHost = 'weiyi/put'.md5($USERID);

    $cuzai = $Mem ->g($YZHost);

    if($cuzai){

        $Mem ->s($YZHost,1,10);
        return apptongxin(array(),200,-1,'请不要重复查询',$YZTOKEN,$WY);
    }

    $Mem ->s($YZHost,1,10);


    if( $ID == '' ){


        return apptongxin(array(),200,-1,'订单不存在',$YZTOKEN,$WY);
    }
    $DD =db('dingdan');

    $wode = $DD ->zhicha('orderid,id,uid,off,rejine,payjine')->where(array( 'orderid' => $ID ))-> find();

    if($wode){

        if($wode['uid'] != $USERID ){
        
            return apptongxin(array(),415,-1,'非法订单',$YZTOKEN,$WY);
        }

        $SHUJU =$wode;
        $SHUJU['log'] = logac('payoff');


    
    }else{

        return apptongxin(array(),415,-1,'非法订单',$YZTOKEN,$WY);
    
    
    }


    


}else if($MOD == 'delete'){
    /*删除数据*/

}



return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);