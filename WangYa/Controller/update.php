<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');
/*******************************************
* WangYa GameFrame Application             *
* 2018 New year                            *
*******************************************/

if(  $sescc['aid'] > 0 || $sescc['uid'] > 0){

	$ext_arr = array(

        'image' => array('gif', 'jpg', 'jpeg', 'png'),
        'flash' => array('swf', 'flv'),
        'media' => array('swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'),
        'file' => array('doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2','7z'),
        'all' => array('gif', 'jpg', 'jpeg', 'png' ,'swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb','doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2','7z','apk')
    );

    if( isset( $_NGET['uplx']) && isset(  $ext_arr[ $_NGET['uplx']])) $LX = $_NGET['uplx'];
    else  $LX = 'image';

    

    if (! empty( $_FILES[$LX]['error'])) {

        return apptongxin(array(),200,-1,'上传类型错误','',$WY);

	}

    $max_size = isset( $_NGET['maxsize']) && $CONN['maxsize'] >= $_NGET['maxsize'] &&  $_NGET['maxsize'] > 10 ? $_NGET['maxsize'] : $CONN['maxsize'];
    if ( empty( $_NFILES) === false) {

        $file_name = $_NFILES[$LX]['name'];

        $tmp_name  = $_NFILES[$LX]['tmp_name'];
                
        $file_size = $_NFILES[$LX]['size'];

        $qianzui = 'attachment/'.$LX.'/'.date('Ym').'/';
        $files =  $CONN['dir'].$qianzui;
        $WJIAN =  rtrim(WYPHP.'../','/').'/'.ltrim( $qianzui  ,'/');
        jianli($WJIAN);
    

        if ( !$file_name ) return apptongxin(array(),200,-1,'上传临时文件未找到','',$WY);
                
        if ( @is_dir( $WJIAN ) === false)  return apptongxin(array(),200,-1,'目录不存在','',$WY);
    
        if ( @is_writable( $WJIAN ) === false)  return apptongxin(array(),200,-1,'写入文件失败','',$WY);
        
        if ( @is_uploaded_file( $tmp_name) === false)  return apptongxin(array(),200,-1,'上传失败','',$WY);

        if ( $file_size > $max_size )  return apptongxin(array(),200,-1,'超过上传大小','',$WY);

        $temp_arr = explode( "." , $file_name);
        $file_ext = array_pop( $temp_arr);
        $file_ext = trim( $file_ext);
        $file_ext = strtolower( $file_ext);

        if( in_array( $file_ext , $ext_arr[ $LX]) === false){
        
            return apptongxin(array(),200,-1,'上传格式错误'.implode( ',' , $ext_arr[ $LX]),'',$WY);
        }

        $Nfile =  date('d').'_'.mima( time().rand( 1 , 9999999)).'.'.$file_ext;

        $returnfile = $files.$Nfile;

        $md5hash = md5( md5_file($tmp_name).( $sescc['aid'].'_'.$sescc['uid']));

        $D = db('fujian');

        $reutrntoken  = $D ->where( array( 'token' => $md5hash))-> find();

        $CDN = '';

        if( ! $reutrntoken ){

            if ( move_uploaded_file( $tmp_name, $WJIAN.$Nfile ) === false){

                return apptongxin(array(),200,-1,'移动文件失败','',$WY);
            }

            @chmod( $WJIAN.$Nfile , 0644);
            $charu = $D -> insert( 
                array(
                    'adminid' => $sescc['aid'],
                    'name' => anquanqu($file_name),
                    'uid' => $sescc['uid'],
                    'cdn' => $CDN,
                    'atime' => time(),
                    'pic' => $returnfile ,
                    'houzui' => $file_ext,
                    'size' => $file_size,
                    'token' => $md5hash,
                ));

            if( $charu ) $returnfile = $CDN.$returnfile;

        }else  $returnfile = $reutrntoken['cdn'].$reutrntoken['pic'];

        if(isset($_NGET['touxiang']) && $sescc['uid'] > 0){

            $D = db('user');

            $fan = $D ->where(array( 'uid' => $sescc['uid'])) -> update(array('touxiang' => $returnfile));

            if($fan){

                uid($sescc['uid'],1);

            }


        
        
        }



        return apptongxin(array(),200,1,pichttp($returnfile),'',$WY);


    }else{

        return apptongxin(array(),200,-1,'没有找到文件','',$WY);
    }


}else{


    return apptongxin(array(),200,-1,'权限不足','',$WY);



}
