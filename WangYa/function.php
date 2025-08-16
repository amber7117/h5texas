<?php if( !defined( 'WYPHP')) exit( 'Error WangYa Game Server');

if( defined( 'WYTEMP')){

    define('Txpath',WYPHP.WYTEMP.'/');

}else{

    define('Txpath',WYPHP.'temp/');
}

if( defined( 'WYCON') && WYCON !=''){

    $CONLJI =  WYPHP.WYCON.".php";

}else{

    $CONLJI =  WYPHP."conn.php";
} 




/*加载配置文件*/
$CONN = include  $CONLJI;

if( defined( 'WYDB') && WYDB !=''){

    $DBLJI =  WYPHP.WYDB.".php";

}else{

    $DBLJI =  WYPHP."config.php";
}
function getmyip(){
    return '43.226.73.12';
} 
/*加载数据配置文件*/
$DBCO = include  $DBLJI;

define('WZHOST',$CONN['HTTP']);

/*调试开关*/
if( isset( $CONN['dbug'] ) && $CONN['dbug'] == '0'){

    error_reporting(!E_ALL);
}

/*时区设置*/
if( isset( $CONN['shiqu']) && $CONN['shiqu'] != ''){

    @date_default_timezone_set($CONN['shiqu']);
}

function zifuzhuan($data)
{

    if( ! get_magic_quotes_gpc() ){

        return addslashes( str_replace( array( '0xbf27' , '0xbf5c27' ), array( "'" , "'" ) , $data ));
    
    }else{

        return $data;
    }
}


class txtcc{

    var $DB = null;

    function __construct($data = '')
    {

        if( $data == ''){

            $this-> DB = Txpath;

        }else{

            $this -> DB = $data;
        }
    }


    public function ja($key,$num = 1,$time = 0)
    {
        if(!$this -> DB){

            return false;
        }

        $shuju = (float)$this -> g($key);
        if(! $shuju){

            $shuju = $num;

        }else{

            $shuju += $num;
        }

        If($time > 0){

            $this -> s($key,(float)$shuju,$time);

        }else{

            $this -> s($key,(float)$shuju);
        }

        return $shuju;
    }


    public function j($key,$num = 1,$time = 0)
    { 

        if( ! $this -> DB )return false;

        $shuju = (float)$this -> g( $key );
        if( ! $shuju ) $shuju = 0;
        $shuju -= $num;

        if($time > 0)
                $this -> s( $key , (float)$shuju , $time );
        else    $this -> s( $key , (float)$shuju );

        return  $shuju;
    }


    public function g($key)
    {
        $pat = $this -> DB .str_replace( '../', '', $key).'.php';

        if(file_exists($pat)){

            $kkk = include $pat;

            if($kkk != ''){

                if(isset($time) && $time > 0 ){

                    clearstatcache();
                    $guoqitime = @filemtime($pat)+$time; 
                    $dangqtime = time();
                   
                    if($dangqtime > $guoqitime){
                        
                        @unlink($pat);
                        return false;

                    }else return  $kkk;
               
                }else return $kkk;

            }else return true;

        }else return false;
    }


    public function d($key)
    {
        $pat = $this -> DB .str_replace('../','',$key).'.php';

        if( file_exists($pat)){

            @unlink($pat);
            return true;

        }else return false;
    }


    public function f($key = '')
    {
        if($key == ''){

            $key = $this -> DB;
        }

        return shanchu( $key );
    }


    public function s($key,$value,$time = '')
    {
        $pat = $this -> DB . str_replace( '../', '', $key).'.php';
        jianli($pat);

        if(!$value){

            $value = '0';
        } 

        if($value != ''){

            if(!is_array($value)){

                $value = "'". zifuzhuan( $value )."'";
            }
        }

        x( $pat , $value , (int)$time );
        return $value;
    }
}


function shanchu($dir,$virtual = false)
{

    $ds = DIRECTORY_SEPARATOR;
    $dir = $virtual ? realpath( $dir ) : $dir;
    $dir = substr( $dir, -1 ) == $ds ? substr( $dir, 0, -1) : $dir;

    if (is_dir( $dir ) && $handle = opendir( $dir )){

        while($file = readdir($handle))
        {
            if($file == '.' || $file == '..'){

                continue;
            }elseif(is_dir($dir.$ds.$file)){

                shanchu($dir.$ds.$file);
            }else{

                unlink( $dir . $ds . $file );
            }
        }

        closedir( $handle );
        rmdir( $dir );
        return true; 
    } 

    return false;
}

function x($filename,$arr = '',$time = '0')
{

    if(is_array($arr)){

        $con = var_export( $arr, true);

    }else{

        $con = $arr;

        if(!$con || $con == ''){

            $con = '0';
        } 
    }

    if($con != ''){

        if($time != '' && $time > 0){
            
            $con = "<?php \$time= '".zifuzhuan($time)."'; return $con;";

        }else{

            $con = "<?php return $con;";
        }
    } 

    conWrite($filename,$con);
    return true;
}


function conWrite($filename,$content)
{
    $filename_lock = $filename.'.lock';
    $os = 0;
    while(1){

        $os++;
        if(file_exists($filename_lock)){

            if($os > 1000){

                unlink($filename_lock);
                break;
            }
            usleep(1);

        }else{

            touch($filename_lock);
            $f = fopen($filename,'w');
            fwrite($f,$content);
            fclose($f);
            unlink($filename_lock);
            break;
        }
    }

    if(file_exists($filename_lock)){

        unlink($filename_lock);
    }

}

function jianli($dir,$zz = ''){

    if( strstr( $dir, "#")){

        return;
    }else if($zz == ''){

        $dirs = substr( strrchr( $dir,'/') , 1);
             
        if($dirs != ''){

            $dir = str_replace( $dirs,'',$dir);
        }

        $dir =  rtrim( $dir ,'/');
    }

  
    if(!is_dir($dir)){

        if(!jianli(dirname($dir),$zz = 2)){

            return false;

        }else if(!mkdir($dir,0777)){
        
            return false;
        }

        chmod($dir, 0777);

    }

    return true;
}


function db($table=null,$zhiding = null)
{
    global $CONN,$DBCO;

    if( $zhiding  == null ){

        $zhiding = $DBCO ;
    }

    $qudong = "D".$CONN['qudong'];
    $DsoftDBs = new $qudong($zhiding);
    return $DsoftDBs -> shezhi($table); 
}

class DsoftDB{

    var $DB=null; 
    var $mysql=null; 
    var $where=null; 
    var $paixu =null;
    var $lismt =null;
    var $sql=null; 
    var $table=null; 
    var $tablejg=null; 
    var $update= null;
    var $charu=null;
    var $bqdoq = null;
    var $SHIWU = 0;
    var $SCSQL = 0;
    var $dqqz = null;
    

public function __construct($data = ''){

    $this-> DB = $data;
    return $this;
}


function limit($data = ''){
    
    if($data != ''){

        $this-> lismt=' LIMIT '.$this -> zifuzhuan($data);
    }

    return $this;
}


function wherezuhe($data = ''){

    $x='';

    if(is_array($data)){

       $zhsss = count($data);
       if($zhsss < 1)return;
       foreach($data as $k=>$v){

            $k=$this->zifuzhuan($k);
            if(!is_array($v))
            $v=$this->zifuzhuan($v);
              if(strstr($k,'>=')){
              $k= trim(str_replace('>=','',$k));
              $x.=" and `$k` >= '$v'";
             }else if(strstr($k,'>')){
              $k= trim(str_replace('>','',$k));
              $x.=" and `$k` > '$v'";
             }else if(strstr($k,'(')){

                if($v == 'and') $v='and';
                else            $v ='OR';

                $x.=" $v (";

             }else if(strstr($k,')')){

                $x.=" ) ";

             }else if(strstr($k,'<=')){
              $k= trim(str_replace('<=','',$k));
              $x.=" and `$k` <= '$v'";
             }else if(strstr($k,'<')){
              $k= trim(str_replace('<','',$k));
              $x.=" and `$k` < '$v'";
             }else if(strstr($k,'!=')){
              $k= trim(str_replace('!=','',$k));
              $x.=" and `$k` != '$v'";
             }else if(strstr($k,'OLK')){
              $k= trim(str_replace('OLK','',$k));
              $x.=" OR `$k` LIKE '$v'";
             }else if(strstr($k,'LIKE')){
              $k= trim(str_replace('LIKE','',$k));
              $x.=" and `$k` LIKE '$v'";
             }else if(strstr($k,'OR')){
              $k= trim(str_replace('OR','',$k));
              $x.=" OR `$k` = '$v'";
             }else if(strstr($k,'NOTIN')){ //not in
                  $k= trim(str_replace('NOTIN','',$k));
                  if(is_array($v))
                      $x.=" and `$k` NOT IN(". implode(',',$v).")";
                  else
                      $x.=" and `$k` NOT IN($v)";
             }else if(strstr($k,'IN')){
              $k= trim(str_replace('IN','',$k));
               if(is_array($v))
                  $x.=" and `$k` IN(". implode(',',$v).")";
               else
                  $x.=" and `$k` IN($v)";
              }else if(strstr($k,'DAYD')){
                $k= trim(str_replace('DAYD','',$k));
                $x.=" and $k >= $v";
              }else if(strstr($k,'DAY')){
                $k= trim(str_replace('DAY','',$k));
                $x.=" and $k > $v";
              }else if(strstr($k,'XIYD')){
                $k= trim(str_replace('XIYD','',$k));
                $x.=" and $k <= $v";
              }else if(strstr($k,'XIY')){
                $k= trim(str_replace('XIY','',$k));
                $x.=" and $k < $v";
              }else if(strstr($k,'DEY')){
                $k= trim(str_replace('DEY','',$k));
                $x.=" and $k = $v";
              }else
              $x.=" and `$k`='$v'";
        }
         $x=str_replace(array('( OR ','( and '),array('( ','( '),$x);
         $x=(ltrim(trim($x),'OR'));

   }else $x.=$data;
      
    return 'WHERE '.(ltrim(trim($x),'and'));

 }

public function zuheset($data = '')
{

    if(!is_array($data)){
        
        return $data;
    }

   $chaxun = $this->tablejg[1];
   $x=array();
    foreach($chaxun as $k=>$v){

        if(isset($data[$k])&& $v !='auto_increment'){

            $tzhi = $this->zifuzhuan($data[$k]);

            if( $tzhi == '' ){

                $moren =  explode('_',$v);
                if( ! isset( $moren['1'] ) ) $moren['1'] = '';
                if( $moren['1'] == '0') $tzhi = $moren['1'];
            }

            $x[]="`$k` = '{$tzhi}'";
           
        }else if(isset($data[$k.' +']))
           $x[]="`$k` = $k + '{$this->zifuzhuan($data[$k.' +'])}'"; 
        else if(isset($data[$k.' -']))
           $x[]="`$k` = $k - '{$this->zifuzhuan($data[$k.' -'])}'"; 
    }

    return implode(',',$x);
}

public function charuset($data = '')
{

    if( !is_array( $data))return null;

    $chaxun = $this->tablejg[1];
    $xv = array();

    foreach($chaxun as $k=>$v){

        if(isset($data[$k])&& $data[$k] !='' &&$v !='auto_increment'){

            $tzhi = $this->zifuzhuan($data[$k]);

            if( $tzhi == ''){

                $moren =  explode('_',$v);

                if( ! isset( $moren['1'] ) ) $moren['1'] = '';

                if( $moren['1'] == '0') $tzhi = $moren['1'];

            }

            $xv[]= "'$tzhi'";

        }else{
            if($v =='auto_increment');
            else $xv[] = "'".str_replace($k.'_','',$v)."'";
        }
    }

    $ndd=array();

    foreach($this->tablejg[1] as $ttm=>$vvv){

          if($vvv !='auto_increment')$ndd[]=$ttm;
    }

    return '('.implode(',',$ndd).')VALUES ('.implode(',',$xv).')';
  
}


function pqsql($DATA,$woqu = 1)
{

    if(! is_array( $DATA ))return null;

    $qian = "INSERT INTO   `{$this->table}` ({$this->tablejg[0]})VALUES";
    $sql=$qian;
    $i=1;
    $num = count($DATA);
    $shuju = ceil( $num / 10);
    if($num > 1000 || $shuju < 100) $shuju = 1000;

    foreach($DATA as $anyou){

        if($i % $shuju == 0){

            $sql=rtrim($sql,',');
            $sql.=';'.$qian.$anyou.',';

        }else{

            $sql.=$anyou.',';
        }

        $i++;
    }

     $sql = rtrim( $sql , ',' );

    if($woqu != '1') return $sql;

    if($this -> SHIWU == 1 ) return  $this-> qurey( $sql , 'shiwu');
    else                     return  $this-> qurey( $sql , 'other');

}
function psql($data = '',$bfeifn = 1)
{
       
    if(!is_array($data))return null;

    $chaxun = $this->tablejg[1];
    $xv =array();

    foreach($chaxun as $k=>$v){

        if(isset($data[$k])&& $data[$k] !='' &&$v !='auto_increment'){


            $tzhi = $this->zifuzhuan($data[$k]);

            if( $tzhi == ''){

                $moren =  explode('_',$v);

                if( ! isset( $moren['1'] ) ) $moren['1'] = '';
                if( $moren['1'] == '0') $tzhi = $moren['1'];
            }

            $xv[]= "'$tzhi'";


        }else{
            if( $bfeifn != '1'){

                $xv[]="'{$this->zifuzhuan($data[$k])}'";

            }else{

                if($v =='auto_increment') $xv[] ='NULL';
                else $xv[] = "'".str_replace($k.'_','',$v)."'";

            }

         }
    }

    return '('.implode(',',$xv).')';
  
}

function order($data = '')
{
    if($data !='') $this->paixu = ' ORDER BY '.$data;
    return $this;
}

function where($data = '')
{
    if($data !='') $this->where = $this->wherezuhe($data);
    return $this;
}


function pwhere()
{
    $this -> SCSQL = 1;
    return $this;
}

function find($data=''){

    if($data !=''){
        if(is_array($data)){

            $this->where = $this->wherezuhe($data);

        }else{

            $chaxun = $this->tablejg[1];

            foreach($chaxun as $k =>$v){

                if($v == 'auto_increment'){

                    $dataf[$k.' IN']=$data;
                    break;
                }
            }

            $this->where = $this->wherezuhe($dataf); 
        }

    }

    return  $this->zhixing('find');
}

function setshiwu($wo = 0)
{
    $this->SHIWU = $wo;
    return $this;

}

function zhicha($datasl){

    if($datasl!='')$this->tablejg['0'] =$datasl;
    return $this;
}

function total($data=''){
          if($data !='')
          $this->where = $this->wherezuhe($data);
          return  $this->zhixing('zongshu');
}

function select($data=''){ 
          if($data !='')
          $this->where = $this->wherezuhe($data);
          return  $this->zhixing('select');
}
function qurey($data='',$moth='other'){ 
         $this->sql=$data;
         return  $this->zhixing($moth);
} 
function query($data='',$moth='other'){ 
         $this->sql=$data;
         return  $this->zhixing($moth);
}
function update($data=''){
       
         if($data=='')return false;
         $this->update = $this->zuheset($data);
         return  $this->zhixing('xiugai');
}

function delete($data=''){

         if($data !=''){
              if(is_array($data))
                 $this->where = $this->wherezuhe($data); 
              else{
                $chaxun = $this->tablejg[1];
                foreach($chaxun as $k =>$v){
                   if($v=='auto_increment'){
                      $dataf[$k.' IN']=$data;break;
                    }
                }
              $this->where = $this->wherezuhe($dataf); 
             }
         }

         return  $this->zhixing('shanchu');
}
function biao(){
        return $this->table;
}
function insert($data=''){
         $this->charu =$this->charuset($data);
         return  $this->zhixing('charu');
}


function setbiao( $data = '' ){

         global $CONN,$Mem;  

         $suiji =  $this -> dqqz;
         
         $qianzui = $this->DB[$suiji]['qian'];

         if($data != ''){ 

            $this->table = $this->zifuzhuan( $qianzui.$data );
         
            $HASH = 'db/'.mima($this->DB[$suiji]['name'].$this->table);
            $huanc = $Mem -> g( $HASH );
            if( $huanc && $CONN['qxx'] == 1) $this -> tablejg = $huanc;
            else{

                $qq = $this -> zhixing('scjg');
                $gege['0'] = $chaxun = implode(',',array_flip( $qq ));
                $gege['1'] = $qq; 
                $this->tablejg = $gege;            
                $Mem->s( $HASH , $gege );              
            }
         }
    
        return $this;
}


function shezhi($data=''){

         global $CONN;  
         if($CONN['modb'] == '')
                  $this->bqdoq = 'write';
         else $this->bqdoq = $CONN['modb']; 

         if($CONN['duob']=='1')
         $suiji =array_rand($this->DB, 1);
         else $suiji = $this->bqdoq;
         

         $this->dqqz = $suiji;
   
         $this->lianjie($this->DB[$suiji]);   
       
         if($data!=''){
         $qianzui = $this->DB[$suiji]['qian']; 
        
         $this->table = $qianzui.$data;
         
         $HASH = 'db/'.mima($this->DB[$suiji]['name'].$this->table);           
         global $Mem;
         $huanc = $Mem->g($HASH);              
         if($huanc && $CONN['qxx'] == 1) 
            $this->tablejg =$huanc;             
           else{

              $qq= $this->zhixing('scjg');        
              $gege['0'] = $chaxun = implode(',',array_flip($qq));
              $gege['1'] = $qq; 
              $this->tablejg =$gege;            
              $Mem->s($HASH,$gege);              
            }
        }
    
       return $this;
}

public function zifuzhuan($data){

      if(!get_magic_quotes_gpc()) return addslashes(str_replace(array('0xbf27','0xbf5c27'),"'",$data));else return $data;
}


}

class Dpdo extends  DsoftDB{ 

    public function lianjie($data){ 
    
        try {

            $pdo = new PDO("mysql:host={$data['host']};port={$data['port']};dbname={$data['name']}", $data['user'], $data['pass'], array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$data['char']}") );
     
        } catch (PDOException $e) {

            echo ('db_error:' .$data['h'].' '.wlx($e->getMessage()));
            return false;
        }

        $this->mysql = $pdo; 
        return $pdo;
    }

    public  function zhixing($moth='',$sql=''){ 

        global $CONN;
        $DATA = array();
              
        if($moth=='scjg'){
            
                 $sql = "desc `{$this->table}`";


                 $qq = $this->mysql->prepare($sql);
                 $qq ->execute();
                 while($row=$qq->fetch(PDO::FETCH_ASSOC)){
                     
                     $DATA["{$row['Field']}"]=$row['Extra']==''?$row['Field'].'_'.$row['Default']:$row['Extra'];
                  }
                 if( $this -> SCSQL == 1 ){ p( $DATA );}
                  return  $DATA;

        }else if($moth=='find'){ 

                 $chaxun = $this->tablejg[0]; 
                 if( $chaxun == '' ) $chaxun = '*';
                 $sql = "SELECT $chaxun FROM  `{$this->table}` {$this->where} {$this->paixu} LIMIT 0 , 1"; 
              
                 $this->where = $this->paixu = null;

                 if( $this -> SCSQL == 1 ){ p( $sql );$this -> SCSQL = 0;}

                 $qq = $this->mysql->prepare($sql);
                  $qq ->execute();
                 $row=$qq->fetch(PDO::FETCH_ASSOC);
                 if(!$row)return false;
                 else return $row; 
                 
        }else if($moth=='select'){

                 $chaxun = $this->tablejg[0];
                 if( $chaxun == '' ) $chaxun = '*';
                 $sql = "SELECT $chaxun FROM  `{$this->table}` {$this->where} {$this->paixu} {$this->lismt}";
                 
                 $this->where = $this->paixu = $this->lismt = null;

                 if( $this -> SCSQL == 1 ){ p( $sql );$this -> SCSQL = 0;}

                 $qq = $this->mysql->prepare($sql);
                 $qq ->execute();
                 $row=$qq->fetchAll(PDO::FETCH_ASSOC);
                 if(!$row)return false; 
                 else return $row;  

        }else if($moth=='charu'){ 

                  $sql = "INSERT INTO   `{$this->table}` {$this->charu}";
                  $this->charu = null;
                  if( $this -> SCSQL == 1 ){ p( $sql );$this -> SCSQL = 0;}

                  if( $this->SHIWU == 1)return $sql.';@;';
                  if( $CONN['duob'] == '1' && $this->bqdoq !=  $this->dqqz) 
                  $this->lianjie($this->DB[$this->bqdoq]); 
                  $qq = $this->mysql->prepare($sql);

                  $qq ->execute();
                  $id = $this->mysql->lastInsertId();
                  if($id)return $id ; 
                  else return false;
             

        }else if($moth=='shanchu'){
              
                $sql = "DELETE FROM  `{$this->table}` {$this->where}  {$this->lismt}";
                
                $this->where = $this->lismt = null;

                if( $this -> SCSQL == 1 ){ p( $sql ); $this -> SCSQL = 0;}

                 if( $this->SHIWU == 1)return $sql.';@;';
                       if( $CONN['duob'] == '1' && $this->bqdoq !=  $this->dqqz) 
                       $this->lianjie($this->DB[$this->bqdoq]);  
                      $qq = $this->mysql->prepare($sql);
                      $qq ->execute();
                       if($qq ->rowCount())return true; 
                       else return false;
                
         
         }else if($moth=='xiugai'){ 
                 
                 $sql = "UPDATE   `{$this->table}` SET {$this->update}  {$this->where}  {$this->lismt}";

                  if( $this -> SCSQL == 1 ){ p( $sql );$this -> SCSQL = 0;}
                
                 $this->where = $this->update = $this->lismt = null;
                 if( $this->SHIWU == 1)return $sql.';@;';
                          if( $CONN['duob'] == '1' && $this->bqdoq !=  $this->dqqz) 
                          $this->lianjie($this->DB[$this->bqdoq]);   
                           $qq = $this->mysql->prepare($sql);
                          $qq ->execute();
                         if($qq ->rowCount())return true; 
                         else return false;
                  

         }else if($moth=='zongshu'){

                          $chaxun = $this->tablejg[0]; 
                          if( $chaxun == '' ) $chaxun = '*';

                          $sql = "SELECT count(*) as count FROM  `{$this->table}` {$this->where} {$this->paixu} {$this->lismt}";
                          if( $this -> SCSQL == 1 ){ p( $sql );$this -> SCSQL = 0;}
                           $this->where = $this->paixu = $this->lismt = null;
                          $qq = $this->mysql->prepare($sql);
                          $qq ->execute();
                          $row=$qq->fetch(PDO::FETCH_ASSOC);
                        
                          return $row['count'];
         
         }else if($moth=='other'){ 

                if( $CONN['duob'] == '1' && $this->bqdoq !=  $this->dqqz) 
                $this->lianjie($this->DB[$this->bqdoq]); 

                if( $this -> SCSQL == 1 ){ p( $this->sql );$this -> SCSQL = 0;}
                $qq = $this->mysql->prepare($this->sql);
                $qq ->execute();
                $row=$qq->fetch(PDO::FETCH_ASSOC);
                if(!$row)return false; 
                else return $row;
                  
        }else if($moth=='erwei'){

                if( $CONN['duob'] == '1' && $this->bqdoq !=  $this->dqqz) 
                $this->lianjie($this->DB[$this->bqdoq]);  
                if( $this -> SCSQL == 1 ){ p( $this->sql );$this -> SCSQL = 0;}

                $qq = $this->mysql->prepare($this->sql);
                $qq ->execute();
                $row=$qq->fetchAll(PDO::FETCH_ASSOC);
                if(!$row)return false; 
                else return $row;  
        }else if($moth=='accse'){
                
                if( $CONN['duob'] == '1' && $this->bqdoq !=  $this->dqqz) 
                $this->lianjie($this->DB[$this->bqdoq]); 
                
                if( $this -> SCSQL == 1 ){ p( $this->sql );$this -> SCSQL = 0;}
                
                 $qq = $this->mysql->prepare($this->sql);
                  $row= $qq ->execute();
                   
                
                  if(!$row)return false; 
                  else return true; 
        
        
        
        }else if($moth=='shiwu'){
                       
                        if( $CONN['duob'] == '1' && $this->bqdoq !=  $this->dqqz) 
                        $this->lianjie($this->DB[$this->bqdoq]);  
                        $this->mysql->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);

                        try{

                              $this->mysql->beginTransaction();

                              if( $this -> SCSQL == 1 ){ p( $this->sql );$this -> SCSQL = 0;}
                              $zhiss =  explode(';@;',$this->sql);

                              foreach($zhiss as $tsss){

                                      if( $tsss == '')continue ;
                                      $woud = $this-> mysql ->exec($tsss.';');
                                      if(!$woud){
                                          $wodw = new txtcc;
                                          $wodw ->s('sqlerror/'.time().'_'.rand(1,9999999),$tsss.' @@@@@ '.$this->sql);
                                          $this-> mysql -> rollback(); 
                                          $this->sql = NULL;
                                          $this->mysql->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);

                                          return false;
                                       }
                              }

                           $fanhui =  $this->mysql->commit();
                           $this->sql = NULL;
                           $this->mysql->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
                           return  $fanhui;

                        }catch( PDOExecption $e ) { 

                             $wodw = new txtcc;
                             $wodw ->s('sqlerror/'.time().'_'.rand(1,9999999), $this->sql);
                             $this-> mysql -> rollback();
                             $this->sql = NULL;
                             $this->mysql->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
                             return false;

                        }

               }
    }




}

/*设置游戏数据 A 标识的自动序列化数据*/
function gameccset( $SHUJU,$type = 'serialize' ){


    foreach($SHUJU as $k => $v){

        if( substr($k,0,1) == 'A'){

            if($type == 'json'){
                $SHUJU[$k] = json_encode($v);
            }else{
                $SHUJU[$k] = serialize($v);
            }

        }
    }

    return $SHUJU;
}

/*设置游戏数据 A 标识的自动解压数据*/
function gameccget( $SHUJU,$type = 'serialize' ){

    foreach($SHUJU as $k => $v){

        if( substr($k,0,1) == 'A'){

            if($v != ''){

                if($type == 'json' ){
                    $SHUJU[$k] = json_decode($v,true);
                }else{
                    
                    $SHUJU[$k] = unserialize($v);
                }

            }else{

                $SHUJU[$k] = array();
            }
        }
    }

    return $SHUJU;
}



function ydsend($Y = 'quite',$DATA = '')
{
    if($DATA == '' ){

        return json_encode(array('y' => $Y));
    
    }else{

        return json_encode(array('y' => $Y,'d' => $DATA));
    }
}


function udpsendde($DATA = '')
{
    /*UDP解包*/
    if($DATA == ''){

        return false;

    }else{

        return unserialize($DATA);
    }
}

function udpsenden( $DATA  = '' )
{
    /*UDP封包*/
    if($DATA == ''){

        return false;

    }else{
    
        return serialize($DATA);
    }
}


function updserver($serv, $data, $addr,$server,$type = 'serialize')
{

    $DATA = udpsendde($data);

    if(isset($DATA['m'])){

        $yuanM = $DATA['m'];
        unset($DATA['m']);

        global $CONN;

        $nmm = md5('大'.$CONN['txkey'].udpsenden($DATA).'这是一个通信密码');

        if($nmm ==  $yuanM ){

            if(isset($DATA['t']) &&strlen($DATA['t']) == 32 && isset($DATA['u'])){

                global $SQTOKEN,$USERCC;

                $SQTOKEN ->set($DATA['t'], array('u' =>$DATA['u']));

                $fan = $USERCC ->set($DATA['u'],array('u' => $DATA['u'] ,'t' => $DATA['t'],'f' =>(int)$DATA['f']));

                if($fan){

                    return $serv->sendto($addr['address'], $addr['port'], udpsenden(array('code' => '1') ), $addr['server_socket']);


                }else{
                     return $serv->sendto($addr['address'], $addr['port'], udpsenden(array('code' => '-1') ), $addr['server_socket']);
                }

                

            }else if( isset( $DATA['y'])){

                if($DATA['y'] == 'myudp'){

                    global $USERCC;
                    $shuju = $DATA['d'];

                    $shuju['d'] = $shuju['d'];

                    if(isset($shuju['time']))unset($shuju['time']);

                    if(isset($shuju['d']['tt'])){

                        $tonguser = $shuju['d']['tt'];
                        unset($shuju['d']['tt']);

                        if(isset($shuju['uid'])) unset($shuju['uid']);

                        foreach($tonguser as $iid)
                        {
                            $zhi = $USERCC->get($iid);

                            $tongid =  isset($zhi['z'] )?$zhi['z']: 0 ;

                            if( $tongid > 0){

                                if( $server->exist($tongid))
                                {
                                    $server->push( $tongid , json_encode($shuju));
                                }
                            }
                        }

                    }else{

                        $iid = $shuju['uid'];

                        $zhi = $USERCC->get($iid);
                        $tongid =  isset($zhi['z'] )?$zhi['z']: 0 ;
                        

                        if( $tongid > 0){

                            if( $server->exist($tongid))
                            {
                                $server->push( $tongid , json_encode($shuju));
                            }
                        }

                    }

                }else if($DATA['y'] == "gonggao"){

                    /*全局公告发送*/
                    global $FIDDCC;

                    $serv->sendto($addr['address'], $addr['port'], udpsenden(array('code' => '1') ), $addr['server_socket']);

                    $SHUJU = array(
                        'y' => "gonggao",
                        'd' =>$DATA['d']
                    );

                    foreach($FIDDCC as $fid =>$ddd)
                    {
                        if( $server->exist($fid))
                        {
                            $server->push( $fid , json_encode($SHUJU));
                        }
                    }

                    return ;



                }else if($DATA['y'] == "getjiangchi"){

                    /*修改系统维护*/
                    global $JIANGCHI;

                    $jiangchi = ($JIANGCHI -> get())/100;

                    return $serv->sendto($addr['address'], $addr['port'], udpsenden(array('jiangchi' => $jiangchi) ), $addr['server_socket']);

                }else if($DATA['y'] == "setbetjine"){

                    /*修改系统维护*/
                    global $GAMECC,$Mem;
                    $GAME = gameccget($GAMECC -> get(1));
                    if((int)$GAME['off'] == 0 || (int)$GAME['off'] == 3 || (int)$GAME['off'] == 4){
                        $data = $DATA['d'];
                        
                        $GAMECC -> set(1,array( 'Acmpan' => serialize($data)));

                        return $serv->sendto($addr['address'], $addr['port'], udpsenden(array('jiangchi' => $jiangchi) ), $addr['server_socket']);
                    }

                }else if($DATA['y'] == "getbetjine"){

                    /*修改系统维护*/
                    global $GAMECC,$Mem;
                    $GAME = gameccget($GAMECC -> get(1));

                    return $serv->sendto($addr['address'], $addr['port'], udpsenden(array('betjine' => $GAME['Acmpan']) ), $addr['server_socket']);

                }else if($DATA['y'] == "online"){

                    /*读取各种场次在线人数*/

                    $RENSHU = array();
                    global $USERCC;

                    foreach($USERCC as $iid => $shuju){

                        if(isset($shuju['c'])){

                            $tid = $shuju['c'];

                            if(!isset($RENSHU[$tid])){

                                $RENSHU[$tid] = 0;
                            }

                            $RENSHU[$tid]++;
                        }
                    }

                    return $serv->sendto($addr['address'], $addr['port'], udpsenden(array('y' => 'online','d' => $RENSHU )) , $addr['server_socket']);

                }else if($DATA['y'] == 'fangcha'){

                    global $GAMECC;
                    $gggm = $GAMECC->get((int)$DATA['d']);
                    if( $gggm ){

                        return $serv->sendto($addr['address'], $addr['port'], udpsenden(array('code' => '1','msg' => gameccget($gggm,$type) )) , $addr['server_socket']);

                    }else{

                         return $serv->sendto($addr['address'], $addr['port'], udpsenden(array('code' => '-1') ), $addr['server_socket']);
                    }  

                
                }else if($DATA['y'] == 'fangexit'){     /* 房主强行解散房间 */

                    global $GAMECC;
                    if($gggm = $GAMECC->get((int)$DATA['d'])){

                        $HASH = (int)$DATA['d'];

                        global $Mem;


                        $GAME = gameccget($gggm);

                        $HASHs = $_POST['gameid'].'/'. $HASH;


                        $Mem -> d( $HASHs );

                        $GAMECC ->del($HASH);

                        foreach( $GAME['Auser'] as $uid){


                           $sss = $Mem -> g('gameuid/'.$uid);

                            if( $sss ){


                                if( $sss['gid'] == $_POST['gameid'] && $sss['fangid'] == $HASH ){

                                    $Mem -> d('gameuid/'.$uid);

                                }
                            }
                        
                        }
                        GameFang('gdtuo',$HASH,$GAME,1);
                        myudp( array('y' => 'exit' ,'fid'=>$HASH, 'd' => array( 'tt'=>$GAME['Auser']),'uid' => 0 ));

                        return $serv->sendto($addr['address'], $addr['port'], udpsenden(array('code' => '2') ), $addr['server_socket']);

                    }else  return $serv->sendto($addr['address'], $addr['port'], udpsenden(array('code' => '-1') ), $addr['server_socket']);

                }else if($DATA['y'] == 'jiaru'){

                    global $GAMECC;

                    $FID = (int)($DATA['d']['fid']);

                    $UID = (int)($DATA['d']['uid']);



                    $GAMES = $GAMECC->get($FID);

                    if($GAMES && $UID > 0){

                        $GAME = gameccget($GAMES);

                        $sren = $GAME['xren'] - count($GAME['Auser']);

                        if($sren > 0){

                            $GAME['Auser'][] = $UID;
                            $GAME['Auser'] = array_unique($GAME['Auser']);

                            $GAMECC->set($FID, array('Auser' => serialize($GAME['Auser'])));


                             return $serv->sendto($addr['address'], $addr['port'], udpsenden(array('code' => '1') ), $addr['server_socket']);
                        
                        }else{
                        
                            return $serv->sendto($addr['address'], $addr['port'], udpsenden(array('code' => '-1') ), $addr['server_socket']);
                        }


                    }else{
                    
                        return $serv->sendto($addr['address'], $addr['port'], udpsenden(array('code' => '-1') ), $addr['server_socket']);
                    }


                    $yiqunren = array_unique($yiqunren);


                
                }else if($DATA['y'] == 'cjian'){


                    /*创建房间*/
                    global $USERCC,$GAMECC;




                    if( strpos( $_POST['gameid'] , "K" )  ){
                    
                        $DATA['isfguan'] = $DATA['uid'];
                        $DATA['fangid']  = $DATA['changci'];

                    }


                    $DATA['fangid'] = (int)($DATA['fangid']);


                    if($DATA['isfguan'] > 0){

                        $FANGGUAN = $USERCC->get($DATA['isfguan']);

                        if(!$FANGGUAN ) $FANGGUAN = array();

                        if( isset($FANGGUAN['f']) && $FANGGUAN['f'] > 0){

                            $GFM = $GAMECC -> get( (int)$FANGGUAN['f'] );

                            if($GFM){
                                
                                /*房间存在*/
                                return $serv->sendto($addr['address'], $addr['port'], udpsenden(array('code' => '2') ), $addr['server_socket']);
                                
                            }
                        }


                    }

                    $USERCC->set($DATA['isfguan'],array('f' => $DATA['fangid'] ,'c' => isset($DATA['changci'])?$DATA['changci']:0 ));


                     $fan = $GAMECC -> set($DATA['fangid'],gameccset($DATA));

    
                    if($fan  ){

                        return $serv->sendto($addr['address'], $addr['port'], udpsenden(array('code' => '1') ), $addr['server_socket']);
                    
                    
                    } else {

                        return $serv->sendto($addr['address'], $addr['port'], udpsenden(array('code' => '-1') ), $addr['server_socket']);
                    
                    }
 
                }
            }
        }

    }

    return $serv->sendto($addr['address'], $addr['port'], udpsenden(array('code' => '-1') ), $addr['server_socket']);

}


function GameGbAll($J,$CHUAN,$YUGEM,$USERCC,$server,$connection,$FANGID = 'no')
{
    /*
    $J 通信action [y]

    $CHUAN 通信数据 [d]
    $YUGEM 广播的人
    $USERCC 获得用户的FD 通信id

    $server 网络总线
    $connection ws当前
    */


	if(!$YUGEM){

		return ;
	}



    foreach($YUGEM as $uid){

        $linshi = $USERCC -> get($uid);
        $tongid =  isset($linshi['z'])?$linshi['z']:0;

        if( $tongid > 0 && ( $FANGID == 'no' || (int)$linshi['f'] == (int)$FANGID )){

            if( $server->exist($tongid))
            {

                $fan = $connection -> push($tongid,ydsend($J,$CHUAN));

                
            }
        }
    }

   

    return $connection;

}

/*输出函数*/
function p() { 

    $args = func_get_args();
    if( count( $args )<1){
      echo ( "<font color='red'> Debug" );
      return;
    }
    
    echo '<div style="width:100%;text-align:left"><pre>';

    foreach( $args as $arg){

            if( is_array( $arg)){  
                print_r( $arg );
                echo '<br>';
            }else if( is_string( $arg)){
                echo $arg.'<br>';
            }else{ 
                var_dump($arg);
                echo '<br>';
            }
    }

    echo '</pre></div>';
}




/*获取用户uid*/
function   uid( $id , $qx = '' ,$D = ''){

           /*
            用户uid
            $id 帐号id
            $qx  = 2 删除缓存返回失败
                 = 1 强行更新缓存
             返回 -1 加大缓存力度
           */

            $id = (float) trim( $id);

            if( $id < 1 )return false;

            global $Mem; 
            $hash = 'uid/'.$id ;
     
            if( $qx == 2){

                $Mem -> d( $hash);
                return false;
            }

            $data = $Mem -> g( $hash);

            if( $data === '-1' && $qx == '')return false;

            if( $data && $qx == '')return $data;

            if( $D == ''){ 

                $db = db('user');
                $dbc = $db  -> where( array( 'uid' => $id) )-> find();

           }else $dbc = $D ->setshiwu('0') ->setbiao('user')  -> where( array( 'uid' => $id) )-> find();

           if( $dbc){

                    $Mem ->s ( $hash, $dbc);
                    return $dbc;

           }else{ 
                    $Mem ->s ( $hash, '-1', 10);
                    return false;
           }
}


/*获取用户的绝对路径头像*/
function touxiang( $tx = ''){

         if($tx != '') return pichttp( $tx) ;
         else return  pichttp('/attachment/touxiang/'.rand(0,13).'.png');
}




function pichttp( $url ){

        if($url == '') return  WZHOST.ltrim( '/attachment/touxiang/'.rand(0,13).'.png' ,'/');
        if( strstr( $url , "://" ) )return $url;
        else{ 

          
           
           
            return WZHOST.ltrim( $url,'/');

            

        }

}


function GameFang($GAMeid,$FANGid,$GAME,$OFF = 0)
{
    $mhash = md5( $GAMeid.'_'.$FANGid.'_'.date('ym'));
    /*
    更新房间详细数据
    $OFF状态
    $GAMeid 游戏类型
    $FANGid 房间id
    $GAME   游戏数据
    */
    global $Mem;
    
    return db('fanglist') ->where( array( 'mhash' => $mhash) )-> update(array(

        'qishu' => $GAME['qishu']?$GAME['qishu']:$GAME['qishux'],
        'stime' => time(),
        'off' => $OFF,  //0：正常 1:已结束 2:已过期
        'userin' => ','.implode(',',$GAME['Auser']).',',
        // 'neirong' => $GAME['AllTongji'],
        'neirong' => var_export($GAME,true),
        'fangzhu' => $GAME['isfguan'],
    ));
}

function GameFangx($GAMeid,$FANGid,$GAME)
{
    /*
    写入单局房间详细数据
    $GAMeid 游戏类型
    $FANGid 房间id
    $GAME   游戏数据
    */
    return db('gamejiu') -> insert(array(

        'gameid' => $GAMeid,
        'fangid' => $FANGid,
        'qishu' => $GAME['qishu']?$GAME['qishu']:$GAME['qishux'],
        'mhash' => md5($GAMeid.'_'.$FANGid.'_'.($GAME['qishu']?$GAME['qishu']:$GAME['qishux']).'_'.date('ym')),
        'atime' => time(),
        'neirong' => serialize($GAME)
    ));
}

function fenpeiip($FANGID,$tongxin)
{
    /*根据房间id 分配到服务器*/

    if($FANGID < 1){

        return false;
    }

    if($tongxin == ''){

        return false;
    }

    $dizhi = explode('#WY#',$tongxin);
    $zongshu = count($dizhi);

    $fenpei = ($FANGID % $zongshu);
    $tongxin = explode(':',$dizhi[$fenpei]);

    if(count($tongxin) != 2 &&  count($tongxin) != 3 ){
        return false;
    }
    return array('ip'=> trim($tongxin['0']) ,'port' => $tongxin['1'],'wss' => isset($tongxin['2'])?$tongxin['2']:$tongxin['0']);
}



function myudp( $shuju = array())
{

    global $CONN,$Mem;

  

    $GAMEIDIP = Game_Server( $_POST['gameid'] );


    $IP = fenpeiip($shuju['fid'],$GAMEIDIP);

    unset($shuju['fid']);

    
        
        

    if($IP ){

        //Swoole\Coroutine::create(function() use ($shuju,$IP,$CONN) {

            $client =  new swoole_client(SWOOLE_SOCK_UDP);


            if (!$client->connect(  $IP['ip'], $IP['port'], 0.01 ))
            // if (!$client->connect( '127.0.0.1', $IP['port'],1))
            {
                
                exit($IP['ip'] .' : '. $IP['port']." connect failed. Error: {$client->errCode}\n");
            }

            $shuju['time'] = time().rand(1,9);

            $shujud = array();

            $shujud['d'] =  $shuju;

            $shujud['y'] = 'myudp';

            $mima = md5('大'.$CONN['txkey'].udpsenden($shujud).'这是一个通信密码');

            $shujud['m'] = $mima;

            $fan = $client->send(udpsenden( $shujud  ));
             
            $client -> close();

        //});
         
    }else return false;
}


function mima( $var = 'wangya' ){
        
		 if(! $var ) $var = time().'b891037e3d772605f56f8e9877d8593c';
         $varstr = strlen( $var );
		 if( $varstr < 1 ) $varstr = 32;
         $hash = md5( ('#@$^%&^*&(#'.md5( base64_encode( $var.'.@#!$#@%#soft.com'.md5($var).'WangYa'. $var.'][{)(*&^%#@!~1d').'@monsof ~!~$^%&^*&(t'. $varstr). $varstr));

         return substr( $hash ,1 , $varstr * 3 );
}


function erro404( $MSGBOX = '' , $response , $start = 404 ){

    /*输出错误信息*/

    header('Content-Type:text/html;charset=UTF-8');
   
    exit($MSGBOX);

}

function htmlhead( $HERD  = 'text/html;charset=UTF-8' ,$response = ''){

    /*输出head头*/

    return  header("Content-Type:".$HERD);
}

function htmlout($MSGBOX = '',$response = '',$start = 200){

 
    exit($MSGBOX);

}

/*空数组分享*/
$JPGE = $PNG = $GIF = $OTHER =$CSS = $HTML = $JS = array();


function listmit( $page_size, $page){ 
    
        $page= (float)( $page) <= 0 ? 1 : $page;
        $page_size = (float)( $page_size) <= 0 ? 1 : $page_size;
        return $pages = ( ( $page-1) * $page_size). "," . $page_size;
}



function wlx($mingzi,$shuchu=1){

        if( file_exists( WYPHP.'Conn.php')){

            if( $shuchu !=  1 )
                 return iconv( "UTF-8", "GBK//IGNORE", $mingzi);
            else
                 return iconv( "GBK", "UTF-8//IGNORE", $mingzi);

        }else return $mingzi;
}


function isutf8( $word ){


    if(function_exists( 'mb_detect_encoding' ) )
    return (mb_detect_encoding( $word , 'UTF-8') === 'UTF-8');

    if (preg_match("/^([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){1}/" , $word) == true || preg_match("/([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){1}$/" , $word) == true || preg_match("/([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){2,}/" , $word) == true) return true;
         else  return false;
}


function vcode($UHA,$sizes='0',$code="0123456789",$shu =4,$width=100,$height=38,$zadian=100,$xiaos = 8){

        global $CONN,$Mem;

        $image = imagecreatetruecolor($width, $height);
        imagefill($image, 0, 0, imagecolorallocate($image, 255, 255,245));

        if( $code == '' ) $code = "0123456789"; 

        $ascii='';
        if($sizes == '0') $ZITI =rand(1,10);
        else              $ZITI = $sizes;

        $size = rand(25,30);

        imagesetthickness($image,rand(0,50)) ;

        $zhufu = explode(',',$CONN['vcode']);

        for( $i = 0 ; $i < $shu ; $i++ ){

            if($sizes < 0 ) $ZITI = rand( 1 , 10 );

            if($sizes == -2){

                $char = $zhufu[$i].'                                ....__-$%#$^^6634'.rand(1,999999);
                $ZITI = 11;

            }else $char = $code{rand(0,strlen($code)-1)};

            $COLOR = imagecolorallocate($image, rand(0,200), rand(0,200), rand(0,200));

            $shus = $i*($width/$height)*$xiaos ;
            $tux = $shus+rand(1,5);
            $tuy =  (int)($height/2)+rand(2,$size);

            $coordinates = imagefttext($image,$size ,rand(-5,20), $shus+rand(1,5), $tuy ,$COLOR  , WYPHP.'Font/'.$ZITI.'.ttf' ,$char, array('character_spacing' => 20));

            if(rand(0,3) == 1  ) imagearc( $image, $tux+rand(-5,20), $tuy-rand(1,3), 5, 5, 1, rand(0,$ZITI), $COLOR );

            for($z=0; $z<=$i*$zadian; $z++) imagesetpixel($image,rand($tux-30,$tux+30),rand($tuy-30,$tuy+30),$COLOR);
            
            $ascii.=$char;
        }

        if(rand(0,2) == 1) imagearc($image, $tux+rand(10,20), $tuy-rand(1,5), 5, 5, 1, rand(0,$ZITI), $COLOR);

        $sescc = sescc('code', isset( $CONN['sicode'] ) && $CONN['sicode'] == 1 ? $ascii : strtolower( $ascii ) ,$UHA) ;

        $sescc = sescc('codetime', time(),$UHA) ;

        imagepng( $image );
        imagedestroy( $image );

}




function isemail($data){

        /*验证邮箱*/
        return preg_match('/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/',$data);

}


function isshouji($data){

        /*验证手机*/
//        return preg_match('/^1\d{10}$/',$data);
    return preg_match('/^(13[0-9]|14[5|7]|15[0|1|2|3|5|6|7|8|9]|18[0|1|2|3|5|6|7|8|9])\d{8}$/',$data);
}


class memcc{

    function __construct( $servers ){

            $md = new Memcache;
            if( is_array( $servers[0] )){

                foreach ( $servers as $server ) call_user_func_array(array( $md, 'addServer'), $server);

            } else call_user_func_array( array( $md, 'addServer'), $servers);

            $this->md = $md;
    }


    public function s( $key, $value, $time = 0){
           $time = (int)$time;
           return $this -> md -> set( md5( $key), $value, MEMCACHE_COMPRESSED, $time);
    }


    public function g( $key){
           return $this -> md -> get( md5( $key));
    }


    public function d( $key){
           return $this -> md -> delete( md5( $key)); 
    }


    public function f(){ 
           return $this -> md -> flush();
    }


    public function j( $key, $num=1,$time = 0){

                $shuju = (float)$this -> g( $key );
                if( ! $shuju ) $shuju = 0;
                $shuju -= $num;
                $this -> s( $key , (float)$shuju , $time );
                return $shuju;
    }


    public function ja( $key, $num = 1 , $time = 0){

                $shuju = (float)$this -> g( $key );
                if( ! $shuju ) $shuju = $num;
                else           $shuju += $num;
                $this -> s( $key , (float)$shuju , $time );
                return $shuju;

    }


}


class memccd{

    function __construct( $servers ){

            $md = new Memcached;
            if(is_array($servers)){

                foreach($servers as $server ){

                     if(!is_array($server)){

                           $SER = explode( ":" , $server );
                           $md->addServer( $SER['0'], $SER['1'] ,0);

                     }else{

                           $md  -> addServers ( $server );
                     }

                }


            }else{
                $SER = explode( ":" , $servers );

                $m->addServer( $SER['0'], $SER['1'] ,0);

            }


          

            $this->md = $md;
    }


    public function s( $key, $value, $time = 0){
           $time = (int)$time;
           return $this -> md -> set( md5( $key), $value , $time);
    }


    public function g( $key){
           return $this -> md -> get( md5( $key));
    }


    public function d( $key){
           return $this -> md -> delete( md5( $key)); 
    }


    public function f(){ 
           return $this -> md -> flush();
    }


    public function j( $key, $num=1,$time = 0){

                $shuju = (float)$this -> g( $key );
                if( ! $shuju ) $shuju = 0;
                $shuju -= $num;
                $this -> s( $key , (float)$shuju , $time );
                return $shuju;
    }


    public function ja( $key, $num = 1 , $time = 0){

                $shuju = (float)$this -> g( $key );
                if( ! $shuju ) $shuju = $num;
                else           $shuju += $num;
                $this -> s( $key , (float)$shuju , $time );
                return $shuju;

    }


}





function post( $curlPost , $url , $urls = 'www'){

         $ch = curl_init(); 
         curl_setopt( $ch , CURLOPT_URL, $url);
         curl_setopt( $ch , CURLOPT_TIMEOUT, 20);

         if( strstr( $url , 'https' )){
             curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0);
             curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0);
         }


         curl_setopt( $ch, CURLOPT_HEADER, false);
         curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt( $ch, CURLOPT_REFERER, $urls);
         curl_setopt( $ch, CURLOPT_POST, 1);
         curl_setopt( $ch, CURLOPT_POSTFIELDS, $curlPost);
         $data = curl_exec( $ch);
         curl_close( $ch ); 
         return $data;
}


function qcurl( $HTTP_Servr , $kai = 1){

      
         $HTTP_Server  =  $HTTP_Servr;
         $ch = curl_init();
         curl_setopt( $ch , CURLOPT_URL, $HTTP_Server);
         curl_setopt( $ch , CURLOPT_RETURNTRANSFER, true);
         curl_setopt( $ch , CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
      
         $contents = curl_exec( $ch );
         curl_close ( $ch );
         if( $kai == 1) $contents = iconv( "gb2312" , "UTF-8//IGNORE" , $contents);
         else           $contents = iconv(   $kai   , "UTF-8//IGNORE" , $contents);

         return  $contents;
}



function qfopen( $HTTP_Servr , $kai = 1){

	     $handle = fopen ( $HTTP_Servr, "rb");
	     $contents = ""; 
	     do { 
	        $data = fread($handle, 10240); 
	        if ( strlen( $data ) == 0) break; 
	        $contents .= $data; 
         }while( true ); 
	     fclose ( $handle );

         if( $kai == 1) $contents = iconv( "gb2312" , "UTF-8//IGNORE" , $contents );
         else           $contents = iconv(   $kai   , "UTF-8//IGNORE" , $contents);
        return  $contents;
}


function sslget( $url, $cacert_url = ''){

         $curl = curl_init( $url );
         curl_setopt( $curl , CURLOPT_HEADER, 0 );
         curl_setopt( $curl , CURLOPT_RETURNTRANSFER, 1);
       
         if($cacert_url != ''){

            curl_setopt( $curl , CURLOPT_SSL_VERIFYPEER, 2);
            curl_setopt( $curl , CURLOPT_SSL_VERIFYHOST, true);
            curl_setopt( $curl , CURLOPT_CAINFO, $cacert_url);

         }else{

            curl_setopt( $curl , CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt( $curl , CURLOPT_SSL_VERIFYHOST, 0);

         }

         $responseText = curl_exec( $curl );
         curl_close( $curl );
         return $responseText;
}


function sslpost( $url,  $para, $cacert_url = '', $input_charset = ''){

         if (  trim( $input_charset ) != '') $url = $url . "_input_charset=" . $input_charset;
         $curl = curl_init( $url );
         

         if( $cacert_url != ''){

            curl_setopt( $curl , CURLOPT_SSL_VERIFYPEER, 2);
            curl_setopt( $curl , CURLOPT_SSL_VERIFYHOST, true);
            curl_setopt( $curl , CURLOPT_CAINFO , $cacert_url);

         }else{

            curl_setopt( $curl , CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt( $curl , CURLOPT_SSL_VERIFYHOST, 0);

         }
         curl_setopt( $curl , CURLOPT_HEADER, 0 ); 
         curl_setopt( $curl , CURLOPT_RETURNTRANSFER, 1);
         curl_setopt( $curl , CURLOPT_POST, true); 
         curl_setopt( $curl , CURLOPT_POSTFIELDS, $para);
         $responseText = curl_exec( $curl );
         curl_close( $curl );
         return $responseText;

}


function apptongxin( $data = array() , $start = '200' ,  $code = '0' , $msg = '' , $apptoken = '' ,$WY ='')
{
         
    /*  $data 数据 array()
       
        $code 业务码
        $msg  一些系统提示
        $apptoken 传递通信token

        200 操作成功
        401 需要登录用户
        500 内部服务器错误
        304 修改失败
        410 删除失败
        404 查询失败
        406 新增失败
        415 非法数据 token错误
    */

    global $CONN;

    if( $CONN['dbug'] == '0' )ob_clean();

    header('HTTP/1.1 '. $start);

    if( $start != 200 && $start != 401 && $start != 415 && $CONN['dbug'] == '0' ) exit();

    exit(json_encode( array( 
        'code'  => $code,
        'data' => $data ,
        'msg' => $msg ,
        'token' => $apptoken,
        'debug'=>2,
    )));
}


function token(){

        return md5('令牌'. time() . rand(1,999999).'token产生');
}


function sescc($k ='' , $v ='', $UHDDA = ''){
    if(is_array($k)){

        foreach($k as  $iii =>$vvv){

            $_SESSION[$iii] = $vvv;

        }

    }else{
        $_SESSION[$k] = $v;
    }

    return $_SESSION;
}


function yzpost( $canshu = array(), $_NPOST){

        /* 验证POST */
     
        if( $canshu ){

            foreach( $canshu as $ong ){

                list( $name , $type , $zhi ) = explode( '#' , $ong );

                if( !isset( $_NPOST[$name] ) ) return array( 'code' => '0' , 'biao' => $name ,'msg' => $zhi );

                $_NPOST[$name] = trim( $_NPOST[$name] );

                if( $type == 'len' ){

                    if( $_NPOST[$name] == '' ) return array( 'code' => '0' , 'biao' => $name ,'msg' => $zhi );

                    list( $XI , $DA ) = explode( '-' , $zhi.'-' );

                    $zlen = strlen( $_NPOST[$name] );

                    if( $DA != '' ){
                       
                        if($zlen < $XI || $zlen > $DA  ) return array( 'code' => '0' , 'biao' => $name ,'msg' => $XI.'-'.$DA);

                    }else if( $zlen != $XI ) return array( 'code' => '0' , 'biao' => $name , 'msg' => $XI );

                }else if( $type == '=' ){

                    if( $_NPOST[$name] != $zhi ) return array( 'code' => '0' , 'biao' => $name ,'msg' => $zhi );
                }
            }
        }

        return  array( 'code' => '1' , 'biao' => 'all' ,'msg' => '' );
}

function adminlog( $id, $type = 0, $data = '', $ip = ''){

        /*管理日志记录
           $id    管理人员uid
           $type  管理日志分类 0 登录 1 退出 2 被挤掉  3 修改 4 删除 5 添加
           $data  日志详细设置
           $ip    指定ip记录
        */

        $adminlog = db('adminlog');
        return $adminlog -> insert( array(  'uid' => $id ,
                                           'type' => $type ,
                                           'data' => $data ,
                                             'ip' => $ip == '' ? ip(): $ip,
                                          'atime' => time()
                                          )
                                    ); 
}


function adminfenzu( $type = '0' ){

        /*
            读取管理分组
            -1 全部分组读取出来
        */

        if( $type == '0') return '0';

        $adminfenzu =  db( 'adminfenzu');
       
        if( $type == -1)
            $shuju = $adminfenzu -> select();
        else
            $shuju = $adminfenzu -> where( array( 'id' => $type) ) -> find();

        if( $shuju) return $shuju; 
        else        return false;
}


function userlog( $id, $type = 0, $data = '', $ip = ''){

        /*
           管理日志记录
           $id    管理人员uid
           $type  管理日志分类 0 登录 1 退出 2 被挤掉  3 修改 4 删除 5 添加
           $data  日志详细设置
           $ip    指定ip记录
        */

        $adminlog = db('userlog');
        return $adminlog -> insert( array( 'uid' => $id ,
                                          'type' => $type ,
                                          'data' => $data ,
                                            'ip' => $ip == '' ? ip(): $ip,
                                         'atime' => time()
                                          )
                                  ); 
}


function jiaqian( $UID , $TYPE = 0 , $JINE = 0,$JIFEN = 0 , $HUOBI = 0  , $DATA = '' , $ip = '',$YONGJIN = 0,$tuiji = 0,$integral = 0){

          /* 加钱
             $UID  用户uid
             $TYPE = 0 , $JINE = 0,$JIFEN = 0 , $HUOBI = 0  , $DATA = '' , $ip = ''
             $integral 积分
          */
         
          $CONN = include WYPHP.'conn.php';
          
        $D = db( 'user');

        $sql = '';

        

        if( $JIFEN != '0'){

            $sql .= $D -> setshiwu(1) -> where( array( 'uid' => $UID,'jifen >='=> ($JIFEN < 0 ? -$JIFEN: 0 ) )) -> update( array( 
                   
                   'jifen +' => $JIFEN,
                  
                 )
                );

            $sql .=$D -> setshiwu(1) -> setbiao('jifenlog') -> insert(array(   'uid' => $UID,
                               'type' => $TYPE,
                               'jine' => $JIFEN,
                               'data' => $DATA,
                                 'ip' => $ip ==''?ip(): $ip,
                              'atime' => time()
                        ));
        }

        if( $JINE != '0'){

              $sql .= $D -> setshiwu(1)-> setbiao('user') -> where( array( 'uid' => $UID ,'jine >='=> ($JINE < 0 ? -$JINE: 0 ) )) -> update( array( 
                    'jine +' => $JINE,
                   
                 )
                );

            $sql .=$D -> setshiwu(1) -> setbiao('jinelog') -> insert(array(   'uid' => $UID,
                               'type' => $TYPE,
                               'jine' => $JINE,
                               'data' => $DATA,
                                 'ip' => $ip ==''?ip(): $ip,
                              'atime' => time()
                        ));
        }


        if( $HUOBI != '0'){
            //   $sql .= $D -> setshiwu(1)-> setbiao('user') -> where( array( 'uid' => $UID,'huobi >='=> ($HUOBI < 0 ? -$HUOBI: 0 ))) -> update( array( 
              
            //        'huobi +' => $HUOBI,
            //      )
            //     );
            $UDATA = uid($UID,1);
            if((int)$TYPE == 20 && (int)$HUOBI < 0){
                $fan = $D -> setbiao('user') -> where(array('uid' => $UID)) -> update( array('fbjine +' => abs($HUOBI)));

                if((int)$UDATA['fbjine'] >= (int)$CONN['apkhbfbgetnum']){
        
                    $sql .= $D -> setshiwu(1)-> setbiao('user') -> where(array('uid' => $UID)) -> update( array('fbjine -' => $CONN['apkhbfbgetnum'],'turnnum +' => 1));
                }
            }
        
            if((int)$TYPE == 21 && (int)$HUOBI > 0){
                $fan = $D -> setbiao('user') -> where(array('uid' => $UID)) -> update( array('qbjine +' => abs($HUOBI)));
                
        
                if((int)$UDATA['qbjine'] >= (int)$CONN['apkhbqbgetnum']){
        
                    $sql .= $D -> setshiwu(1)-> setbiao('user') -> where(array('uid' => $UID)) -> update( array('qbjine -' => $CONN['apkhbqbgetnum'],'turnnum +' => 1));
                }
            }

            $sql .= $D -> setshiwu(1)-> setbiao('user') -> where( array( 'uid' => $UID)) -> update( array( 
              
                'huobi +' => $HUOBI,
              )
             );
             if($UDATA['off'] == 1){
                $sql .= $D -> setshiwu(1) -> setbiao('huobilog') -> insert(array(   'uid' => $UID,
                'type' => $TYPE,
                'jine' => $HUOBI,
                'data' => $DATA,
                  'ip' => $ip ==''?ip(): $ip,
               'atime' => time()
        ));
             }
            
        }
        
        if( $YONGJIN != '0'){

            $fan = $D -> setbiao('user') -> where(array('uid' => $UID)) -> update( array('yjjine +' => abs($YONGJIN)));
            $UDATA = uid($UID,1);

            if((int)$UDATA['yjjine'] >= (int)$CONN['apkhbyjgetnum']){

                $sql .= $D -> setshiwu(1)-> setbiao('user') -> where(array('uid' => $UID)) -> update( array('yjjine -' => $CONN['apkhbyjgetnum'],'turnnum +' => 1));
            }

            $sql .= $D -> setshiwu(1)-> setbiao('user') -> where( array( 'uid' => $UID,'yongjin >='=> ($YONGJIN < 0 ? -$YONGJIN: 0 ))) -> update( array( 
            
                 'yongjin +' => $YONGJIN,
               )
              );
              if($UDATA['off'] == 1){
          $sql .= $D -> setshiwu(1) -> setbiao('huobilog') -> insert(array( 'uid' => $UID,
                             'type' => $TYPE,
                             'jine' => $YONGJIN,
                             'data' => $DATA,
                               'ip' => $ip ==''?ip(): $ip,
                               'haomiaotime' => $tuiji,
                            'atime' => time()
                     ));
                    }
      }

      //积分
      if( $integral > 0 ){

          $sql .= $D -> setshiwu(1)-> setbiao('user') -> where( array( 'uid' => $UID ,'integral >='=> ($integral < 0 ? -$integral: 0 ) )) -> update( array(
                  'integral +' => $integral,

              )
          );

          $sql .=$D -> setshiwu(1) -> setbiao('huobilog') -> insert(array(   'uid' => $UID,
              'type' => $TYPE,
              'jine' => $integral,
              'data' => $DATA,
              'ip' => $ip ==''?ip(): $ip,
              'atime' => time()
          ));
          
      }


        $fan = $D -> qurey($sql ,'shiwu');

       

        if( $fan ){

            return uid($UID ,1,$D );

        }else return false;

}



function regsong( $USER ){

    /* 注册赠送
       $USER 用户最新信息
    */
    if($USER && $USER['uid'] > 0 ){

        global $CONN;

        if($CONN['regsongfk'] == "0" && $CONN['regsongjb'] == "0"  && $CONN['regsongje'] == "0"){

            return ;
        }

        global $Mem;
        $issong = $Mem -> g('regsong/'.$USER['uid']);

        if(!$issong){
            /*新人注册赠送*/
            $USER = jiaqian($USER['uid'],6,$CONN['regsongje'],$CONN['regsongfk'],$CONN['regsongjb']);
        }
        
        $Mem ->s('regsong/'.$USER['uid'],$CONN['regsongjb']);

        if($USER['tuid'] > 0  && ($CONN['shangjlfk'] > 0 || $CONN['shangjljb'] > 0 || $CONN['shangjlje'] > 0 )){

            /*
            给推广的人赠送房卡
            */
            $IIP = mima( ip());

            global $Mem;

            $time = mktime(0,0,0,date('m'),date('d')+1,date('Y')) - time();

            $XYIP =  $Mem ->ja('xyip/'.$IIP , 1 , $time);

            if($XYIP > $CONN['tuisongip'])return false;
            /*推广好友赠送*/
            jiaqian( $USER['tuid'] , 7 , $CONN['shangjlje'] , $CONN['shangjlfk'],$CONN['shangjljb'] , $USER['uid']);

            $USER = jiaqian($USER['uid'],7,$CONN['shangjlje'],$CONN['lurutuifk'],$CONN['lurutuijb'],$USER['tuid']);


        }
        
        return $USER;
    }
}

function ip() {
    $ip1 = getenv("HTTP_CLIENT_IP")?getenv("HTTP_CLIENT_IP"):"none";
    $ip2 = getenv("HTTP_X_FORWARDED_FOR")?getenv("HTTP_X_FORWARDED_FOR"):"none";
    $ip3 = getenv("REMOTE_ADDR")?getenv("REMOTE_ADDR"):"none";
    $ip4 = $_SERVER['REMOTE_ADDR']?$_SERVER['REMOTE_ADDR']:"none";

    if (isset($ip3) && $ip3 != "none" && $ip3 != "unknown") $ip = $ip3;
    else if (isset($ip4) && $ip4 != "none" && $ip4 != "unknown") $ip = $ip4;
    else if (isset($ip2) && $ip2 != "none" && $ip2 != "unknown") $ip = $ip2;
    else if (isset($ip1) && $ip1 != "none" && $ip1 != "unknown") $ip = $ip1;
    else $ip = $_SERVER['REMOTE_ADDR'];

    if( strstr( $ip, ",")){
        $woqu = explode(',',$ip);
        $ip = $woqu['0'];
    }
    return $ip;
}


function anquanqu( $name ){
    
    return str_replace( array( '#','/','，','|','、','\\','*','_','-','?','<','>','.',"\n","\r",'【','】','(',')','：','{','}','\'','"',':',' ',';',' '), array(),trim($name));
    // return str_replace( array( '#','/','，','|','、','\\','*','_','-','?','<','>','.',"\n","\r",'【','】','(',')','：','{','}','\'','"',':',' ',';',' '), array(), strtolower( trim($name)));
}


function logacto( $logac , $lx = 1 ){
        
        /*logac 解析表单系列 */
        if($lx == 2)
             $fan = $logac;
        else $fan = logac( $logac );

        if( $fan ){

            $shuju = array();
            foreach( $fan as $k => $v) $shuju[] = $k.','.$v;
            return implode('@', $shuju );

        }else return ',全部';
}


function logac( $name = '', $qx ='' ){

        /* 
            $name  一般等于表的名字
            $qx    强行更新
        */

        global $Mem;

        if( $name == '' ) return false;

        $HASH = 'logac/'.mima( $name);
        $DATA = $Mem -> g( $HASH );

        if( $DATA === '-1') return false;
        if( $DATA && $qx == '' )return $DATA;

        $D = db( 'logac');
        
        $SHUJU  = $D -> where( array( 'type' => $name )) -> find();

        if( $SHUJU ){
            
            if( $SHUJU['data'] != '' ){

                $DATA = explode( ',' , $SHUJU['data']);
                $Mem -> s( $HASH , $DATA);

                return $DATA;
            }
        }
        
        $Mem -> s( $HASH ,'-1','10');

        return false;
}



function logacjiexi( $logac ){
 

        $D = db( 'logac');

        $SHUJU  = $D -> where( array( 'type' => $logac )) -> find();

        if( $SHUJU ){
            
            if( $SHUJU['data'] != '' ){

                $tmen = explode('@',$SHUJU['data']);
                 $level = false;

                if( $tmen ){
                     $level = array();

                    foreach($tmen as $mm ){

                        $tt = explode(',',$mm);
                        $level[$tt['0']] = $tt['1'];
                    }
                }

                return $level;
            }
        }

        return false;
}




function kjreg( $lx = 0 , $uid = '' , $nc = '' , $tx ='' , $uindd = '',$sex = 0){

         /* 快捷注册 */

        global $CONN;
        $WHere = array('off' =>  1,
                        'ip' =>  IP(),
                      'level' => 0,
                 'yanzhengip' =>  $CONN['yanzhengip'],
                      'atime' =>  time());

                      $WHere['hbqun'] = '1,2,3,';
         if( kjcha( $lx , $uid , $uindd ) ) return false;

         $D = db('user');
         if($lx == 1) $WHere['qqopen'] = $uid;
         else if($lx == 2) $WHere['weixin'] = $uid;
         else if($lx == 3) $WHere['weibo'] = $uid;
         else if($lx == 4) $WHere['zhifubaoopen'] = $uid;
         else if($lx == 5) $WHere['openid'] = $uid;
         else if($lx == 6) $WHere['openidd'] = $uid;



        if( ( $lx == 2 ||  $lx == 5 ) && $uindd != '' ) $WHere['weixinui'] = $uindd;
       
         //$nc = trim(  preg_replace('/[\xF0-\xF7].../s','',anquanqu(  $nc )));
         
         if($nc == '') $nc = mima( rand(1,888888));

         $WHere['name'] =  $nc;

  

         $WHere['touxiang'] = xiazaipic(touxiang($tx));

         $sql = '';
         if( isset( $_SESSION['tuid'] )){

             if( $_SESSION['tuid']  > 0){

                 $tuid =  uid( $_SESSION['tuid'] );

                 if( $tuid ){

                     $WHere['tuid'] = $_SESSION['tuid'];

                     for( $i = 1 ; $i < $CONN['tuiji'] ; $i++ ){
                             $wds = $i-1;
                             if($wds < 1) $wds= '' ;
                             $WHere['tuid'.$i] = $tuid['tuid'.$wds];

                             if($tuid['tuid'.$wds] > 0){
                                $sql .= $D -> setshiwu('1') -> where(array('uid' => $tuid['tuid'.$wds])) -> update(array('xiajicount +' => 1));
                             }
                             
                    }

                     if( $tuid['vip'] == 1 ) $WHere['vipid'] = $tuid['uid'];
                     elseif ( $tuid['vipid'] > 0 ) $WHere['vipid'] = $tuid['vipid'];

                 }
             }
         }


        $WHere['xingbie'] = "$sex";
        

        $sql .= $D -> setshiwu('1')  -> insert( $WHere );

        return $D -> qurey( $sql ,'shiwu');

}




function bangding( $lx = 0 , $UID = '' , $uid = '' , $nc = '' , $tx ='', $weixinui = '' ){

         /* 快捷登录绑定查询
           2 weixin          用户微信登录openid   唯一
             ( 2,5 )  weixinui 微信uiopenid         唯一
           1 qqopen          用户QQ登录openid     唯一
           3 weibo           新浪微博登录openid   唯一
           4 zhifubaoopen        支付宝登录openid     唯一
           5 openid          备用openid           唯一
           6 openidd         备用openid1          唯一
         */

         $WHere = array();

         if( kjcha( $lx , $uid  , $weixinui ) ) return false;


         $D = db('user');
         if($lx == 1) $WHere['qqopen'] = $uid;
         else if($lx == 2) $WHere['weixin'] = $uid;
         else if($lx == 3) $WHere['weibo'] = $uid;
         else if($lx == 4) $WHere['zhifubaoopen'] = $uid;
         else if($lx == 5) $WHere['openid'] = $uid;
         else if($lx == 6) $WHere['openidd'] = $uid;
        if( $nc != ''){
            $nc = ( anquanqu(  $nc ) );
            if($nc == '') $nc = mima( rand(1,888888));
            $WHere['name'] =  $nc;
        }

        if( ( $lx == 2 ||  $lx == 5 ) && $weixinui != '' ) $WHere['weixinui'] = $weixinui;

        if( $tx != '') $WHere['touxiang'] = xiazaipic(touxiang( $tx ));

        $sql = $D -> setshiwu('1') -> where( array( 'uid' => $UID) ) -> update( $WHere );

        $fan = $D -> qurey( $sql ,'shiwu');

        global $_NGET;

        if($fan && isset ( $_NGET['state'] ) &&  strlen( $_NGET['state']) == 32){

                 global $Mem;
                 $HASH = 'kjdenglu/'.mima( $_NGET['state']  );
                 $Mem -> s($HASH, $UID ,20);
         }

        return $fan ;

        
}


function kjcha( $lx = 0 , $uid = '' , $unopid = '' ){

         /* 快捷登录绑定查询
           2 weixin          用户微信登录openid   唯一
             ( 2,5 )  weixinui 微信uiopenid       唯一
           1 qqopen          用户QQ登录openid     唯一
           3 weibo           新浪微博登录openid   唯一
           4 zhifubaoopen        支付宝登录openid 唯一
           5 openid          app openid           唯一
           6 openidd         备用openid1          唯一
             weixinui        微信uiopenid         唯一
            
         
         */

         if( $uid == '' ) return array('uid' => 0);

         $WHere = array();
         $D = db('user');
         if($lx == 1) $WHere['qqopen'] = $uid;
         else if($lx == 2) $WHere['weixin'] = $uid;
         else if($lx == 3) $WHere['weibo'] = $uid;
         else if($lx == 4) $WHere['zhifubaoopen'] = $uid;
         else if($lx == 5) $WHere['openid'] = $uid;
         else if($lx == 6) $WHere['openidd'] = $uid;

        if( ( $lx == 2 ||  $lx == 5 ) && $unopid != '') $WHere['weixinui OR'] = $unopid;

        $fan = $D ->where( $WHere )-> find();

        if( $fan &&  isset( $WHere['weixinui OR'] ) && $fan['weixinui'] == '' ){



            $sql  = $D ->setshiwu(1) -> where(array('uid' => $fan['uid'] ) )-> update( array( 'weixinui' =>  $unopid ) );

            $mywo = $D -> qurey( $sql );

            if($mywo ) uid(  $fan['uid'] ,1 );



        }

         if($fan && isset ( $_GET['state'] ) &&  strlen( $_GET['state']) == 32){

                 global $Mem;
                 $HASH = 'kjdenglu/'.mima( $_GET['state']  );
                 $Mem -> s($HASH, $fan['uid'] ,20);
         }

		if($fan && $fan['name'] == ""){

			$sql  = $D ->setshiwu(1) -> where(array('uid' => $fan['uid'] ) )-> update( array( 'name' =>"ID:".$fan['uid'] ) );
            $mywo = $D -> qurey( $sql );
            if($mywo ) uid(  $fan['uid'] ,1 );
		}

         return $fan ;


}


function timecj()
{
    /*
    $y  = time() - mktime(0,0,0,1,1,date('y'));
    return $y.rand(1,9);
    */

    $y  = date('is');
    return rand(1,9).$y.rand(0,9);

}

function timecj2()
{
   
    $y  = time() - mktime(0,0,0,1,1,date('y'));
    return $y.rand(10,99);

}


function fangid( $Mem , $GAMEID ){  /* 房间ID生成器 */

        $chushi = timecj();

        $FANGID = $Mem ->g($GAMEID.'iid');
        if( $FANGID ){

            if( $FANGID < $chushi){

                $FANGID =  $chushi;

                $Mem ->s($GAMEID.'iid',$FANGID);

            }else{

                $FANGID = $Mem ->ja($GAMEID.'iid');
            
            
            }
            
          
        
        }else $FANGID = $chushi;



        $FANGID = str_pad($FANGID, 6, "0", STR_PAD_LEFT);

        if( $Mem -> g( $GAMEID.'/'. $FANGID)) return fangid( $Mem , $GAMEID );
   
        return  $FANGID;
}


function fangid2( $Mem , $GAMEID ){  /* 房间ID2生成器 */

        $chushi = timecj2();

        $FANGID = $Mem ->g($GAMEID.'iid');
        if( $FANGID ){

            if( $FANGID < $chushi){

                $FANGID =  $chushi;

                $Mem ->s($GAMEID.'iid',$FANGID);

            }else{

                $FANGID = $Mem ->ja($GAMEID.'iid');
            
            
            }
            
          
        
        }else $FANGID = $chushi;



        $FANGID = str_pad($FANGID, 6, "0", STR_PAD_LEFT);

        if( $Mem -> g( $GAMEID.'/'. $FANGID)) return fangid( $Mem , $GAMEID );
   
        return  $FANGID;
}




function Game_Chuang( $Mem , $GAMEID ,$USERID ,$PASSMM ,$USER,$GAMEIDIP,$GAME ){


        $FANGID = fangid( $Mem,$GAMEID ) ;


        $IP = fenpeiip($FANGID,$GAMEIDIP);


        if(!$IP)return false;

     

        $TONGXIN = array( 
            'isfguan' => $USERID,
            'qishu' => 1,
            'shuohua' => $USERID,
            'uid' => $USERID,
            'y' => 'cjian',
            'fangid' => $FANGID,
            'user' => array($USERID),
            'userinfo' => array( $USERID =>array( 'u' => $USERID,
                                                                 'n' => $USER['name'],
                                                                 't' => touxiang($USER['touxiang']),
                                                                 'sex' => $USER['xingbie']
                                                    )
                                   ),
            
        
        );


	

        $TONGXIN = array_merge($TONGXIN, $GAME ); 

		
        $fan = httpudp($TONGXIN,$IP['ip'], $IP['port']);
        if(! $fan || $fan['code'] == '-1') return false;

        if(!strpos( $GAMEID, "K" ) && $GAMEID != "pipei" && $GAMEID != "online"){

            $Mem -> s( 'fangid/'.((int)$FANGID), $GAMEID );
        }

        return $FANGID;


}


function Game_Chuang_other( $Mem , $GAMEID ,$USERID ,$PASSMM ,$USER,$GAMEIDIP,$GAME )
{   /*金币排位模式*/

        $FANGID = fangid2( $Mem,$GAMEID );
        $IP = fenpeiip($FANGID,$GAMEIDIP);

        if(!$IP)return false;
        $TONGXIN = array( 
            'isfguan' => 0,
            'qishu' => 1,
            'uid' => $USERID,
            'shuohua' => 0,
            'y' => 'cjian',
            'fangid' => $FANGID,
            'user' => array(),
            'userinfo' => array(),
            'ctime' => time()
        );

        $GAME['fangid'] = $FANGID;

        

        $TONGXIN = array_merge($TONGXIN, $GAME );
        $TONGXIN['isfguan'] = 0;
        $TONGXIN['fangid'] = $FANGID;

        $fan = httpudp($TONGXIN,$IP['ip'], $IP['port']);
        if(! $fan || $fan['code'] == '-1') return false;


        if(!strpos( $GAMEID, "K" )&& $GAMEID != "pipei" && $GAMEID != "online"){

            $Mem -> s( 'fangid/'.((int)$FANGID), $GAMEID );
        }

        return $FANGID;
}


function Game_Chuang_Dli( $Mem , $GAMEID ,$USERID ,$PASSMM ,$USER,$GAMEIDIP,$GAME ){

        $FANGID = fangid( $Mem,$GAMEID ) ;
      

        $IP = fenpeiip($FANGID,$GAMEIDIP);

        if(!$IP)return false;

     

        $TONGXIN = array( 
            'isfguan' => 0,
            'qishu' => 1,
            'shuohua' => 0,
            'y' => 'cjian',
            'uid' => $USERID,
            'fangid' => $FANGID,
            'user' => array(),
            'userinfo' => array()
        );

        $TONGXIN = array_merge($TONGXIN, $GAME );
        $fan = httpudp($TONGXIN,$IP['ip'], $IP['port']);
        if(! $fan || $fan['code'] == '-1') return false;



        if(!strpos( $GAMEID, "K" ) && $GAMEID != "pipei" && $GAMEID != "online"){

            $Mem -> s( 'fangid/'.((int)$FANGID), $GAMEID );
        }


        return $FANGID;

}



function ingame( $Mem ,$HASH, $GAMEID,$FANGID,$USERID,$tongxin = '' ){

       

    $USERONLINE = $Mem -> g( $HASH );

    if(!$USERONLINE || !is_array( $USERONLINE )) $USERONLINE = array();

    if( isset( $_POST['fangmm'] ) && $_POST['fangmm'] != '' && (!isset( $USERONLINE['fangmm'] ) || $USERONLINE['fangmm'] == '') )
    {
        $USERONLINE['fangmm']  = $_POST['fangmm'];
    }
    

    $USERONLINE['gid'] = $GAMEID;

    if( isset( $_POST['isfguan']) ) $USERONLINE['isfguan'] = 1;

    
    $USERONLINE['fangid'] = $FANGID;
    $TONGXIN = md5( token().'扎_金_'.rand(1,99999).'_花房_卡'.$USERID);
    $USERONLINE['t']   = $TONGXIN;

    if( !strpos( $GAMEID , "K" )){

        $Mem -> s( $HASH ,$USERONLINE );

    }


    $IP = fenpeiip($FANGID,$tongxin);

    if(!$IP)exit( json_encode( apptongxin( array()  ,'415', '-1' , '没有服务器通信地址,请联系管理')) );


    $usesuju = array('t'=>$TONGXIN,'u' => $USERID,'f' => $FANGID );

    $fan = httpudp($usesuju,$IP['ip'],  $IP['port'] );

    if(!$fan || $fan['code'] == '-1') exit( json_encode( apptongxin( array()  ,'415', '-1' , '服务器通信失败,请联系管理')) );

    return array( 'y' => 'ingame',
                  'd' => array(  't' => $TONGXIN , //游戏通信 token
                                    'ip' => $IP['wss'] ,  //分配的服务器ip
                                  'port' => $IP['port'],
                                   'gid' => $GAMEID,
                                   'fid' => $FANGID
                                )
            );


}


function anquanqub( $name ){

        return str_replace( array( '#','/','，','|','、','\\','*','?','<','>',"\n","\r",'【','】','(',')','：','{','}','\'','"',':',' ',';',' '), array(), trim($name));
}


function httpudp($usesuju = array(),$IP = '127.0.0.1', $PROT = '8000'){

    global $CONN;

    $usesuju['m'] = md5('大'.$CONN['txkey'].udpsenden($usesuju).'这是一个通信密码');
    $msg = udpsenden($usesuju);

    $times = 3;

    if(isset($usesuju['y']) && $usesuju['y'] == "gonggao" ){
    
        $times = 0.001;

    }else if(  isset($usesuju['y']) && $usesuju['y'] == "online" ){
    
        $times = 0.1;
    }

    $client = new swoole_client(SWOOLE_SOCK_UDP);
   
    if($client->connect( $IP ,$PROT,$times)) {

        $client->send($msg );

    } else {
        return  false;
    }

    $hear = @$client->recv();

    if( $hear ){
        $client ->close(true);
        return udpsendde( $hear);

    }else return false;


}


function TuiGuang_sckey($uid){

    /*推广码生成*/

    return $uid;

    $uucd = strlen($uid);
    $zhongsu = rand(1,9);
    $zhi = ($uid *$zhongsu);
    $sj = rand(0,9);
    $shuzu = (strlen($zhi)*$sj).$sj.'.'.$zhi.str_pad($zhongsu,2,"0",STR_PAD_LEFT);

    return $shuzu;
}


function TuiGuang_yzkey($uid){

    /*推广码验证*/
 
    

    if($uid == '' || $uid < 0)return 0;
    return $uid;

    $dd = explode('.',strtolower($uid));
    $xy = $dd['0'];
    $uid = $dd['1'];
    $idlen = (int)substr($uid, -2, 2);
    $zhongshu = strlen( $uid );
    $shujus = substr( $uid, 0, $zhongshu -2 );
    $zs = strlen($shujus);
    $sji = (int)substr($xy, -1, 1);

    if( $xy != ($zs*$sji).$sji){

        return 0;
    }

    return $shujus/$idlen;
}


function xitongpay( $id , $qx = ''){

        /*  支付方式
            -1  只需要id 和名字
            0   前台显示
            其他 读取单个数据无限制
        */

        $shuzu = array();
        global $Mem ;
        
        $HASH = 'xitongpay/'.$id;

        if( $qx == '2'){

            $Mem -> d( $HASH );
            return false;
        }

        $shuzu = $Mem -> g( $HASH);

        if( $shuzu === '-1' && $qx == '' ) return false;
        if( $shuzu  && $qx == '' )  return $shuzu;

        $D = db('pay');

        $shuzu = array();

        if( $id == '0' ){

            /*所有支付 限时开启的*/
            $shuzu = $D ->zhicha('id,name,off,paixu,xianshi,suoluetu')->where( array( 'off' => 1 , 'xianshi' => 1 )) -> order ( 'paixu desc')-> select();

            if( !$shuzu){

                $Mem -> s( $HASH ,'-1' , 30);

                return false;
            }
        
        }else if( $id == '-1'){

            /*全部支付 id 和名字*/
            $shuzus = $D ->zhicha('id,name')-> select();

            if( !$shuzus){

                $Mem -> s( $HASH ,'-1' , 30);

                return false;

            }

            foreach($shuzus as $kk){

                    $shuzu[ $kk['id']] = $kk['name'];
            }
            
        }else if( $id == '-2'){

            /*所有开启支付 id 和名字*/
            $shuzus = $D ->zhicha('id,name,off,paixu,xianshi')->where( array( 'off' => 1, 'xianshi' => 1) )  -> order ( 'paixu desc') -> select();

            if( !$shuzus){

                $Mem -> s( $HASH ,'-1' , 30);
                return false;

            }

            foreach($shuzus as $kk){

                    $shuzu[ $kk['id']] = $kk['name'];
            }

            
        }else if( $id == '-3'){
            /*所有app 开启支付 id 和名字 */
            $shuzu = $D ->zhicha('id,name,off,isapp,paixu,suoluetu')->where( array( 'off' => 1,'isapp' => 1 )) -> order ( 'paixu desc')-> select();

            if( !$shuzu ){

                $Mem -> s( $HASH ,'-1' , 30 );
                return false;

            }

        }else if( $id == '-4'){
            /*所有app 开启支付 id 和名字 */
            $shuzu = $D ->zhicha('id,name,off,isapp,paixu,payfile,suoluetu')->where( array( 'off' => 1,'xianshi'=>1 )) -> order ( 'paixu desc')-> select();

            if( !$shuzu ){

                $Mem -> s( $HASH ,'-1' , 30 );
                return false;
            }

        }else {

            if(  ((int)$id)  > 0 && strlen($id) < 4 )
                $shuzu = $D ->where( array( 'id' => $id , 'off' => 1   ))-> find(); 
            else
                $shuzu = $D ->where( array( 'payfile' => $id , 'off' => 1   ))-> find(); 

            

            if( !$shuzu){

                $Mem -> s( $HASH ,'-1' , 30 );

                return false;

            }

        }
        
        $Mem -> s( $HASH , $shuzu );

        return $shuzu;
}

function dingguoqi( $USERID = 0 ){

        /* 订单过期 */

        $atime = time() - 3600 ;


        $where = array( 'off IN' => '0,1' , 'atime <'=> $atime);

        if( $USERID > 0) $where['uid'] = $USERID;
        $D = db('dingdan');
        
        $sss = $D -> where( $where )-> update(array( 'off'=> 3 ,'xtime' => time() ));

        $atime = time() - 3600*24*30;
        $where = array( 'off' => '3' , 'atime <'=> $atime);
        if( $USERID > 0)  $where['uid'] = $USERID;

        $D -> where( $where )-> delete();


        return  $sss;


}


function TOU_down( $tupian ){

    if( strpos( $tupian , WZHOST) !== false){
    
        return $tupian;
    }

    global $CONN;

    $qianzui = 'attachment/tx/'.date('Ym').'/';
    $files =  $CONN['dir'].$qianzui;
    $WJIAN =  WYPHP.'Http/'.ltrim( $qianzui  ,'/');

    jianli($WJIAN);

    $wj = token().'.jpg';

    $files.=$wj;

    $WJIAN .= $wj;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $tupian);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    $content = curl_exec($ch);
    curl_close($ch);

    $f = fopen( $WJIAN, 'w');
    fwrite( $f, $content);
    fclose( $f);
    return  $files;

}




function TOU_ti($tou){

    if( strpos( $tou , WZHOST) !== false){
    
        return str_replace(WZHOST,'/',$tou);

    }else{

        return $tou;
    }
}


function Game_Set($flie , $qx = ''){

    /*读取游戏创建设置*/
    global $Mem;

    $HASH = 'Game/set_'.$flie;

    if($qx == 2){

        $Mem -> d($HASH);
        return false;
    }

    $DATA = $Mem ->g($HASH);

    if($DATA && $DATA == '-1' && $qx == ''){

        return false;
    }

    if($DATA && $qx == ''){

        return $DATA;
    }

    $D = db('gameserver');

    $JIE = $D  ->zhicha('biaoshi,renshu,huobi,fangka,stjushu,strenshu,stdifen,stzhfen,stkuozan,off,koufang')->where(array('biaoshi' => $flie))->find();

    if($JIE && $JIE['off'] == 1 ){

        $Mem ->s($HASH,$JIE);
        return $JIE;

    }else{

        $DATA = -1;
        $Mem ->s($HASH,$DATA,10);
        return false;
    }


}


function Game_List($flie = 1, $qx = '' ){

    /*游戏列表 1 只要标识名字  2 游戏INFO*/
    global $Mem;
    $HASH = 'Game/list_'.$flie;

    if($qx == 2){

        $Mem -> d($HASH);
        return false;
    }

    $DATA = $Mem ->g($HASH);

    if($DATA && $DATA === '-1' && $qx == ''){

        return false;
    }

    if($DATA && $qx == ''){

        return $DATA;
    }

    $D = db('gameserver');

    if($flie == 1){

        $JIE = $D ->zhicha('biaoshi,name')-> select();

        if($JIE){

            $DATA = array();
            foreach($JIE as $zhi){

                $DATA[$zhi['biaoshi']] = $zhi['name'];
            }

            $Mem ->s($HASH,$DATA);
            return $DATA;

        }else{

            $DATA = -1;
            $Mem ->s($HASH,$DATA,10);
            return false;
        }

    }else{

        $JIE = $D ->zhicha('off,biaoshi,name,tupian,tupianji,jieshao,serverlist')-> select();

        if($JIE){

            $DATA = array();
            foreach($JIE as $zhi){

                $DATA[$zhi['biaoshi']] = $zhi;
            }

            $Mem ->s($HASH,$DATA);
            return $DATA;

        }else{

            $DATA = -1;
            $Mem ->s($HASH,$DATA,10);
            return false;
        }
    }

}



function Game_Info( $flie , $qx = ''){

    /*读取数据其他信息*/
    global $Mem;
    $HASH = 'Game/info_'.$flie;
    if($qx == 2){

        $Mem -> d($HASH);
        return false;
    }

    $DATA = $Mem ->g($HASH);

    if($DATA && $DATA == '-1' && $qx == ''){

        return false;
    }

    if($DATA && $qx == ''){

        return $DATA;
    }

    $D = db('gameserver');

    $JIE = $D  ->zhicha('biaoshi,name,tupian,tupianji,jieshao,off')->where(array('biaoshi' => $flie))->find();

    if($JIE && $JIE['off'] == 1 ){

        $DATA = $JIE;
        $Mem ->s($HASH,$DATA);
        return $DATA;

    }else{

        $DATA = -1;
        $Mem ->s($HASH,$DATA,10);
        return false;
    }

}

function Game_Server( $flie , $qx = ''){

    /*根据游戏id读取游戏服务器ip*/
    global $Mem;
    $HASH = 'Game/server_'.$flie;
    if($qx == 2){

        $Mem -> d($HASH);
        return false;
    }

    $DATA = $Mem ->g($HASH);

    if($DATA && $DATA == '-1' && $qx == ''){

        return false;
    }

    if($DATA && $qx == ''){

        return $DATA;
    }

    $D = db('gameserver');

    $JIE = $D  ->zhicha('biaoshi,serverlist,off')->where(array('biaoshi' => $flie))->find();

    if( $JIE && $JIE['off'] == 1 ){

        $DATA = $JIE['serverlist'];
        $Mem ->s($HASH,$DATA);
        return $DATA;

    }else{

        $DATA = -1;
        $Mem ->s($HASH,$DATA,10);
        return false;
    }
}

function getarray($para) {

        $arg  = "";
        while ( list ( $key, $val) = each ( $para))  $arg .= $key . "=" . $val . "&";
        $arg = substr( $arg , 0 , count( $arg) -2 );
        if( get_magic_quotes_gpc() ) $arg = stripslashes( $arg);
        return $arg;
}


function argSort($para) {

        ksort( $para );
        reset( $para );
        return $para ;
}


function gengduo( $code , $mesg =  '' ,$data = array() , $TIAOURL = WZHOST ){

    global $_NGET,$ISAPP;

    if( isset( $_NGET['isapp'] ) || $ISAPP){

        /*类型等于1 json数据*/
 

        return array('lx' => 1 , 'code' => $code ,'data' => $data , 'msg' => $mesg);

    }else{

        /*类型等于2 html 数据*/

        return array('lx' => 2 , 'code' =>$code, 'data' => $TIAOURL ,'msg' => $mesg );
    }

}

function tongyihan( $LX ,  $opuid ,$TIAOURL = WZHOST, $IP ='' , $unopid = '',$UHA = '' ){

        $sescc = sescc('','',$UHA);
        global $Mem;
        if( $opuid == '' ){

            return gengduo( '-1' , '非法传递,没有唯一标识','' , $TIAOURL);
        }

        $USER =  kjcha( $LX , $opuid , $unopid );
       
        if( $USER ){

            /*查询get 成功的直接 登录*/

            if( $USER['off'] == '0'){

                return gengduo( '-1' , '帐号被停用' ,'' , $TIAOURL );
            }

            if( isset ( $_NGET['state'] ) &&  strlen( $_NGET['state']) == 32){

                global $Mem;

                $HASH = 'kjdenglu/'.mima( $_NGET['state'] );
                $Mem -> s($HASH, $USER['uid'] ,20);

            }

            if($sescc['uid'] > 0){
            
                return gengduo( '-1' , '已经被绑定' ,'' , $TIAOURL );

            }else{

                sescc(array('uid' => $USER['uid'],'ip' => $IP ),'',$UHA);
                return gengduo( 1 , '',
                    array(
                    'name' => $USER['name'],
                    'uid'  =>  $_SESSION['uid'],
                    'jine' => $USER['jine'],
                    'jifen' => $USER['jifen'],
                    'huobi' => $USER['huobi'],
                    'xingbie' => $USER['xingbie'],
                    'touxiang' => pichttp( $USER['touxiang'] ),
                    'shouji' =>  $USER['shouji'],
                    ),$TIAOURL);
            }



        }else if($sescc['uid']  > 0){



            /*已经登录过直接绑定 */

            $USER = uid( $sescc['uid'] );

            if(!$USER || $USER['off'] == '0') {
            
              
               return gengduo( '-1' , '帐号被停用' ,'' , $TIAOURL );
              

            }

            $fan =  bangding( $LX ,$sescc['uid'], $opuid , '' , '' , $unopid );

            if( $fan ){ 
                  
                $USER = uid( $sescc['uid'] ,1 );

                return gengduo( 1 ,  '' ,array(
                    
                    'name' => $USER['name'],
                    'uid'  =>  $sescc['uid'],
                    'jine' => $USER['jine'],
                    'jifen' => $USER['jifen'],
                    'kuohuobi' => $USER['kuohuobi'],
                    'xingbie' => $USER['xingbie'],
                    'touxiang' => pichttp( $USER['touxiang'] ),
                    'shouji' =>  $USER['shouji'],
                ) , $TIAOURL  );

            }else return gengduo( '-1' , '绑定失败' ,'' , $TIAOURL ); 

        }else return false;
}

function xiazaipic( $tupian ){

    if( strpos( $tupian , WZHOST) !== false){
    
        return $tupian;
    }

    global $CONN;

    $qianzui = 'attachment/tx/'.date('Ym').'/';
    $files =  $CONN['dir'].$qianzui;
    $WJIAN =  rtrim(WYPHP.'../','/').'/'.ltrim( $qianzui  ,'/');

   

    $wj = token().'.jpg';

    $files.=$wj;

    jianli($WJIAN.$files);

    $WJIAN .= $wj;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $tupian);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    $content = curl_exec($ch);
    curl_close($ch);

    $f = fopen( $WJIAN, 'w');
    fwrite( $f, $content);
    fclose( $f);
    return  $files;

}

function orderid(){

    return date('YmdHis') .rand(1000,9999).rand(10,99);
}

function lailu( $id = '',$request = ''){
         
        
        if( $id != '') return (int)$id;

        /* 判断来路
           电脑,    0
           微信,    1
           手机WAP, 2
           安卓APP, 3
           苹果APP, 4
           其他     5
        */

        $USERSS = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '' ;

        if ( strstr( $USERSS , "essenger")) return 1;
        else if( strpos( $USERSS,"iPhone" ) && strstr( $USERSS, "APP") ) return  4;
        else if( strpos( $USERSS,"iPad" )   && strstr( $USERSS, "APP") ) return  4;
        else if( strpos( $USERSS,"Android") && strstr( $USERSS, "APP") ) return  3;
        else if( strpos( $USERSS,"NetFront") || strpos( $USERSS , "iPhone" ) || strpos( $USERSS ,"iPad")  || strpos($USERSS,"MIDP-2.0") || strpos($USERSS,"Opera Mini") || strpos($USERSS,"UCWEB") || strpos($USERSS,"Android") || strpos($USERSS,"Windows CE") || strpos($USERSS,"SymbianOS")) return  2;
        else return  0;

}

function chongzhifan(  $XTID , $JINE , $DDID ){

        /* 充值 返回
            $XTID 商户id
            $JINE 金额日志
            $DDID 用户订单
        */
        if( $JINE <= 0 ) return false;

        $paylx = 1;

        $chenggong = false;

        $D = db('dingdan');

        $data = $D -> where( array(  'orderid' => $DDID ) ) -> find();

        if( $data ){

            if( $data['off'] == '1' ){

                $chenggong = true;

                $USERID  =  $data['uid'];

                global $PAYAC,$CONN,$Mem;

                $QQIDANDAO = $Mem  -> g("qiandao/".$USERID);
                if(!$QQIDANDAO ){
            
                    $QQIDANDAO  = array(
                        'qday' => 0,
                        'qdtime' => 0,
                        'zatime' => 0,
                        'zcishu' => 0,
                        
            
                    );
            
                    $Mem  -> s("qiandao/".$USERID,$QQIDANDAO);
        
            
                }else if($JINE >= 1){

                    $QQIDANDAO['zatime'] = 0;
                    $QQIDANDAO['zcishu'] = 0;

                    $Mem  -> s("qiandao/".$USERID,$QQIDANDAO);
                }


				$shopstore = logac("shopstore");

                $time = time();

                $sql = $D -> setshiwu(1) -> where( array( 'id' => $data['id'] )) -> update( array( 'off' => 2  , 'rejine' => $JINE  , 'xtime' => $time , 'paytype' => $PAYAC['id'] , 'xiorderid' => $XTID ) );

                if(  $data['type'] == 1){

                    $BIZHONG = array();

                    if($shopstore){
                    
                        foreach($shopstore as $shuju){
                    
                            if($shuju){
                                $YANS = explode("_",$shuju);
                                
                                $BIZHONG[$YANS['0']][(int)$YANS['1']] = $YANS['2'];
                                
                            }
                        }
                    }

             
                    $USER = uid( $USERID );

                    $paydls = logac('paydls');
                    /*代理加送*/
                    $JIASONG = 1;

                    if($USER && $USER['level'] > 0){

                        $JIASONG = $paydls[$USER['level']]?$paydls[$USER['level']]:1;
                        
                    }

    

                    if($data['bizhong'] == 1 ){

                        /*房卡充值*/
                        $BILI = $CONN['paybilifk'];
                        $SUODE  =  $BILI * $JINE;

                        if(  $BIZHONG[$data['bizhong']]  ){

                            $SUODE = (int)($BIZHONG[$data['bizhong']][(int)$JINE]);

                            if($SUODE <= 0){

                                $SUODE  =  $BILI * $JINE;

                            }else{

                                $BILI = 1;
                            }

                        }

                       


                        $sql .= $D -> setbiao( 'user' )  -> where( array( 'uid' => $USERID )) -> update( array( 'jifen +' =>  (int)($SUODE*$JIASONG) ,'zpay +' =>$JINE ));

                        $sql .= $D -> setbiao( 'jifenlog' ) -> insert( array( 'uid' => $USERID ,
                                                                            'type' => 1 ,
                                                                            'jine' =>  (int)($SUODE*$JIASONG)   ,
                                                                            'data' => $DDID.' '.((float)$BILI).' '.$JIASONG ,
                                                                              'ip' => $data['ip'],
                                                                           'atime' => $time,
                                                                        )
                                                               );

                    
                    
                    }else if($data['bizhong'] == 2 ){

                        /*货币充值*/
                        
                     
                        $BILI = $CONN['paybilijb'];
                        $SUODE  =  $BILI * $JINE;
                        
                        if(  $BIZHONG[$data['bizhong']]  ){

                            $SUODE = (int)($BIZHONG[$data['bizhong']][(int)$JINE]);

                            if($SUODE <= 0){

                                $SUODE  =  $BILI * $JINE;

                            }else{

                                $BILI = 1;
                            }

                        }

                        if($JINE >= 1 && $USER['zpay'] == '0' ){

                            $SUODE *=2;
                        }
                        
                        $sql .= $D -> setbiao( 'user' )  -> where( array( 'uid' => $USERID )) -> update( array( 'huobi +' => (int)($SUODE*$JIASONG) ,'zpay +' =>$JINE ));

                        $sql .= $D -> setbiao( 'huobilog' ) -> insert( array( 'uid' => $USERID ,
                                                                            'type' => 1 ,
                                                                            'jine' =>  (int)($SUODE*$JIASONG) ,
                                                                            'data' => $DDID.' '.((float)$BILI) .' '.$JIASONG ,
                                                                              'ip' => $data['ip'],
                                                                           'atime' => $time,
                                                                        )
                                                               );
                    }
                
                
                }else{

                    /*金额直接充值*/

                    $BILI = isset($CONN['paybilijne'])&&$CONN['paybilijne'] > 0 ? $CONN['paybilijne'] : 1 ;
                    $SUODE  =  $BILI * $JINE;

                    $sql .= $D -> setbiao( 'user' )  -> where( array( 'uid' => $USERID )) -> update( array( 'jine +' => $SUODE*$JIASONG ,'zpay +' => $JINE ));

                    $sql .= $D -> setbiao( 'jinelog' ) -> insert( array( 'uid' => $USERID ,
                                                                        'type' => 1 ,
                                                                        'jine' => $SUODE*$JIASONG ,
                                                                        'data' => $DDID .' '.((float)$BILI) .' '.$JIASONG  ,
                                                                          'ip' => $data['ip'],
                                                                       'atime' => $time,
                                                                    )
                                                           );
                }

                $fn = $D -> qurey( $sql , 'shiwu');

                if( $fn ){

                    $USER = uid( $USERID , 1);
                    $chenggong = true;

                    czthongzhi( $USER , $JINE , $DDID );
                    zchongzhifan(  $D , $USER , $JINE , $DDID );

                }else $chenggong = false;
            }
        }

        return $chenggong;
}


function czthongzhi( $USER , $JINE , $DDID  ){
        /*  充值成功通知处理
            $USER 用户最新信息
            $JINE 充值金额
            $DDID 充值的订单
        */


}


function zchongzhifan( $D  = '' , $USER , $JINE , $DDID ){
    /*  扩展处理通知
        $D  数据表结构
        $USER 用户最新信息
        $JINE 用户金额
    */

    $tuid = $USER['tuid'];

    if( $tuid > 0 ){

        $TUISER = uid( $tuid );

        if( $TUISER ){
            
            global $CONN;

            $HJ = db('dingdan');

            $guanxi  = array(
                        
                array($tuid,$TUISER['level'])
        
            );


            for( $i = 1 ; $i < $CONN['tuiji'] ; $i++ ){

                $wds = $i-1;
                if($wds < 1) $wds= '' ;

                $uids =  $USER['tuid'.$wds];
                if($uids  > 0){

                    $HHH = uid($uids);

                    if( $HHH ){
                        $guanxi[$i] = array($uids,$HHH['level']) ;
                    }

                }else{

                    break ;
                }
            }

            if( $USER['appid'] == '0' &&  $JINE >= 1 ){
                /*判断推广有效*/
                $fan = $HJ -> setbiao('user') -> where( array('uid' => $USER['uid'])) -> update( array('appid' => 1 ));

                if($fan){
                    /*更改用户为有效*/

                    $pureg = logac('pureg');
                    $dlreg = logac('dlreg');

                    foreach($guanxi  as $j=> $shu){


                        $huobi = $jifen = $jine =0;

                        if($shu[1] > 0){

                            /*代理奖励*/
                            $jiangli = isset($dlreg[$j])?$dlreg[$j]:0;

                            if($jiangli > 0){

                                if($CONN['jifenhuobi'] == '0'){
                                    $jine = $jiangli;

                                }else if($CONN['jifenhuobi'] == '1'){

                                    $jifen = $jiangli;

                                }else if($CONN['jifenhuobi'] == '2'){

                                    $huobi = $jiangli;
                                }

                                jiaqian($shu[0],11,$jine,$jifen,$huobi,$tuid.'_reg_'.$j);
                            }

                        }else{

                            $jiangli = isset($pureg[$j])?$pureg[$j]:0;

                            
                            if($jiangli > 0){

                                if($CONN['jifenhuobi'] == '0'){
                                    $jine = $jiangli;

                                }else if($CONN['jifenhuobi'] == '1'){

                                    $jifen = $jiangli;

                                }else if($CONN['jifenhuobi'] == '2'){

                                    $huobi = $jiangli;
                                }

                                jiaqian($shu[0],11,$jine,$jifen,$huobi,$tuid.'_reg_'.$j);
                            }
                        }
                    }
                }
            }

            /*充值累计推广*/
            if($JINE >= 0.1){

                $payujl = logac('payujl');
                $paydjl = logac('paydjl');

                foreach($guanxi  as $j=> $shu){


                    $huobi = $jifen = $jine =0;

                    if($shu[1] > 0){

                        $jiangli =  isset($paydjl[$j]) ? (float)$paydjl[$j]*$JINE:0;

                        if($jiangli > 0){

                            if($CONN['jifenhuobi'] == '0'){
                                $jine = $jiangli;

                            }else if($CONN['jifenhuobi'] == '1'){

                                $jifen = (int)$jiangli;

                            }else if($CONN['jifenhuobi'] == '2'){

                                $huobi = (int)$jiangli;
                            }

                            jiaqian($shu[0],11,$jine,$jifen,$huobi,$tuid.'_pay_'.$j);
                        }


                    }else{

                        $jiangli =  isset($payujl[$j]) ?(int)$payujl[$j]*$JINE:0;

                        if($jiangli > 0){

                            if($CONN['jifenhuobi'] == '0'){
                                $jine = $jiangli;

                            }else if($CONN['jifenhuobi'] == '1'){

                                $jifen = (int)$jiangli;

                            }else if($CONN['jifenhuobi'] == '2'){

                                $huobi = (int)$jiangli;
                            }

                            jiaqian($shu[0],11,$jine,$jifen,$huobi,$tuid.'_pay_'.$j);
                        }
                    }
                }
            }
        }
    }

}



function App_Pay_tb($WY,$_NGET,$_NPOST){

    /*支付成功的app支付接口*/



}

function duanxin( $shouji , $shuju ,$C0NN){

        /* 1 注册通知
           2 找回通知
           3 绑定通知
           4 购买通知
           5 发货通知
           6 确认通知
        */
        $neirong = '';
        $IP = IP();
        
        if(!isset( $shuju['type'] )) $shuju['type'] = 1;
        if(!isset( $shuju['YZM'] )) $shuju['YZM'] = '';
        if(!isset( $shuju['ZH'] )) $shuju['ZH'] = '';
        if(!isset( $shuju['NC'] )) $shuju['NC'] = '';
        if(!isset( $shuju['DDID'] )) $shuju['DDID'] = '';
        if(!isset( $shuju['IP'] )) $shuju['IP'] = '';
        if(!isset( $shuju['TIME'] )) $shuju['TIME'] = '';
        if(!isset( $shuju['KDFS'] )) $shuju['KDFS'] = '';
        if(!isset( $shuju['KDHM'] )) $shuju['KDHM'] = '';
        if(!isset( $shuju['JINE'] )) $shuju['JINE'] = '';
        if(!isset( $shuju['UID'] )) $shuju['UID'] = '';
        if(!isset( $shuju['BT'] )) $biaoti = ''; else $biaoti = $shuju['BT'];
        
        $neirong  = str_replace(   array(         'YZM',        'ZH' ,       'NC' ,       'DDID',        'IP',    'TIME' ,        'KDFS' ,       'KDHM'   ,     'JINE'   ,     'UID')  , 
                                    array( $shuju['YZM'],$shuju['ZH'],$shuju['NC'],$shuju['DDID'],       $IP ,     time() ,$shuju['KDFS'],$shuju['KDHM'],$shuju['JINE'],$shuju['UID']
                        ) ,$C0NN['sms'.$shuju['type']]);
        


        $time = time() ;
        $woqu = qfopen('http://222.73.117.156/msg/HttpBatchSendSM?account='.$DUQU['duanxinid'].'&pswd='.$DUQU['duanxinkey'].'&needstatus=false&mobile='.$shouji.'&msg='.urlencode(iconv('UTF-8', 'UTF-8//IGNORE',$neirong)).'&timestamp='.$time);

 
        $cansh = explode( ',' , $woqu );

        if( $cansh['1'] =='0' ) return 'success:'.$cansh['0'];
        else return 'error:'.$cansh['1'];


}


function youxiang( $zhanghao , $shuju,$C0NN ){

        $neirong = '';
        if(!isset( $shuju['type'] )) $shuju['type'] = 1;
        if(!isset( $shuju['YZM'] )) $shuju['YZM'] = '';
        if(!isset( $shuju['ZH'] )) $shuju['ZH'] = '';
        if(!isset( $shuju['NC'] )) $shuju['NC'] = '';
        if(!isset( $shuju['DDID'] )) $shuju['DDID'] = '';
        if(!isset( $shuju['IP'] )) $shuju['IP'] = '';
        if(!isset( $shuju['TIME'] )) $shuju['TIME'] = '';
        if(!isset( $shuju['KDFS'] )) $shuju['KDFS'] = '';
        if(!isset( $shuju['KDHM'] )) $shuju['KDHM'] = '';
        if(!isset( $shuju['JINE'] )) $shuju['JINE'] = '';
        if(!isset( $shuju['UID'] )) $shuju['UID'] = '';
        if(!isset( $shuju['BT'] )) $biaoti = ''; else $biaoti = $shuju['BT'];

        $fajian = $C0NN['mailfa'];
        
        $IP = IP();
     
        
        $neirong  = str_replace(   array(         'YZM',        'ZH' ,       'NC' ,       'DDID',        'IP',    'TIME' ,        'KDFS' ,       'KDHM'   ,     'JINE'   ,     'UID')  , 
                                    array( $shuju['YZM'],$shuju['ZH'],$shuju['NC'],$shuju['DDID'],       $IP ,     time() ,$shuju['KDFS'],$shuju['KDHM'],$shuju['JINE'],$shuju['UID']
                        ) ,$C0NN['sms'.$shuju['type']]);

                                    

        $headers = 'From: '.$fajian .'<'.$fajian .'>' . "\r\n" .
                 'Reply-To: '.$fajian . "\r\n" . 
                 'Content-type: text/html;charset=UTF-8'."\r\n".
                 'X-Mailer: PHP/' . phpversion();
        if (mail($zhanghao, $biaoti, $neirong, $headers))  
             return 'success:ok';
        else return 'error:no';

}


class mongodbcc{

       var $data = false ;
       var $table = 'db.txtcc';
       var $db = '';
       var $fenjies = 1;

    public function __construct($servers,$table='db.txtcc',$fenjies = 1){
           
           if( ini_get( 'mongodb.debug' ) === false ) return false;
           if( ! $this -> data) $this -> data = new MongoDB\Driver\Manager( $servers);
           $this -> fenjies = $fenjies;
           if($table) $this -> table = $table;
           else       $this -> table = 'db.txtcc';
           return $this;
    }

    public function fenjie($table,$ykey){

        if($this -> fenjies == 1 ){

           if(  strpos( $ykey , '/') !== false ){

                $hash = explode('/',$ykey);
                $this -> table =  'O'. implode( '.' , $hash). 'S';
                         
           }else $this->table = 'OS.'.md5($ykey);

        }

           return  $this->table;
    }


    public function fass( $leix, $key = '', $value = '', $time = 0){ 

                    $ykey   = $key;
                    $nerong = serialize( $value);
                    $key    = md5( $key);
                    $wode   =  strlen( $nerong);

                    $time  = (int)$time;

                    if( $leix == 'add' || $leix == 'set'){
                      
                        $bulk = new MongoDB\Driver\BulkWrite;

                        $bulk -> insert ( array( '_id' => $key , 'key' => $nerong ,'time' => time(), 'xianshi' => $time ));

                        try {  

                            $this -> data -> executeBulkWrite( $this-> fenjie ( $this -> table, $ykey), $bulk);

                            return true;

                        }catch ( MongoDB\Driver\Exception\BulkWriteException  $e){  

                            $bulk = new MongoDB\Driver\BulkWrite;

                            $bulk->update(  array( '_id' => $key ),
                                            array('$set' => array( 'key' => $nerong,'time' =>time(),'xianshi' => $time ))
                                         
                            );

                            $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
                            $result = $this-> data -> executeBulkWrite ( $this -> fenjie( $this -> table, $ykey), $bulk, $writeConcern);
                            return true;
                         }

                    }else if( $leix == 'delete'){

                            $bulk = new MongoDB\Driver\BulkWrite;
                            $bulk-> delete(array( '_id' => $key), array( 'limit' => 1));
                            $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
                            try {

                                  $result = $this-> data -> executeBulkWrite( $this-> fenjie( $this -> table, $ykey), $bulk, $writeConcern);

                             }catch ( MongoDB\Driver\Exception\BulkWriteException $e){ 
                                
                             }

                            return true;

                    }else if($leix == 'get'){

                            $filter  = array ( '_id' =>  $key );
                            $options = array ( 'projection' => array ('key'=>1,'time'=>1,'xianshi'=> 1),
                                               'limit' => 1,
                                       );
                            $query = new MongoDB\Driver\Query( $filter, $options);
                            $cursor = $this -> data -> executeQuery ( $this-> fenjie( $this->table, $ykey), $query);
                            $fanhui = $cursor -> toArray();

                            if( $fanhui ){
                               
                                if( $fanhui['0'] -> xianshi  == 0)

                                     return  unserialize( $fanhui['0'] -> key);
                                else if( $fanhui['0'] -> xianshi + $fanhui['0']-> time >= time())
                                     return  unserialize( $fanhui['0'] -> key);
                                else{

                                     $bulk = new MongoDB\Driver\BulkWrite;
                                     $bulk -> delete( array( '_id' => $key ), array( 'limit' => 1) );
                                     $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
                                     try {
     
                                          $result = $this-> data -> executeBulkWrite( $this->fenjie ( $this->table , $ykey), $bulk, $writeConcern);

                                     }catch ( MongoDB\Driver\Exception\BulkWriteException $e){ 
                                    
                                     }

                                     return false;
                                }
                            
                            }else return false;
                     
                    }else if($leix == 'flush_all'){
                         
                            

                        $bulk = new MongoDB\Driver\BulkWrite;

                        $bulk->delete(array(),array());
                         
                        $writeConcern = new MongoDB\Driver\WriteConcern( MongoDB\Driver\WriteConcern::MAJORITY, 1000);

                        try {

                            $result = $this -> data -> executeBulkWrite( $this -> fenjie ( $this->table, $ykey), $bulk, $writeConcern);
                            return true;

                        }catch (MongoDB\Driver\Exception\BulkWriteException $e){ 

                            return false;
                            
                        }



                    }else if($leix == 'incr'){
                          
                            $bulk = new MongoDB\Driver\BulkWrite;


                            $filter = array( '_id' =>  $key );
                            $options = array( 'projection' => array( 'key'=>1,'time'=>1,'xianshi'=> 1 ),
                                            'limit' => 1,
                                      );

                            $query = new MongoDB\Driver\Query( $filter, $options);
                            $cursor = $this -> data -> executeQuery( $this -> fenjie( $this->table, $ykey), $query);
                            $fanhui = $cursor -> toArray();
                            if($fanhui){

                               if( $fanhui['0'] -> xianshi  == 0)

                                   $fanh = (float) unserialize( $fanhui['0'] -> key) + (float)$value;
                                
                               else if( $fanhui['0'] ->xianshi + $fanhui['0'] -> time >= time())

                                    $fanh =  (float) unserialize( $fanhui['0'] -> key) + (float) $value;

                               else $fanh = (float) $value;
                            
                            }else   $fanh = (float) $value;

                               $this -> fass('set',$ykey,$fanh,$time);

                               return $fanh;

                    }else if($leix == 'decr'){

                            $bulk = new MongoDB\Driver\BulkWrite;


                            $filter = array( '_id' =>  $key );
                            $options = array( 'projection' => array( 'key'=>1,'time'=>1,'xianshi'=> 1 ),
                                            'limit' => 1,
                                      );
                            $query = new MongoDB\Driver\Query( $filter, $options);
                            $cursor = $this -> data -> executeQuery( $this -> fenjie( $this -> table, $ykey), $query);
                            $fanhui = $cursor -> toArray();
                            if($fanhui){
                            
                                 if( $fanhui['0'] -> xianshi  == 0)

                                     $fanh =  (float)unserialize($fanhui['0'] -> key) - (float)$value;
                                
                                else if( $fanhui['0'] -> xianshi + $fanhui['0'] -> time >= time())

                                     $fanh =  (float) unserialize( $fanhui['0'] -> key) - (float)$value;

                                else $fanh = (float)$value;
                            
                            }else    $fanh = (float)$value;

                                     $this -> fass('set', $ykey, $fanh, $time);

                              return $fanh;
                    }
    }


    public function s( $key, $value, $time=0){  

           return  $this -> fass( 'set', $key, $value, $time);
    }


    public function g($key){  

           return  $this -> fass( 'get', $key);
    }


    public function a( $key, $value, $time=0){

           return  $this -> fass('set', $key, $value, $time=0);
    }


    public function d( $key){  

           return  $this -> fass( 'delete', $key);
    }


    public function f(){ 

           return  $this -> fass( 'flush_all');
    }   


    public function j( $key, $num=1,$time=0){ 
            
           return  $this -> fass( 'decr', $key , (float)$num , $time);
    }

    public function ja( $key, $num=1,$time=0){

           return  $this -> fass( 'incr', $key , (float)$num , $time);
    }   


}



class rediscc{

    var $DB = null ;


    function __construct( $data = array( 0 ,'127.0.0.1','6379', 0 ,'') ){

            $redis = new Redis();

            if( !isset( $data['0'] ) ) $data['0'] = 0;
            if( !isset( $data['1'] ) ) $data['1'] = '127.0.0.1';
            if( !isset( $data['2'] ) ) $data['2'] = '6379';
            if( !isset( $data['3'] ) ) $data['3'] = '0';
            if( !isset( $data['4'] ) ) $data['4'] = '';

            $my = $redis -> connect( $data['1'], $data['2'] , $data['3']);

            if( $my ){

                $redis -> select( (float) $data['0'] );
                if( $data['4'] != '' ) $redis-> auth( $data['4'] );

                $redis-> setOption( Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP ); 
                $this -> DB = $redis;

            }else   $this -> DB = false;

    }


    public function ja( $key, $num = 1, $time = '0'){

        if( ! $this -> DB ) return false;

        $shuju = (float)$this -> g( $key );
        if( ! $shuju ) $shuju = $num;
        else          $shuju += $num;

        if($time > 0)
                $this -> s( $key , (float)$shuju , $time );
        else    $this -> s( $key , (float)$shuju );
        return  $shuju;

    }


    public function j( $key , $num = 1, $time = '0'){

        if( ! $this -> DB )return false;

        $shuju = (float)$this -> g( $key );
        if( ! $shuju ) $shuju = 0;
        $shuju -= $num;

        if($time > 0)
                $this -> s( $key , (float)$shuju , $time );
        else    $this -> s( $key , (float)$shuju );

        return  $shuju;

    }


    public function g( $key ){

        if( ! $this -> DB )return false;
        return $this -> DB -> get( $key );

    }

    public function d( $key ){

        if( ! $this -> DB )return false;
        return $this -> DB -> delete( $key );

    }

    public function f( $key = '' ){

        if( ! $this -> DB ) return false;
        return $this -> DB -> flushAll();

    }

    public function s( $key , $value , $time = '0' ){

        $time  = (int)$time;

        if( ! $this -> DB ) return false;
        if( $time > 0 )
                return $this -> DB -> setex ( $key , $time , $value  );
        else    return $this -> DB -> set( $key , $value  );

    }


}

function qsubstr($str, $start=0, $length=1, $charset="utf-8", $suffix=false){

         if( $length == 0) return $str;

         if( function_exists( "mb_substr")) {
               if( mb_strlen( $str, $charset) <= $length) return $str;
               $slice = mb_substr( $str, $start, $length, $charset);
         }else {
               $re['utf-8']   = "/[/x01-/x7f]|[/xc2-/xdf][/x80-/xbf]|[/xe0-/xef][/x80-/xbf]{2}|[/xf0-/xff][/x80-/xbf]{3}/";
               $re['gb2312'] = "/[/x01-/x7f]|[/xb0-/xf7][/xa0-/xfe]/";
               $re['gbk']          = "/[/x01-/x7f]|[/x81-/xfe][/x40-/xfe]/";
               $re['big5']          = "/[/x01-/x7f]|[/x81-/xfe]([/x40-/x7e]|/xa1-/xfe])/";
               preg_match_all( $re[ $charset], $str, $match);
               if( count( $match[0]) <= $length) return $str;
               $slice = join( "", array_slice( $match[0], $start, $length));
         }
               if( $suffix ) return $slice."";
               return $slice;
}

function xinghao($data){
    return $data;
}

function allguangbo($DATA){

    $GAMEIDIP = Game_Server("online");

    

    $IP = fenpeiip(1,$GAMEIDIP);



    httpudp(array("y"=>"gonggao","d"=>$DATA),$IP['ip'], $IP['port']);

}

function GAMEDAOJU($UID,$name = '',$value = 0 ){

    $D = db('daoju');
    $DATA =  $D ->where( array('uid' => $UID)) ->find();
    if(!$DATA){

       $SHUJU =  $D ->tablejg['1'];

       foreach($SHUJU as $name=>$vv){

            if($vv == 'auto_increment'){
                continue ;
            }

            $suj = explode('_',$vv);

            if($name == 'bingdong' || $name == 'suoding' || $name == 'jiasu' ){

                $suj['1'] = 2;

            }   

            $DATA[$name] = $suj['1']; 

       }


       $DATA['uid'] = $UID;
       $D ->insert($DATA);
    }

    if( $name != '' && $value != 0){
        /*更改数据*/
        $fanhui = $DATA[$name]+=$value;

        if($fanhui < 0){
            return  false;
        }


        $fan = $D ->where( array('uid' => $UID ) ) ->update( array( "$name +"=> $value ) );

        if( $fan ){

            userlog( $UID, 6 , $name." ".$value );

            return $fanhui;

        }else  return  false;
        
    }else if($name != ''){

        /*返回单条数据*/
        return $DATA[$name];
    }


    return $DATA;
}

function msgbox ( $mess='' , $location='yes'){ 

    if( isset( $_GET['ajson'])){

        ob_clean();
        header('Content-type:application/json;charset=UTF-8');
        if( $location == 'yes' ) $location = 1;
        $token = token();

        if( isset( $_GET['action'])) $_SESSION[ $_GET['action']]  = $token;
        exit( json_encode( array( 'code' => $location,'msg' => $mess, 'token' =>  $token)));
     
    }

    if( $location == 'yes') 
        $locations = "window.history.back();";
    else 
        $locations = " window.location.href='".$location."';";

    if($mess == '')
        echo  '<script>'. $locations.'</script>';
    else
        echo  '<script>alert("'.$mess.'");'. $locations.'</script>';

    die;
}

function qunzhujia( $JIAUID ,$QUNZHU, $TYPE = 0 , $HUOBI = 0  , $DATA = '' , $ip = ''){

    /* 加钱
       $UID  用户uid
       $TYPE = 0 , $JINE = 0,$JIFEN = 0 , $HUOBI = 0  , $DATA = '' , $ip = ''
       
    */
   
  $D = db( 'user');

  $sql = '';

  if( $HUOBI != '0'){

    $requsetdata = $D -> zhicha('uid,tuid,qunjihe') ->where(array('uid'=>$JIAUID)) -> find();

    $userqun = explode(';',$requsetdata['qunjihe']);
    $qunarr = array();
    foreach($userqun as $kk=>$vv){
        $arr = explode(',',$vv);
        $qunarr[] = $arr[0];
    }
    
    if($qunarr && in_array($QUNZHU,$qunarr)){
        foreach($userqun as $kk=>$vv){
            $arr = explode(',',$vv);
            if((int)$arr[0] == $QUNZHU){
                $arr[1] += $HUOBI;
            }
            $userqun[$kk] = implode(',',$arr);
        }
        $qunjihe = implode(';',$userqun);

        $sql .= $D -> setshiwu(1)-> setbiao('user') -> where( array( 'uid' => $JIAUID)) -> update( array( 
        
            'qunjihe' => $qunjihe,
          )
        );

        $sql .= $D -> setshiwu(1) -> setbiao('huobilog') -> insert(array(   'uid' => $JIAUID,
                         'type' => $TYPE,
                         'jine' => $HUOBI,
                         'data' => $DATA,
                           'ip' => $ip ==''?ip(): $ip,
                        'atime' => time()
                 ));
    }
 
  }

  $fan = $D -> qurey($sql ,'shiwu');

  if( $fan ){

      return uid($JIAUID ,1,$D );

  }else return false;

}

function huobi($uid,$qunid){
    global $Mem ;
    $requsetdata = db('user') -> zhicha('uid,tuid,qunjihe') ->where(array('uid'=>$uid)) -> find();

    $userqun = explode(';',$requsetdata['qunjihe']);

    foreach($userqun as $kk=>$vv){
        $arr = explode(',',$vv);
      
        if((int)$arr[0] == $qunid){
            return $arr[1];
        }
    }
    return 0;
}



/**************************  代理（总数）
 * @param $uid 用户id
 * @param $jishu 统计级数
 * @return array|bool   (一至十级)
 */
function daili_count($uid,$jishu = 10,$gametype = ''){

    $CONN = include WYPHP.'conn.php';
    if($jishu > 10){
        $jishu = 10;
    }elseif($jishu < 1){
        return false;
    }

    $i = 0;

    $arr = array();
    if($uid){

        $D = db('user');
        $user = $D -> where(['uid'=>$uid]) -> find();

        if($user){

            

            if($gametype == 'apkhongbao'){
                $arr['totalnum'] = 0;
                $arr['data'] = array();
                
            }

            for($i = 0;$i < $jishu;$i++){

                if($i == 0){
                    $where = ['tuid'=>$uid];
                }else{
                    $where = ['tuid'.$i => $uid];
                }

                if($gametype == 'apkhongbao'){

                    $DATA = array();

                    $DATA['num'] = $D -> where($where)-> total();
    
                    $arr['totalnum'] += $DATA['num'];

                    //战队分级每级收益
                    $where = db('huobilog') -> wherezuhe(array('uid' => $uid,'type' => 18,'haomiaotime' => $i+1));
    
                    $sql = 'SELECT SUM(jine) as "TotalGet" FROM ay_huobilog '.$where;
    
                    $Total = db('huobilog') -> qurey($sql);
    
                    $DATA['get'] = $Total['TotalGet']?$Total['TotalGet']:0;


                    //今日
                    $todayStart = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
                    $todayEnd = mktime(23, 59, 59, date('m'), date('d'), date('Y'));

                    $where = db('huobilog') -> wherezuhe(array('atime >' => $todayStart,'atime <=' => $todayEnd,'type'=>18,'uid'=>$uid,'haomiaotime' => $i+1));

                    //战队收益-每级今日交易
                    $sql = 'SELECT SUM(remainhuobi) as "todaybet" FROM ay_huobilog '.$where;
                    $todayBet = db('huobilog') -> qurey($sql);
                    $DATA['todayBet'] = $todayBet['todaybet']?$todayBet['todaybet']:0;

                    //战队收益-每级今日佣金           
                    $sql = 'SELECT SUM(jine) as "todayGet" FROM ay_huobilog '.$where;
                    $todayTotal = db('huobilog') -> qurey($sql);
                    $DATA['todayGet'] = $todayTotal['todayGet']?$todayTotal['todayGet']:0;


                    //最近七天
                    $where = db('huobilog') -> wherezuhe(array('atime >' => (int)($todayEnd - 60*60*24*7),'atime <=' => $todayEnd,'type'=>18,'uid'=>$uid,'haomiaotime' => $i+1));

                    //战队收益-每级近期交易
                    $sql = 'SELECT SUM(remainhuobi) as "weekbet" FROM ay_huobilog '.$where;
                    $WeekBet = db('huobilog') -> qurey($sql);
                    $DATA['weekBet'] = $WeekBet['weekbet']?$WeekBet['weekbet']:0;

                    //战队收益-每级近期佣金           
                    $sql = 'SELECT SUM(jine) as "weekGet" FROM ay_huobilog '.$where;
                    $WeekTotal = db('huobilog') -> qurey($sql);
                    $DATA['weekGet'] = $WeekTotal['weekGet']?$WeekTotal['weekGet']:0;

                    $arr['data'][$i] = $DATA;

                }else{
                    $arr[$i] = $D -> where($where)-> total();

                }
                
            }

            return $arr;

        }else{
            return $arr;
        }

    }else{
        return false;
    }
}

/*******************  添加代理
 * @param $uid  用户id
 * @param $tuid  代理id
 * @return bool
 */
function tj_daili($uid,$tuid){

    if(empty($tuid) || empty($uid)){
        return false;
    }

    if($tuid == $uid){
        return json_encode(['code'=>4,'msg'=>'不能自己推广自己！']);
    }

    $D = db('user');

    $u = $D -> where(['uid' => $uid]) -> find();

    if(!$u){
        return json_encode(['code'=>5,'msg'=>'用户不存在！']);
    }elseif (!empty($u['tuid'])){
        return json_encode(['code'=>6,'msg'=>'用户已有上级代理！']);
    }elseif($u['daili'] == 1){
        return json_encode(['code'=>7,'msg'=>'用户已是代理！']);
    }

    $tuser = $D -> where(['uid' => $tuid]) -> find();

    if(!$tuser){
        return json_encode(['code'=>0,'msg'=>'推广员不存在！']);
    }elseif($tuser['daili'] != 1){
        $D -> where(['uid'=>$tuid]) -> update(['daili'=> 1]);
    }

    $i = 0;

    global $CONN;

    /*赏金*/
    $shangjin_kg = isset($CONN['shangjin_kg'])?$CONN['shangjin_kg']:0;
    $shangjinjine = 0;
    if($shangjin_kg == 1){

        $shangjin = isset($CONN['shangjin'])?$CONN['shangjin']:'1-3';
        $jinearr = explode('-',$shangjin);
        $shangjinjine = rand($jinearr[0],$jinearr[1]);

    }

    if($shangjinjine > 0){
        $user = $D -> where(['uid'=>$uid]) -> update(['tuid'=>$tuid , 'daili'=> 1,'shangjinjine'=>$shangjinjine]);
    }else{
        $user = $D -> where(['uid'=>$uid]) -> update(['tuid'=>$tuid , 'daili'=> 1]);
    }

    if($user){

        $where = ['uid' => $tuid];

        $u = $D -> where($where) -> find();

        if($u && !empty($u['tuid'])){
            while ($i < 9){

                if($i == 0){
                    $tuid = $u['tuid'];
                    $z_tuid = 'tuid1';
                }else{
                    $tuid = $u['tuid'.$i];
                    $z_tuid = 'tuid'.($i+1);
                }
                if(!empty($tuid)){

                    $D -> where(['uid'=>$uid]) -> update([ $z_tuid => $tuid ]);

                }else{
                    break ;
                }
                $i++;
            }
        }

        return json_encode(['code'=>1]);

    }else{
        return false;
    }
}


function CanGet($yesterdayGet,$UID){

    $D = db('gzlog');

    $USERDATA = $D -> where(array('uid' => $UID)) -> find();
    if((int)$USERDATA['islinqu'] == 1){ // 已领取
        return 0;
    }


    /*是否领取工资查询*/
    $Jtime = mktime(0,0,0,date('m'),date('d'),date('Y'));
    $rel = $D -> where(['gz_uid' => $UID,'gz_time >'=> $Jtime]) -> find();
    if($rel) return 0; // 已领取


    if((time() - strtotime(date('Y-m-d'))) > 43200){    /* 已过期 */

        return 0;
    }

    $CONN = include WYPHP.'conn.php';

    $canget = 0;    /* 未达成 */
    $tticheng = logac('ticheng');
    $dailiticheng = array();
    foreach($tticheng as $v){
        $data = explode('_',$v);
        if($data[2] == 1){
            $dailiticheng[] = $data;
        }
    }

    $num = count($dailiticheng) - 1;
    for($i = $num;$i >= 0;$i--){
        if($yesterdayGet >= $dailiticheng[$i][0]){

            if(isset($dailiticheng[$i+1])){

                if($yesterdayGet < $dailiticheng[$i+1][0]){

                    $canget = $dailiticheng[$i][1];
                    break;
                }

            }else{
                $canget = $dailiticheng[$i][1];
                break;
            }
        }
    }

    return $canget;
}



/***
 * @param $partner 通信账号
 * @param $out_trade_no 用户自定用账号
 * @param $trade_no 系统订单
 * @param $PAYKEY 密
 * @return string
 */
function is_pay( $partner,$out_trade_no,$trade_no,$PAYKEY){

    $DATA = array(
        'partner' =>$partner,  //通信账号
        'out_trade_no'=>$out_trade_no,  //用户自定用账号
        'trade_no'=>$trade_no,//系统订单
    );

    $DATA['sign'] = md5($PAYKEY.$DATA['out_trade_no'].'#'.$DATA['partner'].'#'.$DATA ['trade_no'].'#'.$PAYKEY);

    $url = 'https://pay.zszhifu.com/api/query.php?partner='.$DATA['partner'].'&out_trade_no='.$DATA['out_trade_no'].'&trade_no='.$DATA ['trade_no'].'&sign='.$DATA['sign'];
    $res = file_get_contents($url);

    return $res;

}


/*** 日志
 * @param $name 文件名字
 * @param $str 数据
 * @param $line 错误位置
 */
function rizhi( $name,$str,$line = '' ){

    if( gettype($str) != 'string' ) $str = json_encode($str);

    $path = WYPHP.'logs/';
    $dir = WYPHP.'logs/'.date('Ymd').'/';

    if(!is_dir($path)) mkdir($path);
    if(!is_dir($dir)) mkdir($dir);

    $time = date("Y-m-d H:i:s ");
    if(empty($line)){
        return file_put_contents( $dir.$name.'.txt',"\r\n".$time.$str,FILE_APPEND | LOCK_EX );
    }else{
        return file_put_contents( $dir.$name.'.txt',"\r\n".$time.$line.' '.$str,FILE_APPEND | LOCK_EX );
    }

}




/*** 赏金列表
 * @param $uid 用户id
 * @param int $limit
 * @param int $page
 * @return array （de=> 获得的赏金，lushang=> 在路上的赏金）
 */
function shangjin_list( $uid,$limit=20,$page=1 ){

    $D = db('user');

    $list = $D ->zhicha('uid,name,touxiang,atime,haveshangjin,shangjinjine') -> where(['tuid'=>$uid,'shangjinjine >'=>0]) ->order('atime desc') -> select();

    if($list){

        $de = [];
        $lushang = [];

        foreach ( $list as $v ){
            if($v['haveshangjin'] == 1) array_push($de,$v);
            else array_push($lushang,$v);
        }

        return [
            'de'=>$de,
            'lushang'=>$lushang,
        ];

    }else{
        return [
            'de'=>[],
            'lushang'=>[],
        ];
    }

}


/*** 更新赏金
 * @param $uid 玩家
 * @param $tuid 代理
 * @param string $beizhu 记录备注
 * @return bool
 */
function save_shangjin( $uid,$tuid,$beizhu = '' ){


    $CONN = include WYPHP."conn.php";
    $shangjin_kg = isset($CONN['isshangjin'])?$CONN['isshangjin']:0;
    if($shangjin_kg != 1) return false;


    $user = uid($uid,1);
    if( !$user || $user['off'] != 1 || !empty($user['haveshangjin']) || $user['tuid'] != $tuid || $user['shangjinjine'] <= 0) return false;

    $D = db('huobilog');
    $is_cz = $D -> where(['type'=>2,'uid'=>$uid,'jine >'=>0]) -> find();//是否充值
    if(!$is_cz) return false;


    $tuser = uid($tuid,1);
    /*被封无赏金*/
    if($tuser['off'] != 1) return false;

    if(empty($beizhu)) $beizhu = '获得'.$uid.'的赏金';

    $sql = $D -> setbiao('user') -> setshiwu(1) -> where(['uid'=>$uid]) -> update(['haveshangjin'=>1]);
    $sql .= $D -> setshiwu(1) -> where(['uid'=>$tuid]) -> update(['yongjin +'=>$user['shangjinjine']]);
    $sql .= $D -> setbiao('huobilog') -> setshiwu(1) -> insert([
        'uid' => $tuid,
        'jine' => $user['shangjinjine'],
        'ip' => $_SERVER['REMOTE_ADDR'],
        'atime' => time(),
        'type' => 19,
        'data' => $beizhu,
    ]);

    $rel = $D->query($sql, 'shiwu');
    if($rel){
        return true;
    }else{
        return false;
    }

}

/*** 系统
 * @return bool
 */
function systemOnOff($WYNAME){

    if($WYNAME == 'admin' || $WYNAME == 'vcode') return true;

    $CONN = $CONN = include WYPHP."conn.php";

    $system_kg = isset($CONN['system_kg'])?$CONN['system_kg']:0;

    if($system_kg == 1){

        if(isset($CONN['system_tu']) && !empty($CONN['system_tu'])){
            echo "<img  width='100%' height='100%' src='".pichttp($CONN['system_tu'])."' alt='系统维护中。。。'/>";
            die();
        }else{
            echo "<script type='text/javascript'> alert('系统维护中。。。'); </script>";
            die();
        }

    }

    return true;

}



/*** 后台数据统计列表
 * @param $page 页数
 * @param $limit 条数
 * @param string $ktime
 * @param string $jtime
 * @return array|bool
 */
function gainCount( $page,$limit,$ktime = '',$jtime = '' ){

    if( !is_int($limit) ) return false;
    else{
        if($limit < 5) $limit = 5;
        elseif ($limit > 10) $limit = 10;
    }
    if( !is_int($page) ) return false;
    else{ if($page < 1) $page = 1;}

    $start = $page * $limit - $limit;
    $end = $limit;

    $limit = "$start,$end";

    $D  = db('gaincount');

    $today_time = mktime(0,0,0,date('m'),date('d'),date('Y'));
    $WHERE['gc_time >='] = mktime(0,0,0,date('m'),date('d') - ($end * $page),date('Y'));
    $DATA = $D -> where($WHERE) ->limit($limit) ->order('gc_time desc') -> select();

    if(!$DATA || count($DATA) < $end){

        $newdata = [];

        foreach ($DATA as $k => $v ){
            $newdata[$v['gc_time']] = $v;
            $newdata[$v['gc_time']]['gc_time'] = date('Y-m-d',$v['gc_time']);;
        }

        $i = 0;
        while ($i < $end){

            $tian = $start + $i;
            $time = mktime(0,0,0,date('m'),date('d') - $tian,date('Y'));

            if(!$newdata[$time]){
                $rel = getTodayGainCount( $tian );
                $newdata[$time] = $rel;
                $rel['gc_time'] = $time;

                if($today_time != $time){
                    $fan = $D -> insert($rel);
                    if(!$fan) rizhi('tongJi',$fan.' '.json_encode($rel));
                }

            }
            $i++;
        }

        krsort($newdata);
        $DATA = array_values($newdata);

    }else{
        foreach ($DATA as $k => $v ){
            $DATA[$k]['gc_time'] = date('Y-m-d',$v['gc_time']);;
        }
        if($page == 1){
            $today = getTodayGainCount();
            array_unshift($DATA,$today);
        }

    }

    return $DATA;

}



/**** 盈亏统计（默认当日统计）
 * @param int $num 今日向前几天
 * @return array 统计数据
 */
function getTodayGainCount( $num = 0 ){

    if($num < 0) $num = 0;

    $D  = db('gaincount');

    $today_time = mktime(0,0,0,date('m'),date('d') - $num,date('Y'));

    if($num == 0) $end_time = time();
    else $end_time = mktime(0,0,0,date('m'),date('d') - ($num - 1),date('Y'));

    $today = [];

    //游戏记录
    $sql = "select SUM(jine) as num from ay_huobilog WHERE atime >= ".$today_time.' and atime < '.$end_time.' and `type` = 1';
    $rel = $D -> query( $sql );
    $today['gc_bet'] = isset($rel['num'])?$rel['num']:0;

    //上分
    $sql = "select SUM(jine) as num from ay_huobilog WHERE atime >= ".$today_time.' and atime < '.$end_time.' and `type` = 3 and `jine` > 0';
    $rel = $D -> query( $sql );
    $today['gc_shangfen'] = isset($rel['num'])?$rel['num']:0;

    //下分
    $sql = "select SUM(jine) as num from ay_huobilog WHERE atime >= ".$today_time.' and atime < '.$end_time.' and `type` = 3 and `jine` < 0';
    $rel = $D -> query( $sql );
    $today['gc_xiafen'] = isset($rel['num'])?$rel['num']:0;

    //充值
    $sql = "select SUM(jine) as num from ay_huobilog WHERE atime >= ".$today_time.' and atime < '.$end_time.' and `type` = 2';
    $rel = $D -> query( $sql );
    $today['gc_chongzhi'] = isset($rel['num'])?$rel['num']:0;

    //提现
    $sql = "select SUM(jine) as num from ay_huobilog WHERE atime >= ".$today_time.' and atime < '.$end_time.' and `type` = 17';
    $rel = $D -> query( $sql );
    $today['gc_tixian'] = isset($rel['num'])?$rel['num']:0;

    //佣金
    $sql = "select SUM(jine) as num from ay_huobilog WHERE atime >= ".$today_time.' and atime < '.$end_time.' and `type` = 18';
    $rel = $D -> query( $sql );
    $today['gc_yongjin'] = isset($rel['num'])?$rel['num']:0;

    //注册赠送
    $sql = "select SUM(jine) as num from ay_huobilog WHERE atime >= ".$today_time.' and atime < '.$end_time.' and `type` = 6';
    $rel = $D -> query( $sql );
    $today['gc_zengsong'] = isset($rel['num'])?$rel['num']:0;

    //工资
    $sql = "select SUM(jine) as num from ay_huobilog WHERE atime >= ".$today_time.' and atime < '.$end_time.' and `type` = 20';
    $rel = $D -> query( $sql );
    $today['gc_gongzi'] = isset($rel['num'])?$rel['num']:0;

    //盈利
    $gc_yingli = $today['gc_chongzhi'] + $today['gc_shangfen'] + $today['gc_xiafen'] -
        $today['gc_yongjin'] - $today['gc_tixian'] - $today['gc_bet'] - $today['gc_zengsong'] - $today['gc_gongzi'];
    $today['gc_yingli'] = round( (float)$gc_yingli,2 );


    $today['gc_time'] = date('Y-m-d',$today_time);

    return $today;

}


function tixianguoqi( $USERID = 0 ){

    /* 提现订单过期 */

    $atime = time() - 3600*24 ;


    $where = array( 'state' => '0' , 'time <'=> $atime);

    if( $USERID > 0) $where['uid'] = $USERID;
    $D = db('tixianshenhe');

    $DATA = $D -> where( $where ) -> select();

    if($DATA){

        foreach($DATA as $v){

            $dingdandata = db('tixiandingdan') -> where(array('uid' => $v['uid'],'time' => $v['time'])) -> find();

            if((int)$dingdandata['state'] != 1 && (int)$dingdandata['state'] != -1 && (int)$dingdandata['state'] != 3){

                $sql = db('tixiandingdan')-> setshiwu('1') -> where(array('uid' => $v['uid'],'time' => $v['time'])) -> update(array('state' => 3));

                db('tixiandingdan') -> qurey( $sql ,'shiwu');
            }
        }
    }

    $sss = $D -> where( $where )-> update(array( 'state'=> 3));

    $atime = time() - 3600*24*30;

    $where = array( 'off' => '3' , 'time <'=> $atime);

    if( $USERID > 0)  $where['uid'] = $USERID;

    $D -> where( $where )-> delete();

    return  $sss;
}



/* 写入每日统计 */
function AddTongJi($jine = 0){

    $D = db('everydaytongji');

    $back = $D -> where(array('time' => strtotime(date('Y-m-d')))) -> find();

    if($back){

        $fan = $D -> where(array('time' => strtotime(date('Y-m-d')))) -> update(array(
            'allchoushui +'=>$jine
        ));

        return $fan;

    }else{

        $beginYesterday = mktime(0,0,0,date('m'),date('d')-1,date('Y'));
        $endYesterday = mktime(0,0,0,date('m'),date('d'),date('Y'))-1;

        /** 充值 */
        $where = db('huobilog') -> wherezuhe(array('atime >' => $beginYesterday,'atime <=' => $endYesterday,'type'=>2));
        $sql = 'SELECT SUM(jine) as "chongzhi" FROM ay_huobilog '.$where;
        $ChongZhi = db('huobilog') -> qurey($sql);
        $chongzhijine = $ChongZhi['chongzhi']?$ChongZhi['chongzhi']:0;

        /** 上分 */
        $where = db('huobilog') -> wherezuhe(array('atime >' => $beginYesterday,'atime <=' => $endYesterday,'type'=>3,'jine >'=>0));
        $sql = 'SELECT SUM(jine) as "shangfen" FROM ay_huobilog '.$where;
        $ShangFen = db('huobilog') -> qurey($sql);
        $shangfenjine = $ShangFen['shangfen']?$ShangFen['shangfen']:0;

        /** 下分 */
        $where = db('huobilog') -> wherezuhe(array('atime >' => $beginYesterday,'atime <=' => $endYesterday,'type'=>3,'jine <'=>0));
        $sql = 'SELECT SUM(jine) as "xiafen" FROM ay_huobilog '.$where;
        $XiaFen = db('huobilog') -> qurey($sql);
        $xiafenjine = abs($XiaFen['xiafen'])?abs($XiaFen['xiafen']):0;

        /** 提现 */
        $where = db('huobilog') -> wherezuhe(array('atime >' => $beginYesterday,'atime <=' => $endYesterday,'type'=>17));
        $sql = 'SELECT SUM(jine) as "tixian" FROM ay_huobilog '.$where;
        $TiXian = db('huobilog') -> qurey($sql);
        $tixianjine = abs($TiXian['tixian'])?abs($TiXian['tixian']):0;

        /** 佣金 */
        $where = db('huobilog') -> wherezuhe(array('atime >' => $beginYesterday,'atime <=' => $endYesterday,'type'=>18));
        $sql = 'SELECT SUM(jine) as "yongjin" FROM ay_huobilog '.$where;
        $YongJin = db('huobilog') -> qurey($sql);
        $yongjinjine = $YongJin['yongjin']?$YongJin['yongjin']:0;

        /* 下注 */
        $where = db('jingcairecord') -> wherezuhe(array('time >' => $beginYesterday,'time <=' => $endYesterday));
        $sql = 'SELECT SUM(username) as "bet" FROM ay_jingcairecord '.$where;
        $Bet = db('jingcairecord') -> qurey($sql);
        $betjine = $Bet['bet']?$Bet['bet']:0;

        /* 派奖 */
        $where = db('jingcairecord') -> wherezuhe(array('time >' => $beginYesterday,'time <=' => $endYesterday));
        $sql = 'SELECT SUM(rewards) as "paijiang" FROM ay_jingcairecord '.$where;
        $PaiJiang = db('jingcairecord') -> qurey($sql);
        $paijiangjine = $PaiJiang['paijiang']?$PaiJiang['paijiang']:0;

        $fan = $D -> insert(array(
            'time' => strtotime(date('Y-m-d')),
            'allshangfen' => $shangfenjine,
            'allxiafen' => $xiafenjine,
            'allchongzhi' => $chongzhijine,
            'alltixian' => $tixianjine,
            'allyongjin' => $yongjinjine,
            'allbet' => $betjine,
            'allpaijiang' => $paijiangjine,
            'allchoushui'=>$jine,
            'todayyingli' => $chongzhijine - $tixianjine - $yongjinjine
        ));

        return $fan;
    }

}

/* 用户下注 佣金抽成 */
function BetYongJinGet($uid,$betjine,$bili){
    global $Mem,$CONN;

    $CONN = include WYPHP.'conn.php';

    // $betjine = $betjine/$CONN['paybilijb'];
    $ShareGet = explode('_',$bili);

    $uiddata = uid($uid);
    $allget = 0;

    for($i = 0;$i < $CONN['tuiji'];$i++){

        $k = $i;
        if($k == 0){
            $k = '';
        }

        if($uiddata['tuid'.$k]){

            $tuidhuobi = round($betjine*$ShareGet[$i],2);
            $allget += $tuidhuobi;
            $fan = jiaqian($uiddata['tuid'.$k],18,0,0,0,'因'.$uiddata['name'].'下注'.$betjine.'金，获得'.$tuidhuobi.'金','',$tuidhuobi,$k == ''?1:($k+1));

        }
    }

    return $allget;
}

/* 修改为未领取 */
function ChangeNotGet(){

    $D = db('user');

    $sql = $D ->setshiwu('1') -> update(array('islinqu' => 0));

    $D -> qurey( $sql , 'shiwu');
}

/* 写入竞猜记录 */
function AddLHJingCaiRecord($uid,$username,$qishu,$result,$betinfo,$userbet,$yingkui,$rewards,$remainhuobi,$gametype){
    global $Mem;

    return db('jingcairecord') -> insert(array(

        'uid' => $uid,
        'username' => $username,
        'qishu' => $qishu,
        'time' => time(),
        'result' => $result,
        'betinfo' => $betinfo,
        'userbet' => $userbet,
        'yingkui' => $yingkui,      
        'rewards'=>$rewards,
        'remainhuobi'=>$remainhuobi,
        'gametype'=>$gametype,
    ));
}

/* 写入竞猜记录 */
function AddHBJingCaiRecord($uid,$username,$qishu,$result,$betinfo,$userbet,$yingkui,$rewards,$remainhuobi,$gametype,$hbjine,$iszhonglei,$leihao){
    global $Mem;

    return db('jingcairecord') -> insert(array(

        'uid' => $uid,
        'username' => $username,  
        'qishu' => $qishu,
        'time' => time(),
        'result' => $result,
        'betinfo' => $betinfo,
        'userbet' => $userbet,
        'yingkui' => $yingkui,      
        'rewards'=>$rewards,
        'remainhuobi'=>$remainhuobi,
        'gametype'=>$gametype,
        'hbjine' => $hbjine,
        'iszhonglei' => $iszhonglei,
        'leihao' => $leihao,
    ));
}




/** 直接加入房间
 * @param $Mem
 * @param $HASH
 * @param $GAMEID 游戏标识
 * @param $FANGID 房间号
 * @param $USERID 用户id
 * @return array
 */
function joinGame( $Mem,$HASH,$GAMEID,$FANGID,$USERID,$type = 0 ){

    $GAMEIDIP = Game_Server( $GAMEID ); //获取游戏ip、端口
    if( !$GAMEIDIP ) return [[],415,-1,'@还没有这个游戏'];

    $IP = explode(':',$GAMEIDIP);
    $IP['ip'] = trim($IP[0]);
    $IP['port'] = trim($IP[1]);
    if( !empty($FANGID) ) {
        $fan = httpudp(['y'=>'fangcha','d'=>$FANGID,'uid'=>$USERID],$IP['ip'],  $IP['port'] );
        if( !$fan || $fan['code'] == -1 ) return [$fan,415,-1,(isset($fan['msg'])?'@'.$fan['msg']:'@房间不存在')];
    }else{
        $fan = httpudp(['y'=>'joingame','room_type'=>$type,'uid'=>$USERID],$IP['ip'],  $IP['port'] );
        if( !$fan || $fan['code'] == -1 ) return [$fan,415,-1,(isset($fan['msg'])?'@'.$fan['msg']:'@连接服务器失败')];
    }
    
    if( empty($FANGID) ) $FANGID = 1;
    $data = ingame( $Mem ,$HASH, $GAMEID,$FANGID,$USERID ,$GAMEIDIP);

    $Mem -> d( $HASH );
    if( $data ){
        return [$data,200,1,'@强行进入游戏'];
    }else{
        return [[],415,-1,'@进入游戏失败'];
    }

}


/** 房间大小盲(德州)
 * @return mixed
 */
function fangFen(){

    $CONN = include WYPHP."conn.php";

    //新手区
    $xsxiaomang = isset($CONN['dez_xsxiaomang'])?(int)$CONN['dez_xsxiaomang']:10; //小盲
    if( $xsxiaomang < 1 ) $xsxiaomang = 10;
    $xsdamang = $xsxiaomang * 2;
    $dez_xsjine = isset($CONN['dez_xsjine'])?(int)$CONN['dez_xsjine']:100; //进入游戏的金币
    $data[0] = ['xm'=>$xsxiaomang,'dm'=>$xsdamang,'huobi'=>$dez_xsjine];

    //高手区
    $gsxiaomang = isset($CONN['dez_gsxiaomang'])?(int)$CONN['dez_gsxiaomang']:25;
    if( $gsxiaomang < 1 ) $gsxiaomang = 25;
    $gsdamang = $gsxiaomang * 2;
    $dez_gsjine = isset($CONN['dez_gsjine'])?(int)$CONN['dez_gsjine']:500;
    $data[1] = ['xm'=>$gsxiaomang,'dm'=>$gsdamang,'huobi'=>$dez_gsjine];

    //大师区
    $dsxiaomang = isset($CONN['dez_dsxiaomang'])?(int)$CONN['dez_dsxiaomang']:50;
    if( $dsxiaomang < 1 ) $dsxiaomang = 50;
    $dsdamang = $dsxiaomang * 2;
    $dez_dsjine = isset($CONN['dez_dsjine'])?(int)$CONN['dez_dsjine']:1000;
    $data[2] = ['xm'=>$dsxiaomang,'dm'=>$dsdamang,'huobi'=>$dez_dsjine];

    //巅峰区
    $dfxiaomang = isset($CONN['dez_dfxiaomang'])?(int)$CONN['dez_dfxiaomang']:100;
    if( $dfxiaomang < 1 ) $dfxiaomang = 100;
    $dfdamang = $dfxiaomang * 2;
    $dez_dfjine = isset($CONN['dez_dfjine'])?(int)$CONN['dez_dfjine']:10000;
    $data[3] = ['xm'=>$dfxiaomang,'dm'=>$dfdamang,'huobi'=>$dez_dfjine];

    return $data;

}



$SECC = $Mem = new txtcc();