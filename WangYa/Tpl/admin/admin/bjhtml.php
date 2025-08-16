<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

$D  = db('admin');

if($MOD == 'get'){
    /*获取数据*/

    $ID = isset($_NPOST['id'])?$_NPOST['id']:'';

    if($ID == ''){


        $SDKMULU = WYPHP.'../game/';

        $WENJIAN = array();


        $ds = DIRECTORY_SEPARATOR;
        $dir = 1 ? realpath( $SDKMULU ) : $SDKMULU;

        $dir = substr( $dir, -1 ) == $ds ? substr( $dir, 0, -1) : $dir;

        if (is_dir( $dir ) && $handle = opendir( $dir )){

            while($file = readdir($handle))
            {
                if($file == '.' || $file == '..'){
                     continue;

                }else if( strpos($file, ".html" ) ){
                
                    $WENJIAN[] = str_replace('.html','',$file);
                }
            }
        }

        $SHUJU['data'] = $WENJIAN;

 




    }else{

        $SDKMULU = WYPHP.'../game/';

        $WENJIAN = array();


        $ds = DIRECTORY_SEPARATOR;
        $dir = 1 ? realpath( $SDKMULU ) : $SDKMULU;

        $dir = substr( $dir, -1 ) == $ds ? substr( $dir, 0, -1) : $dir;

        if (is_dir( $dir ) && $handle = opendir( $dir )){

            while($file = readdir($handle))
            {
                if($file == '.' || $file == '..'){
                     continue;

                }else if( strpos($file, ".html" ) ){
                
                    $WENJIAN[] = str_replace('.html','',$file);
                }
            }
        }

        if(!in_array($ID, $WENJIAN)){
        

            return apptongxin($SHUJU,415,-1,'文件不存在',$YZTOKEN,$WY);
        }

        $SHUJU['data'] = file_get_contents($SDKMULU.$ID.'.html');


    
    
    }



}else if($MOD == 'post'){
    /*新增数据*/

}else if($MOD == 'put'){
    /*修改数据*/

    $ID = isset($_NPOST['id'])?$_NPOST['id']:'';
    $INROD = isset($_NPOST['nn'])?$_NPOST['nn']:'';

    if($ID == ""){

        return apptongxin($SHUJU,415,-1,'文件不存在',$YZTOKEN,$WY);
    }

    $SDKMULU = WYPHP.'../game/';
       

    $WENJIAN = array();


        $ds = DIRECTORY_SEPARATOR;
        $dir = 1 ? realpath( $SDKMULU ) : $SDKMULU;

        $dir = substr( $dir, -1 ) == $ds ? substr( $dir, 0, -1) : $dir;

        if (is_dir( $dir ) && $handle = opendir( $dir )){

            while($file = readdir($handle))
            {
                if($file == '.' || $file == '..'){
                     continue;

                }else if( strpos($file, ".html" ) ){
                
                    $WENJIAN[] = str_replace('.html','',$file);
                }
            }
        }

        if(!in_array($ID, $WENJIAN)){
        

            return apptongxin($SHUJU,415,-1,'文件不存在',$YZTOKEN,$WY);
        }


        file_put_contents($SDKMULU.$ID.'.html',htmlspecialchars_decode($INROD));


}else if($MOD == 'delete'){
    /*删除数据*/

}



return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);