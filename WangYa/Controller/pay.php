<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');
/*******************************************
* WangYa GameFrame Application             *
* 2018 New year                            *
*******************************************/

htmlhead(  'text/html;charset=UTF-8' ,$WY);

$FYUTIME = $CONN['FYUTIME'] ; //防御秒数
$FYUNUM  = $CONN['FYUNUM'] ;  //防御数量

/* 防御公式
   每个ip 连接次数

   防御秒数 最多 请求 防御数量 次数
*/


if( isset( $PLAYFS ) && $PLAYFS > 0 && isset( $PAYFILE )){

    
       
    if($ISAPP){

        htmlhead('application/json;charset=UTF-8' ,$WY);

    }else{

        htmlhead(  'text/html;charset=UTF-8' ,$WY);
    }

        
    try {

        $PAYAC = xitongpay( $PAYFILE );
        
        if($PAYAC){

            $lujin = WYPHP.'Tpl/pay/'. anquanqu( $PAYAC ['payfile'] ).'.php';
            if( is_file( $lujin ) ){

                return include $lujin;
            }

        }else{
        
            return htmlout('当前支付接口关闭'.$PAYFILE  ,$WY);
        }

    } catch (Exception $e) {   

        return htmlout($e->getMessage() ,$WY);
    }   

        return htmlout('文件不存在' ,$WY);

}


$IP = ip();

if( $FYUTIME > 0 ){ 

$FYUCC = 'fangyucc/'.mima( $IP );

$YUCC  = $Mem -> g( $FYUCC );

        if( $YUCC ){

            $YUCC = $Mem -> ja( $FYUCC , 1 , $FYUTIME );

            if( $YUCC > $FYUNUM){
            
                return htmlout('<a href="'.WZHOST.'">CC 防御,请稍后再操作</a>',$WY);
            }
             
        }else $Mem -> s( $FYUCC , 1 , $FYUTIME );

}




if($sescc['ip'] == '' ) $sescc = sescc(array('ip' => $IP),'',$UHA);

if( $CONN['yanzhengip'] == 1 && $IP != $sescc['ip'] ){

    $sescc = sescc(array('uid' => 0 ),'',$UHA);

}


$USERID =  isset($sescc['uid'])?$sescc['uid']:0;

if( $USERID < 1){


    return htmlout('<a href="'.WZHOST.'">请等登录</a>',$WY,200);
}

$USER = uid( $USERID );

if( $USER ['off'] == '0'){

    $sescc = sescc(array('uid' => 0 ),'',$UHA);
    return htmlout('<a href="'.WZHOST.'">帐号关闭</a>',$WY,200);

}

if( isset( $_NGET['y'] ) ){

    $JINE =  ceil(isset( $_NGET['jine']) ? $_NGET['jine']: 1 );

    if( $JINE <= 0 ) $JINE = 1;

    $diqu = 0;

    $ORDER = isset( $_NGET['order']) ? $_NGET['order']: '';

    if( $ORDER != '' && strlen( $ORDER ) < 10  ){
    
        return htmlout('<a href="'.WZHOST.'">支付订单错误</a>',$WY,200);
    }



    $PAYTY = (int) ( isset( $_NGET['paytype']) ? $_NGET['paytype'] : 0 );


    $PAYAC = xitongpay( $PAYTY);

    if( !$PAYAC || !is_array( $PAYAC ) || count( $PAYAC ) < 1){
    
   

        return htmlout('<a href="'.WZHOST.'">没有支付方式</a>',$WY,200);
  
    
    }



    $CONN['payjine'] = isset( $CONN['payjine'] ) ? $CONN['payjine'] : 1;
  
    if( $JINE < $CONN['payjine'] ) $JINE = $CONN['payjine']; 
     $D = db( 'dingdan' );



    if(isset( $_NGET['cha'] ) &&  $ORDER != "" ){

        $DINDATA = $D ->where( array( 'tongyiid' => $ORDER ) )-> select();

        if(! $DINDATA || $DINDATA['0']['uid'] != $USERID ){
        
            return htmlout('<a href="'.WZHOST.'">非法订单</a>',$WY,200);
        
        
        } 

        $zong    = 0;
        $hongbao = 0;

        foreach($DINDATA as $ooo){
           
            if( $ooo['type'] == '0'){

                $hongbao = $ooo['hongjine'];
                $zong   += $ooo['payjine'];
            }
        }

        $JINE = $zong - $hongbao;


    
    }

    $paylx = (int) ( isset( $_NGET['paylx']) ? $_NGET['paylx'] : 1 );
    $BIZHONG = (int) ( isset( $_NGET['bizhong']) ? $_NGET['bizhong'] : 0 );

    if( $paylx == 1 ) $diqu = 0;
    else              $diqu = 1;



    $DINGID = array(     'uid' => $USERID ,
                     'orderid' => orderid() ,
                     'payjine' => $JINE ,
                        'off'  => 1 ,
                        'shid' => $USER['tuid'],/* 给代理的充值 */
                        'type' => 1,
                      
                    'tongyiid' => $ORDER,
                     'paytype' => $PAYAC['id'], 
                       'atime' => time(),
                          'ip' => $IP ,
                        'diqu' => $diqu,
                       'lailu' => lailu()
    );

    if( $DINGID['type'] == 1){

       if($BIZHONG > 2 || $BIZHONG < 0 )$BIZHONG = 0;

       $DINGID['bizhong'] = $BIZHONG;
    }

    
    $DINGID['mhash'] = md5( implode('前', $DINGID) ). md5( implode('后', $DINGID ));

    usleep( rand( 500 , 50000 ) );

   

    $sql = $D -> setshiwu(1) -> insert( $DINGID );

    

    $fanhui = $D -> qurey( $sql , 'shiwu' );

    if( $fanhui ){



        $lujin = WYPHP.'Tpl/pay/'. anquanqu( $PAYAC ['payfile'] ).'.php';

        if( is_file( $lujin ) ){

             $PLAYFS = 1;

            return include $lujin;

        }else{


            return htmlout('<a href="'.WZHOST.'">支付文件错误</a>',$WY,200);

        }

    }else{

        return htmlout('<a href="'.WZHOST.'">插入失败联系管理</a>',$WY,200);


    }


}else{

 
    return htmlout('<a href="'.WZHOST.'">未知错误</a>',$WY,200);


}