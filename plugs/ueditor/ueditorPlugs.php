<?php

namespace plugs\ueditor;
use app\common\controller\Plugs;
use think\Db;
/**
 * 百度编辑器插件
 * @author yxhsea
 */

    class ueditorPlugs extends Plugs{

        Public $info =[
            'name'=>'ueditor',
            'title'=>'百度编辑器',
            'description'=>'百度编辑器，可以灵活应用到各个地方。',
            'webvisit'=>0,
            'status'=>1,
            'author'=>'yxhsea',
            'version'=>'1.0'
        ];

        //安装钩子
        Public function install(){
            Db::execute("INSERT INTO `tplus_hooks` VALUES ('', 'ueditor', '百度编辑器钩子', '1', ".time().", '')");
            return true;
        }

        //移除钩子
        Public function uninstall(){
            Db::execute("delete from `tplus_hooks` where name='ueditor'");
            return true;
        }

        //实现的ueditor钩子方法
        Public function ueditor($param){
            $name= isset($param['name']) ? $param['name'] : '';
            $value = isset($param['value']) ? $param['value'] : '';
            $this->assign('name',$name);
            $this->assign('value',$value);
            return $this->fetch('index');
        }

    }