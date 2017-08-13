<?php

namespace plugs\documentedit;
use app\common\controller\Plugs;
use think\Db;

/**
 * 编辑器插件插件
 * @author onion
 */

    class documenteditPlugs extends Plugs{

        public $info =[
            'name'=>'documentedit',
            'title'=>'编辑器插件',
            'description'=>'这是一个编辑器的插件，可以灵活的应用到各处',
            'status'=>1,
            'author'=>'onion',
            'version'=>'1.0'
        ];

        Public function install(){
            //写入钩子
            Db::execute("INSERT INTO `tplus_hooks` VALUES ('', 'documentedit', '	编辑器钩子', '1', ".time().", '')");
            return true;
        }

        Public function uninstall(){
            //移除钩子
            Db::execute("delete from `tplus_hooks` where name='documentedit'");
            return true;
        }
        //实现的dcumentEdit钩子方法
        public function documentedit($param = []){
            if(!isset($param['name'])) 
                $name='content';
            else
                $name=$param['name'];

            if(!isset($param['value']))
                $value='';
            else
                $value=$param['value']; 
            $this->assign('name',$name);                   
            $this->assign('value',$value);                   
            return $this->fetch('edit');
        }

    }