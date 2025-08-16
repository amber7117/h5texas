<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

$PANC = '已填写,录入即为修改';

if($MOD == 'get'){
    /*获取数据*/

    $CODE = 1;
    $STAT = 200;

    $yinc = array(

        'duanxinkey',
        'kjqqkey',
        'kjwxkey',
        'kjkwxkey',
        'kjweibokey',
        'kjzfbkey',
    );

    foreach($yinc as $kl){

        if( isset( $CONN[$kl])){

            if($CONN[$kl] != '' ){
            
                $CONN[$kl] = $PANC;
            }
        }
    }

    $SHUJU = $CONN;
    $YZTOKEN = token();

    $GAMEIDIP = Game_Server("videolonghuK");

    $IP = fenpeiip(1,$GAMEIDIP);

    $fan = httpudp(array("y"=>"getbetjine","d"=>''),$IP['ip'], $IP['port']);

    $SHUJU['lhbetjine'] = implode('_',$fan['betjine']);

    $fan = httpudp(array("y"=>"getjiangchi","d"=>''),$IP['ip'], $IP['port']);

    $SHUJU['lhjiangchi'] = $fan['jiangchi'];

    $lhgethave = $Mem -> g('lhgethave');
    $SHUJU['lhgethave'] = $lhgethave==1?'是':'否';

    sescc('token',$YZTOKEN,$UHA);


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


    $unset = array(
        '',
        'dir',
        'token',
        'lang',
        'ttoken',
        'apptoken',
        // 'HTTP',
        'lang',
        'qtpl',
        'htpl' ,
    );

    $_NPOST['apkhbdailims'] = str_replace(array("\r\n", "\r", "\n"), "  ",$_NPOST['apkhbdailims']);
    
    if(isset($_NPOST['apkhbfbyongjinbili']) && $_NPOST['apkhbfbyongjinbili'] != $CONN['apkhbfbyongjinbili']){
        
        $arr = explode('_',$_NPOST['apkhbfbyongjinbili']);

        if($CONN['tuiji'] < count($arr)){
            $_NPOST['tuiji'] = count($arr);
        }
        
        for($i=0;$i < count($arr);$i++){

            $ziduan = 'tuid'.$i;
            if($i == 0){
                $ziduan = 'tuid';
            }
            $D = db('user');

            $sql = "select count(*) from information_schema.columns where table_name = 'ay_user' and column_name = '".$ziduan."';";

            $fn = $D -> qurey( $sql);

            if($fn){
                if($fn['count(*)'] == 0){
                    $sql1 = "alter table ay_user add ".$ziduan." bigint(20) unsigned default '0';";
                    $fn = $D -> qurey( $sql1 );
                }
            }
            
        }

        system('rm -r -f /var/www/html/WangYa/temp/db');
        system('chmod -R 777 /var/www/html/');
    }

    if(isset($_NPOST['apkhbqbyongjinbili']) && $_NPOST['apkhbqbyongjinbili'] != $CONN['apkhbqbyongjinbili']){
        
        $arr = explode('_',$_NPOST['apkhbqbyongjinbili']);

        if($CONN['tuiji'] < count($arr)){
            $_NPOST['tuiji'] = count($arr);
        }
        
        for($i=0;$i < count($arr);$i++){

            $ziduan = 'tuid'.$i;
            if($i == 0){
                $ziduan = 'tuid';
            }
            $D = db('user');

            $sql = "select count(*) from information_schema.columns where table_name = 'ay_user' and column_name = '".$ziduan."';";

            $fn = $D -> qurey( $sql);

            if($fn){
                if($fn['count(*)'] == 0){
                    $sql1 = "alter table ay_user add ".$ziduan." bigint(20) unsigned default '0';";
                    $fn = $D -> qurey( $sql1 );
                }
            }
            
        }

        system('rm -r -f /var/www/html/WangYa/temp/db');
        system('chmod -R 777 /var/www/html/');
    }

    if($_NPOST['apkflhbnum'] > 50){
        $_NPOST['apkflhbnum'] = 50;
    }
    
    $_NPOST['apkhbniurule'] =  TOU_ti($_NPOST['apkhbniurule']);
    $_NPOST['apkhbxtrule'] =  TOU_ti($_NPOST['apkhbxtrule']);
    $_NPOST['apkhbroomrule'] =  TOU_ti($_NPOST['apkhbroomrule']);
    
    $_NPOST['apkhblogo'] =  TOU_ti($_NPOST['apkhblogo']);
    $_NPOST['apkhbKeFu'] =  TOU_ti($_NPOST['apkhbKeFu']);
    $_NPOST['apkhbgonggao'] =  TOU_ti($_NPOST['apkhbgonggao']);
    $_NPOST['apkhberweima'] =  TOU_ti($_NPOST['apkhberweima']);
    $_NPOST['apkhberweima1'] =  TOU_ti($_NPOST['apkhberweima1']);
    $_NPOST['apkhbtgrule'] =  TOU_ti($_NPOST['apkhbtgrule']);
    $_NPOST['apkhbCZKeFu'] =  TOU_ti($_NPOST['apkhbCZKeFu']);
    $_NPOST['apkhbHDKeFu'] =  TOU_ti($_NPOST['apkhbHDKeFu']);
    $_NPOST['apkhbrule'] =  TOU_ti($_NPOST['apkhbrule']);


    
    $_NPOST['lhdailims'] = str_replace(array("\r\n", "\r", "\n"), "  ",$_NPOST['lhdailims']);
    
    if($_NPOST['lhbetjine']){
        $GAMEIDIP = Game_Server("videolonghuK");

        $IP = fenpeiip(1,$GAMEIDIP);

        $betarr = explode('_',$_NPOST['lhbetjine']);

        $DATA = httpudp(array("y"=>"setbetjine","d"=>$betarr),$IP['ip'], $IP['port']);

    }

    if(isset($_NPOST['lhtodayget']) && $_NPOST['lhtodayget'] != $CONN['lhtodayget']){
        global $Mem;
        $Mem -> s('lhgethave',0);
    }
    
    if(isset($_NPOST['lhyongjinbili']) && $_NPOST['lhyongjinbili'] != $CONN['lhyongjinbili']){
        
        $arr = explode('_',$_NPOST['lhyongjinbili']);

        $_NPOST['tuiji'] = count($arr);

        $_NPOST['jishu'] = count($arr);

        for($i=0;$i < count($arr);$i++){

            $ziduan = 'tuid'.$i;
            if($i == 0){
                $ziduan = 'tuid';
            }
            $D = db('user');

            $sql = "select count(*) from information_schema.columns where table_name = 'ay_user' and column_name = '".$ziduan."';";

            $fn = $D -> qurey( $sql);

            if($fn){
                if($fn['count(*)'] == 0){
                    $sql1 = "alter table ay_user add ".$ziduan." bigint(20) unsigned default '0';";
                    $fn = $D -> qurey( $sql1 );
                }
            }
            
        }
        system('rm -r -f /var/www/html/WangYa/temp/db');
        system('chmod -R 777 /var/www/html/');
    }

    $_NPOST['lhKeFu'] =  TOU_ti($_NPOST['lhKeFu']);
    $_NPOST['lherweima'] =  TOU_ti($_NPOST['lherweima']);
    $_NPOST['lhgonggaotu'] =  TOU_ti($_NPOST['lhgonggaotu']);
    

    $_NPOST['erweima'] =  TOU_ti($_NPOST['erweima']);
    $_NPOST['kefu'] =  TOU_ti($_NPOST['kefu']);
    $_NPOST['CZKeFu'] =  TOU_ti($_NPOST['CZKeFu']);
    $_NPOST['GZH'] =  TOU_ti($_NPOST['GZH']);

    $_NPOST['logo'] =  TOU_ti($_NPOST['logo']);
    $_NPOST['xiaoxi'] =  TOU_ti($_NPOST['xiaoxi']);

    foreach($unset as $kl){

        if( isset( $_NPOST[$kl]) ) unset( $_NPOST[$kl] );
    }

    foreach($_NPOST as $k => $v){

        if( !is_array( $v ) && $v == $PANC ){

            /*判断包含没修改的值 直接清理k*/
            unset($_NPOST[$k]);


        }else if(!isset($CONN[$k])){

            /*conn不存在的值清理掉*/
            unset($_NPOST[$k]);
        }
    }

    $YUCONN = $CONN;


    $CONN = array_merge($CONN, $_NPOST);
    global $CONLJI;

    x($CONLJI,$CONN);

    $yinc = array(

        'duanxinkey',
        'kjqqkey',
        'kjwxkey',
        'kjkwxkey',
        'kjweibokey',
        'kjzfbkey',
    );

    foreach($yinc as $kl){

        if( isset( $YUCONN[$kl])){

            if($YUCONN[$kl] != '' ){
            
                $YUCONN[$kl] = $PANC;
            }
        }

        if( isset( $_NPOST[$kl])){

            if( $_NPOST[$kl] != '' ){
            
                $_NPOST[$kl] = $PANC;
            }
        }
    }



    adminlog($sescc['aid'], 3 , serialize( array( 'ac' => $AC , 'mo' => $MOD ,'yuan'=> $YUCONN , 'data'=> $_NPOST )));

    $CODE = 1;



}else if($MOD == 'delete'){
    /*删除数据*/

}


return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);