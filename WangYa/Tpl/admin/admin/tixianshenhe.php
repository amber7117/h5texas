<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

$D = db('tixianshenhe');

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

        if( isset($_NPOST['uid']) && $_NPOST['uid'] != '' ){

            $WHERE['uid'] = (int)$_NPOST['uid'];

        }

        if( isset($_NPOST['type']) && $_NPOST['type'] != '' && $_NPOST['type'] > -1 ){

            $WHERE['type'] = $_NPOST['type'] + 1;
        }

        if( isset($_NPOST['state']) && $_NPOST['state'] != '' && $_NPOST['state'] > -1 ){

            $WHERE['state'] = (int)$_NPOST['state'];
        }

        tixianguoqi();

        $DATA = $D -> where($WHERE) ->limit($limit) ->order('time desc') -> select();

        if($DATA){

            foreach($DATA as $k=>$v){

                $DATA[$k]['time'] = date('Y-m-d H:i:s',substr($v['time'],0,10));

            }
            $CODE = 1;
            $STAT = 200;
            $SHUJU['data'] = $DATA;

        }else{

            $CODE = -1;

        }

        /*货币*/

        $SHUJU['bizhong'] = array($CONN['huobi'],$CONN['yongjin']);

        $SHUJU['shenhestate'] = array('待审核','已同意','已拒绝','已过期');   //0:待审核 1：已同意  2：已拒绝   3:已过期


    }else{

        /*读取一条数据*/

    }



}else if($MOD == 'post'){
    /*新增数据*/

    $TOKEN = isset($_NPOST['ttoken'])?$_NPOST['ttoken']:"";

    if($TOKEN == '' || $sescc['token'] !=  $TOKEN){

        $YZTOKEN = token();
        sescc('token',$YZTOKEN,$UHA);
        return apptongxin($SHUJU,415,-1,'token错误',$YZTOKEN,$WY);

    }

    $DATA = $D -> where(array('id'=>(int)$_NPOST['id'])) -> find();

    if($DATA){

        if((int)$DATA['state'] != 0 && (int)$DATA['state'] != 3){

            return apptongxin($SHUJU,415,-1,'不能操作该订单',$YZTOKEN,$WY);

        }

        global $Mem;
        $isset = $Mem -> g('tixiansuo/'.$DATA['uid']);

        if($isset){
            return apptongxin($SHUJU,415,-1,'提现不要太频繁喔',$YZTOKEN,$WY);
        }
        $Mem -> s('tixiansuo/'.$DATA['uid'],$_NPOST,6);

        $dingdan = db('tixiandingdan') -> where(array('uid' => $DATA['uid'],'time' => $DATA['time'])) -> find();

        if(!$dingdan){

            return apptongxin($SHUJU,415,-1,'没有找到这个提现订单',$YZTOKEN,$WY);

        }

        if((int)$dingdan['state'] != 0 && (int)$dingdan['state'] != 2) return apptongxin($SHUJU,415,-1,'不能操作该订单1',$YZTOKEN,$WY);

        if((int)$_NPOST['state'] == 1){     //同意

            if($DATA['openid']){
                
                $fan = $D -> where(array('id'=>(int)$_NPOST['id'])) -> update(array('state'=>(int)$_NPOST['state']));

                if($fan){

                    $tx_type = isset($CONN['tx_type'])?(int)$CONN['tx_type']:1;
                    if( $tx_type == 0 ){
                        //零钱

                        $post_data = array (
                            'mid' => $CONN['tx_uid'], //在掌上零钱里面获取的uid
                            'jine' => (float)$DATA['txjine'], //要请求发放的金额
                            'openid'=> $DATA['openid'], //第二步获取的openid
                            //'tixianid'=> (int)$tixianid+1000, //本地的提现id【要求唯一】字符串类型的数字，最大长度11位数
                            'tixianid'=> $DATA['time'], //本地的提现id【要求唯一】字符串类型的数字，最大长度11位数
                            'lailu' =>'sd', //可选参数
                        );


                        $mkey = md5($post_data['mid'].$post_data['jine'].$post_data['openid'].$CONN['tx_key']);

                        $post_data['mkey'] = $mkey;
                        $post_data['lx'] = 999;//保持默认
                        $url ='http://jfcms10.com/jieru.php';

                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        // post数据
                        curl_setopt($ch, CURLOPT_POST, 1);
                        // post的变量
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

                        $output = curl_exec($ch);
                        curl_close($ch);

                        //打印获得的数据
                        //print_r($output);
                        $back = $output;
                        $output = json_decode($output,true);

                        if($output['o'] == 'yes'){  //成功

                            $sql = db('tixiandingdan')-> setshiwu('1') -> where(array('uid' => $DATA['uid'],'time' => $DATA['time'])) -> update(array('state' => 1,'tixianback' => $back));

                            db('tixiandingdan') -> qurey( $sql ,'shiwu');

                            return apptongxin($SHUJU,200,1,"同意申请成功",$YZTOKEN,$WY);

                        }else if($output['o'] == 'shenhe'){

                            $sql = db('tixiandingdan')-> setshiwu('1') -> where(array('uid' => $DATA['uid'],'time' => $DATA['time'])) -> update(array('state' => 2,'tixianback' => $back));

                            db('tixiandingdan') -> qurey( $sql ,'shiwu');

                            return apptongxin($SHUJU,415,-1,'请去提现平台审核订单',$YZTOKEN,$WY);

                        }else if($output['o'] == 'no'){

                            $sql = db('tixiandingdan')-> setshiwu('1') -> where(array('uid' => $DATA['uid'],'time' => $DATA['time'])) -> update(array('state' => -1,'tixianback' => $back));

                            db('tixiandingdan') -> qurey( $sql ,'shiwu');

                            $jine = (float)$DATA['txjine']*$CONN['paybilijb'];

                            $userjine = (float)$jine + ((float)$jine*(float)$CONN['txsxf']);

                            $fan = jiaqian($DATA['uid'],17,0,0,$userjine,'返回金币'.$userjine.'金','',0);
                            if(!$fan){

                                return apptongxin($SHUJU,415,-1,'提现平台'.$output['msg']."请手动返回用户金币",$YZTOKEN,$WY);

                            }

                            return apptongxin($SHUJU,415,-1,'提现平台'.$output['msg']."已返回用户金币",$YZTOKEN,$WY);

                        }

                    }else{
                        //云代付

                        $post_data = array (
                            'appid' => $CONN['tx_uid'], //在掌上零钱里面获取的uid
                            'amount' => (float)$DATA['txjine'], //要请求发放的金额
                            'recipient_openid'=> $DATA['openid'], //用户openid
                            'order_no'=> $DATA['time'], //本地的提现id【要求唯一】字符串类型的数字，最大长度11位数
                            'channel' =>'wx', //支付渠道
                            'description' =>'tx', //订单描述
                        );

                        ksort($post_data);
                        reset($post_data);
                        $md5str = "";
                        foreach ($post_data as $key => $val) {
                            $md5str = $md5str . $key . "=" . $val . "&";
                        }

                        $post_data['sign'] = strtoupper(md5($md5str . "key=" . $CONN['tx_key']));
                        $url = file_get_contents('http://47.104.70.65/api/api/host');
                        $url .='/api/api/withdraw';

                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        // post数据
                        curl_setopt($ch, CURLOPT_POST, 1);
                        // post的变量
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

                        $output = curl_exec($ch);
                        curl_close($ch);

                        //打印获得的数据
                        //print_r($output);
                        $back = $output;
                        $output = json_decode($output,true);

                        if($output['code'] == '40011' && ($output['data']['return_code'] == 'success' || $output['data']['return_code'] == 'SUCCESS')){  //成功

                            $sql = db('tixiandingdan')-> setshiwu('1') -> where(array('uid' => $DATA['uid'],'time' => $DATA['time'])) -> update(array('state' => 1,'tixianback' => $back));

                            db('tixiandingdan') -> qurey( $sql ,'shiwu');

                            return apptongxin($SHUJU,200,1,"同意申请成功",$YZTOKEN,$WY);

                        }elseif ($output['code'] == '40010'){ //审核(审核后没有还回数据)

                            $sql = db('tixiandingdan')-> setshiwu('1') -> where(array('uid' => $sescc['uid'],'time' => $dingdannum)) -> update(array('state' => -1,'tixianback' => $back));
                            db('tixiandingdan') -> qurey( $sql ,'shiwu');
                            msgbox($output['data'],$backlujing);

                        } else{

                            $sql = db('tixiandingdan')-> setshiwu('1') -> where(array('uid' => $DATA['uid'],'time' => $DATA['time'])) -> update(array('state' => -1,'tixianback' => $back));

                            db('tixiandingdan') -> qurey( $sql ,'shiwu');

                            $jine = (float)$DATA['txjine']*$CONN['paybilijb'];

                            $userjine = (float)$jine + ((float)$jine*(float)$CONN['txsxf']);

                            $fan = jiaqian($DATA['uid'],17,0,0,$userjine,'返回金币'.$userjine.'金','',0);
                            if(!$fan){

                                return apptongxin($SHUJU,415,-1,'提现平台'.$output['errmsg']."请手动返回用户金币",$YZTOKEN,$WY);

                            }

                            return apptongxin($SHUJU,415,-1,'提现平台'.$output['errmsg']."已返回用户金币",$YZTOKEN,$WY);

                        }

                    }

                }else{
                    return apptongxin($SHUJU,415,-1,"操作失败，请重试",$YZTOKEN,$WY);
                }

            }else{

                return apptongxin($SHUJU,415,-1,"没有openID",$YZTOKEN,$WY);
    
            }

        }else{

            $fan = $D -> where(array('id'=>(int)$_NPOST['id'])) -> update(array('state'=>(int)$_NPOST['state']));

            if($fan){

                $sql = db('tixiandingdan')-> setshiwu('1') -> where(array('uid' => $DATA['uid'],'time' => $DATA['time'])) -> update(array('state' => -1));

                db('tixiandingdan') -> qurey( $sql ,'shiwu');

                $jine = (float)$DATA['txjine']*$CONN['paybilijb'];

                $userjine = (float)$jine + ((float)$jine*(float)$CONN['txsxf']);

                $fan = jiaqian($DATA['uid'],21,0,0,$userjine,'返回金币'.$userjine.'金','',0);
                if(!$fan){
    
                    return apptongxin($SHUJU,415,-1,"返回金币失败，请手动返回用户金币",$YZTOKEN,$WY);
    
                }
    
                return apptongxin($SHUJU,200,1,"拒绝申请成功，已返回用户金币",$YZTOKEN,$WY);

            }else{

                return apptongxin($SHUJU,415,-1,"操作失败，请重试",$YZTOKEN,$WY);

            }
            
        }
    }else{
        return apptongxin($SHUJU,415,-1,"没有找到这个审核订单",$YZTOKEN,$WY);
    }

}else if($MOD == 'put'){
    /*修改数据*/

}else if($MOD == 'delete'){
    /*删除数据*/

}



return apptongxin($SHUJU,$STAT,$CODE,$MSG,$YZTOKEN,$WY);