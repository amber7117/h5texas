var HTTP = '/';
var TXIN = HTTP+'admin.php';
var USIN =HTTP+'json.php';
var OPID ;
var TOKEN;
var QT = 1;
var $_GET = (function(){
    var url = window.document.location.href.toString();
    var u = url.split("?");
    if(typeof(u[1]) == "string"){
        u = u[1].split("&");
        var get = {};
        for(var i in u){
            var j = u[i].split("=");
            get[j[0]] = j[1];
        }

        return get;
    } else {
        return {};
    }

})();

function setCookie(c_name, value, expiredays){
    var exdate=new Date();
    expiredays = 360000;
    exdate.setDate(exdate.getDate() + expiredays);
    document.cookie=c_name+ "=" + escape(value) + ((expiredays==null) ? "" : ";expires="+exdate.toGMTString()+";path=/");
}
  
function getCookie(name)
{
    var arr,reg=new RegExp("(^| )"+name+"=([^;]*)(;|$)");

    if(arr=document.cookie.match(reg)){

        return (arr[2]);

    }else{
        return null;
    }
}
 
function delCookie(name)
{
    var exp = new Date();
    exp.setTime(exp.getTime() - 1);
    var cval=getCookie(name);
    if(cval!=null)
    document.cookie= name + "="+cval+";expires="+exp.toGMTString();
}

function time(times){

    if(times < 1) return 'Unknown';
    var newDate = new Date();
    newDate.setTime(times * 1000);
    return newDate.toLocaleString().replace(/:\d{1,2}$/,' ');

}

function ajaxfan(XMLHttpRequest){

    /*200 操作成功
    401 需要登录用户
    500 内部服务器错误
    304 修改失败  put
    410 删除失败  delete
    404 查询失败  get  select
    406 新增失败  post
    415 非法数据 token错误
    */

    var data;

   

  
    status = XMLHttpRequest.status;

    if(status == '0'){


        layer.alert("连接服务器失败,请检查服务器是否开启");
        $(".degluee").hide();
        return false;
    }

    if(XMLHttpRequest.response){

        if(XMLHttpRequest.response != "")
            data = JSON.parse(XMLHttpRequest.response);
        else data ={};

    }else{

        if(XMLHttpRequest.responseText != "")
            data = JSON.parse(XMLHttpRequest.responseText);
        else data ={};

    }


    apptoken = getCookie("apptoken");

    if(!apptoken || apptoken.lenght < 62){

        if(data.data.apptoken) setCookie("apptoken",data.data.apptoken);
    }

    if(data.token && data.token != ""){
            
        TOKEN = data.token;
    }

    if( status == 400 ){

        layer.msg("修改失败");

    }else if( status == 410 ){

        layer.msg("删除失败");

    }else if( status == 404 ){

        layer.msg("查询失败");

    }else if( status == 406 ){

        layer.msg("新增失败");


    }else if( status == 415 ){

        if( data.code == -99 ){

       
            if(QT == 2){
            
                qtdenglu();

            }else{

                denglu();
            }
            return;
            


        }else if( data.code == -9 ){

             $("#imgages").attr({"src":HTTP+"vcode.php?apptoken="+getCookie("apptoken")+"&timess="+Math.round(Math.random()*99999) });
            
        }

        layer.msg(data.msg);

    }else{

        layer.msg("系统错误");
    }
}

function dengludo(){

  

    zhanghao = $("#adzhanghao").val();

    pass = $("#admima").val();

    vcode  = $("#adcode").val();


    $.ajax({

        url:TXIN,
        type: "POST",
        data:{y:"login",d:"get",zhanghao:zhanghao,pass:pass,vcode:vcode,apptoken:getCookie("apptoken")},
        dataType: "json",
        timeout:"3000",
        success: function(data){

            window.location.reload();

        },error:function(XMLHttpRequest){
            ajaxfan(XMLHttpRequest);
        }

    });

    return false;
}

function quite(){

     $.ajax({

        url:TXIN,
        type: "POST",
        data:{y:"quite",apptoken:getCookie("apptoken")},
        dataType: "json",
        timeout:"3000",
        success: function(data){


            window.location.href="index.html";

        },error:function(XMLHttpRequest){
            ajaxfan(XMLHttpRequest);
        }

    });


}

function denglu(){

$=layui.jquery;

html ='<form class="layui-form" style="padding:30px 30px 0px 0px;" onsubmit="return dengludo();">';

html +='<div class="layui-form-item"><label class="layui-form-label">登录帐号</label><div class="layui-input-block"><input type="text" name="zhanghao" required  lay-verify="required" placeholder="管理帐号" id="adzhanghao" autocomplete="off" class="layui-input"></div></div>';

html +='<div class="layui-form-item"><label class="layui-form-label">登录密码</label><div class="layui-input-block"><input type="password" name="pass" required="" lay-verify="required"  id="admima" placeholder="请输入密码" autocomplete="off" class="layui-input"></div></div>';

html +='<div class="layui-form-item"> <label class="layui-form-label">验证码</label><div class="layui-input-inline" style="width:88px;font-size:12px;float:left;margin-left:0px;"><input type="text" name="vcode" required="" lay-verify="required" placeholder="请输入验证码" id="adcode" autocomplete="off" class="layui-input"></div><div class="layui-form-mid layui-word-aux" style="padding:0px;margin-left:0px;"><img id="imgages" style="position:relative;top:2px;width:100px;height:38px;"></div> </div>';

html +='<div class="layui-form-item"><div class="layui-input-block"><button class="layui-btn" lay-submit="" lay-filter="formDemo">立即登录</button> <button type="reset" class="layui-btn layui-btn-primary">重置</button></div></div></form>';

if( $(window).width() > 700){

    kuan =['500px', '350px'];

}else{

    kuan =['380px', '350px'];

}


layer.open({
   title:'帐号登录',
  area: kuan,
  type: 1, 
  content: html,
  success: function(layero, index){

    $("#imgages").click(function(){

        $("#imgages").attr({"src":HTTP+"vcode.php?apptoken="+getCookie("apptoken")+"&timess="+Math.round(Math.random()*99999) });

    });

    $("#imgages").attr({"src":HTTP+"vcode.php?apptoken="+getCookie("apptoken")+"&timess="+Math.round(Math.random()*99999) });
   
  }
});

}

function ncaidan(name){

    $("#daad"+name).find('.zengjian').remove();

    i = $("#daad"+name).find('input').length;

    var  zx= "<a style=\\'color:#fff;cursor:pointer;\\' class=\\'ashanchu\\' data=\\'"+name+'X'+i+"\\'>点击我删除</a>";

    html ='<div class="layui-input-inline" id="'+name+'X'+i+'" onclick="layer.tips(\''+zx+'\', \'#'+name+'X'+i+'\',{ time:30000,tips: 1});" style="margin-bottom:10px;" ><b style="float:left;height:38px;line-height:38px;margin-right:8px;width:25px;">'+i+'</b><input type="text" name="'+name+'['+i+']" value="" placeholder="请输入值" autocomplete="off" class="layui-input"></div>';


    html +='<div class="layui-input-inline zengjian" style="margin-bottom:10px;" ><a href="javascript:ncaidan(\''+name+'\')" class="layui-btn">增加菜单</a></div>';

    $("#daad"+name).append(html);

     $(".layui-input-inline").click(function(){

            $(".ashanchu").click(function(){

                iid = $(this).attr('data');
                $("#"+iid).remove();
                layer.close(layer.index);
            });
    });


}

var formatJson = function(json, options) {
	var reg = null,
		formatted = '',
		pad = 0,
		PADDING = '    '; 

	options = options || {};
	options.newlineAfterColonIfBeforeBraceOrBracket = (options.newlineAfterColonIfBeforeBraceOrBracket === true) ? true : false;
	options.spaceAfterColon = (options.spaceAfterColon === false) ? false : true;

    if(json == ''){

        return ;
    
    }else if (typeof json !== 'string') {
		json = JSON.stringify(json);
	} else {

		json = JSON.parse(json);
		json = JSON.stringify(json);
	}
 
	reg = /([\{\}])/g;
	json = json.replace(reg, '\r\n$1\r\n');
 
	reg = /([\[\]])/g;
	json = json.replace(reg, '\r\n$1\r\n');
 
	reg = /(\,)/g;
	json = json.replace(reg, '$1\r\n');
 
	reg = /(\r\n\r\n)/g;
	json = json.replace(reg, '\r\n');
 
	reg = /\r\n\,/g;
	json = json.replace(reg, ',');
 
	if (!options.newlineAfterColonIfBeforeBraceOrBracket) {			
		reg = /\:\r\n\{/g;
		json = json.replace(reg, ':{');
		reg = /\:\r\n\[/g;
		json = json.replace(reg, ':[');
	}
	if (options.spaceAfterColon) {			
		reg = /\:/g;
		json = json.replace(reg, ':');
	}
 
	$.each(json.split('\r\n'), function(index, node) {
		var i = 0,
			indent = 0,
			padding = '';
 
		if (node.match(/\{$/) || node.match(/\[$/)) {
			indent = 1;
		} else if (node.match(/\}/) || node.match(/\]/)) {
			if (pad !== 0) {
				pad -= 1;
			}
		} else {
			indent = 0;
		}
 
		for (i = 0; i < pad; i++) {
			padding += PADDING;
		}
 
		formatted += padding + node + '\r\n';
		pad += indent;
	});
 
	return formatted;
};





function jsfrom(zifu){

    /*'name#管理名字#text#50%#提示这是一个#默认值'*/

    zifu = zifu.split("#"); 

    name = zifu['0']?zifu['0']:'';
    title = zifu['1']?zifu['1']:'';
    type = zifu['2']?zifu['2']:'';
    css = zifu['3']?zifu['3']:'';
    tishi = zifu['4']?zifu['4']:'';
    moren = zifu['5']?zifu['5']:'';
    verify = zifu['6']?zifu['6']:'';


    html = '<div class="layui-form-item" title="'+tishi+'"><label class="layui-form-label">'+title+'</label><div class="layui-input-block" id="daad'+name+'">';

    if(type == 'text'){

        html +='<input type="text" value="'+moren+'" name="'+name+'" style="'+css+'" lay-verify="'+verify+'" placeholder="'+tishi+'" autocomplete="off" class="layui-input">';

    }else if(type == 'textgbi'){

        html +='<input type="text" value="'+moren+'" name="'+name+'" style="'+css+'" lay-verify="'+verify+'" placeholder="'+tishi+'" autocomplete="off" class="layui-input layui-disabled">';

    }else if(type == 'timetext'){

        html +='<input type="text" value="'+moren+'" name="'+name+'" style="'+css+'" lay-verify="'+verify+'" placeholder="'+tishi+'" autocomplete="off" class="layui-input">';

    }else if(type == 'textshow'){

        html +='<div class="layui-form-mid layui-word-aux">'+moren+'</div>';

    }else if(type == 'selectshow'){

        $zhi = eval(moren);
        if($zhi){
            
            html +='<div class="layui-form-mid layui-word-aux">'+$zhi[tishi]+'</div>';

        }else{

            html +='<div class="layui-form-mid layui-word-aux">'+moren+'</div>';

        }

    }else if(type == 'time'){

        html +='<div class="layui-form-mid layui-word-aux">'+time(moren)+'</div>';
    
    
    }else if(type == 'caidan'){
   
        $zhi = eval(moren);
       

        if($zhi){

            for(var i in $zhi){

                var  zx= "<a style=\\'color:#fff;cursor:pointer;\\' class=\\'ashanchu\\' data=\\'"+name+'X'+i+"\\'>点击我删除</a>";

                html +='<div class="layui-input-inline" id="'+name+'X'+i+'" onclick="layer.tips(\''+zx+'\', \'#'+name+'X'+i+'\',{ time:30000,tips: 1});" style="margin-bottom:10px;" ><b style="float:left;height:38px;line-height:38px;margin-right:8px;width:25px;">'+i+'</b><input type="text" name="'+name+'['+i+']" value="'+$zhi[i]+'" placeholder="'+tishi+'" autocomplete="off" class="layui-input"></div>';
            }

            setTimeout(function(){

                $(".layui-input-inline").click(function(){

                        $(".ashanchu").click(function(){

                            iid = $(this).attr('data');
                            $("#"+iid).remove();
                            layer.close(layer.index);
                        });
                });
            },50);
        }


        html +='<div class="layui-input-inline zengjian" style="margin-bottom:10px;" ><a href="javascript:ncaidan(\''+name+'\')" class="layui-btn">增加菜单</a></div>';

    }else if(type == 'select'){

        gg = '<option value=""></option>';

        szu = eval(tishi);

        if(szu && typeof(szu) != "undefined" && szu != '' ){

            gg = '';

            for(var k in szu){
                gg+= '<option value="'+k+'">'+szu[k]+'</option>';
            
            }
        }

        html+='<select name="'+name+'">'+gg+'</select>';

    }else if(type == 'selectk'){

        gg = '<option value=""></option>';

        szu = eval(tishi);

        if(szu && typeof(szu) != "undefined" && szu != '' ){

            gg = '';

            for(var k in szu){
                gg+= '<option value="'+szu[k]+'">'+szu[k]+'</option>';
            
            }
        }

        html+='<select name="'+name+'">'+gg+'</select>';

    }else if(type == 'checkbox'){

        szu = eval(tishi);

        if(  typeof(szu) != "undefined" && szu  && szu != '' ){

            for(var k in szu){
                html+= '<input type="checkbox" name="'+name+'['+k+']" value="'+k+'"  style="'+css+'" title="'+szu[k]+'">';
            
            }

        }
    
    
    }else if(type == 'date'){

        html+='<input type="text" value="'+moren+'" name="'+name+'" style="'+css+'" lay-verify="'+verify+'" placeholder="'+tishi+'" autocomplete="off" class="layui-input" onclick="layui.laydate({elem: this})">';
    
    
    
    }else if(type == 'textarea'){

        html+='<textarea name="'+name+'" style="'+css+'" lay-verify="'+verify+'" placeholder="'+tishi+'"  class="layui-textarea">'+moren+'</textarea>';
    
    
    
    }else if(type == 'ui'){

        var linshi = name;
        html+='<textarea class="layui-textarea" name="'+name+'" style="'+css+'" lay-verify="content" id="UI'+name+'"></textarea>';


    }else if(type == 'radio'){

        szu = eval(tishi);

        if(  typeof(szu) != "undefined" && szu  && szu != '' ){


            for(var k in szu){
                html+= '<input type="radio" name="'+name+'" value="'+k+'" style="'+css+'" title="'+szu[k]+'">';
            
            }

        }


    }else if(type == 'switch'){

        html+= '<input type="checkbox" name="'+name+'" lay-skin="switch"  style="'+css+'" lay-text="'+tishi+'">';
    
    
    }else if(type == 'tjcz'){

        if(moren == ''){


            html +='<button type="submit" class="layui-btn layui-btn-danger" onclick="layer.close(OPID);">返回</button>';
        
        }else{
        
            html +='<button class="layui-btn" type="submit" lay-submit name="'+name+'" style="'+css+'" lay-filter="'+verify+'" >'+moren+'</button>  <button type="submit" class="layui-btn layui-btn-primary  layui-btn-small" onclick="layer.close(OPID);">返回</button>';
        }

        
    
    }else if(type == 'update'){


        var linshi = name;



        html +='<div class="layui-input-inline" style="width:50%;"><input type="text" value="'+moren+'" name="'+name+'" id="ky'+name+'" style="'+css+'" lay-verify="'+verify+'" placeholder="'+tishi+'" autocomplete="off" class="layui-input"> </div><div id="up'+linshi+'" class="layui-input-inline">文件上传</div>';

        setTimeout(function(){


            eval('var uploader'+linshi+' = WebUploader.create({auto: true,server:"'+HTTP+'update.php?&uplx=all&apptoken='+getCookie("apptoken")+'",pick: "#up'+linshi+'",resize: true,fileVal :"all"});uploader'+linshi+'.on( "uploadError",function(){layer.msg("上传失败");});uploader'+linshi+'.on( "uploadSuccess", function( file,response ) { if(response.code == 1){ $("#ky'+linshi+'").val(response.msg); }else if(response.code == -1){ layer.msg(response.msg); } });');

     
        },10);

    }else if(type == 'updateshow'){


        var linshi = name;

        html +='<div class="layui-input-inline" style="width:auto;"><input type="hidden" value="'+moren+'" name="'+name+'" id="ky'+name+'" style="'+css+'" lay-verify="'+verify+'" placeholder="'+tishi+'" autocomplete="off" class="layui-input"><img  id="img'+name+'" style="width:100px;height:100px;display:block;border-radius:100%;border:1px solid #ccc; "> </div><div id="up'+linshi+'" class="layui-input-inline">文件上传</div>';

        setTimeout(function(){

            eval('var uploader'+linshi+' = WebUploader.create({auto: true,server:"'+HTTP+'update.php?uplx=all&apptoken='+getCookie("apptoken")+'",pick: "#up'+linshi+'",resize: true,fileVal :"all"});uploader'+linshi+'.on( "uploadError",function(){layer.msg("上传失败");});uploader'+linshi+'.on( "uploadSuccess", function( file,response ) { if(response.code == 1){ $("#ky'+linshi+'").val(response.msg); $("#img'+linshi+'").attr({"src":response.msg}); }else if(response.code == -1){ layer.msg(response.msg); } });');

        },10);

    }else{

        html +='<input type="'+type+'" value="'+moren+'" name="'+name+'" style="'+css+'" lay-verify="'+verify+'" placeholder="'+tishi+'" autocomplete="off" class="layui-input">';

    }

    html +='</div></div>';
    return html;
}

 ;!function(e){"use strict";var t=function(){this.v="1.0.9_rls"};t.fn=t.prototype;var n=document,o=t.fn.cache={},i=function(){var e=n.scripts,t=e[e.length-1].src;return t.substring(0,t.lastIndexOf("/")+1)}(),r=function(t){e.console&&console.error&&console.error("Layui hint: "+t)},l="undefined"!=typeof opera&&"[object Opera]"===opera.toString(),a={layer:"modules/layer",laydate:"modules/laydate",laypage:"modules/laypage",laytpl:"modules/laytpl",layim:"modules/layim",layedit:"modules/layedit",form:"modules/form",upload:"modules/upload",tree:"modules/tree",table:"modules/table",element:"modules/element",util:"modules/util",flow:"modules/flow",carousel:"modules/carousel",code:"modules/code",jquery:"modules/jquery",mobile:"modules/mobile","layui.all":"dest/layui.all"};o.modules={},o.status={},o.timeout=10,o.event={},t.fn.define=function(e,t){var n=this,i="function"==typeof e,r=function(){return"function"==typeof t&&t(function(e,t){layui[e]=t,o.status[e]=!0}),this};return i&&(t=e,e=[]),layui["layui.all"]||!layui["layui.all"]&&layui["layui.mobile"]?r.call(n):(n.use(e,r),n)},t.fn.use=function(e,t,u){function s(e,t){var n="PLaySTATION 3"===navigator.platform?/^complete$/:/^(complete|loaded)$/;("load"===e.type||n.test((e.currentTarget||e.srcElement).readyState))&&(o.modules[m]=t,y.removeChild(p),function i(){return++v>1e3*o.timeout/4?r(m+" is not a valid module"):void(o.status[m]?c():setTimeout(i,4))}())}function c(){u.push(layui[m]),e.length>1?f.use(e.slice(1),t,u):"function"==typeof t&&t.apply(layui,u)}var f=this,d=o.dir=o.dir?o.dir:i,y=n.getElementsByTagName("head")[0];e="string"==typeof e?[e]:e,window.jQuery&&jQuery.fn.on&&(f.each(e,function(t,n){"jquery"===n&&e.splice(t,1)}),layui.jquery=jQuery);var m=e[0],v=0;if(u=u||[],o.host=o.host||(d.match(/\/\/([\s\S]+?)\//)||["//"+location.host+"/"])[0],0===e.length||layui["layui.all"]&&a[m]||!layui["layui.all"]&&layui["layui.mobile"]&&a[m])return c(),f;var p=n.createElement("script"),h=(a[m]?d+"lay/":o.base||"")+(f.modules[m]||m)+".js";return p.async=!0,p.charset="utf-8",p.src=h+function(){var e=o.version===!0?o.v||(new Date).getTime():o.version||"";return e?"?v="+e:""}(),o.modules[m]?!function g(){return++v>1e3*o.timeout/4?r(m+" is not a valid module"):void("string"==typeof o.modules[m]&&o.status[m]?c():setTimeout(g,4))}():(y.appendChild(p),!p.attachEvent||p.attachEvent.toString&&p.attachEvent.toString().indexOf("[native code")<0||l?p.addEventListener("load",function(e){s(e,h)},!1):p.attachEvent("onreadystatechange",function(e){s(e,h)})),o.modules[m]=h,f},t.fn.getStyle=function(t,n){var o=t.currentStyle?t.currentStyle:e.getComputedStyle(t,null);return o[o.getPropertyValue?"getPropertyValue":"getAttribute"](n)},t.fn.link=function(e,t,i){var l=this,a=n.createElement("link"),u=n.getElementsByTagName("head")[0];"string"==typeof t&&(i=t);var s=(i||e).replace(/\.|\//g,""),c=a.id="layuicss-"+s,f=0;a.rel="stylesheet",a.href=e+(o.debug?"?v="+(new Date).getTime():""),a.media="all",n.getElementById(c)||u.appendChild(a),"function"==typeof t&&!function d(){return++f>1e3*o.timeout/100?r(e+" timeout"):void(1989===parseInt(l.getStyle(n.getElementById(c),"width"))?function(){t()}():setTimeout(d,100))}()},t.fn.addcss=function(e,t,n){layui.link(o.dir+"css/"+e,t,n)},t.fn.img=function(e,t,n){var o=new Image;return o.src=e,o.complete?t(o):(o.onload=function(){o.onload=null,t(o)},void(o.onerror=function(e){o.onerror=null,n(e)}))},t.fn.config=function(e){e=e||{};for(var t in e)o[t]=e[t];return this},t.fn.modules=function(){var e={};for(var t in a)e[t]=a[t];return e}(),t.fn.extend=function(e){var t=this;e=e||{};for(var n in e)t[n]||t.modules[n]?r("模块名 "+n+" 已被占用"):t.modules[n]=e[n];return t},t.fn.router=function(e){for(var t,n=(e||location.hash).replace(/^#/,"").split("/")||[],o={dir:[]},i=0;i<n.length;i++)t=n[i].split("="),/^\w+=/.test(n[i])?function(){"dir"!==t[0]&&(o[t[0]]=t[1])}():o.dir.push(n[i]),t=null;return o},t.fn.data=function(t,n){if(t=t||"layui",e.JSON&&e.JSON.parse){if(null===n)return delete localStorage[t];n="object"==typeof n?n:{key:n};try{var o=JSON.parse(localStorage[t])}catch(i){var o={}}return n.value&&(o[n.key]=n.value),n.remove&&delete o[n.key],localStorage[t]=JSON.stringify(o),n.key?o[n.key]:o}},t.fn.device=function(t){var n=navigator.userAgent.toLowerCase(),o=function(e){var t=new RegExp(e+"/([^\\s\\_\\-]+)");return e=(n.match(t)||[])[1],e||!1},i={os:function(){return/windows/.test(n)?"windows":/linux/.test(n)?"linux":/iphone|ipod|ipad|ios/.test(n)?"ios":void 0}(),ie:function(){return!!(e.ActiveXObject||"ActiveXObject"in e)&&((n.match(/msie\s(\d+)/)||[])[1]||"11")}(),weixin:o("micromessenger")};return t&&!i[t]&&(i[t]=o(t)),i.android=/android/.test(n),i.ios="ios"===i.os,i},t.fn.hint=function(){return{error:r}},t.fn.each=function(e,t){var n,o=this;if("function"!=typeof t)return o;if(e=e||[],e.constructor===Object){for(n in e)if(t.call(e[n],n,e[n]))break}else for(n=0;n<e.length&&!t.call(e[n],n,e[n]);n++);return o},t.fn.stope=function(t){t=t||e.event,t.stopPropagation?t.stopPropagation():t.cancelBubble=!0},t.fn.onevent=function(e,t,n){return"string"!=typeof e||"function"!=typeof n?this:(o.event[e+"."+t]=[n],this)},t.fn.event=function(e,t,n){var i=this,r=null,l=t.match(/\(.*\)$/)||[],a=(t=e+"."+t).replace(l,""),u=function(e,t){var o=t&&t.call(i,n);o===!1&&null===r&&(r=!1)};return layui.each(o.event[a],u),l[0]&&layui.each(o.event[t],u),r},e.layui=new t}(window);