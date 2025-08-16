<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');
/*******************************************
* WangYa GameFrame Application             *
* 2018 New year                            *
*******************************************/

include WYPHP.'ewm.php';

if( isset($_NGET['madaxiao']) ){
    $ma_daxiao = $_NGET['madaxiao'];
}else{
    $ma_daxiao = 5;
}

if( isset( $_NGET['data'] )){ 

    if($_NGET['data'] == '' ) $_NGET['data'] = WZHOST;
    $value =  urldecode( $_NGET['data'] );
    QRcode::png($value, false, 'm', $ma_daxiao,true);
    
}
