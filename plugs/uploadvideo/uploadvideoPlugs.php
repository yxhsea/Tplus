<?php

namespace plugs\uploadvideo;
use app\common\controller\Plugs;
use think\Db;
/**
 * 视频上传插件插件
 * @author yxhsea
 */
class uploadvideoPlugs extends Plugs{

    Public $info =[
        'name'=>'uploadvideo',
        'title'=>'视频上传插件',
        'description'=>'这是一个视频上传插件，插件依赖于vendor下的Qiniu类库。',
        'webvisit'=>0,
        'status'=>1,
        'author'=>'yxhsea',
        'version'=>'1.0'
    ];

    Public function install(){
        //写入钩子
        Db::execute("INSERT INTO `tplus_hooks` VALUES ('', 'uploadvideo', '	视频上传钩子', '1', ".time().", '')");
        return true;
    }

    Public function uninstall(){
        //移除钩子
        Db::execute("delete from `tplus_hooks` where name='uploadvideo'");
        return true;
    }

    //实现的uploadvideo钩子方法
    Public function uploadvideo($param){
        $param['name'] = isset($param['name']) ? $param['name'] : '';
        $param['value'] = isset($param['value']) ? $param['value'] : '';
        $this->assign('name', $param['name']);
        $this->assign('value',$param['value']);

        $config = Db::name('addons')->where(['name'=>'uploadvideo'])->value('config');
        $config = json_decode($config,true);
        $this->assign('domain_bucket',$config['domain_bucket']);
        $this->assign('upload_size',$config['upload_size']);
        return $this->fetch('index');
    }

}