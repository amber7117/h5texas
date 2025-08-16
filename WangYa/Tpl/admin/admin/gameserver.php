<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

$D  = db('gameserver');

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

        $DATA = $D ->zhicha('id,name,biaoshi,off,atime')-> where($WHERE) ->limit($limit) -> select();

        if($DATA){

            $CODE = 1;
            $STAT = 200;
            $SHUJU['data'] = $DATA;

        }else{

            $CODE = -1;

        }

        $SHUJU['off'] = logac('off2');
        $SHUJU['bizhong'] = array($CONN['jine'],$CONN['jifen'],$CONN['huobi']);
        $SHUJU['koufang'] = logac('koufang');




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

        $SHUJU = $DATA;

        if($SHUJU['tupian'] != ''){
        
            $SHUJU['tupian'] = pichttp($SHUJU['tupian']);
        }

        if($SHUJU['serverlist'] != ''){
        /*游戏服务器*/

            $SHUJU['serverlist'] = explode('#WY#',$SHUJU['serverlist']);
        
        }else $SHUJU['serverlist'] = array();

        if($SHUJU['stjushu'] != ''){
            /*可以创建局数*/

            $SHUJU['stjushu'] = explode('#WY#',$SHUJU['stjushu']);
        
        }else $SHUJU['stjushu'] = array();

        if( $SHUJU['strenshu'] != ''){
            /*可以加入人数*/

            $SHUJU['strenshu'] = explode('#WY#',$SHUJU['strenshu']);
        
        }else $SHUJU['strenshu'] = array();

        if(isset($SHUJU['stdifen']) && $SHUJU['stdifen'] != ''){
            /*可以闲家底分*/

            $SHUJU['stdifen'] = explode('#WY#',$SHUJU['stdifen']);
        
        }else $SHUJU['stdifen'] = array();

        if(isset($SHUJU['stzhfen']) && $SHUJU['stzhfen'] != ''){
            /*可以庄家底分*/

            $SHUJU['stzhfen'] = explode('#WY#',$SHUJU['stzhfen']);
        
        }else $SHUJU['stzhfen'] = array();
    
    
    
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

    if(isset($_NPOST['serverlist']) && $_NPOST['serverlist'] != ''){
        /*游戏服务器*/

        $_NPOST['serverlist'] = implode('#WY#',$_NPOST['serverlist']);
    
    }else $_NPOST['serverlist'] = '';

    if(isset($_NPOST['stjushu']) && $_NPOST['stjushu'] != ''){
        /*可以创建局数*/

        $_NPOST['stjushu'] = implode('#WY#',$_NPOST['stjushu']);
    
    }else $_NPOST['stjushu'] = '';

    if(isset($_NPOST['strenshu']) && $_NPOST['strenshu'] != ''){
        /*可以加入人数*/

        $_NPOST['strenshu'] = implode('#WY#',$_NPOST['strenshu']);
    
    }else $_NPOST['strenshu'] = '';

    if(isset($_NPOST['stdifen']) && $_NPOST['stdifen'] != ''){
        /*可以闲家底分*/

        $_NPOST['stdifen'] = implode('#WY#',$_NPOST['stdifen']);
    
    }else $_NPOST['stdifen'] = '';

    if(isset($_NPOST['stzhfen']) && $_NPOST['stzhfen'] != ''){
        /*可以庄家底分*/

        $_NPOST['stzhfen'] = implode('#WY#',$_NPOST['stzhfen']);
    
    }else $_NPOST['stzhfen'] = '';


    if(isset($_NPOST['stkuozan']) && $_NPOST['stkuozan'] != ''){
        /*扩展数据*/

        $shuju = json_decode($_NPOST['stkuozan'],true);

        if($shuju && is_array($shuju)){

            $_NPOST['stkuozan'] = json_encode($shuju);
        
        }else $_NPOST['stkuozan'] = '';
    
    
    }else $_NPOST['stkuozan'] = '';

    if(isset($_NPOST['shops']) && $_NPOST['shops'] != ''){
        /*商城数据*/

        $shuju = json_decode($_NPOST['shops'],true);

      
        if($shuju && is_array($shuju)){

            $_NPOST['shops'] = json_encode($shuju);
        
        }else $_NPOST['shops'] = '';
    
    
    }else $_NPOST['shops'] = '';


    $_NPOST['atime'] =$_NPOST['ctime'] = time();
    $_NPOST['adminid'] = $sescc['aid'];

    $_NPOST['ip'] = IP();

    $_NPOST['tupian'] = TOU_ti($_NPOST['tupian']);

    $fanhui = $D -> insert($_NPOST);

    if( $fanhui){ 


        Game_List(1, 1);
        Game_List(2, 1);
        Game_Info( $_NPOST['biaoshi'] , 1);
        Game_Set($_NPOST['biaoshi'] , 1);
        Game_Server( $_NPOST['biaoshi'],1);

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

    if(isset($_NPOST['serverlist']) && $_NPOST['serverlist'] != ''){
        /*游戏服务器*/

        $_NPOST['serverlist'] = implode('#WY#',$_NPOST['serverlist']);
    
    }else $_NPOST['serverlist'] = '';

    if(isset($_NPOST['stjushu']) && $_NPOST['stjushu'] != ''){
        /*可以创建局数*/

        $_NPOST['stjushu'] = implode('#WY#',$_NPOST['stjushu']);
    
    }else $_NPOST['stjushu'] = '';

    if(isset($_NPOST['strenshu']) && $_NPOST['strenshu'] != ''){
        /*可以加入人数*/

        $_NPOST['strenshu'] = implode('#WY#',$_NPOST['strenshu']);
    
    }else $_NPOST['strenshu'] = '';

    if(isset($_NPOST['stdifen']) && $_NPOST['stdifen'] != ''){
        /*可以闲家底分*/

        $_NPOST['stdifen'] = implode('#WY#',$_NPOST['stdifen']);
    
    }else $_NPOST['stdifen'] = '';

    if(isset($_NPOST['stzhfen']) && $_NPOST['stzhfen'] != ''){
        /*可以庄家底分*/

        $_NPOST['stzhfen'] = implode('#WY#',$_NPOST['stzhfen']);
    
    }else $_NPOST['stzhfen'] = '';


    if(isset($_NPOST['stkuozan']) && $_NPOST['stkuozan'] != ''){
        /*扩展数据*/

        $shuju = json_decode($_NPOST['stkuozan'],true);

        if($shuju && is_array($shuju)){

            $_NPOST['stkuozan'] = json_encode($shuju);
        
        }else $_NPOST['stkuozan'] = '';
    
    
    }else $_NPOST['stkuozan'] = '';

    if(isset($_NPOST['shops']) && $_NPOST['shops'] != ''){
        /*商城数据*/

        $shuju = json_decode($_NPOST['shops'],true);

        if($shuju && is_array($shuju)){

            $_NPOST['shops'] = json_encode($shuju);
        
        }else $_NPOST['shops'] = '';
    
    
    }else $_NPOST['shops'] = '';


    $_NPOST['ctime'] = time();
    $_NPOST['adminid'] = $sescc['aid'];

    $_NPOST['ip'] = IP();

    $_NPOST['tupian'] =  TOU_ti($_NPOST['tupian']);

    $fan = $D ->where( array( 'id' => $ID))-> update( $_NPOST);

    if($fan){

        Game_Server($DATAS['biaoshi'] , 1);
        Game_Info($DATAS['biaoshi'] , 1);
        Game_List(1, 1);
        Game_List(2, 1);
        Game_Set($DATAS['biaoshi'],1);

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

        Game_Server($DATAS['biaoshi'] , 2);
        Game_Info($DATAS['biaoshi'] , 2);
        Game_List(1, 1);
        Game_List(2, 1);
        Game_Set($DATAS['biaoshi'],2);
        adminlog($sescc['aid'], 4 , serialize( array( 'ac' => $AC , 'mo' => $MOD , 'id'=> $ID,'yuan'=> $DATAS  )));

    }else{

        return apptongxin($SHUJU,410,-1,"删除失败?",$YZTOKEN,$WY);
    }

}



return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);