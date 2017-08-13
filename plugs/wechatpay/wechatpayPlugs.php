<?php
namespace plugs\wechatpay;
use app\common\controller\Plugs;
use plugs\wechatpay\controller\wechatpay;
use think\Db;
use think\Session;

/**
 * 微信登录插件
 * @author 初心
 */

class wechatpayPlugs extends Plugs{

    Public $info =[
        'name'=>'wechatpay',
        'title'=>'微信支付',
        'description'=>'发起jsapi微信支付，依赖微信登录插件，需通过微信登录插件进行授权登录。',
        'webvisit'=>1,
        'status'=>1,
        'author'=>'初心',
        'version'=>'1.0'
    ];

    Public $admin_list = [
        'pid'		=>	'3',				//默认父菜单为扩展
        'font_class'=>	'plug',				//列表图标，参考 /system/menu/fontselect.html
        'model'		=>	'wechat_pay',		//要查的表
        'fields'	=>	'*',			//要查的字段
        'map'		=>	'',				//查询条件, 如果需要可以在插件类的构造方法里动态重置这个属性
        'order'		=>	'create_time desc',		//排序方式
        'search'=>'out_trade_no',//搜索字段
        'listKey'	=>	[				//这里定义的是除了id序号外的表格里字段显示的表头名
            'openid'=>'OpenId',
            'money'=>'订单金额',
            'out_trade_no'=>'商户订单号',
            'transaction_id'=>'微信支付订单号',
            'create_time'=>'支付时间',
        ],
    ];

    Public $custom_adminlist = 'list';

    Public $custom_adminlist_title = '支付订单';

    Public function install(){

        $sql = <<<sql
CREATE TABLE IF NOT EXISTS `tplus_wechat_pay` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
  `openid` varchar(50) NOT NULL,
  `money` float(11,2) NOT NULL,
  `out_trade_no` varchar(32) NOT NULL COMMENT '商户订单号',
  `transaction_id` varchar(32) NOT NULL COMMENT '微信支付订单号',
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
sql;
        Db::execute($sql);
        //写入钩子
        Db::execute("INSERT INTO `tplus_hooks` VALUES ('', 'wechatpay', '微信支付钩子', '1', ".time().", '')");
        return true;
    }

    Public function uninstall(){
        Db::execute('DROP TABLE IF EXISTS t_wechat_pay');
        //移除钩子
        Db::execute("delete from `tplus_hooks` where name='wechatpay'");
        return true;
    }

    //实现的wechatpay钩子方法
    Public function wechatpay($wid = 0){
        //获取sign签名.
        $wechatpay = new wechatpay();
        $host = url('',['wid'=>$wid],true,true);
        $host = rtrim($host,'.html');
        $sign = $wechatpay->getSign(Session::get('wechat_appid'), Session::get('wechat_appsecret'), $host);
        $this->assign('sign',$sign);
        return $this->fetch('pay');
    }

}