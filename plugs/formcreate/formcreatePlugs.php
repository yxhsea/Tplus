<?php

namespace plugs\formcreate;
use app\common\controller\Plugs;
use think\Db;

/**
 * 表单构造器插件
 * @author 初心
 */

    class formcreatePlugs extends Plugs{

        Public $info =[
            'name'=>'formcreate',
            'title'=>'表单构造器',
            'description'=>'轻松拖拽式生成表单',
            'webvisit'=>0,
            'status'=>1,
            'author'=>'初心',
            'version'=>'1.0'
        ];

        Public $admin_list = [
            'pid'		=>	'3',				//默认父菜单为扩展
            'font_class'=>	'check-square-o',				//列表图标，参考 /system/menu/fontselect.html
        ];

        Public $custom_adminlist = 'formcreate';
        
        Public $custom_adminlist_title = '表单构造器';

        Public function install(){
            //写入钩子
            Db::execute("INSERT INTO `tplus_hooks` VALUES ('', 'formcreate', '	表单构造器插件', '1', ".time().", '')");
            return true;
        }

        Public function uninstall(){
            //移除钩子
            Db::execute("delete from `tplus_hooks` where name='formcreate'");
            return true;
        }

        //实现的formcreate钩子方法
        Public function formcreate($param){

        }

    }