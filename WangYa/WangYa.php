<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

$AGENT = $SHOUJI = $IP = null;

$ISAPP = false;

$JPGE = $PNG = $GIF = $OTHER =$CSS = $HTML = $WEIZHI = $JS = array();

$FUJIAN = array('gif'=> 'gif', 'jpg' => 'jpg', 'jpeg' => 'jpeg', 'png' => 'png', 'swf' => 'swf', 'flv' => 'flv', 'mp3' => 'mp3', 'wav' => 'wav', 'wma' => 'wma', 'wmv' => 'wmv' , 'mid' => 'mid', 'avi' => 'avi' , 'mpg' => 'mpg', 'asf' => 'asf' , 'rm' => 'rm', 'rmvb' => 'rmvb', 'doc' => 'doc', 'docx' => 'docx', 'xls' => 'xls', 'xlsx' => 'xlsx', 'ppt' => 'ppt', 'htm' => 'htm', 'html' => 'html', 'txt' => 'txt', 'zip' => 'zip', 'rar' => 'rar', 'gz' => 'gz', 'bz2' => 'bz2', '7z' => '7z' ,'apk'=>'apk') ;

include WYPHP.'function.php';

systemOnOff( WYNAME );

function fangxss( $name ){

    return str_replace( array( '<','>'), array('乳','猪'),  $name );
}

if($_POST){

    foreach($_POST as $k=>$v){
        if(!is_array($v)){
            $_POST[$k] =fangxss($v);
        }

    }
}

$IP = ip();
define( 'QTPL' , WYPHP.'Tpl/home/'.($CONN['qtpl']).'/');
define( 'HTPL' , WYPHP.'Tpl/admin/'.($CONN['htpl']).'/');
define( 'TPL'  , $CONN['dir'].'Tpl/');
define( 'DQTPL', $CONN['dir'].'Tpl/home/'.$CONN['qtpl'].'/');
define( 'DHTPL', $CONN['dir'].'Tpl/admin/'.$CONN['htpl'].'/');

$LANG = include  WYPHP.'Lang/'.$CONN['lang'].'.php';

function request_uri(){

        if ( isset( $_SERVER['argv']))
                $uri = $_SERVER['PHP_SELF'].( empty( $_SERVER['argv']) ? '' :  ( '?'. $_SERVER['argv'][0] )   );
        else if( isset( $_SERVER['QUERY_STRING'])) $uri = $_SERVER['PHP_SELF'].(empty($_SERVER['QUERY_STRING'] ) ? '' : ( '?'. $_SERVER['QUERY_STRING'] ));
        else if( isset( $_SERVER['REQUEST_URI']) ) $uri = $_SERVER['REQUEST_URI'];
        else  $uri = $_SERVER['PHP_SELF'].(empty($_SERVER['QUERY_STRING'] ) ? '' : ( '?'. $_SERVER['QUERY_STRING'] ));
        $_SERVER['REQUEST_URI'] = $uri;
}

request_uri();

$URI = ltrim( strtolower( urldecode( trim($_SERVER["REQUEST_URI"]))),'/'); 

$WY  = null ;

/*接收用户GET*/

    if($_GET)
    {
        $_NGET = $_GET;

    }else $_NGET = array();

    /*接收用户COOKIE*/

    if($_COOKIE)
    {
        $_NCOOKIE = $_COOKIE;

    }else $_NCOOKIE = array();

    /*接收用户POST*/
    if($_POST)
    {
        $_NPOST = $_POST;

        

        if( strstr( strtolower( json_encode( $_NPOST) && !isset($PAYFILE)), $DBCO[$CONN['modb']]['qian']) && ( isset($_NPOST['y']) && $_NPOST['y']!= "error" )){
        
            return  erro404('非法',$WY,500);
        }

    }else $_NPOST = array();



    /*产生登录token*/

    if(isset($_NGET['apptoken']) && strlen($_NGET['apptoken'] ) > 63){

        $SESSIONID = $_NGET['apptoken'];

    }else if(isset($_NPOST['apptoken']) && strlen($_NPOST['apptoken'] ) > 63){

        $SESSIONID = $_NPOST['apptoken'];

    }else if(isset( $_NCOOKIE['apptoken']) && strlen($_NCOOKIE['apptoken'] ) > 63){

        $SESSIONID = $_NCOOKIE['apptoken'];

    }else{

        $SESSIONID = md5('yierwangluo'.time().rand(1,99999999)).md5(time().rand(1,99999999));
    }

ini_set('session.save_handler','files');
jianli( Txpath . "session/");
session_save_path( Txpath . "session/");
session_id($SESSIONID);
session_start();

    setcookie('apptoken' , $SESSIONID , time()+3600*24,'/');


    /* token 内部存放ID */

    $UHA = md5($SESSIONID);
    
    /*读取缓存*/
    $_NSESSION = $sescc = sescc('','',$UHA);

    if(isset($_NGET['tuid'])){
        sescc('tuid',(int)$_NGET['tuid'],$UHA) ;
    }
    if(isset($_NGET['room_id'])){
        sescc('roomid',(int)$_NGET['room_id'],$UHA) ;
    }

    $AGENT = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '' ;

    $SHOUJI = ( 
        strpos( $AGENT, "NetFront" ) ||
        strpos( $AGENT, "iPhone") ||
        strpos( $AGENT, "iPad")  ||
        strpos( $AGENT, "MIDP-2.0") ||
        strpos( $AGENT, "Opera Mini") ||
        strpos( $AGENT, "UCWEB") ||
        strpos( $AGENT, "Android") ||
        strpos( $AGENT, "Windows CE") ||
        strpos( $AGENT, "SymbianOS")

    );



    if(strpos( $AGENT, "WangYaAPP") ){
    
        $ISAPP = true;
    }

    $_NFILES = array();

    if( $sescc['uid'] > 0 || $sescc['aid'] > 0){

        /*接收上传文件*/

        if($_FILES)
        {
            $_NFILES = $_FILES;

        }else $_NFILES = array();

    }

  
    $ZHURU = array( '<', '>', '..', '(', ')','"',"'","*",'[',']','{','}','$');


    foreach( $ZHURU  as $anyou){

        if( strpos( $URI , $anyou) !== false && ( isset($_NGET['y']) && $_NGET['y'] != "error" ) ){

            return  erro404('非法',$WY,500);
        }

    }


include WYPHP.'Controller/'.WYNAME.'.php';
