<?php
namespace app\home\controller;

use app\common\controller\Tplus;

use think\Session;

class Index extends Tplus
{
    public function index(){
        $openid = Session::get('wechat_openid');
        $name = Session::get('wechat_nickname');
        $imgurl = Session::get('wechat_img_url');

        var_dump($openid);
        var_dump($name);
        var_dump($imgurl);
        exit;
    }

    public function test(){
        //plugs('WechatLogin');
//        var_dump('qq');
        $res = plugs('sms',['mobile'=>'18373288691','tpl_id'=>'39145','tpl_value'=>'#code#=1234']);
        p($res);
    }


}
