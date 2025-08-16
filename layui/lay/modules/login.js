/** layui-v1.0.9_rls MIT License By http://www.layui.com */

layui.define('layer', function(e){
  var layer = layui.layer;
  var $=layui.jquery;

    $.ajax({

        url:TXIN,
        type: "POST",
        data:{y:"index",d:"get",apptoken:getCookie("apptoken")},
        dataType: "json",
        timeout:"3000",
        success: function(data){

            apptoken = getCookie("apptoken");

            if(!apptoken || apptoken.lenght < 62){

                if(data.data.apptoken) setCookie("apptoken",data.data.apptoken);
            }
            

           WY_Main(data.data);

        },error:function(XMLHttpRequest){

            

            ajaxfan(XMLHttpRequest);

            $(".degluee").html('<a href="javascript:denglu();">请先登录</a>');
        }
    });

    e('login', {});
}); 