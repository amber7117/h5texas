<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

if( $USERID < 1){

    return apptongxin($SHUJU,415,-99,"no login",$YZTOKEN,$WY);
}

if($MOD == 'get'){
    /*获取数据*/

    $YZHost = 'weiyi/put'.md5($USERID);
    $cuzai = $Mem ->g($YZHost);

    if($cuzai){

        $Mem ->s($YZHost,1,1);
        return apptongxin(array(),415,$CODE,'请不要重复提交',$YZTOKEN,$WY);
    }

    $Mem ->s($YZHost,1,1);

    $SHUJU = '';

    global $Mem;

    $fileArray[]=NULL;
    $dir = '/var/www/html/WangYa/temp/apkmsg/'.$USERID;
    if (false != ($handle = opendir ( $dir ))) {
        $i=0;
        while ( false !== ($file = readdir ( $handle )) ) {
            //去掉"“.”、“..”以及带“.xxx”后缀的文件
            if ($file != "." && $file != ".."&&strpos($file,".")) {

                $filename=str_replace(strrchr($file, "."),"",$file); 
                $fileArray[$i]="apkmsg/".$USERID.'/'.$filename;
                if($i==100){
                    break;
                }
                $i++;
            }
        }
        //关闭句柄
        closedir ( $handle );
    }
    
    $msgdata = array();

    foreach($fileArray as $lujing){

        $data = $Mem -> g($lujing);
        if($data){
            $data['time'] = date('m-d H:i',$data['time']);
            $msgdata[] = $data;
        }

    }

    if($msgdata){

        $SHUJU = $msgdata;

    }else{

        $CODE = -1;
    }

}else if($MOD == 'post'){
    /*新增数据*/

}else if($MOD == 'put'){
    /*修改数据*/

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

    $DATAS = $D -> where( array('id' => $ID ) ) -> find();

    $YZTOKEN = token();
    sescc('token',$YZTOKEN,$UHA);

    if(!$DATAS){

        
        return apptongxin($SHUJU,404,-1,$MSG,$YZTOKEN,$WY);
    }


    if( $DATAS['uid'] != $USERID){
        
        return apptongxin($SHUJU,415,-1,'非法删除',$YZTOKEN,$WY);
    }

    $fan = $D -> where( array('id' => $ID ) ) -> delete();

    if( $fan ){

        userlog( $USERID, 4, serialize(array($DATAS['name'],$DATAS['neirong'] )));

    }else{

        return apptongxin($SHUJU,410,-1,"删除失败?",$YZTOKEN,$WY);
    }



}



return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);