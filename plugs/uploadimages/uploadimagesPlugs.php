<?php
namespace plugs\uploadimages;
use app\common\controller\Plugs;
use think\Db;

class uploadimagesPlugs extends Plugs{
    public $info = [
        'name' => 'uploadimages',
        'title' => '多单图上传',
        'description' => '多单图上传插件',
        'status' => 1,
        'author' => 'Onion',
        'version' => '1.3'
    ];

    Public function install(){
        //写入钩子
        Db::execute("INSERT INTO `tplus_hooks` VALUES ('', 'uploadimages', '	多图钩子', '1', ".time().", '')");
        return true;
    }

    Public function uninstall(){
        //移除钩子
        Db::execute("delete from `tplus_hooks` where name='uploadimages'");
        return true;
    }

    //实现的uploadimages钩子方法
    public function uploadimages($param){
        $name = $param['name'] ?: 'pics';
        $valArr = $param['value'] ? explode(',', $param['value']) : [];
        //1为单图上传，2为多图上传
        if(!isset($param['type'])){
            $type = 1; //单图上传
        }else{
            $type = $param['type'];
        }
        if(!isset($param['is_file_up'])){
           $tpl = 'upload';
        }else{
           $tpl = 'uploadfile';
        }
        $this->assign('type',$type);
        $this->assign('name',$name);
        $this->assign('valStr',$param['value']);
        $this->assign('valArr',$valArr);
        return $this->fetch($tpl);
    }
}