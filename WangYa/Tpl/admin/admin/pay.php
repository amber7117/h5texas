<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

$D  = db('pay');
$PANC = '已填写,录入即为修改';
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

        $DATA = $D ->zhicha('id,name,payfile,off,xianshi,isapp')-> where($WHERE) ->limit($limit)->order('paixu desc,id desc') -> select();

        if($DATA){

            $CODE = 1;
            $STAT = 200;
            $SHUJU['data'] = $DATA;

        }else{

            $CODE = -1;

        }

       

        $SHUJU['payyesno'] = logac('payyesno');

        $SHUJU['yesno'] = logac('yesno');
        $SHUJU['payfile'] = $LANG['payfile'];




    }else{

        /*读取一条数据*/
        $TOKEN = isset($_NPOST['ttoken'])?$_NPOST['ttoken']:"";

        if($TOKEN == '' || $sescc['token'] !=  $TOKEN){

            $YZTOKEN = token();
            sescc('token',$YZTOKEN,$UHA);
            return apptongxin($SHUJU,415,-1,'token错误',$YZTOKEN,$WY);

        }

        $DATA = $D ->where(array('id' => $ID ))-> find();

        if(!$DATA ){

            return apptongxin($SHUJU,415,-1,'编辑ID错误',$YZTOKEN,$WY);
        }

        if($DATA['paykey'] != ''){
        
            $DATA['paykey']= $PANC;
        }

        $SHUJU = $DATA;
    
    
    
    }



}else if($MOD == 'post'){
    /*新增数据*/

    $TOKEN = isset($_NPOST['ttoken'])?$_NPOST['ttoken']:"";

    if($TOKEN == '' || $sescc['token'] !=  $TOKEN){

        $YZTOKEN = token();
        sescc('token',$YZTOKEN,$UHA);
        return apptongxin($SHUJU,415,-1,'token错误',$YZTOKEN,$WY);

    }

    $YZTOKEN = token();
    sescc('token',$YZTOKEN,$UHA);

    $_NPOST['suoluetu'] =  TOU_ti($_NPOST['suoluetu']);

    $fanhui = $D -> insert($_NPOST);
    
    if( $fanhui){ 

        $_NPOST['paykey'] = $PANC;

        xitongpay( '0' , 1);
        xitongpay( '-1' , 1);
        xitongpay( '-2' , 1);
        xitongpay( '-3' , 1);
        xitongpay( '-4' , 1);

        adminlog($sescc['aid'], 5 , serialize( array( 'ac' => $AC , 'mo' => $MOD ,'id'=> $fanhui ,'data'=> $_NPOST  )));
        return apptongxin($SHUJU,200,1,"新增成功",$YZTOKEN,$WY);

    }else return apptongxin($SHUJU,406,-1,"添加失败",$YZTOKEN,$WY);



}else if($MOD == 'put'){
    /*修改数据*/
    $ID = (int)(isset($_NPOST['id'])?$_NPOST['id']:0);

    if($ID < 1){

        $YZTOKEN = token();
        sescc('token',$YZTOKEN,$UHA);
        return apptongxin($SHUJU,404,-1,$MSG,$YZTOKEN,$WY);
    }

    $TOKEN = isset($_NPOST['ttoken'])?$_NPOST['ttoken']:"";

    if($TOKEN == '' || $sescc['token'] !=  $TOKEN){

        $YZTOKEN = token();
        sescc('token',$YZTOKEN,$UHA);
        return apptongxin($SHUJU,415,-1,'token错误',$YZTOKEN,$WY);

    }

    $YZTOKEN = token();
    sescc('token',$YZTOKEN,$UHA);

    $DATAS = $D -> where( array('id' => $ID ) ) -> find();
    if(!$DATAS){

        return apptongxin($SHUJU,404,-1,$MSG,$YZTOKEN,$WY);
    }

    if(isset( $_NPOST['paykey']) && $_NPOST['paykey'] == $PANC ){
    
        unset($_NPOST['paykey']);
    }

    $_NPOST['suoluetu'] =  TOU_ti($_NPOST['suoluetu']);


    $fan = $D ->where( array( 'id' => $ID))-> update( $_NPOST);

    if( $fan){


        xitongpay( $DATAS['payfile'] , 1);
        xitongpay( $ID , 1);
        xitongpay( '0' , 1);
        xitongpay( '-1' , 1);
        xitongpay( '-2' , 1);
        xitongpay( '-3' , 1);
        xitongpay( '-4' , 1);

        $_NPOST['paykey'] = $DATAS['paykey'] = $PANC;
  
        adminlog($sescc['aid'], 3 , serialize( array( 'ac' => $AC , 'mo' => $MOD , 'id'=> $ID,'yuan'=> $DATAS, 
            'data'=> $_NPOST )));

        return apptongxin($SHUJU,200,1,$MSG,$YZTOKEN,$WY);

    }else{

        return apptongxin($SHUJU,400,-1,$MSG,$YZTOKEN,$WY);
    
    }






}else if($MOD == 'delete'){

    /*删除数据*/
    $ID = (int)(isset($_NPOST['id'])?$_NPOST['id']:0);

    if($ID < 1){

        $YZTOKEN = token();
        sescc('token',$YZTOKEN,$UHA);
        return apptongxin($SHUJU,404,-1,$MSG,$YZTOKEN,$WY);
    
    }

    $TOKEN = isset($_NPOST['ttoken'])?$_NPOST['ttoken']:"";

    if($TOKEN == '' || $sescc['token'] !=  $TOKEN){

        $YZTOKEN = token();
        sescc('token',$YZTOKEN,$UHA);
        return apptongxin($SHUJU,415,-1,'token错误',$YZTOKEN,$WY);

    }

    $YZTOKEN = token();
    sescc('token',$YZTOKEN,$UHA);

    $DATAS = $D -> where( array('id' => $ID ) ) -> find();

    if(!$DATAS){

        return apptongxin($SHUJU,404,-1,$MSG,$YZTOKEN,$WY);
    }

    $fan = $D -> where( array('id' => $ID ) ) -> delete();

    if( $fan ){

        xitongpay( $DATAS['payfile'] , 2);
        xitongpay( $ID , 2);
        xitongpay( '0' , 1);
        xitongpay( '-1' , 1);
        xitongpay( '-2' , 1);
        xitongpay( '-3' , 1);
        xitongpay( '-4' , 1);

        adminlog($sescc['aid'], 4 , serialize( array( 'ac' => $AC , 'mo' => $MOD , 'id'=> $ID,'yuan'=> $DATAS  )));

    }else{

        return apptongxin($SHUJU,410,-1,"删除失败?",$YZTOKEN,$WY);
    }

}



return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);