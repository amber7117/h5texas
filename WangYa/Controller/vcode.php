<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');
/*******************************************
* WangYa GameFrame Application             *
* 2018 New year                            *
*******************************************/

htmlhead( 'image/png' ,$WY);

if(isset($_GET['gametype']) && $_GET['gametype'] == 'apkhongbao'){
    vcode($UHA,10,'',4,160,60,100,15);
}else{
    vcode($UHA,1);
}

