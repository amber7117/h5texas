require = function() {
  function r(e, n, t) {
    function o(i, f) {
      if (!n[i]) {
        if (!e[i]) {
          var c = "function" == typeof require && require;
          if (!f && c) return c(i, !0);
          if (u) return u(i, !0);
          var a = new Error("Cannot find module '" + i + "'");
          throw a.code = "MODULE_NOT_FOUND", a;
        }
        var p = n[i] = {
          exports: {}
        };
        e[i][0].call(p.exports, function(r) {
          var n = e[i][1][r];
          return o(n || r);
        }, p, p.exports, r, e, n, t);
      }
      return n[i].exports;
    }
    for (var u = "function" == typeof require && require, i = 0; i < t.length; i++) o(t[i]);
    return o;
  }
  return r;
}()({
  Msgbox: [ function(require, module, exports) {
    "use strict";
    cc._RF.push(module, "7d417708jpKsKXP38TkALw3", "Msgbox");
    "use strict";
    var THIS = null;
    cc.Class({
      extends: cc.Component,
      properties: {},
      chushi: function chushi() {
        if (!this.MSGBOXNODE) return false;
        if (this.MSGBOXTIME) {
          this.unschedule(this.MSGBOXTIME);
          this.MSGBOXTIME = null;
        }
        this._quxiaozhi = null;
        this._queren = null;
        this.kedian = true;
        this._loadingwen = "";
        cc.SUO = 0;
        THIS.MSGBOXNODE.active = true;
        cc.find("loading", this.MSGBOXNODE).active = false;
        cc.find("alert", this.MSGBOXNODE).active = false;
        cc.find("alert/wenzi", this.MSGBOXNODE).getComponent(cc.RichText).string = "";
        cc.find("alert/wenzi", this.MSGBOXNODE).y = 20;
        cc.find("alert/wenzi", this.MSGBOXNODE).color = new cc.hexToColor("#390404");
        cc.find("alert/loading", this.MSGBOXNODE).active = false;
        cc.find("alert/loading", this.MSGBOXNODE).y = -68;
        cc.find("alert/anniuzu", this.MSGBOXNODE).active = false;
        cc.find("alert/anniuzu/queren", this.MSGBOXNODE).active = false;
        cc.find("alert/anniuzu/quxiao", this.MSGBOXNODE).active = false;
        return true;
      },
      onLoad: function onLoad() {
        cc.msgbox = THIS = this;
        this.MSGBOXTIME = null;
        this.MSGBOXNODE = cc.find("Canvas/Msgbox");
        cc.loader.onProgress = null;
        cc.SUO = 0;
        this._quxiaozhi = null;
        this._queren = null;
        if (this.MSGBOXNODE) {
          THIS.MSGBOXNODE.active = false;
          this.bindall();
        } else cc.loader.loadRes("prefab/Msgbox", function(err, prefab) {
          if (!err) {
            var node = cc.instantiate(prefab);
            node.name = "Msgbox";
            node.parent = cc.find("Canvas");
            node.active = false;
            THIS.MSGBOXNODE = node;
            THIS.bindall();
          }
        });
      },
      bindall: function bindall() {
        this.MSGBOXNODE.setLocalZOrder(999);
        cc.anniu(cc.find("alert/anniuzu/queren", this.MSGBOXNODE), "Msgbox", "QueRen");
        cc.anniu(cc.find("alert/anniuzu/quxiao", this.MSGBOXNODE), "Msgbox", "QuXiao");
        cc.anniu([ cc.find("alert/guanbi", this.MSGBOXNODE), cc.find("zhezhao", this.MSGBOXNODE) ], "Msgbox", "QuXiao");
      },
      QueRen: function QueRen() {
        if (!this.kedian) return;
        this.hide();
        this._queren && this._queren();
      },
      QuXiao: function QuXiao() {
        if (!this.kedian) return;
        this.hide();
        this._quxiaozhi && this._quxiaozhi();
      },
      jieping: function jieping(content, url) {
        this.scheduleOnce(function() {
          if (!this.chushi()) return false;
          cc.find("alert", THIS.MSGBOXNODE).active = true;
          null == content && (content = "");
          cc.find("alert/wenzi", this.MSGBOXNODE).y = 150;
          cc.find("alert/wenzi", this.MSGBOXNODE).color = new cc.hexToColor("#ffffff");
          cc.find("alert/wenzi", this.MSGBOXNODE).setLocalZOrder(999);
          cc.find("alert/guanbi", this.MSGBOXNODE).setLocalZOrder(999);
          cc.find("alert/wenzi", this.MSGBOXNODE).getComponent(cc.RichText).string = content;
          var imgtupian = cc.find("alert/erweima", this.MSGBOXNODE);
          var erweimabeiji = cc.find("alert/erweimabeiji", this.MSGBOXNODE);
          if (!erweimabeiji) {
            var beijing = cc.find("Canvas/beijing");
            if (beijing) {
              erweimabeiji = cc.instantiate(beijing);
              erweimabeiji.name = "erweimabeiji";
              erweimabeiji.parent = cc.find("alert", this.MSGBOXNODE);
            }
          }
          if (!imgtupian) {
            imgtupian = new cc.Node("erweima");
            imgtupian.parent = cc.find("alert", this.MSGBOXNODE);
            var sp = imgtupian.addComponent(cc.Sprite);
            imgtupian.y = -88;
            imgtupian.width = 300;
            imgtupian.height = 300;
            var urls = cc.HTTP.SERVERHTPT() + "/ewm.php?data=" + url;
            cc.loader.load({
              url: urls,
              type: "png"
            }, function(err, tex) {
              try {
                var spriteFrame = new cc.SpriteFrame(tex, cc.Rect(0, 0, tex.width, tex.height));
                sp.getComponent(cc.Sprite).spriteFrame = spriteFrame;
                imgtupian.width = 300;
                imgtupian.height = 300;
              } catch (err) {}
            });
          }
          erweimabeiji && (erweimabeiji.active = true);
          imgtupian.active = true;
          this._quxiaozhi = function() {
            imgtupian.active = false;
            erweimabeiji && (erweimabeiji.active = false);
          };
        }, .1);
      },
      show: function show(content, an, queren, quxiao) {
        this.scheduleOnce(function() {
          if (!this.chushi()) return false;
          an || (an = 2);
          cc.find("alert", THIS.MSGBOXNODE).active = true;
          cc.find("alert/anniuzu", this.MSGBOXNODE).active = true;
          if (3 == an) {
            cc.find("alert/anniuzu/queren", this.MSGBOXNODE).active = true;
            cc.find("alert/anniuzu/quxiao", this.MSGBOXNODE).active = true;
            queren && (this._queren = queren);
            quxiao && (this._quxiaozhi = quxiao);
          } else if (2 == an) {
            queren && (this._queren = queren);
            cc.find("alert/anniuzu/queren", this.MSGBOXNODE).active = true;
          } else if (1 == an) {
            cc.find("alert/anniuzu/quxiao", this.MSGBOXNODE).active = true;
            quxiao && (this._quxiaozhi = quxiao);
          }
          null == content && (content = "");
          cc.find("alert/wenzi", this.MSGBOXNODE).getComponent(cc.RichText).string = content;
        }, .1);
      },
      jiazai: function jiazai(wenzi, ttt) {
        this.scheduleOnce(function() {
          if (!this.chushi()) return false;
          this.kedian = false;
          cc.find("alert", THIS.MSGBOXNODE).active = true;
          cc.find("alert/wenzi", this.MSGBOXNODE).y = 40;
          cc.find("alert/loading", this.MSGBOXNODE).active = true;
          wenzi ? cc.find("alert/wenzi", this.MSGBOXNODE).getComponent(cc.RichText).string = wenzi : cc.find("alert/loading", this.MSGBOXNODE).y = -5;
          this.scheduleOnce(function() {
            if (!this.kedian) {
              cc.find("alert/wenzi", this.MSGBOXNODE).getComponent(cc.RichText).string = "操作失败";
              cc.find("alert/loading", this.MSGBOXNODE).active = false;
              cc.find("alert/anniuzu", this.MSGBOXNODE).active = true;
              cc.find("alert/anniuzu/queren", this.MSGBOXNODE).active = true;
              this.kedian = true;
            }
          }, ttt || 30);
        }, .1);
      },
      loading: function loading() {
        this.scheduleOnce(function() {
          THIS.MSGBOXNODE.active = true;
          cc.find("alert/loading", THIS.MSGBOXNODE).active = true;
          cc.find("Canvas/Msgbox/alert/wenzi").getComponent(cc.RichText).string = "加载中...";
          cc.find("Canvas/Msgbox/alert/anniuzu/queren").active = false;
          cc.find("Canvas/Msgbox/alert/anniuzu/quxiao").active = false;
        }, .1);
      },
      hide: function hide() {
        if (!this.kedian) return;
        THIS.MSGBOXNODE && (THIS.MSGBOXNODE.active = false);
      },
      start: function start() {}
    });
    cc._RF.pop();
  }, {} ],
  dating: [ function(require, module, exports) {
    "use strict";
    cc._RF.push(module, "83f5cmCQKVJSLCyA46u/n85", "dating");
    "use strict";
    var a = 1;
    var b = 1;
    var THIS = "";
    cc.Class({
      extends: cc.Component,
      properties: {
        qunzhucyuan: cc.Prefab,
        kaifangjilu: cc.Prefab,
        jilu: cc.Prefab
      },
      onLoad: function onLoad() {
        cc.MAN = THIS = this;
        cc.SY = this.addComponent("shengying");
        cc.anniu = function(NODE, WENJIAN, HANSHU, ZHI) {
          if (NODE instanceof Array) for (var ms = 0; ms < NODE.length; ms++) {
            if (!NODE[ms]) continue;
            var clickEventHandler = new cc.Component.EventHandler();
            clickEventHandler.target = cc.find("Canvas");
            clickEventHandler.component = WENJIAN;
            clickEventHandler.handler = HANSHU;
            ZHI && (ZHI instanceof Array ? ZHI[ms] && (clickEventHandler.customEventData = ZHI[ms]) : clickEventHandler.customEventData = ZHI);
            NODE[ms].getComponent(cc.Button) && (NODE[ms].getComponent(cc.Button).clickEvents[0] = clickEventHandler);
            cc.Toggle && NODE[ms].getComponent(cc.Toggle) && (NODE[ms].getComponent(cc.Toggle).clickEvents[0] = clickEventHandler);
          } else {
            if (!NODE) return;
            var clickEventHandler = new cc.Component.EventHandler();
            clickEventHandler.target = cc.find("Canvas");
            clickEventHandler.component = WENJIAN;
            clickEventHandler.handler = HANSHU;
            ZHI && (clickEventHandler.customEventData = ZHI);
            NODE.getComponent(cc.Button) && (NODE.getComponent(cc.Button).clickEvents[0] = clickEventHandler);
            cc.Toggle && NODE.getComponent(cc.Toggle) && (NODE.getComponent(cc.Toggle).clickEvents[0] = clickEventHandler);
          }
        };
        cc.pichttp = function(duixiang, urls, width, height) {
          cc.loader.load(urls, function(err, tex) {
            try {
              var spriteFrame = new cc.SpriteFrame(tex, cc.Rect(0, 0, tex.width, tex.height));
              duixiang.getComponent(cc.Sprite).spriteFrame = spriteFrame;
              "undefined" != typeof width && (duixiang.width = width);
              "undefined" != typeof height && (duixiang.height = height);
            } catch (err) {}
          });
        };
        cc.get = function(data) {
          var str = "?";
          for (var k in data) {
            "?" != str && (str += "&");
            str += k + "=" + data[k];
          }
          str = str.replace(/(^\?*)/g, "");
          return str;
        };
        cc.wenzi = function(node, value) {
          if ("undefined" == typeof value) return node.getComponent(cc.Label).string;
          node.getComponent(cc.Label).string = value;
        };
        cc.tupian = function(node, resoures) {
          if ("undefined" == typeof resoures) return node.getComponent(cc.Sprite).spriteFrame;
          node.getComponent(cc.Sprite).spriteFrame = resoures;
        };
        cc.Msgbox = this.addComponent("Msgbox");
        cc.SY.yingyuepurl("/sounds/bg");
        cc.ZYONLINE = this.addComponent("online");
        this.bangding();
        this.fangjianhao = "";
        cc.http = require("http");
        cc.SDKAPI = this.addComponent("sdkapi");
        cc.User = {
          apptoken: ""
        };
        var temData = cc.sys.localStorage.getItem("NEWuserData");
        if (null !== temData && "" != temData) {
          temData = JSON.parse(temData);
          null !== temData && "" != temData && (cc.User = temData);
        }
        this.loginin();
        var temData1 = cc.sys.localStorage.getItem("gdtjoingame1");
        if (null !== temData1 && "" != temData1) {
          temData1 = JSON.parse(temData1);
          if (null !== temData1 && "" != temData1) {
            this.AutoJoinGame(temData1.roomid, "gdtuo");
            cc.sys.localStorage.removeItem("gdtjoingame1");
          }
        }
        this.fangjianhao = "";
        this.CreateRoomData = {
          gid: "gdtuo",
          gametype: 1,
          difenn: 1,
          wanfa: 2,
          shangzhuang: 1e3,
          difen: 1,
          jushu: 10,
          renshu: 8,
          joinlimit: 100,
          qzcm: 1
        };
      },
      WSSHOUDATA: function WSSHOUDATA(Msg) {
        if (!cc.ZYONLINE._YSEGAME) return;
        Msg.y;
      },
      start: function start() {},
      bangding: function bangding() {
        cc.anniu(cc.find("Canvas/beijing/top/touxiangdikuang/fangka/jia"), "dating", "shangcehng");
        cc.anniu(cc.find("Canvas/beijing/dibu/shangcheng"), "dating", "shangcehng");
        cc.anniu(cc.find("Canvas/shangcheng/sc/btn_goumai/an1"), "dating", "fangkagm", "4");
        cc.anniu(cc.find("Canvas/shangcheng/sc/btn_goumai/an2"), "dating", "fangkagm", "20");
        cc.anniu(cc.find("Canvas/shangcheng/sc/btn_goumai/an3"), "dating", "fangkagm", "400");
        cc.anniu(cc.find("Canvas/shangcheng/sc/guanbi"), "dating", "shangcgb");
        cc.anniu(cc.find("Canvas/beijing/dibu/zhanji"), "dating", "zhanji");
        cc.anniu(cc.find("Canvas/zhanji/guanbi"), "dating", "guanbizj");
        cc.anniu(cc.find("Canvas/beijing/julebu"), "dating", "julebu");
        cc.anniu(cc.find("Canvas/julebu/jilebu/top/guanbi"), "dating", "guanbijlb");
        cc.anniu(cc.find("Canvas/beijing/dibu/guize"), "dating", "guizhe1");
        cc.anniu(cc.find("Canvas/guizhe/kuang/guanbi"), "dating", "quxiaogz");
        cc.anniu(cc.find("Canvas/beijing/chuangjianfangjian"), "dating", "chuanjianfj");
        cc.anniu(cc.find("Canvas/chuangjfj/chuangjianfangjian/guynabi"), "dating", "guanbicjfj");
        cc.anniu(cc.find("Canvas/beijing/jiarufangjian"), "dating", "jiarufjian");
        cc.anniu(cc.find("Canvas/JoinGamePanel/guanbi"), "dating", "guanbijrfjian");
        cc.anniu(cc.find("Canvas/shezhi/kuang/kaiyy/kai"), "dating", "off_sy");
        cc.anniu(cc.find("Canvas/shezhi/kuang/guanyy/guan"), "dating", "on_sy");
        cc.anniu(cc.find("Canvas/shezhi/kuang/kaiyx/kai"), "dating", "off_yx");
        cc.anniu(cc.find("Canvas/shezhi/kuang/guanyx/guan"), "dating", "on_yx");
        cc.anniu(cc.find("Canvas/shezhi/kuang/guanbi"), "dating", "off_shezhi");
        cc.anniu(cc.find("Canvas/beijing/top/shezhi"), "dating", "on_shezhi");
        cc.anniu(cc.find("Canvas/chuangjfj/chuangjianfangjian/ptms/button"), "dating", "putmshi");
        cc.anniu(cc.find("Canvas/chuangjfj/chuangjianfangjian/gds/button"), "dating", "gudingms");
        cc.anniu(cc.find("Canvas/chuangjfj/chuangjianfangjian/zyqz/button"), "dating", "ziyqzhuag");
        cc.anniu(cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi1/putongmoshi/qundinganniu"), "dating", "CreateRoom", "1");
        cc.anniu(cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi2/putongmoshi/qundinganniu"), "dating", "CreateRoom", "2");
        cc.anniu(cc.find("Canvas/julebu/jilebu/buju/kfcx/kaifcx"), "dating", "kaifangchaxun");
        cc.anniu(cc.find("Canvas/julebu/jilebu/buju/qzgl/guanbi/qunzhuguanli"), "dating", "qunzhuguanli");
        cc.anniu(cc.find("Canvas/julebu/jilebu/buju/yqcyqzcy/qzcy/qunzcy"), "dating", "qunzhucy");
        cc.anniu(cc.find("Canvas/julebu/jilebu/buju/yqcyqzcy/yqcy/yaoqcy"), "dating", "yaoqingcy");
        cc.anniu(cc.find("Canvas/qunzcy/bj/quxaio"), "dating", "quxiaoqzcy");
        cc.anniu(cc.find("Canvas/qunzcy/bj/queding"), "dating", "chaxuncylb");
        cc.anniu(cc.find("Canvas/kaifangjilu/guanbi"), "dating", "guanbikfcx");
        cc.anniu(cc.find("Canvas/julebu/jilebu/buju/qzgl/kaiqi/qunzhuguanli"), "dating", "quxiaoqxgl");
        cc.anniu(cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi3/putongmoshi/qundinganniu"), "dating", "CreateRoom", "3");
        cc.anniu([ cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi1/putongmoshi/difen/ToggleGroup/toggle1"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi1/putongmoshi/difen/ToggleGroup/toggle2"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi1/putongmoshi/difen/ToggleGroup/toggle3"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi1/putongmoshi/difen/ToggleGroup/toggle4"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi1/putongmoshi/difen/ToggleGroup/toggle5"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi1/putongmoshi/wanfa/ToggleGroup/toggle1"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi1/putongmoshi/wanfa/ToggleGroup/toggle2"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi1/putongmoshi/shangzhuang/ToggleGroup/toggle1"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi1/putongmoshi/shangzhuang/ToggleGroup/toggle2"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi1/putongmoshi/shangzhuang/ToggleGroup/toggle3"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi1/putongmoshi/shangzhuang/ToggleGroup/toggle4"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi1/putongmoshi/jisu/ToggleGroup/toggle1"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi1/putongmoshi/jisu/ToggleGroup/toggle2"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi1/putongmoshi/beishu/ToggleGroup/toggle1"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi1/putongmoshi/beishu/ToggleGroup/toggle2"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi1/putongmoshi/renshu/ToggleGroup/toggle1"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi1/putongmoshi/renshu/ToggleGroup/toggle2"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi1/putongmoshi/renshu/ToggleGroup/toggle3") ], "dating", "GetCreateRoomData", [ "difenn_1", "difenn_2", "difenn_3", "difenn_4", "difenn_5", "wanfa_2", "wanfa_1", "shangzhuang_1000", "shangzhuang_2000", "shangzhuang_3000", "shangzhuang_5000", "jushu_10", "jushu_20", "difen_1", "difen_2", "renshu_8", "renshu_11", "renshu_13" ]);
        cc.anniu([ cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi2/putongmoshi/difen/ToggleGroup/toggle1"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi2/putongmoshi/difen/ToggleGroup/toggle2"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi2/putongmoshi/difen/ToggleGroup/toggle3"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi2/putongmoshi/difen/ToggleGroup/toggle4"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi2/putongmoshi/difen/ToggleGroup/toggle5"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi2/putongmoshi/wanfa/ToggleGroup/toggle1"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi2/putongmoshi/wanfa/ToggleGroup/toggle2"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi2/putongmoshi/shangzhuang/ToggleGroup/toggle1"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi2/putongmoshi/shangzhuang/ToggleGroup/toggle2"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi2/putongmoshi/shangzhuang/ToggleGroup/toggle3"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi2/putongmoshi/shangzhuang/ToggleGroup/toggle4"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi2/putongmoshi/jisu/ToggleGroup/toggle1"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi2/putongmoshi/jisu/ToggleGroup/toggle2"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi2/putongmoshi/beishu/ToggleGroup/toggle1"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi2/putongmoshi/beishu/ToggleGroup/toggle2"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi2/putongmoshi/renshu/ToggleGroup/toggle1"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi2/putongmoshi/renshu/ToggleGroup/toggle2"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi2/putongmoshi/renshu/ToggleGroup/toggle3"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi2/putongmoshi/zunru/ToggleGroup/toggle1"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi2/putongmoshi/zunru/ToggleGroup/toggle2"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi2/putongmoshi/zunru/ToggleGroup/toggle3"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi2/putongmoshi/zunru/ToggleGroup/toggle4"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi2/putongmoshi/zunru/ToggleGroup/toggle5") ], "dating", "GetCreateRoomData", [ "difenn_1", "difenn_2", "difenn_3", "difenn_4", "difenn_5", "wanfa_2", "wanfa_1", "shangzhuang_1000", "shangzhuang_2000", "shangzhuang_3000", "shangzhuang_5000", "jushu_10", "jushu_20", "difen_1", "difen_2", "renshu_8", "renshu_11", "renshu_13", "joinlimit_100", "joinlimit_200", "joinlimit_300", "joinlimit_400", "joinlimit_500" ]);
        cc.anniu([ cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi3/putongmoshi/difen/ToggleGroup/toggle1"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi3/putongmoshi/difen/ToggleGroup/toggle2"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi3/putongmoshi/difen/ToggleGroup/toggle3"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi3/putongmoshi/difen/ToggleGroup/toggle4"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi3/putongmoshi/difen/ToggleGroup/toggle5"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi3/putongmoshi/wanfa/ToggleGroup/toggle1"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi3/putongmoshi/wanfa/ToggleGroup/toggle2"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi3/putongmoshi/shangzhuang/ToggleGroup/toggle1"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi3/putongmoshi/shangzhuang/ToggleGroup/toggle2"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi3/putongmoshi/shangzhuang/ToggleGroup/toggle3"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi3/putongmoshi/shangzhuang/ToggleGroup/toggle4"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi3/putongmoshi/jisu/ToggleGroup/toggle1"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi3/putongmoshi/jisu/ToggleGroup/toggle2"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi3/putongmoshi/beishu/ToggleGroup/toggle1"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi3/putongmoshi/beishu/ToggleGroup/toggle2"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi3/putongmoshi/renshu/ToggleGroup/toggle1"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi3/putongmoshi/renshu/ToggleGroup/toggle2"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi3/putongmoshi/renshu/ToggleGroup/toggle3"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi3/putongmoshi/zunru/ToggleGroup/toggle1"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi3/putongmoshi/zunru/ToggleGroup/toggle2"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi3/putongmoshi/zunru/ToggleGroup/toggle3"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi3/putongmoshi/zunru/ToggleGroup/toggle4"), cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi3/putongmoshi/zunru/ToggleGroup/toggle5") ], "dating", "GetCreateRoomData", [ "difenn_1", "difenn_2", "difenn_3", "difenn_4", "difenn_5", "wanfa_2", "wanfa_1", "shangzhuang_1000", "shangzhuang_2000", "shangzhuang_3000", "shangzhuang_5000", "jushu_10", "jushu_20", "difen_1", "difen_2", "renshu_8", "renshu_11", "renshu_13", "joinlimit_100", "joinlimit_200", "joinlimit_300", "joinlimit_400", "joinlimit_500" ]);
        cc.anniu([ cc.find("Canvas/JoinGamePanel/NumberNode/1"), cc.find("Canvas/JoinGamePanel/NumberNode/2"), cc.find("Canvas/JoinGamePanel/NumberNode/3"), cc.find("Canvas/JoinGamePanel/NumberNode/4"), cc.find("Canvas/JoinGamePanel/NumberNode/5"), cc.find("Canvas/JoinGamePanel/NumberNode/6"), cc.find("Canvas/JoinGamePanel/NumberNode/7"), cc.find("Canvas/JoinGamePanel/NumberNode/8"), cc.find("Canvas/JoinGamePanel/NumberNode/9"), cc.find("Canvas/JoinGamePanel/NumberNode/0") ], "dating", "SelectNum", [ "1", "2", "3", "4", "5", "6", "7", "8", "9", "0" ]);
        cc.anniu(cc.find("Canvas/JoinGamePanel/NumberNode/10"), "dating", "QingKong");
        cc.anniu(cc.find("Canvas/JoinGamePanel/NumberNode/12"), "dating", "DeleteNum");
        cc.anniu(cc.find("Canvas/beijing/dibu/xiaoxi"), "dating", "xiaoxianniu");
        cc.anniu(cc.find("Canvas/xiaoxi/tanck/tancnk/14"), "dating", "quxiaoxxan");
        cc.anniu(cc.find("Canvas/beijing/dibu/hall_new_36"), "dating", "fankui");
        cc.anniu(cc.find("Canvas/fankui/tanck/tancnk/14"), "dating", "quxiaofk");
      },
      fankui: function fankui() {
        cc.find("Canvas/fankui").active = true;
        cc.wenzi(cc.find("Canvas/fankui/tanck/tancnk/label"), "请联系微信客服：" + cc.User.KeFu);
      },
      quxiaofk: function quxiaofk() {
        cc.find("Canvas/fankui").active = false;
      },
      xiaoxianniu: function xiaoxianniu() {
        cc.find("Canvas/xiaoxi").active = true;
      },
      quxiaoxxan: function quxiaoxxan() {
        cc.find("Canvas/xiaoxi").active = false;
      },
      DeleteNum: function DeleteNum() {
        cc.SY.yingxiaopurl("sounds/button");
        this.fangjianhao = this.fangjianhao.substr(0, this.fangjianhao.length - 1);
        cc.wenzi(cc.find("Canvas/JoinGamePanel/joinbg/" + (this.fangjianhao.length + 1)), "");
        "" != this.fangjianhao && null != this.fangjianhao || (cc.find("Canvas/JoinGamePanel/joinbg/srfjh").active = true);
      },
      QingKong: function QingKong() {
        cc.SY.yingxiaopurl("sounds/button");
        this.fangjianhao = "";
        for (var index = 1; index <= 6; index++) cc.find("Canvas/JoinGamePanel/joinbg/" + index).getComponent(cc.Label).string = "";
        cc.find("Canvas/JoinGamePanel/joinbg/srfjh").active = true;
      },
      SelectNum: function SelectNum(lx, num) {
        cc.find("Canvas/JoinGamePanel/joinbg/srfjh").active = false;
        for (var index = 1; index <= 6; index++) if ("" == cc.find("Canvas/JoinGamePanel/joinbg/" + index).getComponent(cc.Label).string) {
          cc.find("Canvas/JoinGamePanel/joinbg/" + index).getComponent(cc.Label).string = num;
          this.fangjianhao += num;
          6 == index && cc.MAN.JoinTheRoom();
          return;
        }
      },
      JoinTheRoom: function JoinTheRoom() {
        var myjson = {
          gid: "gdtuo",
          fid: this.fangjianhao,
          y: "index",
          d: "put",
          apptoken: cc.User.apptoken
        };
        cc.http.URL("J", myjson, function(data, status) {
          var p_data = JSON.parse(data);
          if (200 == status) {
            cc.GamesFangJianData = p_data;
            cc.Msgbox.loading();
            cc.director.preloadScene("game", function(v) {
              cc.director.loadScene("game");
            });
          } else 415 == status && (-3 == p_data.code ? cc.Msgbox.show("加入房间失败") : cc.Msgbox.show(p_data.msg));
        });
      },
      AutoJoinGame: function AutoJoinGame(roomId, gameID) {
        var myjson = {
          gid: gameID,
          fid: roomId,
          y: "index",
          d: "put",
          apptoken: cc.User.apptoken
        };
        cc.http.URL("J", myjson, function(data, status) {
          var p_data = JSON.parse(data);
          if (200 == status) {
            cc.GamesFangJianData = p_data;
            cc.Msgbox.loading();
            cc.director.preloadScene("game", function(v) {
              cc.director.loadScene("game");
            });
            return;
          }
          415 == status && (-3 == p_data.code ? cc.Msgbox.show("加入房间失败") : cc.Msgbox.show(p_data.msg));
        });
      },
      initUser: function initUser(data) {
        if (data.name.length > 8) {
          cc.wenzi(cc.find("Canvas/beijing/top/touxiangdikuang/21/name"), "昵称：" + data.name.substring(0, 7) + "..");
          cc.wenzi(cc.find("Canvas/julebu/jilebu/top/touxiangdikuang/name"), data.name.substring(0, 7) + "..");
        } else {
          cc.wenzi(cc.find("Canvas/beijing/top/touxiangdikuang/21/name"), "昵称：" + data.name);
          cc.wenzi(cc.find("Canvas/julebu/jilebu/top/touxiangdikuang/name"), data.name);
        }
        cc.wenzi(cc.find("Canvas/beijing/top/touxiangdikuang/id/name"), "ID：" + data.uid);
        cc.wenzi(cc.find("Canvas/julebu/jilebu/top/touxiangdikuang/id/label"), data.uid);
        cc.wenzi(cc.find("Canvas/beijing/top/touxiangdikuang/fangka/label"), data.jifen);
        cc.wenzi(cc.find("Canvas/beijing/top/touxiangdikuang/jinbi/label"), data.huobi);
        cc.wenzi(cc.find("Canvas/julebu/jilebu/top/touxiangdikuang/fangka/fangkakuang/label"), data.jifen);
        cc.pichttp(cc.find("Canvas/beijing/top/touxiangdikuang/txk"), data.touxiang, 73, 73);
        cc.pichttp(cc.find("Canvas/julebu/jilebu/top/touxiangdikuang/touxiang"), data.touxiang, 73, 73);
        data.gamefid && this.AutoJoinGame(data.gamefid, data.gamegid);
      },
      GetCreateRoomData: function GetCreateRoomData(lx, shuju) {
        cc.SY.yingxiaopurl("sounds/button");
        var CS = shuju.split("_");
        this.CreateRoomData[CS[0]] = CS[1];
      },
      shangcehng: function shangcehng() {
        cc.SY.yingxiaopurl("sounds/button");
        cc.find("Canvas/shangcheng").active = true;
      },
      shangcgb: function shangcgb() {
        cc.SY.yingxiaopurl("sounds/button");
        cc.find("Canvas/shangcheng").active = false;
      },
      quxiaoqzcy: function quxiaoqzcy() {
        cc.find("Canvas/qunzcy").active = false;
      },
      guizhe1: function guizhe1() {
        cc.SY.yingxiaopurl("sounds/button");
        cc.find("Canvas/guizhe").active = true;
      },
      quxiaogz: function quxiaogz() {
        cc.SY.yingxiaopurl("sounds/button");
        cc.find("Canvas/guizhe").active = false;
      },
      julebu: function julebu() {
        cc.SY.yingxiaopurl("sounds/button");
        if (1 == cc.User.isqunzhu) {
          cc.find("Canvas/julebu/jilebu/buju/yqcyqzcy").active = true;
          cc.find("Canvas/julebu/jilebu/buju/qzgl/kaiqi").active = true;
          cc.find("Canvas/julebu/jilebu/buju/qzgl/guanbi").active = false;
        }
        cc.pichttp(cc.find("Canvas/julebu/jilebu/top/touxiangdikuang/touxiang"), cc.User.touxiang, 73, 73);
        cc.find("Canvas/julebu").active = true;
      },
      kaifangchaxun: function kaifangchaxun() {
        var myjson = {
          y: "zhanji",
          d: "post",
          apptoken: cc.User.apptoken
        };
        cc.http.URL("J", myjson, function(data, status) {
          var DATA = JSON.parse(data);
          cc.find("Canvas/kaifangjilu/kfjltc/scrollview/view/content").removeAllChildren();
          cc.find("Canvas/kaifangjilu/kfjltc/scrollview/view/content").height = 88 * DATA.data.length;
          for (var key in DATA.data) {
            var node = cc.instantiate(THIS.kaifangjilu);
            node.parent = cc.find("Canvas/kaifangjilu/kfjltc/scrollview/view/content");
            node.group = "defult";
            node.name = "yuzhiti" + key;
            cc.wenzi(cc.find("Canvas/kaifangjilu/kfjltc/scrollview/view/content/yuzhiti" + key + "/fangjianhao"), DATA.data[key].fangid);
            cc.wenzi(cc.find("Canvas/kaifangjilu/kfjltc/scrollview/view/content/yuzhiti" + key + "/fangjzt"), DATA.data[key].off);
            cc.wenzi(cc.find("Canvas/kaifangjilu/kfjltc/scrollview/view/content/yuzhiti" + key + "/chuanjsj"), DATA.data[key].atime);
            if ("正常" == DATA.data[key].off) {
              cc.anniu(cc.find("Canvas/kaifangjilu/kfjltc/scrollview/view/content/yuzhiti" + key + "/jiesanfangjian"), "dating", "jieshanfj", DATA.data[key].fangid);
              cc.find("Canvas/kaifangjilu/kfjltc/scrollview/view/content/yuzhiti" + key + "/jiesanfangjian").active = true;
            }
          }
        });
        cc.find("Canvas/kaifangjilu").active = true;
      },
      guanbikfcx: function guanbikfcx() {
        cc.find("Canvas/kaifangjilu").active = false;
      },
      jieshanfj: function jieshanfj(lx, data) {
        cc.SY.yingxiaopurl("sounds/button");
        var myjson = {
          y: "zhanji",
          d: "put",
          roomid: data,
          apptoken: cc.User.apptoken
        };
        cc.http.URL("J", myjson, function(data, status) {
          var DATA = JSON.parse(data);
          if (1 == DATA.code) {
            cc.Msgbox.show(DATA.msg);
            THIS.kaifangchaxun();
          } else cc.Msgbox.show(DATA.msg);
        });
      },
      quxiaotishi: function quxiaotishi() {
        cc.SY.yingxiaopurl("sounds/button");
        cc.find("Canvas/julebu/tishi").active = false;
      },
      kaitongqunzhu: function kaitongqunzhu() {
        cc.SY.yingxiaopurl("sounds/button");
        var myjson = {
          y: "qunzhu",
          d: "post",
          apptoken: cc.User.apptoken
        };
        cc.http.URL("J", myjson, function(data, status) {
          var DATA = JSON.parse(data);
          if (1 == DATA.code) {
            cc.Msgbox.show(DATA.msg);
            cc.User.isqunzhu = 1;
            cc.find("Canvas/julebu/tishi").active = false;
            if (DATA) {
              cc.wenzi(cc.find("Canvas/beijing/top/touxiangdikuang/fangka/label"), DATA.data);
              cc.wenzi(cc.find("Canvas/julebu/jilebu/top/touxiangdikuang/fangka/fangkakuang/label"), DATA.data);
            }
          } else cc.Msgbox.show(DATA.msg);
        });
      },
      qunzhuguanli: function qunzhuguanli() {
        if (1 == cc.User.isqunzhu) {
          cc.find("Canvas/julebu/jilebu/buju/yqcyqzcy").active = true;
          cc.find("Canvas/julebu/jilebu/buju/qzgl/kaiqi").active = true;
          cc.find("Canvas/julebu/jilebu/buju/qzgl/guanbi").active = false;
        } else if (0 == cc.User.isqunzhu) {
          cc.find("Canvas/julebu/tishi").active = true;
          cc.anniu(cc.find("Canvas/julebu/tishi/tishi/queding"), "dating", "kaitongqunzhu");
          cc.anniu(cc.find("Canvas/julebu/tishi/tishi/quxiao"), "dating", "quxiaotishi");
          cc.anniu(cc.find("Canvas/julebu/tishi/tishi/gunabi"), "dating", "quxiaotishi");
        }
      },
      quxiaoqxgl: function quxiaoqxgl() {
        cc.find("Canvas/julebu/jilebu/buju/yqcyqzcy").active = false;
        cc.find("Canvas/julebu/jilebu/buju/qzgl/kaiqi").active = false;
        cc.find("Canvas/julebu/jilebu/buju/qzgl/guanbi").active = true;
      },
      qunzhucy: function qunzhucy() {
        var myjson = {
          y: "qunzhu",
          d: "get",
          apptoken: cc.User.apptoken
        };
        cc.http.URL("J", myjson, function(data, status) {
          var DATA = JSON.parse(data);
          cc.find("Canvas/qunzcy/bj/scrollview/view/content").removeAllChildren();
          if (-1 == DATA.code) cc.Msgbox.show(JSON.parse(data).msg); else if (1 == DATA.code) {
            cc.find("Canvas/qunzcy").active = true;
            cc.find("Canvas/qunzcy/bj/scrollview/view/content").height = 132 * DATA.data.length;
            for (var key in DATA.data) {
              var node = cc.instantiate(THIS.qunzhucyuan);
              node.parent = cc.find("Canvas/qunzcy/bj/scrollview/view/content");
              node.group = "defult";
              node.name = "yuzhiti" + key;
              if (0 == DATA.data[key].request) {
                cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + key + "/tongyi").active = false;
                cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + key + "/jujue").active = false;
                cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + key + "/yichu").active = true;
              }
              cc.pichttp(cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + key + "/touxk/210"), DATA.data[key].touxiang, 86, 86);
              DATA.data[key].name.length > 5 ? cc.wenzi(cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + key + "/xinxi/name"), DATA.data[key].name.substring(0, 4) + "..") : cc.wenzi(cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + key + "/xinxi/name"), DATA.data[key].name);
              cc.wenzi(cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + key + "/xinxi/id"), DATA.data[key].uid);
              cc.wenzi(cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + key + "/xinxi/jifen"), "房卡" + DATA.data[key].jifen);
              cc.wenzi(cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + key + "/xinxi/jinbi"), "金币" + DATA.data[key].huobi);
              cc.anniu(cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + key + "/tongyi"), "dating", "tongyitj", DATA.data[key].uid);
              cc.anniu(cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + key + "/jujue"), "dating", "jujuetj", DATA.data[key].uid);
              cc.anniu(cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + key + "/shangfen"), "dating", "shangfen", {
                uid: DATA.data[key].uid,
                key: key
              });
              cc.anniu(cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + key + "/xiafeng"), "dating", "xiafeng", {
                uid: DATA.data[key].uid,
                key: key
              });
              cc.anniu(cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + key + "/yichu"), "dating", "yichucy", DATA.data[key].uid);
            }
          }
        });
      },
      yaoqingcy: function yaoqingcy() {
        cc.Msgbox.show("点击右上角分享到微信");
        if (window.$FENXIN) {
          window.$FENXIN["1"] = cc.http.SERVERHTPT() + "?tuid=" + cc.User.uid + "&qunid=" + cc.User.uid;
          window.$FENXIN["0"] = "邀请函";
          window.$FENXIN["3"] = "快来和我一起玩~";
          window.dandan();
        }
      },
      chaxuncylb: function chaxuncylb() {
        var uid = cc.find("Canvas/qunzcy/bj/editbox").getComponent(cc.EditBox).string;
        var myjson = {
          y: "qunzhu",
          d: "get",
          apptoken: cc.User.apptoken,
          name: uid
        };
        cc.http.URL("J", myjson, function(data, status) {
          var DATA = JSON.parse(data);
          if (-1 == DATA.code) cc.Msgbox.show(JSON.parse(data).msg); else if (1 == DATA.code) {
            cc.find("Canvas/qunzcy").active = true;
            cc.find("Canvas/qunzcy/bj/scrollview/view/content").removeAllChildren();
            if ("" == uid) {
              cc.find("Canvas/qunzcy/bj/scrollview/view/content").height = 132 * DATA.data.length;
              for (var key in DATA.data) {
                var node = cc.instantiate(THIS.qunzhucyuan);
                node.parent = cc.find("Canvas/qunzcy/bj/scrollview/view/content");
                node.group = "defult";
                node.name = "yuzhiti" + key;
                if (0 == DATA.data[key].request) {
                  cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + key + "/tongyi").active = false;
                  cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + key + "/jujue").active = false;
                  cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + key + "/yichu").active = true;
                }
                cc.pichttp(cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + key + "/touxk/210"), DATA.data[key].touxiang, 86, 86);
                DATA.data[key].name.length > 5 ? cc.wenzi(cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + key + "/xinxi/name"), DATA.data[key].name.substring(0, 4) + "..") : cc.wenzi(cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + key + "/xinxi/name"), DATA.data[key].name);
                cc.wenzi(cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + key + "/xinxi/id"), DATA.data[key].uid);
                cc.wenzi(cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + key + "/xinxi/jifen"), "房卡" + DATA.data[key].jifen);
                cc.wenzi(cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + key + "/xinxi/jinbi"), "金币" + DATA.data[key].huobi);
                cc.anniu(cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + key + "/tongyi"), "dating", "tongyitj", DATA.data[key].uid);
                cc.anniu(cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + key + "/jujue"), "dating", "jujuetj", DATA.data[key].uid);
                cc.anniu(cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + key + "/shangfen"), "dating", "shangfen", {
                  uid: DATA.data[key].uid,
                  key: key
                });
                cc.anniu(cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + key + "/xiafeng"), "dating", "xiafeng", {
                  uid: DATA.data[key].uid,
                  key: key
                });
                cc.anniu(cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + key + "/yichu"), "dating", "yichucy", DATA.data[key].uid);
              }
            } else {
              cc.find("Canvas/qunzcy/bj/scrollview/view/content").height = 132 * DATA.data.data.length;
              for (var _key in DATA.data.data) {
                var node = cc.instantiate(THIS.qunzhucyuan);
                node.parent = cc.find("Canvas/qunzcy/bj/scrollview/view/content");
                node.group = "defult";
                node.name = "yuzhiti" + _key;
                if (0 == DATA.data.data[_key].request) {
                  cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + _key + "/tongyi").active = false;
                  cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + _key + "/jujue").active = false;
                  cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + _key + "/yichu").active = true;
                }
                cc.pichttp(cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + _key + "/touxk/210"), DATA.data.data[_key].touxiang, 86, 86);
                DATA.data[_key].name.length > 5 ? cc.wenzi(cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + _key + "/xinxi/name"), DATA.data[_key].name.substring(0, 4) + "..") : cc.wenzi(cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + _key + "/xinxi/name"), DATA.data[_key].name);
                cc.wenzi(cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + _key + "/xinxi/id"), DATA.data.data[_key].uid);
                cc.wenzi(cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + _key + "/xinxi/jifen"), "房卡" + DATA.data.data[_key].jifen);
                cc.wenzi(cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + _key + "/xinxi/jinbi"), "金币" + DATA.data.data[_key].huobi);
                cc.anniu(cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + _key + "/tongyi"), "dating", "tongyitj", DATA.data.data[_key].uid);
                cc.anniu(cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + _key + "/jujue"), "dating", "jujuetj", DATA.data.data[_key].uid);
                cc.anniu(cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + _key + "/shangfen"), "dating", "shangfen", {
                  uid: DATA.data.data[_key].uid,
                  key: _key
                });
                cc.anniu(cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + _key + "/xiafeng"), "dating", "xiafeng", {
                  uid: DATA.data.data[_key].uid,
                  key: _key
                });
              }
            }
          }
        });
      },
      guanbijlb: function guanbijlb() {
        cc.SY.yingxiaopurl("sounds/button");
        cc.find("Canvas/julebu").active = false;
      },
      yichucy: function yichucy(data1, data) {
        var myjson = {
          y: "zhanji",
          d: "delete",
          removetuid: data,
          apptoken: cc.User.apptoken
        };
        cc.http.URL("J", myjson, function(data, status) {
          var DATA = JSON.parse(data);
          if (1 == DATA.code) {
            cc.Msgbox.show(DATA.msg);
            THIS.qunzhucy();
          } else cc.Msgbox.show(DATA.msg);
        });
      },
      tongyitj: function tongyitj(lx, uid) {
        cc.SY.yingxiaopurl("sounds/button");
        var myjson = {
          y: "qunzhu",
          d: "delete",
          type: 1,
          requestuid: uid,
          apptoken: cc.User.apptoken
        };
        cc.http.URL("J", myjson, function(data, status) {
          var DATA = JSON.parse(data);
          if (1 == DATA.code) {
            cc.Msgbox.show(DATA.msg);
            THIS.qunzhucy();
          } else cc.Msgbox.show(DATA.msg);
        });
      },
      jujuetj: function jujuetj(lx, uid) {
        cc.SY.yingxiaopurl("sounds/button");
        var myjson = {
          y: "qunzhu",
          d: "delete",
          type: 2,
          requestuid: uid,
          apptoken: cc.User.apptoken
        };
        cc.http.URL("J", myjson, function(data, status) {
          var DATA = JSON.parse(data);
          if (1 == DATA.code) {
            cc.Msgbox.show(DATA.msg);
            THIS.qunzhucy();
          } else cc.Msgbox.show(DATA.msg);
        });
      },
      shangfen: function shangfen(lx, data) {
        cc.SY.yingxiaopurl("sounds/button");
        var key = data.key;
        if (parseInt(cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + key + "/xinxi/editboxjb").getComponent(cc.EditBox).string) > 0 && "" == cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + key + "/xinxi/editboxjf").getComponent(cc.EditBox).string) {
          var type = 2;
          var jine = parseInt(cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + key + "/xinxi/editboxjb").getComponent(cc.EditBox).string);
        } else if (parseInt(cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + key + "/xinxi/editboxjf").getComponent(cc.EditBox).string) > 0 && "" == cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + key + "/xinxi/editboxjb").getComponent(cc.EditBox).string) {
          var type = 1;
          var jine = parseInt(cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + key + "/xinxi/editboxjf").getComponent(cc.EditBox).string);
        } else var type = 0;
        if (0 == type) {
          cc.Msgbox.show("操作失败！");
          return;
        }
        var myjson = {
          y: "qunzhu",
          d: "put",
          uid: data.uid,
          jine: jine,
          type: type,
          apptoken: cc.User.apptoken
        };
        cc.http.URL("J", myjson, function(data, status) {
          var DATA = JSON.parse(data);
          if (1 == DATA.code) {
            cc.Msgbox.show(DATA.msg);
            cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + key + "/xinxi/editboxjb").getComponent(cc.EditBox).string = "";
            cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + key + "/xinxi/editboxjf").getComponent(cc.EditBox).string = "";
            cc.wenzi(cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + key + "/xinxi/jinbi"), "金币" + DATA.data.huobi);
            cc.wenzi(cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + key + "/xinxi/jifen"), "房卡" + DATA.data.jifen);
          } else cc.Msgbox.show(DATA.msg);
        });
      },
      xiafeng: function xiafeng(lx, data) {
        cc.SY.yingxiaopurl("sounds/button");
        var key = data.key;
        if (parseInt(cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + key + "/xinxi/editboxjb").getComponent(cc.EditBox).string) > 0 && "" == cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + key + "/xinxi/editboxjf").getComponent(cc.EditBox).string) {
          var type = 2;
          var jine = parseInt(cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + key + "/xinxi/editboxjb").getComponent(cc.EditBox).string);
        } else if (parseInt(cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + key + "/xinxi/editboxjf").getComponent(cc.EditBox).string) > 0 && "" == cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + key + "/xinxi/editboxjb").getComponent(cc.EditBox).string) {
          var type = 1;
          var jine = parseInt(cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + key + "/xinxi/editboxjf").getComponent(cc.EditBox).string);
        } else var type = 0;
        if (0 == type) {
          cc.Msgbox.show("操作失败！");
          return;
        }
        var myjson = {
          y: "qunzhu",
          d: "put",
          uid: data.uid,
          jine: -jine,
          type: type,
          apptoken: cc.User.apptoken
        };
        cc.http.URL("J", myjson, function(data, status) {
          var DATA = JSON.parse(data);
          if (1 == DATA.code) {
            cc.Msgbox.show(DATA.msg);
            cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + key + "/xinxi/editboxjb").getComponent(cc.EditBox).string = "";
            cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + key + "/xinxi/editboxjf").getComponent(cc.EditBox).string = "";
            cc.wenzi(cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + key + "/xinxi/jinbi"), "金币" + DATA.data.huobi);
            cc.wenzi(cc.find("Canvas/qunzcy/bj/scrollview/view/content/yuzhiti" + key + "/xinxi/jifen"), "房卡" + DATA.data.jifen);
          } else cc.Msgbox.show(DATA.msg);
        });
      },
      zhanji: function zhanji() {
        cc.SY.yingxiaopurl("sounds/button");
        var myjson = {
          y: "zhanji",
          d: "get",
          apptoken: cc.User.apptoken
        };
        cc.http.URL("J", myjson, function(data, status) {
          var DATA = JSON.parse(data);
          if (-1 == DATA.code) cc.Msgbox.show(DATA.msg); else {
            cc.find("Canvas/zhanji/scrollview/view/content").removeAllChildren();
            cc.find("Canvas/zhanji/scrollview/view/content").height = 180 * DATA.data.length;
            for (var key in DATA.data) {
              var node = cc.instantiate(THIS.jilu);
              node.parent = cc.find("Canvas/zhanji/scrollview/view/content");
              node.group = "defult";
              node.name = "yuzhiti" + key;
              cc.wenzi(cc.find("Canvas/zhanji/scrollview/view/content/yuzhiti" + key + "/bjingk/shuzi"), parseInt(key) + 1);
              cc.wenzi(cc.find("Canvas/zhanji/scrollview/view/content/yuzhiti" + key + "/bjingk/fjh/label"), DATA.data[key].fangid);
              cc.wenzi(cc.find("Canvas/zhanji/scrollview/view/content/yuzhiti" + key + "/bjingk/duizsj/label"), DATA.data[key].atime);
              var mm = DATA.data[key].neirong;
              var i = 1;
              for (var k in mm) {
                cc.find("Canvas/zhanji/scrollview/view/content/yuzhiti" + key + "/bjingk/jilu/" + i).active = true;
                mm[k].name.length > 6 ? mm[k].shuying.shuying > 0 ? cc.wenzi(cc.find("Canvas/zhanji/scrollview/view/content/yuzhiti" + key + "/bjingk/jilu/" + i + "/name"), "[+" + mm[k].shuying.shuying + "]" + mm[k].name.substring(0, 6) + "..") : cc.wenzi(cc.find("Canvas/zhanji/scrollview/view/content/yuzhiti" + key + "/bjingk/jilu/" + i + "/name"), "[" + mm[k].shuying.shuying + "]" + mm[k].name.substring(0, 6) + "..") : mm[k].shuying.shuying > 0 ? cc.wenzi(cc.find("Canvas/zhanji/scrollview/view/content/yuzhiti" + key + "/bjingk/jilu/" + i + "/name"), "[+" + mm[k].shuying.shuying + "]" + mm[k].name) : cc.wenzi(cc.find("Canvas/zhanji/scrollview/view/content/yuzhiti" + key + "/bjingk/jilu/" + i + "/name"), "[" + mm[k].shuying.shuying + "]" + mm[k].name);
                i++;
              }
            }
            cc.find("Canvas/zhanji").active = true;
          }
        });
      },
      guanbizj: function guanbizj() {
        cc.SY.yingxiaopurl("sounds/button");
        cc.find("Canvas/zhanji").active = false;
      },
      fangkagm: function fangkagm(lx, data) {
        cc.SY.yingxiaopurl("sounds/button");
        cc.Msgbox.show("购买房卡请联系客服：" + cc.User.KeFu);
      },
      guanbicjfj: function guanbicjfj() {
        cc.SY.yingxiaopurl("sounds/button");
        cc.find("Canvas/chuangjfj").active = false;
      },
      chuanjianfj: function chuanjianfj() {
        cc.SY.yingxiaopurl("sounds/button");
        cc.find("Canvas/chuangjfj").active = true;
      },
      jiarufjian: function jiarufjian() {
        cc.SY.yingxiaopurl("sounds/button");
        cc.find("Canvas/JoinGamePanel").active = true;
      },
      guanbijrfjian: function guanbijrfjian() {
        cc.SY.yingxiaopurl("sounds/button");
        this.fangjianhao = "";
        for (var index = 1; index <= 6; index++) cc.find("Canvas/JoinGamePanel/joinbg/" + index).getComponent(cc.Label).string = "";
        cc.find("Canvas/JoinGamePanel/joinbg/srfjh").active = true;
        cc.find("Canvas/JoinGamePanel").active = false;
      },
      on_shezhi: function on_shezhi() {
        cc.SY.yingxiaopurl("sounds/button");
        cc.find("Canvas/shezhi").active = true;
      },
      off_shezhi: function off_shezhi() {
        cc.SY.yingxiaopurl("sounds/button");
        cc.find("Canvas/shezhi").active = false;
      },
      on_sy: function on_sy() {
        cc.SY.yingxiaopurl("sounds/button");
        cc.find("Canvas/shezhi/kuang/guanyy").active = false;
        cc.find("Canvas/shezhi/kuang/kaiyy").active = true;
        cc.SY.setyingyue(1);
      },
      off_sy: function off_sy() {
        cc.SY.yingxiaopurl("sounds/button");
        cc.find("Canvas/shezhi/kuang/guanyy").active = true;
        cc.find("Canvas/shezhi/kuang/kaiyy").active = false;
        cc.SY.setyingyue(0);
      },
      on_yx: function on_yx() {
        cc.SY.yingxiaopurl("sounds/button");
        cc.find("Canvas/shezhi/kuang/guanyx").active = false;
        cc.find("Canvas/shezhi/kuang/kaiyx").active = true;
        cc.SY.setyingxiao(1);
      },
      off_yx: function off_yx() {
        cc.SY.yingxiaopurl("sounds/button");
        cc.find("Canvas/shezhi/kuang/guanyx").active = true;
        cc.find("Canvas/shezhi/kuang/kaiyx").active = false;
        cc.SY.setyingxiao(0);
      },
      putmshi: function putmshi() {
        cc.SY.yingxiaopurl("sounds/button");
        cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi1").active = true;
        cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi2").active = false;
        cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi3").active = false;
      },
      gudingms: function gudingms() {
        cc.SY.yingxiaopurl("sounds/button");
        cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi2").active = true;
        cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi1").active = false;
        cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi3").active = false;
      },
      ziyqzhuag: function ziyqzhuag() {
        cc.SY.yingxiaopurl("sounds/button");
        cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi3").active = true;
        cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi1").active = false;
        cc.find("Canvas/chuangjfj/chuangjianfangjian/moshi2").active = false;
      },
      CreateRoom: function CreateRoom(data, data1) {
        cc.SY.yingxiaopurl("sounds/button");
        if (1 == data1) this.CreateRoomData.gametype = 1; else if (2 == data1) this.CreateRoomData.gametype = 2; else {
          if (3 != data1) return;
          this.CreateRoomData.gametype = 3;
        }
        this.CreateRoomData.y = "index";
        this.CreateRoomData.d = "post";
        this.CreateRoomData.ttoken = cc.User.token;
        this.CreateRoomData.apptoken = cc.User.apptoken;
        cc.http.URL("J", this.CreateRoomData, function(data, status) {
          var p_data = JSON.parse(data);
          if (200 == status) {
            cc.GamesFangJianData = p_data;
            cc.Msgbox.loading();
            cc.director.preloadScene("game", function(v) {
              cc.director.loadScene("game");
            });
          } else 415 == status && (-3 == p_data.code ? cc.Msgbox.show("创建失败请重试：" + p_data.msg) : cc.Msgbox.show(p_data.msg));
        });
      },
      RandomNumBoth: function RandomNumBoth(Min, Max) {
        var Range = Max - Min;
        var Rand = Math.random();
        var num = Min + Rand * Range;
        return num;
      },
      yougedenglu: function yougedenglu() {
        var suiji = "yk" + Date.parse(new Date()) / 1e3 + parseInt(700 * Math.random());
        var myjson = {
          y: "login",
          d: "get",
          zhanghao: suiji,
          pass: suiji,
          apptoken: cc.User.apptoken
        };
        cc.http.URL("J", myjson, function(data, status) {
          if (200 == status) {
            var Json = JSON.parse(data);
            1 == Json.code && cc.MAN.loginin();
          }
        });
      },
      loginin: function loginin() {
        var myjson = {
          y: "index",
          apptoken: cc.User.apptoken
        };
        var self = this;
        cc.http.URL("J", myjson, function(data, status) {
          if (null != data && "" != data) {
            var Data = JSON.parse(data);
            if (null != Data && "" != Data) {
              cc.User = Data.data;
              cc.sys.localStorage.setItem("NEWuserData", JSON.stringify(cc.User));
              var data = cc.sys.localStorage.getItem("NEWuserData");
            }
          }
          if ("200" == status) {
            if (cc.User) {
              cc.MAN.initUser(cc.User);
              cc.wenzi(cc.find("Canvas/beijing/gonggao/mask/label"), cc.User.gonggao);
              cc.pichttp(cc.find("Canvas/xiaoxi/tanck/tancnk/tancnk"), cc.User.xiaoxi, 800, 420);
              var chang = cc.find("Canvas/beijing/gonggao/mask/label").width;
              cc.MAN.schedule(function() {
                cc.find("Canvas/beijing/gonggao/mask/label").x -= 2.5;
                cc.find("Canvas/beijing/gonggao/mask/label").x <= -337 - chang && (cc.find("Canvas/beijing/gonggao/mask/label").x = 520);
              }, .01);
            }
          } else cc.MAN.WeChatLogin();
        });
      },
      WeChatLogin: function WeChatLogin(code, msg) {
        var tt = cc.SDKAPI.weixingo(function(r) {});
      },
      toScene: function toScene() {
        cc.User && cc.User.uid > 0;
      },
      WSCOLSE: function WSCOLSE(value) {
        1 == value;
      },
      onDestroy: function onDestroy() {
        cc.SY.stopAll();
      },
      daojishi: function daojishi(t) {
        if (t < 1) return;
        this.time = t;
        this.unschedule(this.callback);
        this.callback = function() {
          cc.find("Canvas/beijing/daojishi/label").active = true;
          cc.find("Canvas/beijing/daojishi/label").getComponent(cc.Label).string = this.time + "S";
          0 == this.time && this.unschedule(this.callback);
          this.time--;
        };
        this.schedule(this.callback, 1);
      },
      update: function update(dt) {}
    });
    cc._RF.pop();
  }, {
    http: "http"
  } ],
  game: [ function(require, module, exports) {
    "use strict";
    cc._RF.push(module, "b8582UcjI1EQoX3cHqU63qN", "game");
    "use strict";
    var THIS = null;
    cc.Class({
      extends: cc.Component,
      properties: {
        weizhi1: {
          default: null,
          type: cc.Node
        },
        weizhi2: {
          default: null,
          type: cc.Node
        },
        weizhi3: {
          default: null,
          type: cc.Node
        },
        weizhi4: {
          default: null,
          type: cc.Node
        },
        weizhi5: {
          default: null,
          type: cc.Node
        },
        weizhi6: {
          default: null,
          type: cc.Node
        },
        weizhi7: {
          default: null,
          type: cc.Node
        },
        weizhi8: {
          default: null,
          type: cc.Node
        },
        weizhi9: {
          default: null,
          type: cc.Node
        },
        weizhi10: {
          default: null,
          type: cc.Node
        },
        weizhi11: {
          default: null,
          type: cc.Node
        },
        weizhi12: {
          default: null,
          type: cc.Node
        },
        weizhi13: {
          default: null,
          type: cc.Node
        },
        XIAZHUBS: cc.SpriteAtlas,
        kaijiangjieguo: cc.Prefab,
        PUKE: cc.SpriteAtlas,
        DIANSHU: cc.SpriteAtlas,
        BiaoQingPrefab: cc.Prefab,
        TextPrefab: cc.Prefab,
        UserDisslove: cc.Prefab,
        FJINBI: cc.Prefab,
        BiaoQingTuJi: cc.SpriteAtlas
      },
      onLoad: function onLoad() {
        cc.find("Canvas").scaleY = cc.director.getWinSizeInPixels().height / 700;
        cc.find("Canvas").scaleX = cc.director.getWinSizeInPixels().width / 1280;
        cc.MAN = THIS = this;
        this.bangding();
        cc.GAME = cc.GamesFangJianData.data.d;
        cc.ZYONLINE = this.addComponent("online");
        cc.Msgbox = this.addComponent("Msgbox");
        cc.SY = this.addComponent("shengying");
        cc.SY.zyconfig({
          nanaudio: [ "sounds/start", "sounds/grabwoqiang", "sounds/button", "sounds/get_coins", "sounds/send_card" ],
          nvaudio: [ "sounds/grabwoqiang", "sounds/grabbuqiang" ]
        });
        cc.SY.yingyuepurl("/sounds/bg");
        var jiedian = cc.find("Canvas/beijing/zhuozi/my/pai/41");
        var jiedian1 = cc.find("Canvas/beijing/zhuozi/my/pai/mask/3");
        var jiedian2 = cc.find("Canvas/beijing/zhuozi/my/pai/mask");
        var jiedian4 = cc.find("Canvas/beijing/zhuozi/my/pai/mask/31");
        this.gaibianle = false;
        var movenode = null;
        this.num1 = 0;
        jiedian.on(cc.Node.EventType.TOUCH_MOVE, function(event) {
          var delta = event.getDelta();
          var localtion = event.getStartLocation();
          var localtion1 = event.getLocation();
          if (this.gaibianle) if (movenode == cc.find("Canvas/beijing/zhuozi/my/pai/mask/31")) {
            var monix4 = jiedian4.y + delta.y;
            var monix5 = jiedian2.height - delta.y / 2;
            if (monix4 < -97 && delta.y > 0) {
              jiedian4.y = monix4;
              jiedian2.height <= 190 ? jiedian2.height = monix5 : jiedian2.height = 190;
            } else if (delta.y > 0) jiedian2.height <= 190 ? jiedian2.height = jiedian2.height + delta.y / 2 : jiedian2.height = 190; else if (monix4 < -94) if (jiedian2.height <= 190 && monix4 > -290 && monix4 < -94) {
              jiedian4.y = monix4;
              jiedian2.height = jiedian2.height + delta.y / 2;
            } else if (jiedian2.height <= 190 && monix4 < -290) {
              jiedian4.y = -292;
              jiedian2.height = monix5;
            } else {
              jiedian2.height = 190;
              jiedian4.y = monix4;
            }
          } else {
            var monix = jiedian1.x + delta.x;
            var monix1 = jiedian2.width - delta.x / 2;
            if (monix < -65 && delta.x > 0) {
              jiedian1.x = monix;
              jiedian2.width <= 124 ? jiedian2.width = monix1 : jiedian2.width = 124;
            } else if (delta.x > 0) jiedian2.width <= 124 ? jiedian2.width = jiedian2.width + delta.x / 2 : jiedian2.width = 124; else if (monix < -60) if (jiedian2.width <= 124 && monix > -198 && monix < -63) {
              jiedian1.x = monix;
              jiedian2.width = jiedian2.width + delta.x / 2;
            } else if (jiedian2.width <= 124 && monix < -198) {
              jiedian1.x = -200;
              jiedian2.width = monix1;
            } else {
              jiedian1.x = monix;
              jiedian2.width = 124;
            }
          } else if (10 == this.num1) {
            movenode = localtion1.y - localtion.y > localtion1.x - localtion.x ? cc.find("Canvas/beijing/zhuozi/my/pai/mask/31") : cc.find("Canvas/beijing/zhuozi/my/pai/mask/3");
            this.gaibianle = true;
          }
          this.num1++;
        }, this);
        jiedian.on(cc.Node.EventType.TOUCH_END, function(event) {
          if (movenode == cc.find("Canvas/beijing/zhuozi/my/pai/mask/3")) if (movenode.x > -80) {
            jiedian1.active = true;
            jiedian1.runAction(cc.moveTo(.01, -62, -97));
            cc.find("Canvas/beijing/zhuozi/my/pai/mask").width = 124;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask").height = 190;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask").x = 422;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask").y = 196;
            THIS.scheduleOnce(function() {
              cc.find("Canvas/beijing/zhuozi/my/pai/mask/4").active = false;
              cc.find("Canvas/beijing/zhuozi/my/pai/41").active = false;
              jiedian1.runAction(cc.spawn(cc.moveTo(.2, -76.5, -118.9), cc.scaleTo(.2, .72, .73)));
              THIS.scheduleOnce(function() {
                THIS.yifanzhuan = true;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/3").active = false;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/3").scale = 1;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/3").height = 190;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/3").width = 124;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/3").x = -193;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/3").y = -97;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/31").active = false;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/31").scale = 1;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/31").height = 190;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/31").width = 124;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/31").x = -62;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/31").y = -292;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/4").x = -66;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/4").y = -94.3;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/4").height = 190;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/4").width = 124;
                cc.find("Canvas/beijing/zhuozi/my/pai/2").active = true;
              }, 2);
              THIS.scheduleOnce(function() {
                cc.find("Canvas/beijing/zhuozi/my/lvkuang").active = true;
              }, 1.5);
              return;
            }, .02);
            var json = {
              y: "fanpai",
              uid: cc.User.uid
            };
            cc.ZYONLINE.wssend(json);
          } else {
            cc.find("Canvas/beijing/zhuozi/my/pai/mask").width = 124;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask").height = 190;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask").x = 422;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask").y = 196;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/3").active = true;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/3").scale = 1;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/3").height = 190;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/3").width = 124;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/3").x = -193;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/3").y = -97;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/31").active = true;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/31").scale = 1;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/31").height = 190;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/31").width = 124;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/31").x = -62;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/31").y = -292;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/4").x = -66;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/4").y = -94.3;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/4").height = 190;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/4").width = 124;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/4").active = true;
            cc.find("Canvas/beijing/zhuozi/my/pai/41").active = true;
            this.gaibianle = false;
            this.num1 = 0;
          } else if (movenode.y > -100) {
            jiedian4.runAction(cc.moveTo(.01, -62, -97));
            cc.find("Canvas/beijing/zhuozi/my/pai/mask").width = 124;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask").height = 190;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask").x = 422;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask").y = 196;
            THIS.scheduleOnce(function() {
              cc.find("Canvas/beijing/zhuozi/my/pai/mask/4").active = false;
              cc.find("Canvas/beijing/zhuozi/my/pai/41").active = false;
              jiedian4.runAction(cc.spawn(cc.moveTo(.2, -76.5, -118.9), cc.scaleTo(.2, .72, .73)));
              THIS.scheduleOnce(function() {
                THIS.yifanzhuan = true;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/3").active = false;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/3").scale = 1;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/3").height = 190;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/3").width = 124;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/3").x = -193;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/3").y = -97;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/31").active = false;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/31").scale = 1;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/31").height = 190;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/31").width = 124;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/31").x = -62;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/31").y = -292;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/4").x = -66;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/4").y = -94.3;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/4").height = 190;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/4").width = 124;
                cc.find("Canvas/beijing/zhuozi/my/pai/2").active = true;
              }, 2);
              THIS.scheduleOnce(function() {
                cc.find("Canvas/beijing/zhuozi/my/lvkuang").active = true;
              }, 1.5);
              return;
            }, .02);
            var json = {
              y: "fanpai",
              uid: cc.User.uid
            };
            cc.ZYONLINE.wssend(json);
          } else {
            cc.find("Canvas/beijing/zhuozi/my/pai/mask").width = 124;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask").height = 190;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask").x = 422;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask").y = 196;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/3").active = true;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/3").scale = 1;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/3").height = 190;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/3").width = 124;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/3").x = -193;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/3").y = -97;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/31").active = true;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/31").scale = 1;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/31").height = 190;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/31").width = 124;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/31").x = -62;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/31").y = -292;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/4").x = -66;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/4").y = -94.3;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/4").height = 190;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/4").width = 124;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/4").active = true;
            cc.find("Canvas/beijing/zhuozi/my/pai/41").active = true;
            this.gaibianle = false;
            this.num1 = 0;
          }
        }, this);
        jiedian.on(cc.Node.EventType.TOUCH_CANCEL, function(event) {
          if (movenode == cc.find("Canvas/beijing/zhuozi/my/pai/mask/3")) if (movenode.x > -80) {
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/3").active = true;
            jiedian1.runAction(cc.moveTo(.01, -62, -97));
            cc.find("Canvas/beijing/zhuozi/my/pai/mask").width = 124;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask").height = 190;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask").x = 422;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask").y = 196;
            THIS.scheduleOnce(function() {
              cc.find("Canvas/beijing/zhuozi/my/pai/mask/4").active = false;
              cc.find("Canvas/beijing/zhuozi/my/pai/41").active = false;
              cc.find("Canvas/beijing/zhuozi/my/pai/mask/3").runAction(cc.spawn(cc.moveTo(.2, -76.5, -118.9), cc.scaleTo(.2, .72, .73)));
              THIS.scheduleOnce(function() {
                THIS.yifanzhuan = true;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/3").active = false;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/3").scale = 1;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/3").height = 190;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/3").width = 124;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/3").x = -193;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/3").y = -97;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/31").active = false;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/31").scale = 1;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/31").height = 190;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/31").width = 124;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/31").x = -62;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/31").y = -292;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/4").x = -66;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/4").y = -94.3;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/4").height = 190;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/4").width = 124;
                cc.find("Canvas/beijing/zhuozi/my/pai/2").active = true;
              }, 2);
              THIS.scheduleOnce(function() {
                cc.find("Canvas/beijing/zhuozi/my/lvkuang").active = true;
              }, 1.5);
              return;
            }, .02);
            var json = {
              y: "fanpai",
              uid: cc.User.uid
            };
            cc.ZYONLINE.wssend(json);
          } else {
            cc.find("Canvas/beijing/zhuozi/my/pai/mask").width = 124;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask").height = 190;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask").x = 422;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask").y = 196;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/3").active = true;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/3").scale = 1;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/3").height = 190;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/3").width = 124;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/3").x = -193;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/3").y = -97;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/31").active = true;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/31").scale = 1;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/31").height = 190;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/31").width = 124;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/31").x = -62;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/31").y = -292;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/4").x = -66;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/4").y = -94.3;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/4").height = 190;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/4").width = 124;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/4").active = true;
            cc.find("Canvas/beijing/zhuozi/my/pai/41").active = true;
            this.gaibianle = false;
            this.num1 = 0;
          } else if (movenode.y > -100) {
            jiedian4.runAction(cc.moveTo(.01, -62, -97));
            cc.find("Canvas/beijing/zhuozi/my/pai/mask").width = 124;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask").height = 190;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask").x = 422;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask").y = 196;
            THIS.scheduleOnce(function() {
              cc.find("Canvas/beijing/zhuozi/my/pai/mask/4").active = false;
              cc.find("Canvas/beijing/zhuozi/my/pai/41").active = false;
              jiedian4.runAction(cc.spawn(cc.moveTo(.2, -76.5, -118.9), cc.scaleTo(.2, .72, .73)));
              THIS.scheduleOnce(function() {
                THIS.yifanzhuan = true;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/3").active = false;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/3").scale = 1;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/3").height = 190;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/3").width = 124;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/3").x = -193;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/3").y = -97;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/31").active = false;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/31").scale = 1;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/31").height = 190;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/31").width = 124;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/31").x = -62;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/31").y = -292;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/4").x = -66;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/4").y = -94.3;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/4").height = 190;
                cc.find("Canvas/beijing/zhuozi/my/pai/mask/4").width = 124;
                cc.find("Canvas/beijing/zhuozi/my/pai/2").active = true;
              }, 2);
              THIS.scheduleOnce(function() {
                cc.find("Canvas/beijing/zhuozi/my/lvkuang").active = true;
              }, 1.5);
              return;
            }, .02);
            var json = {
              y: "fanpai",
              uid: cc.User.uid
            };
            cc.ZYONLINE.wssend(json);
          } else {
            cc.find("Canvas/beijing/zhuozi/my/pai/mask").width = 124;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask").height = 190;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask").x = 422;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask").y = 196;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/3").active = true;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/3").scale = 1;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/3").height = 190;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/3").width = 124;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/3").x = -193;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/3").y = -97;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/31").active = true;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/31").scale = 1;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/31").height = 190;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/31").width = 124;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/31").x = -62;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/31").y = -292;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/4").x = -66;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/4").y = -94.3;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/4").height = 190;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/4").width = 124;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/4").active = true;
            cc.find("Canvas/beijing/zhuozi/my/pai/41").active = true;
            this.gaibianle = false;
            this.num1 = 0;
          }
        }, this);
        cc.SDKAPI = this.addComponent("sdkapi");
        this.nodeinfo = [ {
          NodeInfo: this.weizhi2,
          nodeName: "2",
          isSelect: false,
          UserInfo: "",
          _isPlayShow: false,
          yifanpai: false
        }, {
          NodeInfo: this.weizhi3,
          nodeName: "3",
          isSelect: false,
          UserInfo: "",
          _isPlayShow: false,
          yifanpai: false
        }, {
          NodeInfo: this.weizhi4,
          nodeName: "4",
          isSelect: false,
          UserInfo: "",
          _isPlayShow: false,
          yifanpai: false
        }, {
          NodeInfo: this.weizhi5,
          nodeName: "5",
          isSelect: false,
          UserInfo: "",
          _isPlayShow: false,
          yifanpai: false
        }, {
          NodeInfo: this.weizhi6,
          nodeName: "6",
          isSelect: false,
          UserInfo: "",
          _isPlayShow: false,
          yifanpai: false
        }, {
          NodeInfo: this.weizhi7,
          nodeName: "7",
          isSelect: false,
          UserInfo: "",
          _isPlayShow: false,
          yifanpai: false
        }, {
          NodeInfo: this.weizhi8,
          nodeName: "8",
          isSelect: false,
          UserInfo: "",
          _isPlayShow: false,
          yifanpai: false
        }, {
          NodeInfo: this.weizhi9,
          nodeName: "9",
          isSelect: false,
          UserInfo: "",
          _isPlayShow: false,
          yifanpai: false
        }, {
          NodeInfo: this.weizhi10,
          nodeName: "10",
          isSelect: false,
          UserInfo: "",
          _isPlayShow: false,
          yifanpai: false
        }, {
          NodeInfo: this.weizhi11,
          nodeName: "11",
          isSelect: false,
          UserInfo: "",
          _isPlayShow: false,
          yifanpai: false
        }, {
          NodeInfo: this.weizhi12,
          nodeName: "12",
          isSelect: false,
          UserInfo: "",
          _isPlayShow: false,
          yifanpai: false
        }, {
          NodeInfo: this.weizhi13,
          nodeName: "13",
          isSelect: false,
          UserInfo: "",
          _isPlayShow: false,
          yifanpai: false
        }, {
          NodeInfo: this.weizhi1,
          nodeName: "my",
          isSelect: false,
          UserInfo: "",
          _isPlayShow: false,
          yifanpai: false
        } ];
        this.WENZIBIAO = {
          1: "艾玛，快点下注吧，别墨迹了！",
          2: "小样，你敢跟吗？",
          3: "哎呀，又从众了。",
          4: "哎呀，今天就靠这把翻身了。",
          5: "小意思，来把大的。",
          6: "哎，朋友，玩得不赖啊。",
          7: "无敌真是寂寞啊！",
          8: "不要羡慕我哟！",
          9: "好多钱呐！",
          10: "催嘛？我在想下什么好。",
          11: "快点下注，别墨迹了！",
          12: "全压了，你敢跟吗？",
          13: "哎呀~又冲动了。",
          14: "好好玩，别浪费我的时间",
          15: "小意思，来把大的。",
          16: "哎，朋友，玩得不赖啊。",
          17: "哈哈哈哈，好牌都在我这！",
          18: "不要羡慕我哟！",
          19: "好多钱啊！",
          20: "催什么啊？我在想下多少好。"
        };
        this.BAOHAO = {
          21: [ "bang", 8 ],
          22: [ "bishi", 2 ],
          23: [ "daku", 3 ],
          24: [ "deyi", 10 ],
          25: [ "emo", 7 ],
          26: [ "fanu", 11 ],
          27: [ "haixiu", 8 ],
          28: [ "han", 9 ],
          29: [ "hanxiao", 8 ],
          30: [ "haqian", 10 ],
          31: [ "jingkong", 6 ],
          32: [ "kanren", 2 ],
          33: [ "keai", 12 ],
          34: [ "kelian", 3 ],
          35: [ "ku", 5 ],
          36: [ "outu", 10 ],
          37: [ "qidao", 10 ],
          38: [ "qinqin", 6 ],
          39: [ "se", 4 ],
          40: [ "shaoxiang", 4 ],
          41: [ "shuai", 10 ],
          42: [ "tiaopi", 8 ],
          43: [ "touxiao", 4 ],
          44: [ "xinsui", 9 ],
          45: [ "ye", 7 ],
          46: [ "yinxiao", 6 ],
          47: [ "yun", 4 ],
          48: [ "zaijian", 5 ],
          49: [ "zhuakuang", 9 ],
          50: [ "ziya", 5 ]
        };
      },
      fengxiangsz: function fengxiangsz(Msg) {
        if (2 == Msg.d.wanfa) var wanfa1 = "小王1点，大王2点"; else if (1 == Msg.d.wanfa) var wanfa1 = "小王1点，大王1点";
        if (window.$FENXIN) {
          window.$FENXIN["1"] = cc.http.SERVERHTPT() + "/?fid=" + Msg.d.roomid;
          window.$FENXIN["0"] = "【广东拖】房号：" + Msg.d.roomid;
          window.$FENXIN["3"] = Msg.d.allqishu + "局，底分" + Msg.d.difen + "," + wanfa1 + ",同点平局,庄家吃到0点 VX:" + cc.User.KeFu;
          window.dandan();
        }
      },
      bangding: function bangding() {
        cc.anniu(cc.find("Canvas/beijing/top/tuichu"), "game", "BackToHall");
        cc.anniu(cc.find("Canvas/beijing/shezhi/kuang/kaiyy/kai"), "game", "off_sy");
        cc.anniu(cc.find("Canvas/beijing/shezhi/kuang/guanyy/guan"), "game", "on_sy");
        cc.anniu(cc.find("Canvas/beijing/shezhi/kuang/kaiyx/kai"), "game", "off_yx");
        cc.anniu(cc.find("Canvas/beijing/shezhi/kuang/guanyx/guan"), "game", "on_yx");
        cc.anniu(cc.find("Canvas/beijing/shezhi/kuang/guanbi"), "game", "off_shezhi");
        cc.anniu(cc.find("Canvas/beijing/top/shezhi"), "game", "on_shezhi");
        cc.anniu(cc.find("Canvas/beijing/button/zhunbei"), "game", "Ready");
        cc.anniu(cc.find("Canvas/beijing/qiangzhuang/1"), "game", "myqzhuang", "0");
        cc.anniu(cc.find("Canvas/beijing/qiangzhuang/2"), "game", "myqzhuang", "1");
        cc.anniu(cc.find("Canvas/beijing/qiangzhuang/3"), "game", "myqzhuang", "2");
        cc.anniu(cc.find("Canvas/beijing/qiangzhuang/4"), "game", "myqzhuang", "3");
        cc.anniu(cc.find("Canvas/beijing/qiangzhuang/5"), "game", "myqzhuang", "4");
        cc.anniu(cc.find("Canvas/beijing/beilv/beilv1/1"), "game", "xiazubeil", "1");
        cc.anniu(cc.find("Canvas/beijing/beilv/beilv1/2"), "game", "xiazubeil", "2");
        cc.anniu(cc.find("Canvas/beijing/beilv/beilv1/3"), "game", "xiazubeil", "3");
        cc.anniu(cc.find("Canvas/beijing/beilv/beilv1/4"), "game", "xiazubeil", "4");
        cc.anniu(cc.find("Canvas/beijing/beilv/beilv1/5"), "game", "xiazubeil", "5");
        cc.anniu(cc.find("Canvas/beijing/beilv/beilv1/6"), "game", "xiazubeil", "6");
        cc.anniu(cc.find("Canvas/beijing/beilv/beilv1/7"), "game", "xiazubeil", "7");
        cc.anniu(cc.find("Canvas/beijing/beilv/beilv1/8"), "game", "xiazubeil", "8");
        cc.anniu(cc.find("Canvas/beijing/beilv/beilv1/10"), "game", "xiazubeil", "10");
        cc.anniu(cc.find("Canvas/beijing/paijujieshu/k/an"), "game", "BackToHall1");
        cc.anniu(cc.find("Canvas/beijing/dibulan/liaotian"), "game", "AlertChat");
        cc.anniu(cc.find("Canvas/beijing/liaotiank/liaotiankuang/biaoq"), "game", "on_biaoq");
        cc.anniu(cc.find("Canvas/beijing/liaotiank/liaotiankuang/yuju"), "game", "on_yuyin");
        cc.anniu(cc.find("Canvas/beijing/guizhe/zhehzao"), "game", "quxiaoguizhe");
        cc.anniu(cc.find("Canvas/beijing/top/guize"), "game", "guizhe");
        cc.anniu(cc.find("Canvas/beijing/top/tanc"), "game", "guizhe1");
        cc.anniu(cc.find("Canvas/beijing/toupiaojs/toupiaojiesan/tongyi"), "game", "toupiaojieshan");
        cc.anniu(cc.find("Canvas/beijing/toupiaojs/toupiaojiesan/jujue"), "game", "quxiaotpjs");
        cc.anniu(cc.find("Canvas/beijing/tuichufj/tuichufangjian/tongyi"), "game", "tuichufangjian");
        cc.anniu(cc.find("Canvas/beijing/tuichufj/tuichufangjian/jujue"), "game", "quxiaotcfj");
        cc.anniu(cc.find("Canvas/beijing/shenqjs/tyan"), "game", "istyjieshan", "2");
        cc.anniu(cc.find("Canvas/beijing/shenqjs/jjan"), "game", "istyjieshan", "1");
        cc.anniu(cc.find("Canvas/beijing/liaotiank/zhezhao"), "game", "guanbibiaoqing");
        cc.anniu(cc.find("Canvas/beijing/zhuozi/my/renwu/xiazhuang"), "game", "xiazhuang");
        cc.anniu(cc.find("Canvas/beijing/button/quxiao"), "game", "BackToHall");
      },
      xiazhuang: function xiazhuang() {
        var json = {
          y: "cancelzhuang"
        };
        cc.ZYONLINE.wssend(json);
      },
      guanbibiaoqing: function guanbibiaoqing() {
        cc.find("Canvas/beijing/liaotiank").active = false;
      },
      quxiaoguizhe: function quxiaoguizhe() {
        cc.SY.yingxiaopurl("sounds/button");
        cc.find("Canvas/beijing/guizhe").active = false;
      },
      toupiaojieshan: function toupiaojieshan() {
        cc.SY.yingxiaopurl("sounds/button");
        cc.find("Canvas/beijing/toupiaojs").active = false;
        var json = {
          y: "exit"
        };
        cc.ZYONLINE.wssend(json);
      },
      istyjieshan: function istyjieshan(lx, data) {
        THIS.tongyi = false;
        cc.SY.yingxiaopurl("sounds/button");
        cc.find("Canvas/beijing/toupiaojs").active = false;
        var json = {
          y: "selectDissolve",
          d: data
        };
        cc.ZYONLINE.wssend(json);
      },
      tuichufangjian: function tuichufangjian() {
        cc.SY.yingxiaopurl("sounds/button");
        cc.find("Canvas/beijing/tuichufj").active = false;
        cc.ZYONLINE.wssend({
          y: "outroom"
        });
      },
      quxiaotpjs: function quxiaotpjs() {
        cc.SY.yingxiaopurl("sounds/button");
        cc.find("Canvas/beijing/toupiaojs").active = false;
      },
      quxiaotcfj: function quxiaotcfj() {
        cc.SY.yingxiaopurl("sounds/button");
        cc.find("Canvas/beijing/tuichufj").active = false;
      },
      guizhe: function guizhe() {
        cc.SY.yingxiaopurl("sounds/button");
        cc.find("Canvas/beijing/guizhe").active = true;
      },
      on_shezhi: function on_shezhi() {
        cc.SY.yingxiaopurl("sounds/button");
        cc.find("Canvas/beijing/shezhi").active = true;
      },
      off_shezhi: function off_shezhi() {
        cc.SY.yingxiaopurl("sounds/button");
        cc.find("Canvas/beijing/shezhi").active = false;
      },
      on_biaoq: function on_biaoq() {
        cc.SY.yingxiaopurl("sounds/button");
        cc.find("Canvas/beijing/liaotiank/liaotiankuang/biaoq/xuanzhongbiaoqing").active = false;
        cc.find("Canvas/beijing/liaotiank/liaotiankuang/yuju/xuanzhongliaotian").active = true;
        cc.find("Canvas/beijing/liaotiank/liaotiankuang/scrollview1").active = false;
        cc.find("Canvas/beijing/liaotiank/liaotiankuang/scrollview2").active = true;
      },
      on_yuyin: function on_yuyin() {
        cc.SY.yingxiaopurl("sounds/button");
        cc.find("Canvas/beijing/liaotiank/liaotiankuang/yuju/xuanzhongliaotian").active = false;
        cc.find("Canvas/beijing/liaotiank/liaotiankuang/biaoq/xuanzhongbiaoqing").active = true;
        cc.find("Canvas/beijing/liaotiank/liaotiankuang/scrollview1").active = true;
        cc.find("Canvas/beijing/liaotiank/liaotiankuang/scrollview2").active = false;
      },
      myqzhuang: function myqzhuang(data, dataa) {
        cc.SY.yingxiaopurl("sounds/button");
        cc.ZYONLINE.wssend({
          y: "myqz",
          d: dataa
        });
      },
      xiazubeil: function xiazubeil(data, dataa) {
        cc.SY.yingxiaopurl("sounds/button");
        cc.ZYONLINE.wssend({
          y: "mybet",
          d: dataa
        });
      },
      on_sy: function on_sy() {
        cc.SY.yingxiaopurl("sounds/button");
        cc.find("Canvas/beijing/shezhi/kuang/guanyy").active = false;
        cc.find("Canvas/beijing/shezhi/kuang/kaiyy").active = true;
        cc.SY.setyingyue(1);
      },
      off_sy: function off_sy() {
        cc.SY.yingxiaopurl("sounds/button");
        cc.find("Canvas/beijing/shezhi/kuang/guanyy").active = true;
        cc.find("Canvas/beijing/shezhi/kuang/kaiyy").active = false;
        cc.SY.setyingyue(0);
      },
      on_yx: function on_yx() {
        cc.SY.yingxiaopurl("sounds/button");
        cc.find("Canvas/beijing/shezhi/kuang/guanyx").active = false;
        cc.find("Canvas/beijing/shezhi/kuang/kaiyx").active = true;
        cc.SY.setyingxiao(1);
      },
      off_yx: function off_yx() {
        cc.SY.yingxiaopurl("sounds/button");
        cc.find("Canvas/beijing/shezhi/kuang/guanyx").active = true;
        cc.find("Canvas/beijing/shezhi/kuang/kaiyx").active = false;
        cc.SY.setyingxiao(0);
      },
      CloseAlert: function CloseAlert() {
        cc.find("Canvas/beijing/liaotiank").active = false;
      },
      SendText: function SendText(lx, value) {
        cc.ZYONLINE.wssend({
          y: "WangYatxt",
          d: value
        });
      },
      AlertChat: function AlertChat() {
        cc.SY.yingxiaopurl("sounds/button");
        cc.find("Canvas/beijing/liaotiank").active = true;
        cc.anniu(cc.find("Canvas/beijing/liaotiank/NewSprite(Splash)"), "game", "CloseAlert");
        cc.find("Canvas/beijing/liaotiank/liaotiankuang/scrollview1/view/content").removeAllChildren();
        var len = 0;
        for (var i in this.WENZIBIAO) len++;
        if ("0" == cc.User.xingbie) for (var i = 11; i < 21; i++) {
          var TextItem = cc.instantiate(this.TextPrefab);
          TextItem.getComponent(cc.Label).string = this.WENZIBIAO[i];
          cc.anniu(TextItem, "game", "SendText", i);
          TextItem.parent = cc.find("Canvas/beijing/liaotiank/liaotiankuang/scrollview1/view/content");
        } else for (var i = 1; i < 11; i++) {
          var TextItem = cc.instantiate(this.TextPrefab);
          TextItem.getComponent(cc.Label).string = this.WENZIBIAO[i];
          cc.anniu(TextItem, "game", "SendText", i);
          TextItem.parent = cc.find("Canvas/beijing/liaotiank/liaotiankuang/scrollview1/view/content");
        }
        cc.find("Canvas/beijing/liaotiank/liaotiankuang/scrollview2/view/content").removeAllChildren();
        for (var i = 21; i < 51; i++) {
          var BiaoQingItem = cc.instantiate(this.BiaoQingPrefab);
          BiaoQingItem.getComponent(cc.Sprite).spriteFrame = this.BiaoQingTuJi.getSpriteFrame(this.BAOHAO[i][0] + "1");
          cc.anniu(BiaoQingItem, "game", "SendText", i);
          BiaoQingItem.parent = cc.find("Canvas/beijing/liaotiank/liaotiankuang/scrollview2/view/content");
        }
      },
      BackToHall: function BackToHall() {
        cc.SY.yingxiaopurl("sounds/button");
        1 == this.qishu || 1 == this.iswanguo ? cc.find("Canvas/beijing/tuichufj").active = true : cc.find("Canvas/beijing/toupiaojs").active = true;
      },
      BackToHall1: function BackToHall1() {
        cc.SY.yingxiaopurl("sounds/button");
        this.replaceScene("dating");
      },
      Share: function Share() {
        cc.SY.yingxiaop(2);
        cc.SDKAPI.jieping(false);
      },
      start: function start() {
        cc.ZYONLINE.wslink(cc.GAME);
        THIS._MyUserID = cc.User.uid;
      },
      WSCOLSE: function WSCOLSE(value) {
        1 == value && this.replaceScene("dating");
      },
      replaceScene: function replaceScene(value) {
        cc.director.preloadScene(value, function(v) {
          cc.director.loadScene(value);
        });
      },
      WSSHOUDATA: function WSSHOUDATA(Msg) {
        var _this = this;
        if (!cc.ZYONLINE._YSEGAME) return;
        switch (Msg.y) {
         case "g":
          this.zhuomianxx(Msg);
          this.initData(Msg);
          this.fengxiangsz(Msg);
          this.qishu = Msg.d.qishu;
          break;

         case "cancelsuccess":
          cc.find("Canvas/beijing/zhuozi/my/renwu/xiazhuang").active = false;
          cc.Msgbox.show(Msg.d);
          break;

         case "allready":
          this.iskaishi = 1;
          try {
            this.daojishi(Msg.d.time - 1);
            for (var key in Msg.d.isPangguan) if (this.getNode(key).UserInfo) {
              this.getNode(key).UserInfo.isPangguan = Msg.d.isPangguan[key];
              1 == Msg.d.isPangguan[key] && (cc.find("renwu/guanzhan", this.getNode(key).NodeInfo).active = true);
            }
            for (var _key in Msg.d.online) this.getNode(_key).UserInfo && (0 == Msg.d.online[_key] ? cc.find("renwu/likai", this.getNode(_key).NodeInfo).active = true : cc.find("renwu/likai", this.getNode(_key).NodeInfo).active = false);
            cc.find("Canvas/beijing/zhuozi/xiaoxi/qingzhunbei").active = false;
            cc.find("Canvas/beijing/button/zhunbei").active = false;
            cc.find("Canvas/beijing/top/tuichu").active = false;
            cc.find("Canvas/beijing/button/quxiao").active = false;
            cc.find("Canvas/beijing/zhuozi/xiaoxi/ddwjzb").active = false;
            cc.find("Canvas/beijing/zhuozi/xiaoxi/yxjjks").active = true;
          } catch (error) {
            console.log(error);
          }
          break;

         case "mypaixin":
          THIS.scheduleOnce(function() {
            cc.SY.yingxiaopurl("sounds/point" + Msg.d.paixin);
          }, 1.5);
          this.getNode(Msg.d.uid) && (this.getNode(Msg.d.uid).yifanpai = true);
          if (Msg.d.uid == this._MyUserID) ; else {
            THIS.fanpail(cc.find("pai/bei1", this.getNode(Msg.d.uid).NodeInfo), cc.find("pai/2", this.getNode(Msg.d.uid).NodeInfo), Msg.d.pai[1]);
            THIS.scheduleOnce(function() {
              cc.find("lvkuang", this.getNode(Msg.d.uid).NodeInfo).active = true;
            }, 1.5);
          }
          break;

         case "ready":
          Msg.d.time && this.daojishi(Msg.d.time - 1);
          if (0 == Msg.d.gameState) for (var _key2 in this.nodeinfo) if (true == this.nodeinfo[_key2].isSelect && this.nodeinfo[_key2].UserInfo.uid == Msg.d.uid) {
            this.nodeinfo[_key2].UserInfo.action = Msg.d.action;
            cc.find("renwu/guanzhan", this.getNode(Msg.d.uid).NodeInfo).active = false;
            this.SetReady(this.nodeinfo[_key2].NodeInfo, this.nodeinfo[_key2].UserInfo.action);
          }
          Msg.d.zf && cc.wenzi(cc.find("shenyujine/label", this.getNode(Msg.d.uid).NodeInfo), Msg.d.zf);
          if (Msg.d.uid == this._MyUserID) if (0 == Msg.d.action) {
            cc.find("Canvas/beijing/zhuozi/xiaoxi/qingzhunbei").active = true;
            cc.find("Canvas/beijing/zhuozi/xiaoxi/ddwjzb").active = false;
            cc.find("Canvas/beijing/button/zhunbei").active = true;
            cc.find("Canvas/beijing/button/quxiao").active = true;
          } else {
            cc.find("Canvas/beijing/zhuozi/xiaoxi/qingzhunbei").active = false;
            cc.find("Canvas/beijing/button/zhunbei").active = false;
            cc.find("Canvas/beijing/button/quxiao").active = false;
            cc.find("Canvas/beijing/zhuozi/xiaoxi/ddwjzb").active = true;
          }
          break;

         case "firstfapai":
          cc.find("Canvas/beijing/zhuozi/xiaoxi/qingzhunbei").active = false;
          cc.find("Canvas/beijing/button/zhunbei").active = false;
          cc.find("Canvas/beijing/button/quxiao").active = false;
          this.daojishi(Msg.d.time - 1);
          for (var _key3 in this.nodeinfo) true == this.nodeinfo[_key3].isSelect && (cc.find("renwu/zhunbei", this.nodeinfo[_key3].NodeInfo).active = false);
          for (var k in this.nodeinfo) if (true == this.nodeinfo[k].isSelect) for (var _key4 in Msg.d.pai) if (THIS.nodeinfo[k].UserInfo.uid == THIS._MyUserID && _key4 == THIS._MyUserID) {
            var node = cc.find("pai/bei", this.nodeinfo[k].NodeInfo);
            node.x = 300.9;
            node.y = 186;
            var x = cc.find("pai/1", this.nodeinfo[k].NodeInfo).x;
            var y = cc.find("pai/1", this.nodeinfo[k].NodeInfo).y;
            node.active = true;
            node.scaleX = 1;
            node.runAction(cc.moveTo(.5, x, y));
          } else if (THIS.nodeinfo[k].UserInfo.uid == _key4 && THIS.nodeinfo[k].UserInfo.uid != THIS._MyUserID) {
            var x = cc.find("pai/1", this.nodeinfo[k].NodeInfo).x;
            var y = cc.find("pai/1", this.nodeinfo[k].NodeInfo).y;
            var node = cc.find("pai/bei", this.nodeinfo[k].NodeInfo);
            node.active = true;
            node.scaleX = 1;
            node.runAction(cc.moveTo(.5, x, y));
          }
          THIS.scheduleOnce(function() {
            for (var _k in this.nodeinfo) if (true == this.nodeinfo[_k].isSelect) for (var _key5 in Msg.d.pai) _key5 == THIS._MyUserID && THIS.nodeinfo[_k].UserInfo.uid == THIS._MyUserID && THIS.fanpail(cc.find("pai/bei", THIS.nodeinfo[_k].NodeInfo), cc.find("pai/1", THIS.nodeinfo[_k].NodeInfo), Msg.d.pai[_key5]);
          }, .5);
          if (2 == Msg.d.gameState) {
            for (var _k2 in this.nodeinfo) if (true == this.nodeinfo[_k2].isSelect) for (var _key6 in Msg.d.pai) _key6 == this._MyUserID && (cc.find("Canvas/beijing/qiangzhuang").active = true);
          } else this.canbetaa(Msg, Msg.d.anniu);
          break;

         case "j":
          THIS.addNewUser(Msg.d);
          break;

         case "secondfapai":
          this.daojishi(Msg.d.time - 3);
          for (var _k3 in this.nodeinfo) for (var _key7 in Msg.d.paixin) if (true == this.nodeinfo[_k3].isSelect && THIS.nodeinfo[_k3].UserInfo.uid == _key7) if (parseInt(Msg.d.paixin[_key7]) > 10) {
            cc.find("lvkuang", THIS.nodeinfo[_k3].NodeInfo).getComponent(cc.Sprite).spriteFrame = this.DIANSHU.getSpriteFrame("zikuang");
            cc.find("lvkuang/6", THIS.nodeinfo[_k3].NodeInfo).getComponent(cc.Sprite).spriteFrame = this.DIANSHU.getSpriteFrame(Msg.d.paixin[_key7]);
          } else {
            cc.find("lvkuang", THIS.nodeinfo[_k3].NodeInfo).getComponent(cc.Sprite).spriteFrame = this.DIANSHU.getSpriteFrame("lvkuang2@2x");
            cc.find("lvkuang/6", THIS.nodeinfo[_k3].NodeInfo).getComponent(cc.Sprite).spriteFrame = this.DIANSHU.getSpriteFrame(Msg.d.paixin[_key7]);
          }
          var _loop = function _loop(_key8) {
            var _loop2 = function _loop2(_k4) {
              if (true == _this.nodeinfo[_k4].isSelect) {
                if (_key8 != THIS._MyUserID && THIS.nodeinfo[_k4].UserInfo.uid != THIS._MyUserID) {
                  if (THIS.nodeinfo[_k4].UserInfo.uid == _key8 && "" != Msg.d.pai[_key8]) {
                    cc.find("pai/bei", THIS.nodeinfo[_k4].NodeInfo).getComponent(cc.Animation).play("fanpai2");
                    THIS.scheduleOnce(function() {
                      for (var _k5 in THIS.nodeinfo) {
                        var x = cc.find("pai/bei1", THIS.nodeinfo[_k5].NodeInfo).x;
                        var y = cc.find("pai/bei1", THIS.nodeinfo[_k5].NodeInfo).y;
                        cc.find("pai/bei", THIS.nodeinfo[_k5].NodeInfo).x = x;
                        cc.find("pai/bei", THIS.nodeinfo[_k5].NodeInfo).y = y;
                      }
                    }, .28);
                    THIS.scheduleOnce(function() {
                      cc.find("pai/bei", THIS.nodeinfo[_k4].NodeInfo).active = false;
                      cc.find("pai/1", THIS.nodeinfo[_k4].NodeInfo).getComponent(cc.Sprite).spriteFrame = this.PUKE.getSpriteFrame(Msg.d.pai[_key8][0]);
                      cc.find("pai/1", THIS.nodeinfo[_k4].NodeInfo).active = true;
                      cc.find("pai/1", THIS.nodeinfo[_k4].NodeInfo).getComponent(cc.Animation).play("fanpai1");
                      THIS.scheduleOnce(function() {
                        var nod3 = cc.find("pai/bei1", THIS.nodeinfo[_k4].NodeInfo);
                        nod3.active = true;
                        nod3.scaleX = 1;
                        var x = cc.find("pai/2", this.nodeinfo[_k4].NodeInfo).x;
                        var y = cc.find("pai/2", this.nodeinfo[_k4].NodeInfo).y;
                        nod3.runAction(cc.moveTo(.3, x, y));
                      }, .2);
                    }, .3);
                  }
                  THIS.scheduleOnce(function() {
                    if (THIS.nodeinfo[_k4].UserInfo.uid == _key8) if (true == THIS.getNode(_key8).yifanpai) THIS.getNode(_key8).yifanpai = false; else {
                      THIS.scheduleOnce(function() {
                        cc.SY.yingxiaopurl("sounds/point" + Msg.d.paixin[_key8]);
                      }, 3);
                      THIS.fanpail(cc.find("pai/bei1", THIS.getNode(_key8).NodeInfo), cc.find("pai/2", THIS.getNode(_key8).NodeInfo), Msg.d.pai[_key8][1]);
                    }
                  }, Msg.d.time - 1);
                }
                if (_key8 == THIS._MyUserID && THIS.nodeinfo[_k4].UserInfo.uid == THIS._MyUserID) {
                  _this.myselfpaixing = Msg.d.paixin[_key8];
                  THIS.scheduleOnce(function() {
                    cc.find("pai/mask/3", THIS.nodeinfo[_k4].NodeInfo).getComponent(cc.Sprite).spriteFrame = this.PUKE.getSpriteFrame(Msg.d.pai[_key8][1]);
                    cc.find("pai/mask/31", THIS.nodeinfo[_k4].NodeInfo).getComponent(cc.Sprite).spriteFrame = this.PUKE.getSpriteFrame(Msg.d.pai[_key8][1]);
                    cc.find("pai/2", THIS.nodeinfo[_k4].NodeInfo).getComponent(cc.Sprite).spriteFrame = this.PUKE.getSpriteFrame(Msg.d.pai[_key8][1]);
                    cc.find("pai/mask/3", THIS.nodeinfo[_k4].NodeInfo).active = true;
                    cc.find("pai/mask/31", THIS.nodeinfo[_k4].NodeInfo).active = true;
                    cc.find("pai/mask/4", THIS.nodeinfo[_k4].NodeInfo).active = true;
                    cc.find("pai/41", THIS.nodeinfo[_k4].NodeInfo).active = true;
                  }, .3);
                  THIS.scheduleOnce(function() {
                    var x = cc.find("pai/bei1", THIS.nodeinfo[_k4].NodeInfo).x;
                    var y = cc.find("pai/bei1", THIS.nodeinfo[_k4].NodeInfo).y;
                    cc.find("pai/bei", THIS.nodeinfo[_k4].NodeInfo).x = x;
                    cc.find("pai/bei", THIS.nodeinfo[_k4].NodeInfo).y = y;
                  }, .58);
                  THIS.scheduleOnce(function() {
                    if (this.yifanzhuan) ; else {
                      cc.find("pai/mask/4", THIS.getNode(_key8).NodeInfo).active = false;
                      cc.find("pai/41", THIS.getNode(_key8).NodeInfo).active = false;
                      cc.find("pai/mask/31", THIS.getNode(_key8).NodeInfo).active = false;
                      cc.find("pai/mask/3", THIS.getNode(_key8).NodeInfo).active = false;
                      THIS.scheduleOnce(function() {
                        cc.SY.yingxiaopurl("sounds/point" + Msg.d.paixin[_key8]);
                      }, 3);
                      THIS.fanpail(cc.find("pai/bei1", THIS.nodeinfo[_k4].NodeInfo), cc.find("pai/2", THIS.nodeinfo[_k4].NodeInfo), Msg.d.pai[_key8][1]);
                    }
                    THIS.scheduleOnce(function() {
                      cc.find("pai/mask/3", THIS.nodeinfo[_k4].NodeInfo).active = false;
                      cc.find("pai/mask/31", THIS.nodeinfo[_k4].NodeInfo).active = false;
                      cc.find("pai/mask/4", THIS.nodeinfo[_k4].NodeInfo).active = false;
                      cc.find("pai/41", THIS.nodeinfo[_k4].NodeInfo).active = false;
                      this.yifanzhuan = false;
                    }, 3);
                  }, Msg.d.time - 1);
                  THIS.scheduleOnce(function() {
                    cc.find("pai/41", THIS.nodeinfo[_k4].NodeInfo).active = false;
                  }, Msg.d.time - 3);
                }
              }
            };
            for (var _k4 in _this.nodeinfo) _loop2(_k4);
          };
          for (var _key8 in Msg.d.pai) _loop(_key8);
          break;

         case "allbet":
          cc.find("Canvas/beijing/zhuozi/xiaoxi/xianjiakaishixiazhu").active = false;
          cc.find("Canvas/beijing/zhuozi/xiaoxi/zhengzaikaipai").active = true;
          for (var index = 1; index <= 8; index++) cc.find("Canvas/beijing/beilv/beilv1/" + index).active = false;
          cc.find("Canvas/beijing/beilv/beilv1/10").active = false;
          break;

         case "myqz":
          parseInt(Msg.d.qzbei) > 0 ? cc.SY.yingxiaopurl("sounds/grabwoqiang") : "0" == Msg.d.qzbei && cc.SY.yingxiaopurl("sounds/grabbuqiang");
          this._MyUserID == Msg.d.uid && (cc.find("Canvas/beijing/qiangzhuang").active = false);
          for (var _key9 in this.nodeinfo) true == this.nodeinfo[_key9].isSelect && this.nodeinfo[_key9].UserInfo.uid == Msg.d.uid && ("0" == Msg.d.qzbei ? cc.find("qiangz/1", this.nodeinfo[_key9].NodeInfo).active = true : "1" == Msg.d.qzbei ? cc.find("qiangz/2", this.nodeinfo[_key9].NodeInfo).active = true : "2" == Msg.d.qzbei ? cc.find("qiangz/3", this.nodeinfo[_key9].NodeInfo).active = true : "3" == Msg.d.qzbei ? cc.find("qiangz/4", this.nodeinfo[_key9].NodeInfo).active = true : "4" == Msg.d.qzbei && (cc.find("qiangz/5", this.nodeinfo[_key9].NodeInfo).active = true));
          break;

         case "mybet":
          for (var _key10 in this.nodeinfo) if (true == this.nodeinfo[_key10].isSelect) {
            if (this.nodeinfo[_key10].UserInfo.uid == Msg.d.uid && Msg.d.uid == this._MyUserID) {
              cc.tupian(cc.find("renwu/10bei", this.nodeinfo[_key10].NodeInfo), this.XIAZHUBS.getSpriteFrame(Msg.d.bet));
              cc.find("renwu/10bei", this.nodeinfo[_key10].NodeInfo).active = true;
              for (var _index = 1; _index <= 8; _index++) cc.find("Canvas/beijing/beilv/beilv1/" + _index).active = false;
              cc.find("Canvas/beijing/beilv/beilv1/10").active = false;
            }
            if (this.nodeinfo[_key10].UserInfo.uid == Msg.d.uid) {
              cc.tupian(cc.find("renwu/10bei", this.nodeinfo[_key10].NodeInfo), this.XIAZHUBS.getSpriteFrame(Msg.d.bet));
              cc.find("renwu/10bei", this.nodeinfo[_key10].NodeInfo).active = true;
            }
          }
          break;

         case "kk":
          THIS.f_KaiShi(Msg.d);
          break;

         case "canbet":
          this.daojishi(parseInt(Msg.d.time) - 1);
          this.canbetaa(Msg, Msg.d.betcm);
          break;

         case "qzt":
          THIS.f_SetQiangZhuang(Msg.d);
          break;

         case "yz":
          THIS.f_SetZhuangBei(Msg.d);
          break;

         case "tuijian":
          1 == THIS.CUOPAI ? THIS.TUIJIANPAI = Msg.d : THIS.scheduleOnce(function() {
            THIS.SetTuiJian(Msg.d);
          }, 1.2);
          break;

         case "tj":
          this.daojishi(Msg.d.time);
          this.gaibianle = false;
          this.num1 = 0;
          this.qishu = Msg.d.qishu;
          cc.wenzi(cc.find("Canvas/beijing/wenzi/jushu/label"), Msg.d.qishu + "/" + Msg.d.allqishu);
          false == Msg.d.iswan ? this.iswanguo = 1 : true == Msg.d.iswan && (this.iswanguo = 2);
          var _loop3 = function _loop3(_k6) {
            var _loop4 = function _loop4(_key12) {
              if (true == _this.nodeinfo[_k6].isSelect) {
                nod4 = cc.find("lvkuang", THIS.nodeinfo[_k6].NodeInfo);
                nod5 = cc.find("lvkuang/6", THIS.nodeinfo[_k6].NodeInfo);
                _key12 == _this._MyUserID && 1 == Msg.d.dangju[_key12].xiazhuang && (cc.find("Canvas/beijing/zhuozi/my/renwu/xiazhuang").active = true);
                if (THIS.nodeinfo[_k6].UserInfo.uid == _key12) if (parseInt(Msg.d.dangju[_key12].paixin) > 10) {
                  nod4.getComponent(cc.Sprite).spriteFrame = _this.DIANSHU.getSpriteFrame("zikuang");
                  nod5.getComponent(cc.Sprite).spriteFrame = _this.DIANSHU.getSpriteFrame(Msg.d.dangju[_key12].paixin);
                  nod4.active = true;
                } else {
                  nod4.getComponent(cc.Sprite).spriteFrame = _this.DIANSHU.getSpriteFrame("lvkuang2@2x");
                  nod5.getComponent(cc.Sprite).spriteFrame = _this.DIANSHU.getSpriteFrame(Msg.d.dangju[_key12].paixin);
                  nod4.active = true;
                }
              }
              THIS.scheduleOnce(function() {
                var nod4 = cc.find("lvkuang", THIS.nodeinfo[_k6].NodeInfo);
                nod4.active = false;
                if (THIS.nodeinfo[_k6].UserInfo.uid == _key12) if (parseInt(Msg.d.dangju[_key12].fen) > 0) {
                  cc.find("renwu/xiaoshengli", THIS.nodeinfo[_k6].NodeInfo).active = true;
                  cc.find("renwu/xiaoshengli", THIS.nodeinfo[_k6].NodeInfo).getComponent(cc.Animation).play("shengli");
                  cc.find("shuying/ying", THIS.nodeinfo[_k6].NodeInfo).active = true;
                  cc.wenzi(cc.find("shuying/ying", THIS.nodeinfo[_k6].NodeInfo), "+" + parseInt(Msg.d.dangju[_key12].fen));
                  _key12 != Msg.d.zhuang && THIS.scheduleOnce(function() {
                    cc.MAN.feijibi(Msg.d.zhuang, _key12, _key12);
                    cc.SY.yingxiaopurl("sounds/feijinbi");
                  }, 1);
                } else {
                  cc.find("shuying/shu", THIS.nodeinfo[_k6].NodeInfo).active = true;
                  cc.wenzi(cc.find("shuying/shu", THIS.nodeinfo[_k6].NodeInfo), parseInt(Msg.d.dangju[_key12].fen));
                  if (_key12 != Msg.d.zhuang) {
                    cc.MAN.feijibi(_key12, Msg.d.zhuang, _key12);
                    cc.SY.yingxiaopurl("sounds/feijinbi");
                  }
                }
                THIS.scheduleOnce(function() {
                  cc.find("Canvas/beijing/jbyzt").removeAllChildren();
                }, 2);
              }, 3);
              THIS.scheduleOnce(function() {
                var nod4 = cc.find("lvkuang", THIS.nodeinfo[_k6].NodeInfo);
                nod4.active = false;
                parseInt(Msg.d.dangju[_key12].fen) > 0 ? cc.find("shuying/ying", THIS.nodeinfo[_k6].NodeInfo).runAction(cc.spawn(cc.fadeOut(2), cc.moveBy(2, 0, 40))) : cc.find("shuying/shu", THIS.nodeinfo[_k6].NodeInfo).runAction(cc.spawn(cc.fadeOut(2), cc.moveBy(2, 0, 40)));
                THIS.scheduleOnce(function() {
                  if (parseInt(Msg.d.dangju[_key12].fen) > 0) {
                    cc.find("shuying/ying", THIS.nodeinfo[_k6].NodeInfo).opacity = 255;
                    cc.find("shuying/ying", THIS.nodeinfo[_k6].NodeInfo).active = false;
                    cc.find("shuying/ying", THIS.nodeinfo[_k6].NodeInfo).y = -35;
                    cc.find("shuying/shu", THIS.nodeinfo[_k6].NodeInfo).y = -35;
                  } else {
                    cc.find("shuying/shu", THIS.nodeinfo[_k6].NodeInfo).opacity = 255;
                    cc.find("shuying/shu", THIS.nodeinfo[_k6].NodeInfo).active = false;
                    cc.find("shuying/shu", THIS.nodeinfo[_k6].NodeInfo).y = -35;
                    cc.find("shuying/ying", THIS.nodeinfo[_k6].NodeInfo).y = -35;
                  }
                }, 4);
                for (var key2 in THIS.nodeinfo) for (var _k8 in Msg.d.Auserinfo) if (THIS.nodeinfo[key2].UserInfo.uid == Msg.d.Auserinfo[_k8].uid) {
                  var nod1 = cc.find("pai/bei", THIS.nodeinfo[key2].NodeInfo);
                  var nod2 = cc.find("pai/bei1", THIS.nodeinfo[key2].NodeInfo);
                  nod2.x = nod1.x;
                  nod2.y = nod1.y;
                }
                cc.find("pai/1", THIS.nodeinfo[_k6].NodeInfo).active = false;
                cc.find("pai/2", THIS.nodeinfo[_k6].NodeInfo).active = false;
                cc.find("renwu/xiaoshengli", THIS.nodeinfo[_k6].NodeInfo).active = false;
              }, 5);
            };
            for (var _key12 in Msg.d.dangju) _loop4(_key12);
          };
          for (var _k6 in this.nodeinfo) {
            var nod4;
            var nod5;
            _loop3(_k6);
          }
          this.scheduleOnce(function() {
            cc.find("Canvas/beijing/zhuozi/xiaoxi/qingzhunbei").active = true;
            cc.find("Canvas/beijing/zhuozi/xiaoxi/zhengzaikaipai").active = false;
            cc.find("Canvas/beijing/button/zhunbei").active = true;
            cc.find("Canvas/beijing/button/quxiao").active = true;
            cc.find("Canvas/beijing/top/tuichu").active = true;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask").width = 124;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask").height = 190;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask").x = 422;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask").y = 196;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/3").scale = 1;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/3").height = 190;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/3").width = 124;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/3").x = -193;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/3").y = -97;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/31").scale = 1;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/31").height = 190;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/31").width = 124;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/31").x = -62;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/31").y = -292;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/4").x = -66;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/4").y = -94.3;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/4").height = 190;
            cc.find("Canvas/beijing/zhuozi/my/pai/mask/4").width = 124;
            for (var _key11 in this.nodeinfo) if (true == this.nodeinfo[_key11].isSelect) {
              for (var _k7 in Msg.d.Auserinfo) Msg.d.Auserinfo[_k7].uid == this.nodeinfo[_key11].UserInfo.uid && cc.wenzi(cc.find("shenyujine/label", this.nodeinfo[_key11].NodeInfo), Msg.d.Auserinfo[_k7].zf);
              cc.find("renwu/10bei", this.nodeinfo[_key11].NodeInfo).active = false;
              cc.find("renwu/zhuang", this.nodeinfo[_key11].NodeInfo).active = false;
              cc.find("pai/bei", this.nodeinfo[_key11].NodeInfo).active = false;
              cc.find("pai/bei1", this.nodeinfo[_key11].NodeInfo).active = false;
              cc.find("pai/2", this.nodeinfo[_key11].NodeInfo).active = false;
              cc.find("pai/1", this.nodeinfo[_key11].NodeInfo).active = false;
              this.iskaishi = 2;
            }
            for (var key1 in Msg.d.online) 0 == Msg.d.online[key1] ? cc.find("renwu/likai", this.getNode(key1).NodeInfo) && (cc.find("renwu/likai", this.getNode(key1).NodeInfo).active = true) : cc.find("renwu/likai", this.getNode(key1).NodeInfo) && (cc.find("renwu/likai", this.getNode(key1).NodeInfo).active = false);
          }, Msg.d.time);
          break;

         case "ztj":
          var _loop5 = function _loop5(_k9) {
            var _loop6 = function _loop6(_key15) {
              if (true == _this.nodeinfo[_k9].isSelect) {
                nod4 = cc.find("lvkuang", THIS.nodeinfo[_k9].NodeInfo);
                nod5 = cc.find("lvkuang/6", THIS.nodeinfo[_k9].NodeInfo);
                if (THIS.nodeinfo[_k9].UserInfo.uid == Msg.d.dangju[_key15].uid && false == cc.find("renwu/likai", THIS.nodeinfo[_k9].NodeInfo).active && false == cc.find("renwu/guanzhan", THIS.nodeinfo[_k9].NodeInfo).active) if (parseInt(Msg.d.dangju[_key15].paixin) > 10) {
                  nod4.getComponent(cc.Sprite).spriteFrame = _this.DIANSHU.getSpriteFrame("zikuang");
                  nod5.getComponent(cc.Sprite).spriteFrame = _this.DIANSHU.getSpriteFrame(Msg.d.dangju[_key15].paixin);
                  nod4.active = true;
                } else {
                  nod4.getComponent(cc.Sprite).spriteFrame = _this.DIANSHU.getSpriteFrame("lvkuang2@2x");
                  nod5.getComponent(cc.Sprite).spriteFrame = _this.DIANSHU.getSpriteFrame(Msg.d.dangju[_key15].paixin);
                  nod4.active = true;
                }
              }
              THIS.scheduleOnce(function() {
                var nod4 = cc.find("lvkuang", THIS.nodeinfo[_k9].NodeInfo);
                nod4.active = false;
                if (THIS.nodeinfo[_k9].UserInfo.uid == Msg.d.dangju[_key15].uid && false == cc.find("renwu/likai", THIS.nodeinfo[_k9].NodeInfo).active && false == cc.find("renwu/guanzhan", THIS.nodeinfo[_k9].NodeInfo).active) if (parseInt(Msg.d.dangju[_key15].fen) > 0) {
                  cc.find("renwu/xiaoshengli", THIS.nodeinfo[_k9].NodeInfo).y = 23.6;
                  cc.find("renwu/xiaoshengli", THIS.nodeinfo[_k9].NodeInfo).active = true;
                  cc.find("renwu/xiaoshengli", THIS.nodeinfo[_k9].NodeInfo).getComponent(cc.Animation).play("shengli");
                  cc.find("shuying/ying", THIS.nodeinfo[_k9].NodeInfo).active = true;
                  cc.wenzi(cc.find("shuying/ying", THIS.nodeinfo[_k9].NodeInfo), "+" + parseInt(Msg.d.dangju[_key15].fen));
                  Msg.d.dangju[_key15].uid != Msg.d.zhuang && THIS.scheduleOnce(function() {
                    cc.MAN.feijibi(Msg.d.zhuang, Msg.d.dangju[_key15].uid, Msg.d.dangju[_key15].uid);
                    cc.SY.yingxiaopurl("sounds/feijinbi");
                  }, 1);
                } else {
                  cc.find("shuying/shu", THIS.nodeinfo[_k9].NodeInfo).active = true;
                  cc.wenzi(cc.find("shuying/shu", THIS.nodeinfo[_k9].NodeInfo), parseInt(Msg.d.dangju[_key15].fen));
                  if (Msg.d.dangju[_key15].uid != Msg.d.zhuang) {
                    cc.MAN.feijibi(Msg.d.dangju[_key15].uid, Msg.d.zhuang, Msg.d.dangju[_key15].uid);
                    cc.SY.yingxiaopurl("sounds/feijinbi");
                  }
                }
              }, 3);
              THIS.scheduleOnce(function() {
                var nod4 = cc.find("lvkuang", THIS.nodeinfo[_k9].NodeInfo);
                nod4.active = false;
                parseInt(Msg.d.dangju[_key15].fen) > 0 ? cc.find("shuying/ying", THIS.nodeinfo[_k9].NodeInfo).runAction(cc.spawn(cc.fadeOut(2), cc.moveBy(2, 0, 40))) : cc.find("shuying/shu", THIS.nodeinfo[_k9].NodeInfo).runAction(cc.spawn(cc.fadeOut(2), cc.moveBy(2, 0, 40)));
              }, 5);
            };
            for (var _key15 in Msg.d.dangju) _loop6(_key15);
          };
          for (var _k9 in this.nodeinfo) {
            var nod4;
            var nod5;
            _loop5(_k9);
          }
          THIS.scheduleOnce(function() {
            cc.find("Canvas/beijing/paijujieshu/k/scrollview/view/content").removeAllChildren();
            cc.wenzi(cc.find("Canvas/beijing/paijujieshu/k/biaotl/jushu"), Msg.d.allqishu + "局");
            cc.wenzi(cc.find("Canvas/beijing/paijujieshu/k/biaotl/fjh/fjhzhi"), cc.MAN.roomid);
            for (var _key13 in Msg.d.dangju) {
              var node = cc.instantiate(this.kaijiangjieguo);
              node.parent = cc.find("Canvas/beijing/paijujieshu/k/scrollview/view/content");
              node.group = "defult";
              node.active = true;
              node.name = "yuzhiti" + _key13;
              if (Msg.d.dangju[_key13].AllTongji > 0) {
                0 == _key13 && (cc.find("Canvas/beijing/paijujieshu/k/scrollview/view/content/" + node.name + "/ying").active = true);
                Msg.d.dangju[_key13].name.length > 7 ? cc.wenzi(cc.find("Canvas/beijing/paijujieshu/k/scrollview/view/content/" + node.name + "/xinxi"), "(" + Msg.d.dangju[_key13].uid + ")" + Msg.d.dangju[_key13].name.substring(0, 6) + "..") : cc.wenzi(cc.find("Canvas/beijing/paijujieshu/k/scrollview/view/content/" + node.name + "/xinxi"), "(" + Msg.d.dangju[_key13].uid + ")" + Msg.d.dangju[_key13].name);
                cc.wenzi(cc.find("Canvas/beijing/paijujieshu/k/scrollview/view/content/" + node.name + "/fenshu"), "+" + Msg.d.dangju[_key13].AllTongji);
                cc.find("Canvas/beijing/paijujieshu/k/scrollview/view/content/" + node.name + "/fenshu").color = cc.Color.YELLOW;
                cc.find("Canvas/beijing/paijujieshu/k/scrollview/view/content/" + node.name + "/xinxi").color = cc.Color.YELLOW;
              } else {
                Msg.d.dangju[_key13].name.length > 7 ? cc.wenzi(cc.find("Canvas/beijing/paijujieshu/k/scrollview/view/content/" + node.name + "/xinxi"), "(" + Msg.d.dangju[_key13].uid + ")" + Msg.d.dangju[_key13].name.substring(0, 6) + "..") : cc.wenzi(cc.find("Canvas/beijing/paijujieshu/k/scrollview/view/content/" + node.name + "/xinxi"), "(" + Msg.d.dangju[_key13].uid + ")" + Msg.d.dangju[_key13].name);
                cc.wenzi(cc.find("Canvas/beijing/paijujieshu/k/scrollview/view/content/" + node.name + "/fenshu"), Msg.d.dangju[_key13].AllTongji);
              }
            }
            cc.find("Canvas/beijing/paijujieshu").active = true;
            for (var _key14 in this.nodeinfo) if (true == this.nodeinfo[_key14].isSelect) for (var _k10 in Msg.d.Auserinfo) Msg.d.Auserinfo[_k10].uid == this.nodeinfo[_key14].UserInfo.uid && cc.wenzi(cc.find("shenyujine/label", this.nodeinfo[_key14].NodeInfo), Msg.d.Auserinfo[_k10].zf);
          }, 5);
          break;

         case "peipai":
          if (1 * Msg.d.u == 1 * THIS._MyUserID) {
            if (THIS.FNAPMMMTIME) {
              THIS.unschedule(THIS.FNAPMMMTIME);
              THIS.FNAPMMMTIME = null;
            }
            cc.find("Canvas/Content/Alert/CuoPaiMB").active = false;
            cc.find("CuoPai", THIS.Control).active = false;
          }
          THIS.f_showPeiPai(Msg.d);
          break;

         case "zbm":
          cc.find("CardList", THIS.Control).active = false;
          cc.find("Canvas/Content/Alert/CuoPaiMB").active = false;
          cc.find("CuoPai", THIS.Control).active = false;
          cc.find("PeiPai", THIS.Control).active = false;
          THIS.FANPAI = false;
          THIS.CUODATA = [];
          THIS.f_AuZhunBei(Msg.d);
          break;

         case "exitxz":
          cc.wenzi(cc.find("Canvas/beijing/shenqjs/namefaqi"), Msg.d.Auserinfo[Msg.d.uid].name + "发起解散请求");
          THIS.StartVote(Msg.d.allState);
          break;

         case "exitts":
          THIS.updateDissolve(Msg.d);
          break;

         case "allexit":
          THIS.Exit(Msg.d);
          break;

         case "end":
          cc.find("Canvas/beijing/paijujieshu/k/scrollview/view/content").removeAllChildren();
          cc.wenzi(cc.find("Canvas/beijing/paijujieshu/k/biaotl/jushu"), Msg.d.allqishu + "局");
          cc.wenzi(cc.find("Canvas/beijing/paijujieshu/k/biaotl/fjh/fjhzhi"), cc.MAN.roomid);
          for (var _key16 in Msg.d.dangju) {
            var node = cc.instantiate(this.kaijiangjieguo);
            node.parent = cc.find("Canvas/beijing/paijujieshu/k/scrollview/view/content");
            node.group = "defult";
            node.active = true;
            node.name = "yuzhiti" + _key16;
            if (data.dangju[_key16].AllTongji > 0) {
              0 == _key16 && (cc.find("Canvas/beijing/paijujieshu/k/scrollview/view/content/" + node.name + "/ying").active = true);
              data.dangju[_key16].name.length > 7 ? cc.wenzi(cc.find("Canvas/beijing/paijujieshu/k/scrollview/view/content/" + node.name + "/xinxi"), "(" + data.dangju[_key16].uid + ")" + data.dangju[_key16].name.substring(0, 6) + "..") : cc.wenzi(cc.find("Canvas/beijing/paijujieshu/k/scrollview/view/content/" + node.name + "/xinxi"), "(" + data.dangju[_key16].uid + ")" + data.dangju[_key16].name);
              cc.wenzi(cc.find("Canvas/beijing/paijujieshu/k/scrollview/view/content/" + node.name + "/fenshu"), "+" + data.dangju[_key16].AllTongji);
              cc.find("Canvas/beijing/paijujieshu/k/scrollview/view/content/" + node.name + "/fenshu").color = cc.Color.YELLOW;
              cc.find("Canvas/beijing/paijujieshu/k/scrollview/view/content/" + node.name + "/xinxi").color = cc.Color.YELLOW;
            } else {
              data.dangju[_key16].name.length > 7 ? cc.wenzi(cc.find("Canvas/beijing/paijujieshu/k/scrollview/view/content/" + node.name + "/xinxi"), "(" + data.dangju[_key16].uid + ")" + data.dangju[_key16].name.substring(0, 6) + "..") : cc.wenzi(cc.find("Canvas/beijing/paijujieshu/k/scrollview/view/content/" + node.name + "/xinxi"), "(" + data.dangju[_key16].uid + ")" + data.dangju[_key16].name);
              cc.wenzi(cc.find("Canvas/beijing/paijujieshu/k/scrollview/view/content/" + node.name + "/fenshu"), data.dangju[_key16].AllTongji);
            }
          }
          cc.find("Canvas/beijing/paijujieshu").active = true;
          for (var _key17 in this.nodeinfo) if (true == this.nodeinfo[_key17].isSelect) for (var _k11 in Msg.d.Auserinfo) Msg.d.Auserinfo[_k11].uid == this.nodeinfo[_key17].UserInfo.uid && cc.wenzi(cc.find("shenyujine/label", this.nodeinfo[_key17].NodeInfo), Msg.d.Auserinfo[_k11].zf);
          break;

         case "quite":
          cc.SY.yingxiaop(2);
          this.OutUser(Msg.d);
          break;

         case "WangYatxt":
          THIS.ShowText(Msg.d);
          break;

         case "WangYa":
          THIS._voiceMsgQueue.push(Msg.d);
          THIS.VoiceShow();
          break;

         case "msg":
          cc.Msgbox.show(Msg.d);
        }
      },
      canbetaa: function canbetaa(Msg, anniu) {
        var _this2 = this;
        cc.find("Canvas/beijing/zhuozi/xiaoxi/yxjjks").active = false;
        cc.find("Canvas/beijing/qiangzhuang").active = false;
        cc.find("Canvas/beijing/zhuozi/xiaoxi/xianjiakaishixiazhu").active = true;
        var _loop7 = function _loop7(key) {
          if (true == _this2.nodeinfo[key].isSelect) {
            cc.find("qiangz/1", _this2.nodeinfo[key].NodeInfo).active = false;
            cc.find("qiangz/2", _this2.nodeinfo[key].NodeInfo).active = false;
            cc.find("qiangz/3", _this2.nodeinfo[key].NodeInfo).active = false;
            cc.find("qiangz/4", _this2.nodeinfo[key].NodeInfo).active = false;
            cc.find("qiangz/5", _this2.nodeinfo[key].NodeInfo).active = false;
            cc.find("Canvas/beijing/zhuozi/zhuangyzt").x = 0;
            cc.find("Canvas/beijing/zhuozi/zhuangyzt").y = 65;
            _this2.nodeinfo[key].UserInfo.uid == Msg.d.zhuang && _this2.scheduleOnce(function() {
              cc.find("Canvas/beijing/zhuozi/zhuangyzt").active = true;
              Msg.d.zhuang == cc.User.uid ? cc.find("Canvas/beijing/zhuozi/zhuangyzt").runAction(cc.moveTo(.6, this.nodeinfo[key].NodeInfo.x + 80, this.nodeinfo[key].NodeInfo.y - 25)) : cc.find("Canvas/beijing/zhuozi/zhuangyzt").runAction(cc.moveTo(.6, this.nodeinfo[key].NodeInfo.x, this.nodeinfo[key].NodeInfo.y - 10));
            }, .1);
            _this2.scheduleOnce(function() {
              cc.find("Canvas/beijing/zhuozi/zhuangyzt").active = false;
              cc.find("Canvas/beijing/zhuozi/zhuangyzt").x = 0;
              cc.find("Canvas/beijing/zhuozi/zhuangyzt").y = 65;
              this.nodeinfo[key].UserInfo.uid == Msg.d.zhuang && (cc.find("renwu/zhuang", this.nodeinfo[key].NodeInfo).active = true);
            }, .8);
            if (Msg.d.zhuang == _this2._MyUserID) ; else if (0 == _this2.getNode(Msg.uid).UserInfo.isPangguan) for (var index = 0; index < anniu.length; index++) cc.find("Canvas/beijing/beilv/beilv1/" + anniu[index]).active = true;
          }
        };
        for (var key in this.nodeinfo) _loop7(key);
      },
      fanpail: function fanpail(nod1, nod2, paimian) {
        nod1.active = true;
        nod2.scaleX = 0;
        nod2.getComponent(cc.Sprite).spriteFrame = this.PUKE.getSpriteFrame(paimian);
        nod1.getComponent(cc.Animation).play("fanpai2");
        this.scheduleOnce(function() {
          nod2.active = true;
          nod2.getComponent(cc.Animation).play("fanpai1");
        }, .3);
      },
      getUserId: function getUserId(key) {
        var userinfo = this.nodeinfo[key].UserInfo;
        if (userinfo) return userinfo.u;
      },
      OutUser: function OutUser(data) {
        if (!data) return;
        if (data == cc.User.uid) {
          cc.Msgbox.show("即将返回大厅～");
          this.scheduleOnce(function() {
            this.replaceScene("dating");
          }, 3);
        } else {
          var nodedata = this.getNode(data);
          if (!nodedata) return;
          var node = nodedata.NodeInfo;
          if (node) {
            for (var s = 0; s < this.nodeinfo.length; s++) this.nodeinfo[s].UserInfo.uid == data && (this.nodeinfo[s].isSelect = false);
            node.active = false;
            nodedata.UserInfo = "";
          }
        }
      },
      initData: function initData(Msg) {
        var num = 0;
        for (var i = 0; i < Msg.d.udata.length; i++) if (parseInt(Msg.d.udata[i].uid) == parseInt(this._MyUserID)) {
          var len = this.nodeinfo.length - 1;
          this.nodeinfo[len].isSelect = true;
          this.nodeinfo[len]["UserInfo"] = Msg.d.udata[i];
        } else if (false == this.nodeinfo[num].isSelect) {
          this.nodeinfo[num].isSelect = true;
          this.nodeinfo[num]["UserInfo"] = Msg.d.udata[i];
          num++;
        }
        for (var key in this.nodeinfo) {
          true == this.nodeinfo[key].isSelect && this.initInfo(this.nodeinfo[key].NodeInfo, this.nodeinfo[key].UserInfo, this.nodeinfo[key].UserInfo.uid);
          0 == Msg.d.gameState && true == this.nodeinfo[key].isSelect && this.SetReady(this.nodeinfo[key].NodeInfo, this.nodeinfo[key].UserInfo.action);
          for (var index = 0; index < Msg.d.udata.length; index++) if (Msg.d.udata[index].uid == this._MyUserID && 0 == Msg.d.gameState) if (0 == Msg.d.udata[index].action) {
            cc.find("Canvas/beijing/zhuozi/xiaoxi/qingzhunbei").active = true;
            cc.find("Canvas/beijing/zhuozi/xiaoxi/ddwjzb").active = false;
            cc.find("Canvas/beijing/button/zhunbei").active = true;
            cc.find("Canvas/beijing/button/quxiao").active = true;
          } else {
            cc.find("Canvas/beijing/zhuozi/xiaoxi/qingzhunbei").active = false;
            cc.find("Canvas/beijing/button/zhunbei").active = false;
            cc.find("Canvas/beijing/button/quxiao").active = false;
            cc.find("Canvas/beijing/zhuozi/xiaoxi/ddwjzb").active = true;
          }
        }
        for (var k in Msg.d.udata) if (1 == Msg.d.udata[k].lx) {
          var uidd = Msg.d.udata[k].uid;
          cc.find("renwu/likai", THIS.getNode(uidd).NodeInfo).active = false;
          0 == Msg.d.udata[k].isPangguan ? cc.find("renwu/guanzhan", THIS.getNode(uidd).NodeInfo).active = false : cc.find("renwu/guanzhan", THIS.getNode(uidd).NodeInfo).active = true;
        } else cc.find("renwu/likai", THIS.getNode(uidd).NodeInfo).active = true;
        if (1 == Msg.d.gameState) for (var key in this.nodeinfo) {
          true == this.nodeinfo[key].isSelect;
          for (var _index2 = 0; _index2 < Msg.d.udata.length; _index2++) if (Msg.d.udata[_index2].uid == this._MyUserID && 0 == Msg.d.gameState) if (0 == Msg.d.udata[_index2].action) {
            cc.find("Canvas/beijing/zhuozi/xiaoxi/qingzhunbei").active = true;
            cc.find("Canvas/beijing/zhuozi/xiaoxi/ddwjzb").active = false;
            cc.find("Canvas/beijing/button/zhunbei").active = true;
            cc.find("Canvas/beijing/button/quxiao").active = true;
          } else {
            cc.find("Canvas/beijing/zhuozi/xiaoxi/qingzhunbei").active = false;
            cc.find("Canvas/beijing/button/zhunbei").active = false;
            cc.find("Canvas/beijing/button/quxiao").active = false;
            cc.find("Canvas/beijing/zhuozi/xiaoxi/ddwjzb").active = true;
          }
        }
        if (0 == Msg.d.gameState) {
          cc.find("Canvas/beijing/top/tuichu").active = true;
          cc.find("Canvas/beijing/zhuozi/xiaoxi/kaishiqiangzhuang").active = false;
        }
        if (2 == Msg.d.gameState) {
          cc.find("Canvas/beijing/zhuozi/xiaoxi/kaishiqiangzhuang").active = false;
          cc.find("Canvas/beijing/zhuozi/xiaoxi/qingzhunbei").active = false;
          cc.find("Canvas/beijing/zhuozi/xiaoxi/xianjiakaishixiazhu").active = false;
          cc.find("Canvas/beijing/zhuozi/xiaoxi/zhengzaikaipai").active = false;
          cc.find("Canvas/beijing/zhuozi/xiaoxi/ddwjzb").active = false;
          cc.find("Canvas/beijing/zhuozi/xiaoxi/yxjjks").active = true;
          for (var _key18 in this.nodeinfo) for (var _k12 in Msg.d.udata) if (0 == Msg.d.udata[_k12].isPangguan && true == this.nodeinfo[_key18].isSelect) {
            true == Msg.d.needqz && _k12 == this._MyUserID && (cc.find("Canvas/beijing/qiangzhuang").active = true);
            for (var key1 in Msg.d.gepai) if (key1 == this._MyUserID) {
              if (Msg.d.gepai[key1]) {
                cc.tupian(cc.find("Canvas/beijing/zhuozi/my/pai/1"), this.PUKE.getSpriteFrame(Msg.d.gepai[key1][0]));
                cc.find("Canvas/beijing/zhuozi/my/pai/1").active = true;
              }
            } else if (Msg.d.gepai[key1]) {
              cc.find("pai/bei", this.getNode(key1).NodeInfo).x = cc.find("pai/1", this.getNode(key1).NodeInfo).x;
              cc.find("pai/bei", this.getNode(key1).NodeInfo).y = cc.find("pai/1", this.getNode(key1).NodeInfo).y;
              cc.find("pai/bei", this.getNode(key1).NodeInfo).active = true;
            }
          }
        }
        if (3 == Msg.d.gameState) {
          cc.find("Canvas/beijing/zhuozi/xiaoxi/qingzhunbei").active = false;
          cc.find("Canvas/beijing/zhuozi/xiaoxi/xianjiakaishixiazhu").active = false;
          cc.find("Canvas/beijing/zhuozi/xiaoxi/zhengzaikaipai").active = false;
          cc.find("Canvas/beijing/zhuozi/xiaoxi/ddwjzb").active = false;
          cc.find("Canvas/beijing/zhuozi/xiaoxi/yxjjks").active = false;
          for (var _key19 in this.nodeinfo) for (var _k13 in Msg.d.udata) if (0 == Msg.d.udata[_k13].isPangguan && true == this.nodeinfo[_key19].isSelect) for (var _key20 in Msg.d.gepai) if (_key20 == this._MyUserID) {
            if (Msg.d.gepai[_key20]) {
              cc.tupian(cc.find("Canvas/beijing/zhuozi/my/pai/1"), this.PUKE.getSpriteFrame(Msg.d.gepai[_key20][0]));
              cc.find("Canvas/beijing/zhuozi/my/pai/1").active = true;
            }
          } else if (Msg.d.gepai[_key20]) {
            cc.find("pai/bei", this.getNode(_key20).NodeInfo).x = cc.find("pai/1", this.getNode(_key20).NodeInfo).x;
            cc.find("pai/bei", this.getNode(_key20).NodeInfo).y = cc.find("pai/1", this.getNode(_key20).NodeInfo).y;
            cc.find("pai/bei", this.getNode(_key20).NodeInfo).active = true;
          }
        }
        if (4 == Msg.d.gameState) {
          cc.find("Canvas/beijing/zhuozi/xiaoxi/xianjiakaishixiazhu").active = true;
          cc.find("Canvas/beijing/zhuozi/xiaoxi/kaishiqiangzhuang").active = false;
          cc.find("Canvas/beijing/zhuozi/xiaoxi/xianjiakaishixiazhu").active = false;
          cc.find("Canvas/beijing/zhuozi/xiaoxi/zhengzaikaipai").active = false;
          cc.find("Canvas/beijing/zhuozi/xiaoxi/ddwjzb").active = false;
          cc.find("Canvas/beijing/zhuozi/xiaoxi/yxjjks").active = false;
          for (var _key21 in this.nodeinfo) for (var _k14 in Msg.d.udata) if (0 == Msg.d.udata[_k14].isPangguan && true == this.nodeinfo[_key21].isSelect) {
            if (_k14 == this._MyUserID) for (var _index3 = 0; _index3 < Msg.d.Acmw.length; _index3++) cc.find("Canvas/beijing/beilv/beilv1/" + Msg.d.Acmw[_index3]).active = true;
            for (var _key22 in Msg.d.gepai) if (_key22 == this._MyUserID) {
              if (Msg.d.gepai[_key22] && Msg.d.gepai[_key22]) {
                cc.tupian(cc.find("Canvas/beijing/zhuozi/my/pai/1"), this.PUKE.getSpriteFrame(Msg.d.gepai[_key22][0]));
                cc.find("Canvas/beijing/zhuozi/my/pai/1").active = true;
              }
            } else if (Msg.d.gepai[_key22]) {
              cc.find("pai/bei", this.getNode(_key22).NodeInfo).x = cc.find("pai/1", this.getNode(_key22).NodeInfo).x;
              cc.find("pai/bei", this.getNode(_key22).NodeInfo).y = cc.find("pai/1", this.getNode(_key22).NodeInfo).y;
              cc.find("pai/bei", this.getNode(_key22).NodeInfo).active = true;
            }
          }
        }
        if (5 == Msg.d.gameState) {
          cc.find("Canvas/beijing/zhuozi/xiaoxi/xianjiakaishixiazhu").active = false;
          cc.find("Canvas/beijing/zhuozi/xiaoxi/kaishiqiangzhuang").active = false;
          cc.find("Canvas/beijing/zhuozi/xiaoxi/xianjiakaishixiazhu").active = false;
          cc.find("Canvas/beijing/zhuozi/xiaoxi/ddwjzb").active = false;
          cc.find("Canvas/beijing/zhuozi/xiaoxi/yxjjks").active = false;
          for (var _key23 in this.nodeinfo) for (var _k15 in Msg.d.udata) if (0 == Msg.d.udata[_k15].isPangguan && true == this.nodeinfo[_key23].isSelect) for (var _key24 in Msg.d.gepai) if (_key24 == this._MyUserID) {
            if (Msg.d.gepai[_key24]) {
              cc.tupian(cc.find("Canvas/beijing/zhuozi/my/pai/1"), this.PUKE.getSpriteFrame(Msg.d.gepai[_key24][0]));
              cc.tupian(cc.find("Canvas/beijing/zhuozi/my/pai/2"), this.PUKE.getSpriteFrame(Msg.d.gepai[_key24][1]));
              cc.find("Canvas/beijing/zhuozi/my/pai/1").active = true;
              cc.find("Canvas/beijing/zhuozi/my/pai/2").active = true;
            }
          } else if (Msg.d.gepai[_key24]) {
            cc.tupian(cc.find("pai/1", this.getNode(_key24).NodeInfo), this.PUKE.getSpriteFrame(Msg.d.gepai[_key24][0]));
            cc.find("pai/1", this.getNode(_key24).NodeInfo).active = true;
            cc.tupian(cc.find("pai/2", this.getNode(_key24).NodeInfo), this.PUKE.getSpriteFrame(Msg.d.gepai[_key24][1]));
            cc.find("pai/2", this.getNode(_key24).NodeInfo).active = true;
          }
        }
      },
      addNewUser: function addNewUser(newData) {
        for (var key in this.nodeinfo) if (this.nodeinfo[key].UserInfo.uid == newData.uid || newData.uid == this._MyUserID) {
          cc.find("renwu/guanzhan", this.nodeinfo[key].NodeInfo).active = false;
          cc.find("renwu/likai", this.nodeinfo[key].NodeInfo).active = false;
          return;
        }
        for (var i = 0; i < this.nodeinfo.length; i++) if (false == this.nodeinfo[i].isSelect) {
          this.nodeinfo[i].isSelect = true;
          this.nodeinfo[i].UserInfo = newData;
          this.initInfo(this.nodeinfo[i].NodeInfo, newData, this.nodeinfo[i].UserInfo.uid);
          break;
        }
      },
      SetReady: function SetReady(node, isZhunBei) {
        cc.find("renwu/zhunbei", node).active = 0 != isZhunBei;
      },
      initInfo: function initInfo(node, userInfo, uid) {
        1 == this.iskaishi ? cc.find("renwu/guanzhan", node).active = true : cc.find("renwu/guanzhan", node).active = false;
        userInfo.name.length > 5 && uid != cc.User.uid ? cc.wenzi(cc.find("renwu/name/label", node), userInfo.name.substring(0, 4) + "..") : cc.wenzi(cc.find("renwu/name/label", node), userInfo.name);
        userInfo.zf ? cc.wenzi(cc.find("shenyujine/label", node), this.GoldName(userInfo.zf)) : cc.wenzi(cc.find("shenyujine/label", node), 0);
        uid == this._MyUserID ? cc.pichttp(cc.find("touxiang", node), userInfo.touxiang, 88, 88) : cc.pichttp(cc.find("touxiang", node), userInfo.touxiang, 69, 68);
        node.active = true;
        cc.find("renwu/zhuang", node).active = false;
        cc.find("renwu/10bei", node).active = false;
        cc.find("renwu/zhunbei", node).active = false;
        cc.find("renwu/likai", node).active = false;
        cc.find("msgbox", node).active = false;
        cc.find("bang1", node).active = false;
      },
      GoldName: function GoldName(value) {
        if (value) {
          if (value < 1e5) return value;
          if (value >= 1e5 && value < 1e8) return (value / 1e4).toFixed(2) + "万";
          if (value >= 1e8) return (value / 1e8).toFixed(2) + "亿";
        }
      },
      zhuomianxx: function zhuomianxx(Msg) {
        if (1 == Msg.d.gametype) {
          cc.find("Canvas/beijing/wenzi/moshi/putonmoshi").active = true;
          cc.find("Canvas/beijing/wenzi/moshi/gudingzhuang").active = false;
          cc.find("Canvas/beijing/wenzi/szxz").active = false;
          cc.find("Canvas/beijing/wenzi/moshi/ziyouqiangzhuang").active = false;
        } else if (2 == Msg.d.gametype) {
          cc.find("Canvas/beijing/wenzi/szxz").active = true;
          cc.find("Canvas/beijing/wenzi/moshi/putonmoshi").active = false;
          cc.find("Canvas/beijing/wenzi/moshi/gudingzhuang").active = true;
          cc.find("Canvas/beijing/wenzi/moshi/ziyouqiangzhuang").active = false;
        } else if (3 == Msg.d.gametype) {
          cc.find("Canvas/beijing/wenzi/szxz").active = true;
          cc.find("Canvas/beijing/wenzi/moshi/putonmoshi").active = false;
          cc.find("Canvas/beijing/wenzi/moshi/gudingzhuang").active = false;
          cc.find("Canvas/beijing/wenzi/moshi/ziyouqiangzhuang").active = true;
        }
        cc.wenzi(cc.find("Canvas/beijing/wenzi/szxz/label"), Msg.d.shangzhuang);
        cc.wenzi(cc.find("Canvas/beijing/wenzi/fangjianhao/label"), Msg.d.roomid);
        cc.MAN.roomid = Msg.d.roomid;
        cc.wenzi(cc.find("Canvas/beijing/wenzi/renchang/label"), Msg.d.xren);
        cc.wenzi(cc.find("Canvas/beijing/wenzi/guangdongtuo/label"), Msg.d.difen);
        cc.wenzi(cc.find("Canvas/beijing/wenzi/jushu/label"), Msg.d.qishu + "/" + Msg.d.allqishu);
        if (2 == Msg.d.wanfa) {
          cc.find("Canvas/beijing/wenzi/2").active = true;
          cc.find("Canvas/beijing/wenzi/1").active = false;
        } else {
          cc.find("Canvas/beijing/wenzi/1").active = true;
          cc.find("Canvas/beijing/wenzi/2").active = false;
        }
        if (1 == Msg.d.gametype) {
          cc.find("Canvas/beijing/wenzi/guangdongtuo").x = cc.find("Canvas/beijing/wenzi/guangdongtuo").x + 52;
          cc.find("Canvas/beijing/wenzi/xiaowangyidian").x = cc.find("Canvas/beijing/wenzi/xiaowangyidian").x + 52;
          cc.find("Canvas/beijing/wenzi/2").x = cc.find("Canvas/beijing/wenzi/2").x + 52;
          cc.find("Canvas/beijing/wenzi/1").x = cc.find("Canvas/beijing/wenzi/1").x + 52;
          cc.find("Canvas/beijing/wenzi/moshi").x = cc.find("Canvas/beijing/wenzi/moshi").x + 52;
          cc.find("Canvas/beijing/wenzi/szxz").x = cc.find("Canvas/beijing/wenzi/szxz").x + 52;
        }
      },
      VoiceShow: function VoiceShow() {
        if (!this._playingSeat && this._voiceMsgQueue.length) {
          cc.audioEngine.pauseAll();
          var data = this._voiceMsgQueue.shift();
          this._playingSeat = true;
          var msgInfo = data.msg;
          var msgfile = "voicemsg.amr";
          cc.voiceMgr.writeVoice(msgfile, msgInfo);
          cc.voiceMgr.play(msgfile);
          var t = data.tim;
          var user = this.getNode(data.uid).NodeInfo;
          if (user) {
            cc.find("YuYin", user).active = true;
            this.scheduleOnce(function() {
              cc.find("YuYin", user).active = false;
              this._playingSeat = false;
              cc.audioEngine.resumeAll();
              this._voiceMsgQueue.length > 0 && this.VoiceShow();
            }, t / 1e3);
          }
        }
      },
      feijibi: function feijibi(qshiwzid, muddid, yzhitiid) {
        if (!this.getNode(qshiwzid).NodeInfo) return;
        for (var index = 0; index < 10; index++) {
          var node = cc.instantiate(this.FJINBI);
          node.parent = cc.find("Canvas/beijing/jbyzt");
          node.group = "defult";
          node.active = true;
          if (yzhitiid == cc.User.uid) {
            node.x = this.getNode(qshiwzid).NodeInfo.x;
            node.y = this.getNode(qshiwzid).NodeInfo.y + 30;
          } else {
            node.x = this.getNode(qshiwzid).NodeInfo.x;
            node.y = this.getNode(qshiwzid).NodeInfo.y;
          }
          node.name = "yuzhiti_" + yzhitiid + "_" + index;
        }
        var indexxx = 0;
        this.schedule(function(xx, ccc) {
          cc.find("Canvas/beijing/jbyzt/yuzhiti_" + xx + "_" + indexxx) && (muddid == cc.User.uid ? cc.find("Canvas/beijing/jbyzt/yuzhiti_" + xx + "_" + indexxx).runAction(cc.moveTo(.5, cc.MAN.getNode(muddid).NodeInfo.x, cc.MAN.getNode(muddid).NodeInfo.y + 30)) : cc.find("Canvas/beijing/jbyzt/yuzhiti_" + xx + "_" + indexxx).runAction(cc.moveTo(.5, cc.MAN.getNode(muddid).NodeInfo.x, cc.MAN.getNode(muddid).NodeInfo.y)));
          indexxx++;
        }.bind(this, yzhitiid), .03, 10, 0);
      },
      getNode: function getNode(uid) {
        for (var i in this.nodeinfo) if (this.nodeinfo[i].UserInfo && this.nodeinfo[i].UserInfo.uid == uid) return this.nodeinfo[i];
        return false;
      },
      ShowText: function ShowText(data) {
        var bqing = data.key;
        var user_Node = this.getNode(data.uid).NodeInfo;
        if (bqing < 21) {
          cc.SY.yingxiaopurl("sounds/" + bqing + ".mp3");
          cc.find("msgbox", user_Node).active = true;
          data.uid == this._MyUserID && (cc.find("Canvas/beijing/liaotiank").active = false);
          cc.wenzi(cc.find("msgbox/yuyinwenzi", user_Node), this.WENZIBIAO[bqing]);
          this.scheduleOnce(function() {
            cc.find("msgbox", user_Node).active = false;
          }, 3);
        } else if (this.BAOHAO[bqing]) {
          cc.find("bang1", user_Node).active = true;
          cc.tupian(cc.find("bang1", user_Node), null);
          var i = 1;
          var repeat = false;
          this.biaoqing = function() {
            cc.tupian(cc.find("bang1", user_Node), this.BiaoQingTuJi.getSpriteFrame(this.BAOHAO[bqing][0] + i));
            i++;
            if (i > this.BAOHAO[bqing][1]) if (this.BAOHAO[bqing][1] > 7) {
              this.unschedule(this.biaoqing);
              cc.find("bang1", user_Node).active = false;
            } else {
              false == repeat && (i = 0);
              repeat = true;
              if (i > this.BAOHAO[bqing][1] && true == repeat) {
                this.unschedule(this.biaoqing);
                cc.find("bang1", user_Node).active = false;
              }
            }
          };
          this.schedule(this.biaoqing, .2);
          cc.find("Canvas/beijing/liaotiank").active = false;
        }
      },
      zhunbeicg: function zhunbeicg() {
        cc.find("Canvas/beijing/button/quxiao").active = false;
        cc.find("Canvas/beijing/button/zhunbei").active = false;
        cc.find("Canvas/beijing/zhuozi/xiaoxi/qingzhunbei").active = false;
        cc.find("Canvas/beijing/zhuozi/my/renwu/zhunbei").active = true;
        cc.find("Canvas/beijing/zhuozi/xiaoxi/ddwjzb").active = true;
      },
      updateDissolve: function updateDissolve(data) {
        for (var i in cc.find("Canvas/beijing/shenqjs/tongyikuang/lyout").children) {
          var item = cc.find("Canvas/beijing/shenqjs/tongyikuang/lyout").children[i];
          if (item.uid == data.uid) if (data.state = 2) {
            cc.find("State/Yes", item).active = true;
            cc.find("State/No", item).active = false;
          } else if (data.state = 1) {
            cc.find("State/Yes", item).active = false;
            cc.find("State/No", item).active = true;
          }
        }
      },
      StartVote: function StartVote(DATA) {
        if (this.TOUDAOJI) {
          this.unschedule(this.TOUDAOJI);
          this.TOUDAOJI = null;
        }
        cc.find("Canvas/beijing/shenqjs/tongyikuang/lyout").removeAllChildren();
        cc.find("Canvas/beijing/shenqjs").active = true;
        var ulist = cc.find("Canvas/beijing/shenqjs/tongyikuang/lyout");
        for (var i in DATA) {
          var USER = this.getNode(i).UserInfo;
          if (!USER) continue;
          var item = cc.instantiate(this.UserDisslove);
          item.active = true;
          cc.pichttp(cc.find("touxiangkuang/210", item), USER.touxiang, 56, 56);
          USER.name.length > 5 ? cc.wenzi(cc.find("mingzikuang/label", item), USER.name.substring(0, 4) + "..") : cc.wenzi(cc.find("mingzikuang/label", item), USER.name);
          if (0 == DATA[i]) {
            cc.find("State/Yes", item).active = false;
            cc.find("State/No", item).active = false;
          } else if (2 == DATA[i]) {
            cc.find("State/Yes", item).active = true;
            cc.find("State/No", item).active = false;
          } else {
            cc.find("State/Yes", item).active = false;
            cc.find("State/No", item).active = true;
          }
          if (i == this._MyUserID) if (0 == DATA[i]) {
            cc.find("Canvas/beijing/shenqjs/tyan").active = true;
            cc.find("Canvas/beijing/shenqjs/jjan").active = true;
          } else {
            cc.find("Canvas/beijing/shenqjs/tyan").active = false;
            cc.find("Canvas/beijing/shenqjs/jjan").active = false;
          }
          item.uid = USER.uid;
          item.parent = ulist;
        }
        var t = 25;
        this.time = t;
        cc.find("Canvas/beijing/shenqjs/daojiszidjs").getComponent(cc.Label).string = this.time + "s后自动同意";
        cc.find("Canvas/beijing/shenqjs/daojiszidjs").active = true;
        this.unschedule(this.callback);
        this.callback = function() {
          cc.find("Canvas/beijing/shenqjs/daojiszidjs").getComponent(cc.Label).string = this.time + "s后自动同意";
          if (0 == this.time || true == THIS.tongyi) {
            this.unschedule(this.callback);
            cc.find("Canvas/beijing/shenqjs/daojiszidjs").active = false;
            var json = {
              y: "selectDissolve",
              d: "2"
            };
            cc.ZYONLINE.wssend(json);
          }
          this.time--;
        };
        this.schedule(this.callback, 1);
      },
      Exit: function Exit(data) {
        cc.find("Canvas/beijing/shenqjs").active = false;
        if (1 == data.jie) {
          cc.find("Canvas/beijing/shenqjs/tongyikuang/lyout").removeAllChildren();
          return;
        }
        cc.find("Canvas/beijing/paijujieshu/k/scrollview/view/content").removeAllChildren();
        cc.wenzi(cc.find("Canvas/beijing/paijujieshu/k/biaotl/jushu"), data.allqishu + "局");
        cc.wenzi(cc.find("Canvas/beijing/paijujieshu/k/biaotl/fjh/fjhzhi"), cc.MAN.roomid);
        for (var key in data.dangju) {
          var node = cc.instantiate(this.kaijiangjieguo);
          node.parent = cc.find("Canvas/beijing/paijujieshu/k/scrollview/view/content");
          node.group = "defult";
          node.active = true;
          node.name = "yuzhiti" + key;
          if (data.dangju[key].AllTongji > 0) {
            0 == key && (cc.find("Canvas/beijing/paijujieshu/k/scrollview/view/content/" + node.name + "/ying").active = true);
            data.dangju[key].name.length > 7 ? cc.wenzi(cc.find("Canvas/beijing/paijujieshu/k/scrollview/view/content/" + node.name + "/xinxi"), "(" + data.dangju[key].uid + ")" + data.dangju[key].name.substring(0, 6) + "..") : cc.wenzi(cc.find("Canvas/beijing/paijujieshu/k/scrollview/view/content/" + node.name + "/xinxi"), "(" + data.dangju[key].uid + ")" + data.dangju[key].name);
            cc.wenzi(cc.find("Canvas/beijing/paijujieshu/k/scrollview/view/content/" + node.name + "/fenshu"), "+" + data.dangju[key].AllTongji);
            cc.find("Canvas/beijing/paijujieshu/k/scrollview/view/content/" + node.name + "/fenshu").color = cc.Color.YELLOW;
            cc.find("Canvas/beijing/paijujieshu/k/scrollview/view/content/" + node.name + "/xinxi").color = cc.Color.YELLOW;
          } else {
            data.dangju[key].name.length > 7 ? cc.wenzi(cc.find("Canvas/beijing/paijujieshu/k/scrollview/view/content/" + node.name + "/xinxi"), "(" + data.dangju[key].uid + ")" + data.dangju[key].name.substring(0, 6) + "..") : cc.wenzi(cc.find("Canvas/beijing/paijujieshu/k/scrollview/view/content/" + node.name + "/xinxi"), "(" + data.dangju[key].uid + ")" + data.dangju[key].name);
            cc.wenzi(cc.find("Canvas/beijing/paijujieshu/k/scrollview/view/content/" + node.name + "/fenshu"), data.dangju[key].AllTongji);
          }
        }
        cc.find("Canvas/beijing/paijujieshu").active = true;
        for (var _key25 in this.nodeinfo) if (true == this.nodeinfo[_key25].isSelect) for (var k in data.Auserinfo) data.Auserinfo[k].uid == this.nodeinfo[_key25].UserInfo.uid && cc.wenzi(cc.find("shenyujine/label", this.nodeinfo[_key25].NodeInfo), data.Auserinfo[k].zf);
        this.unscheduleAllCallbacks();
      },
      f_RandomNumBoth: function f_RandomNumBoth(Min, Max) {
        var Range = Max - Min;
        var Rand = Math.random();
        var num = Min + Math.round(Rand * Range);
        return num;
      },
      QiangZhuangShowHiden: function QiangZhuangShowHiden(isShow, t) {
        var _node = cc.find("Canvas/beijing/qiangzhuang");
        _node.active = isShow;
        if (t) {
          this.daojishi(t);
          setTimeout(function() {
            _node.active = false;
          }, 1e3 * t);
        }
      },
      Ready: function Ready() {
        cc.SY.yingxiaopurl("sounds/button");
        cc.ZYONLINE.wssend({
          y: "ready"
        });
      },
      daojishi: function daojishi(t) {
        if (t <= 1) return;
        this.time = t;
        cc.find("Canvas/beijing/button/naozhong/label").getComponent(cc.Label).string = this.time + 1;
        cc.find("Canvas/beijing/button/naozhong").active = true;
        cc.find("Canvas/beijing/button/naozhong/label").active = true;
        this.callback && this.unschedule(this.callback);
        this.callback = function() {
          cc.find("Canvas/beijing/button/naozhong/label").getComponent(cc.Label).string = this.time;
          if (this.time <= 0) {
            this.unschedule(this.callback);
            cc.find("Canvas/beijing/button/naozhong").active = false;
            cc.find("Canvas/beijing/button/naozhong/label").active = false;
          }
          this.time--;
        };
        this.schedule(this.callback, 1);
      },
      onDestroy: function onDestroy() {
        cc.SY.stopAll();
      }
    });
    cc._RF.pop();
  }, {} ],
  http: [ function(require, module, exports) {
    "use strict";
    cc._RF.push(module, "0dab62haBRCNaRevU+l6tBp", "http");
    "use strict";
    var HTTPSERVER = "http://qse.vdpptj.cn";
    var JSONSERVER = HTTPSERVER + "/json.php";
    var WXLOGIN = HTTPSERVER + "/login/weixinopen.php";
    var WXGALOGIN = HTTPSERVER + "/login/weixingame.php";
    var PAYSERVER = HTTPSERVER + "/pay.php";
    module.exports = {
      URL: function URL(Uid, data, handler) {
        try {
          if ("H" == Uid) var Url = HTTPSERVER; else if ("J" == Uid) var Url = JSONSERVER; else if ("WX" == Uid) var Url = WXLOGIN; else if ("WXGA" == Uid) var Url = WXGALOGIN; else if ("P" == Uid) var Url = PAYSERVER; else var Url = JSONSERVER;
          var xhr = cc.loader.getXMLHttpRequest();
          xhr.onreadystatechange = function() {
            4 === xhr.readyState && handler && handler(xhr.responseText, xhr.status);
          };
          var DATA = null;
          var lx = 1;
          if ("GET" == data.ac) {
            lx = 1;
            data.ac && delete data.ac;
            var str = "?";
            for (var k in data) {
              "?" != str && (str += "&");
              str += k + "=" + data[k];
            }
            Url += encodeURI(str);
            xhr.open("GET", Url, true);
          } else {
            lx = 2;
            data.ac && delete data.ac;
            xhr.open("POST", Url);
            var str = "";
            var input = 0;
            for (var k in data) {
              input > 0 && (str += "&");
              str += k + "=" + data[k];
              input++;
            }
            DATA = str;
          }
          cc.sys.isNative && xhr.setRequestHeader("Accept-Encoding", "gzip,deflate");
          xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
          xhr.timeout = 1e4;
          DATA ? xhr.send(DATA) : xhr.send();
        } catch (e) {}
      },
      SERVERHTPT: function SERVERHTPT() {
        return HTTPSERVER;
      }
    };
    cc._RF.pop();
  }, {} ],
  login: [ function(require, module, exports) {
    "use strict";
    cc._RF.push(module, "280c3rsZJJKnZ9RqbALVwtK", "login");
    "use strict";
    cc.Class({
      extends: cc.Component,
      properties: {
        label: {
          default: null,
          type: cc.Label
        },
        text: "Hello, World!"
      },
      onLoad: function onLoad() {
        this.label.string = this.text;
      },
      update: function update(dt) {}
    });
    cc._RF.pop();
  }, {} ],
  online: [ function(require, module, exports) {
    "use strict";
    cc._RF.push(module, "e2bc26mTTlCi7BLNL3ORsL4", "online");
    "use strict";
    var THIS = null;
    cc.Class({
      extends: cc.Component,
      properties: {},
      onLoad: function onLoad() {
        cc.ZYONLINE = this;
        this._ws = null;
        THIS = this;
        this.xintiao = 0;
        this.XINTIAO = null;
        this.XIAOXILIST = [];
        this.huodeg = false;
        this._YSEGAME = true;
        this.msgbox = null;
        cc.game.on(cc.game.EVENT_HIDE, function() {
          THIS._YSEGAME = false;
        });
        cc.game.on(cc.game.EVENT_SHOW, function() {
          THIS._YSEGAME = true;
        });
      },
      wslink: function wslink(data) {
        if (data.ip.indexOf("wss@") >= 0) {
          data.ip = data.ip.replace("wss@", "wss://");
          this._ws = new WebSocket(data.ip + ":" + data.port, [], cc.url.raw("resources/thirteenyards.cer"));
        } else this._ws = new WebSocket("ws://" + data.ip + ":" + data.port);
        this._ws.onopen = function(evt) {
          THIS.xintiaocha();
        };
        this._ws.onmessage = function(evt) {
          if (!evt || !evt.data) return;
          var str = JSON.parse(evt.data);
          if (!str) return;
          if (!str.y) return;
          if (!THIS._YSEGAME && "xintiao" != str.y) return;
          if ("lianjieok" == str.y) {
            var buf = {
              u: cc.GAME.t,
              y: "tx"
            };
            THIS.wssend(buf);
          } else if ("xintiao" == str.y) {
            THIS.xintiao = 0;
            THIS.wssend({
              y: "xintiao"
            });
          } else "g" == str.y || "j" == str.y || "canbet" == str.y || "bet" == str.y || "ready" == str.y || "allready" == str.y || "myqz" == str.y || "firstfapai" == str.y || "allbet" == str.y || "secondfapai" == str.y || "ztj" == str.y || "tj" == str.y || "quite" == str.y || "mybet" == str.y || "WangYatxt" == str.y || "WangYa" == str.y || "allexit" == str.y || "exitts" == str.y || "exitxz" == str.y || "mypaixin" == str.y || "msg" == str.y || "cancelsuccess" == str.y || "end" == str.y ? cc.MAN.WSSHOUDATA(str) : THIS.XIAOXILIST.push(str);
        };
        this._ws.onerror = function(evt) {};
        this._ws.onclose = function(evt) {
          THIS.xintiao = 7500;
        };
      },
      wssend: function wssend(buf) {
        this._ws && this._ws.readyState == WebSocket.OPEN && this._ws.send(JSON.stringify(buf));
      },
      xixiduilie: function xixiduilie() {
        if (this.XIAOXILIST.length < 1) this.XIAOXILIST = []; else for (var i = 0; i < this.XIAOXILIST.length; i++) {
          if (false != this.huodeg) {
            cc.MAN.WSSHOUDATA(this.XIAOXILIST[i]);
            this.XIAOXILIST.splice(i, 1);
            break;
          }
          if ("g" == this.XIAOXILIST[i].y) {
            cc.MAN.WSSHOUDATA(this.XIAOXILIST[i]);
            this.XIAOXILIST.splice(i, 1);
            this.scheduleOnce(function() {
              this.huodeg = true;
            }, .01);
          }
        }
      },
      xintiaocha: function xintiaocha() {
        if (this.XINTIAO) {
          this.unschedule(THIS.XINTIAO);
          this.XINTIAO = null;
        }
        this.XINTIAO = function() {
          this.xintiao++;
          if (this.xintiao > 8e3) {
            THIS._YSEGAME = false;
            this.unschedule(this.XINTIAO);
            this.XINTIAO = null;
            cc.MAN.WSCOLSE(1);
            return;
          }
          this.xixiduilie();
        };
        this.schedule(this.XINTIAO, .01);
      },
      xiaohui: function xiaohui() {
        this.unscheduleAllCallbacks();
        THIS._YSEGAME = false;
        if (THIS.XINTIAO) {
          THIS.unschedule(THIS.XINTIAO);
          THIS.XINTIAO = null;
        }
        if (this._ws) {
          try {
            this._ws.close();
          } catch (error) {}
          this._ws = null;
        }
      }
    });
    cc._RF.pop();
  }, {} ],
  sdkapi: [ function(require, module, exports) {
    "use strict";
    cc._RF.push(module, "59c1b5uptJE7bx2lXebUX++", "sdkapi");
    "use strict";
    var THIS = null;
    cc.Class({
      extends: cc.Component,
      properties: {},
      onLoad: function onLoad() {
        cc.SDKAPI = THIS = this;
        this.ANDROIDAPI = "com/wywl/games/ThirteenYards";
        this.IOSAPI = "AppController";
        cc.PAYhuidiao = function(data) {
          if (1 == data) {
            cc.msgbox.show("支付成功");
            cc.find("Canvas/zicaidan/qiandao/alert/lunpanan/nengchou") && (cc.find("Canvas/zicaidan/qiandao/alert/lunpanan/nengchou").getComponent(cc.Button).interactable = true);
          } else cc.msgbox.show("支付取消");
        };
        cc.Shareback = function(data) {
          if (1 == data) {
            var myjson = {
              ac: "POST",
              y: "duihuan",
              d: "delete",
              apptoken: cc.userData && cc.User && cc.User.apptoken ? cc.User.apptoken : ""
            };
            cc.http.URL("J", myjson, function(data, status) {
              var DATA = JSON.parse(data);
              if ("200" == status) {
                var D = DATA.data;
                D.jifen && (cc.User.jifen = D.jifen);
                D.huobi && (cc.User.huobi = D.huobi);
                cc.MAN.binuser && cc.MAN.binuser();
              }
            });
            cc.msgbox.show("分享成功");
          } else cc.msgbox.show("分享取消");
        };
      },
      copylink: function copylink(url) {
        if (cc.sys.os == cc.sys.OS_ANDROID && cc.sys.isNative) {
          jsb.reflection.callStaticMethod(this.ANDROIDAPI, "copylink", "(Ljava/lang/String;)V", url);
          cc.Msgbox.show("复制成功");
        } else if (cc.sys.os == cc.sys.OS_IOS && cc.sys.isNative) {
          jsb.reflection.callStaticMethod(this.IOSAPI, "copylink:", url);
          cc.Msgbox.show("复制成功");
        } else {
          var textArea = document.getElementById("clipBoard");
          if (null === textArea) {
            textArea = document.createElement("textarea");
            textArea.id = "clipBoard";
            textArea.textContent = url;
            document.body.appendChild(textArea);
          }
          textArea.select();
          try {
            var msg = document.execCommand("copy") ? "successful" : "unsuccessful";
            cc.Msgbox.show("复制成功");
            document.body.removeChild(textArea);
          } catch (err) {
            cc.Msgbox.show("复制失败");
          }
        }
      },
      share: function share(title, neirong, link, bs) {
        if (cc.shencha) {
          cc.msgbox.jieping(cc.User.name + " \n邀请你一起玩" + cc.GAMENAME + " \n截屏分享获得奖励", cc.http.SERVERHTPT() + "/login.php?y=2&tuid=" + cc.User.uid);
          return;
        }
        bs = 2 == bs;
        if (cc.sys.os == cc.sys.OS_ANDROID && cc.sys.isNative) jsb.reflection.callStaticMethod(this.ANDROIDAPI, "wx_Share", "(Ljava/lang/String;Ljava/lang/String;Ljava/lang/String;Z)V", link, title, neirong, bs); else if (cc.sys.os == cc.sys.OS_IOS && cc.sys.isNative) jsb.reflection.callStaticMethod(this.IOSAPI, "wx_share:shareTitle:shareDesc:pyq:", link, title, neirong, bs); else {
          console.log("@@@@@", title, neirong, link, bs);
          cc.msgbox.jieping(cc.User.name + " \n邀请你一起玩" + cc.GAMENAME + " \n截屏分享获得奖励", cc.http.SERVERHTPT() + "/login.php?y=2&tuid=" + cc.User.uid);
        }
      },
      weixingo: function weixingo() {
        cc.sys.os == cc.sys.OS_ANDROID && cc.sys.isNative ? jsb.reflection.callStaticMethod(this.ANDROIDAPI, "wx_Login", "()V") : cc.sys.os == cc.sys.OS_IOS && cc.sys.isNative ? jsb.reflection.callStaticMethod(this.IOSAPI, "wx_Login") : window.location.href = cc.http.SERVERHTPT() + "/login.php?y=2&apptoken=" + cc.User.apptoken;
      },
      weixinhui: function weixinhui(codes) {
        cc.http.URL("WX", {
          isapp: "isapp",
          ac: "GET",
          code: codes,
          apptoken: cc.User.apptoken
        }, function(data, status) {
          cc.MAN.qureydenglu();
        });
      },
      jieping: function jieping(bs) {
        true;
        return;
        var size;
        var currentDate;
        var fileName;
        var fullPath;
        var texture;
      },
      openurl: function openurl(url) {
        console.log(url);
        window.location.href = url;
      },
      pay: function pay(filde, data) {
        var zhuanhuan = {
          appweixin: 2,
          appalipay: 1,
          iappay: 3
        };
        if (!zhuanhuan[filde]) return cc.msgbox.show("没有app支付sdk");
        cc.sys.os == cc.sys.OS_ANDROID ? jsb.reflection.callStaticMethod(this.ANDROIDAPI, "gopay", "(Ljava/lang/String;I)V", data, parseInt(zhuanhuan[filde])) : cc.sys.os == cc.sys.OS_IOS ? jsb.reflection.callStaticMethod(this.IOSAPI, "gopay:tyid:", data, parseInt(zhuanhuan[filde])) : cc.msgbox.show("不是app支付");
      },
      start: function start() {}
    });
    cc._RF.pop();
  }, {} ],
  shengying: [ function(require, module, exports) {
    "use strict";
    cc._RF.push(module, "34af0Uhdu1DarYMyuevbCkN", "shengying");
    "use strict";
    cc.Class({
      extends: cc.Component,
      properties: {
        audioSource: [],
        nanaudio: [],
        nvaudio: []
      },
      onLoad: function onLoad() {
        this._YingLiang = 1;
        this._YingXiao = 1;
        cc.SY = this;
        var YingLiang = 1;
        var YingXiao = 1;
        YingLiang = parseFloat(cc.sys.localStorage.getItem("yingyue"));
        YingXiao = parseFloat(cc.sys.localStorage.getItem("yingxiao"));
        null != cc.sys.localStorage.getItem("yingxiao") && "" != cc.sys.localStorage.getItem("yingxiao") || (YingXiao = 1);
        null != cc.sys.localStorage.getItem("yingyue") && "" != cc.sys.localStorage.getItem("yingyue") || (YingLiang = 1);
        this._YingLiang = YingLiang;
        this._YingXiao = YingXiao;
      },
      zyconfig: function zyconfig(data) {
        if (data.nanaudio) for (var i = 0; i < data.nanaudio.length; i++) this.yinxiaojiazia(1, i, data.nanaudio[i]);
        if (data.nvaudio) for (var i = 0; i < data.nvaudio.length; i++) this.yinxiaojiazia(0, i, data.nvaudio[i]);
      },
      yinxiaojiazia: function yinxiaojiazia(lxm, i, zhis) {
        cc.loader.loadRes(zhis, cc.AudioClip, function(err, clip) {
          if (err) return;
          if (1 == lxm) cc.SY.nanaudio[i] = clip; else if (2 == lxm) {
            var iid = cc.audioEngine.play(clip, true, 0);
            cc.audioEngine.pause(iid);
            cc.SY.audioSource[i] = iid;
          } else cc.SY.nvaudio[i] = clip;
        });
      },
      yingyuep: function yingyuep(id) {
        if ("undefined" == typeof this.audioSource[id]) {
          this.scheduleOnce(function() {
            this.yingyuep(id);
          }, 1.5);
          return;
        }
        var state = cc.audioEngine.getState(this.audioSource[id]);
        if ("-1" == state) {
          this.scheduleOnce(function() {
            this.yingyuep(id);
          }, 1.5);
          return;
        }
        if ("undefined" == typeof this.audioSource[id]) return;
        cc.audioEngine.setVolume(this.audioSource[id], this._YingLiang);
        cc.audioEngine.resume(this.audioSource[id]);
        cc.audioEngine.setCurrentTime(this.audioSource[id], 0);
      },
      yingyuet: function yingyuet(id) {
        if (!this.audioSource || "undefined" == typeof this.audioSource[id]) return;
        cc.audioEngine.stop(this.audioSource[id]);
      },
      yingyuez: function yingyuez(id) {
        if (!this.audioSource || "undefined" == typeof this.audioSource[id]) return;
        cc.audioEngine.pause(this.audioSource[id]);
      },
      yingyuec: function yingyuec(id) {
        if (!this.audioSource || "undefined" == typeof this.audioSource[id]) return;
        cc.audioEngine.resume(this.audioSource[id]);
      },
      yingyueallstop: function yingyueallstop() {
        if (this._YingLiang <= 0) return;
        for (var i = 0; i < this.audioSource.length; i++) cc.audioEngine.pause(this.audioSource[i]);
      },
      yingyueallsetnum: function yingyueallsetnum() {
        for (var i = 0; i < this.audioSource.length; i++) cc.audioEngine.setVolume(this.audioSource[i], 1 * this._YingLiang);
      },
      yingxiaop: function yingxiaop(id, sex) {
        if (this._YingXiao <= 0) return;
        if ("0" == sex) {
          if (!this.nvaudio || "undefined" == typeof this.nvaudio[id]) return;
          cc.audioEngine.play(this.nvaudio[id], false, 1 * this._YingXiao);
        } else {
          if (!this.nanaudio || "undefined" == typeof this.nanaudio[id]) return;
          cc.audioEngine.play(this.nanaudio[id], false, 1 * this._YingXiao);
        }
      },
      yingxiaopurl: function yingxiaopurl(url) {
        var lujin = url;
        lujin && cc.loader.loadRes(lujin, cc.AudioClip, function(err, clip) {
          cc.audioEngine.play(clip, false, 1 * cc.SY._YingXiao);
        });
      },
      yingyuepurl: function yingyuepurl(url) {
        var lujin = url;
        lujin && cc.loader.loadRes(lujin, cc.AudioClip, function(err, clip) {
          cc.SY.audioSource.push(cc.audioEngine.play(clip, true, 1 * cc.SY._YingLiang));
        });
      },
      stopAll: function stopAll() {
        cc.audioEngine.stopAll();
      },
      pauseAll: function pauseAll() {
        cc.audioEngine.pauseAll();
      },
      resumeAll: function resumeAll() {
        cc.audioEngine.resumeAll();
      },
      setyingxiao: function setyingxiao(num) {
        num = parseFloat(num);
        num > 1 ? num = 1 : num <= 0 && (num = 0);
        this._YingXiao = num;
        cc.sys.localStorage.setItem("yingxiao", num);
      },
      setyingyue: function setyingyue(num) {
        num = parseFloat(num);
        num > 1 ? num = 1 : num <= 0 && (num = 0);
        this._YingLiang = num;
        this.yingyueallsetnum();
        cc.sys.localStorage.setItem("yingyue", num);
      },
      duqu: function duqu() {
        return [ this._YingLiang, this._YingXiao ];
      }
    });
    cc._RF.pop();
  }, {} ]
}, {}, [ "Msgbox", "dating", "game", "http", "login", "online", "sdkapi", "shengying" ]);