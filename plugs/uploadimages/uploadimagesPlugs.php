<?php

namespace plugs\uploadimages;
use app\common\controller\Plugs;
use think\Db;
/**
 * 图片上传插件插件
 * @author yxhsea
 */

    class uploadimagesPlugs extends Plugs{

        Public $info =[
            'name'=>'uploadimages',
            'title'=>'图片上传插件',
            'description'=>'图片上传插件，基于Plupload插件，支持单多图片上传、实时预览图片。',
            'webvisit'=>0,
            'status'=>1,
            'author'=>'yxhsea',
            'version'=>'1.0'
        ];

        Public $admin_list = [
            'menu_title'=> '图片管理',
            'pid'		=>	'3',				//默认父菜单为扩展
            'font_class'=>	'image',				//列表图标，参考 /system/menu/fontselect.html
            'model'		=>	'picture',		//要查的表
            'fields'	=>	'*',			//要查的字段
            'map'		=>	'',				//查询条件, 如果需要可以在插件类的构造方法里动态重置这个属性
            'order'		=>	'create_time desc',		//排序方式
            'search'    =>  'id',//搜索字段
            'listKey'	=>	[				//这里定义的是除了id序号外的表格里字段显示的表头名
                'id'=>'图片ID',
                'path'=>'图片路径',
                'md5'=>'文件md5',
                'sha1'=>'文件sha1编码',
                'create_time'=>'上传时间',
            ],
        ];

        Public $custom_adminlist = 'list';

        Public $custom_adminlist_title = '图片列表';

        Public function install(){
            //安装钩子
            $sql = <<<sql
CREATE TABLE IF NOT EXISTS `tplus_picture` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id自增',
  `path` varchar(255) NOT NULL DEFAULT '' COMMENT '路径',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '图片链接',
  `md5` char(32) NOT NULL DEFAULT '' COMMENT '文件md5',
  `sha1` char(40) NOT NULL DEFAULT '' COMMENT '文件sha1编码',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '状态',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='图片表';
sql;
            Db::execute($sql);//创建图片数据表
            Db::execute("INSERT INTO `tplus_hooks` VALUES ('', 'uploadimages', '图片上传钩子', '1', ".time().", '')");
            return true;
        }

        Public function uninstall(){
            Db::execute('DROP TABLE IF EXISTS `tplus_picture`');//删除数据表
            Db::execute("delete from `tplus_hooks` where name='uploadimages'");//移除钩子
            return true;
        }

        //实现的uploadimages钩子方法
        Public function uploadimages($param){
            $upload_type = isset($param['type']) ? $param['type'] : 1;//1为单图上传，2为多图上传
            $name = isset($param['name']) ? $param['name'] : '';
            $img_val = $value = isset($param['value']) ? $param['value'] : '';
            $imgs = explode(',',$value);
            $imgs_arr = [];
            foreach ($imgs as $key => $value){
                $imgs_arr[$key]['img_id'] = $value;
                $imgs_arr[$key]['img_src'] = $this->request->root(true).'/'.Db::name("picture")->where(['id'=>$value])->value('path');
            }
            $this->assign('name',$name);
            $this->assign('value',$img_val);
            $this->assign('imgs_arr',$imgs_arr);
            $this->assign('upload_type',$upload_type);
            $this->assign('tips',isset($param['tips']) ? $param['tips'] : '');
            return $this->fetch('index');
        }
    }