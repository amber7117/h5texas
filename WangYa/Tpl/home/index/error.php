<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

$_NPOST;

$_NPOST['fid'] = (int)$_NPOST['fid'];
$_NPOST['uid'] = (int)$_NPOST['uid'];
$AHASH = 'error/'.$_NPOST['fid'].'/'.$_NPOST['uid'].'_'.time().'_'.rand(1,10);
if($_NPOST['cuowu'] != ''){
$Mem ->s($AHASH,$_NPOST['cuowu']);
}
return apptongxin($SHUJU,200,1,"ok",$YZTOKEN,$WY);