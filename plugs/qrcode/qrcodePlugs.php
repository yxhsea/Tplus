<?php

namespace plugs\qrcode;
use app\common\controller\Plugs;
use think\Db;

/**
 * php生成二维码插件
 * @author yxhsea@foxmail.com
 */

class qrcodePlugs extends Plugs{

    Public $info =[
        'name'=>'qrcode',
        'title'=>'php生成二维码',
        'description'=>'这是一个生成二维码的插件，插件依赖于vendor下的phpqrcode类库',
        'webvisit'=>0,
        'status'=>1,
        'author'=>'yxhsea',
        'version'=>'1.0'
    ];

    Public function install(){
        //写入钩子
        Db::execute("INSERT INTO `tplus_hooks` VALUES('','qrcode','生成二维码插件','1',".time().",'')");
        return true;
    }

    Public function uninstall(){
        //移除钩子
        Db::execute("DELETE FROM `tplus_hooks` WHERE `name` = 'qrcode'");
        return true;
    }

    //实现的qrcode钩子方法，plugs('qrcode',['url'=>'http://www.qq.com','path'=>'code.png','size'=>4]);详情用法见readme.md
    Public function qrcode($param){
        $url = isset($param['url']) ? $param['url'] : 'http://www.baidu.com';
        $path = isset($param['path']) ? $param['path'] : false;
        $size = isset($param['size']) ? $param['size'] : 4;
        //引入二维码的类库
        vendor('Phpqrcode/phpqrcode');
        $QRcode = new \QRcode ();
        $QRcode::png($url,$path,QR_ECLEVEL_L,$size,2,false,0xFFFFFF,0x000000);
        exit();
    }
}