<?php

namespace plugs\focusimg;
use app\common\controller\Plugs;
use think\Db;

/**
 * 轮播管理插件
 * @author Onion
 */

    class focusimgPlugs extends Plugs{
        public $callback = 1;
        public $info =[
            'name'=>'focusimg',
            'title'=>'轮播管理',
            'description'=>'这是一个带有分类的轮播图管理插件，要求先安装单多图上传插件',
            'status'=>1,
            'author'=>'Onion',
            'version'=>'1.0'
        ];

        public $admin_list = [
            'menu_title' => '轮播管理',
            'pid' => '3',
            'font_class' => 'plug',
            'model'=>'FocusCat',		//要查的表
			'fields'=>'*',			//要查的字段
			'map'=>'',				//查询条件, 如果需要可以再插件类的构造方法里动态重置这个属性
			'order'=>'id desc',		//排序,
            'search'=>'title',//搜索字段
			
        ];


        public $custom_adminlist = 'adminlist'; //后台默认列表
        public $custom_adminlist_title = '轮播分类'; //后台默认列表标题

        public function install(){
            //安装 2017.1.10
            //防止已存在表冲突，安装时先删除旧表
            Db::execute('DROP TABLE IF EXISTS t_focus_cat');
            $sql = <<<sql
CREATE TABLE `tplus_focus_cat` (
  `id` int(15) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(55) NOT NULL,
  `name` varchar(35) NOT NULL,
  `create_time` int(15) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
sql;
            Db::execute($sql);
            Db::execute('DROP TABLE IF EXISTS t_focus_img');
            $sql = <<<sql
CREATE TABLE `tplus_focus_img` (
            `id` int(15) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(60) NOT NULL,
  `links` varchar(150) NOT NULL,
  `pic_id` int(15) NOT NULL DEFAULT '0',
  `status` tinyint(3) NOT NULL DEFAULT '1',
  `create_time` int(15) NOT NULL,
  `catid` int(15) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
sql;
            Db::execute($sql);
            //写入轮播钩子
            Db::execute("INSERT INTO `tplus_hooks` VALUES ('', 'focusMap', '轮播钩子', '1', ".time().", '')");
            return true;
        }

        public function uninstall(){
            //卸载时移除数据 2017.1.10
            Db::execute('DROP TABLE IF EXISTS t_focus_cat');
            Db::execute('DROP TABLE IF EXISTS t_focus_img');
            //移除钩子
            Db::execute("delete from `tplus_hooks` where name='focusMap'");
            return true;
        }

        //实现的focusMap钩子方法
        public function focusMap($param){

        }

    }