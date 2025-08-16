<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');
/*
    房费扣除
    游戏数据
    用户充值
    管理操作
    代理代充
    给用户充
    注册赠送
    绑定推广
    签到奖励
    大转盘奖励
    大转盘消耗
    推广计费
    Airobt 
*/

$D  = db('admin');



if($MOD == 'get'){
    /*获取数据*/

    $ID = (int)(isset($_NPOST['id'])?$_NPOST['id']:0);

    $CODE = 1;
    $STAT = 200;

    $YZTOKEN = token();
    sescc('token',$YZTOKEN,$UHA);
    $SHUJU = array($CONN['yongjin'],$CONN['integral'],$CONN['huobi']);


    



}else if($MOD == 'post'){
    /*新增数据*/

}else if($MOD == 'put'){
    /*修改数据*/

    $TOKEN = isset($_NPOST['ttoken'])?$_NPOST['ttoken']:"";

    if($TOKEN == '' || $sescc['token'] !=  $TOKEN){

        $YZTOKEN = token();
        sescc('token',$YZTOKEN,$UHA);
        return apptongxin($SHUJU,415,-1,'token错误',$YZTOKEN,$WY);

    }


    $YZTOKEN = token();
    sescc('token',$YZTOKEN,$UHA);

    $UID    = (int)(isset($_NPOST['uid'])?$_NPOST['uid']:0);
    $HUOBI  = (int)(isset($_NPOST['huobi'])?$_NPOST['huobi']:0);
    $JINE  = (float)(isset($_NPOST['jine'])?$_NPOST['jine']:0);

    rizhi( 'jinjia',$JINE );

    $ujj = uid($UID,1);

    if(!$ujj){

        return apptongxin($SHUJU,415,-1,'用户不存在',$YZTOKEN,$WY);

    }

    if( $JINE > 100000000000){
    

        return apptongxin($SHUJU,415,-1,'非法数量,格式错误[ 100000000000 ]',$YZTOKEN,$WY);

    }else  if( $JINE <=0){

        if($HUOBI == '0'){

            $type = 'yongjin';

        }else if($HUOBI == '2'){

            $type = 'huobi';

        }else if($HUOBI == '1'){

            $type = 'integral';

        }

        $num = db('user')->zhicha($type)->where(['uid'=>$UID])->find();

        if(abs($JINE) > $num[$type]){
            return apptongxin($SHUJU,415,-1,'余额不足、该用户的余额为：'.$num[$type],$YZTOKEN,$WY);
        }
    }

    $SHUJU = array($CONN['yongjin'],$CONN['integral'],$CONN['huobi']);

    if(! isset($SHUJU[$HUOBI] )){

        return apptongxin(array() ,415,-1,'非法更改类型',$YZTOKEN,$WY);
    
    }

    $cHUOBI = $cJIFEN = $integral = $cJINE = $yongjin = 0;
    
    if($HUOBI == '0'){

        $yongjin = $JINE;
    
    }else if($HUOBI == '2'){

        $cHUOBI = $JINE;
    
    }else if($HUOBI == '1'){

        $integral = $JINE;
    
    }

    $fan = jiaqian( $UID , 3 , $cJINE,$cJIFEN , $cHUOBI,'','',$yongjin,0,$integral);

    if( $fan ){


        $SHUJU = array( 'uid' => $UID ,'name' =>  $ujj['name'] );

        adminlog($sescc['aid'], 3 , serialize( array( 'ac' => $AC , 'mo' => $MOD ,'yuan'=> $ujj , 'data'=> $_POST )));

        return apptongxin($SHUJU,200,1,'更改成功',$YZTOKEN,$WY);


    
    }else{
        
        return apptongxin($SHUJU,415,-1,'更改失败',$YZTOKEN,$WY);
    
    }





    



}else if($MOD == 'delete'){
    /*删除数据*/

}



return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);