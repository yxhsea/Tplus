<?php
/*
* 后台菜单控制器
* Author: yxhsea@foxmail.com
*/
namespace app\system\controller;

use think\Db;
use think\Page;
use think\Config;

class Addons extends Admin
{	


    //创建向导首页
    public function create(){
        if(!is_writable(PLUGS_PATH))
            $this->error('您没有创建目录写入权限，无法使用此功能');

        $hooks = Db::name('Hooks')->field('name,description')->select();
        $this->assign('Hooks',$hooks);
        return $this->fetch();
    }

    //预览
    public function preview($output = true){
        $data                   =   $this->request->param();
        $data['info']['status'] =   (int)$data['info']['status'];
        $extend                 =   array();
        $custom_config          =   trim($data['custom_config']);
        if(isset($data['has_config']) && $custom_config){
            $custom_config = <<<str


        Public \$custom_config = '{$custom_config}';
str;
            $extend[] = $custom_config;
        }

        $admin_list = trim($data['admin_list']);
        if(isset($data['has_adminlist']) && $admin_list){
            $admin_list = <<<str


        Public \$admin_list = [
            {$admin_list}
        ];
str;
           $extend[] = $admin_list;
        }

        $custom_adminlist = trim($data['custom_adminlist']);
        if(isset($data['has_adminlist']) && $custom_adminlist){
            $custom_adminlist = <<<str


        Public \$custom_adminlist = '{$custom_adminlist}';
        
        Public \$custom_adminlist_title = '{$data['info']['title']}';
str;
            $extend[] = $custom_adminlist;
        }

        $extend = implode('', $extend);
        $hook = '';
        foreach ($data['hook'] as $value) {
            $hook .= <<<str
        //实现的{$value}钩子方法
        Public function {$value}(\$param){

        }

str;
        }
        $webvisit = isset($data['has_outurl']) ? 1:0;
        $tpl = <<<str
<?php

namespace plugs\\{$data['info']['name']};
use app\common\controller\Plugs;

/**
 * {$data['info']['title']}插件
 * @author {$data['info']['author']}
 */

    class {$data['info']['name']}Plugs extends Plugs{

        Public \$info =[
            'name'=>'{$data['info']['name']}',
            'title'=>'{$data['info']['title']}',
            'description'=>'{$data['info']['description']}',
            'webvisit'=>{$webvisit},
            'status'=>{$data['info']['status']},
            'author'=>'{$data['info']['author']}',
            'version'=>'{$data['info']['version']}'
        ];{$extend}

        Public function install(){
            return true;
        }

        Public function uninstall(){
            return true;
        }

{$hook}
    }
str;

        if($output)
            exit($tpl);
        else
            return $tpl;
    }

    public function checkForm(){
        $data                   =   $_POST;
        $data['info']['name']   =   trim($data['info']['name']);
        if(!$data['info']['name'])
            $this->error('插件标识必须');
        //检测插件名是否合法
        $addons_dir             =   ONETHINK_ADDON_PATH;
        if(file_exists("{$addons_dir}{$data['info']['name']}")){
            $this->error('插件已经存在了');
        }
        $this->success('可以创建');
    }

    public function build(){
    	
        $data                   =   $this->request->param();
        $data['info']['name']   =   trim($data['info']['name']);
        $addonFile              =   $this->preview(false); 
        $addons_dir             =   PLUGS_PATH;
        //创建目录结构
        $files          =   array();
        $addon_dir      =   "$addons_dir/{$data['info']['name']}/";
        $files[]        =   $addon_dir;
        $addon_name     =   "{$data['info']['name']}Plugs.php";
        $files[]        =   "{$addon_dir}{$addon_name}";
        if(isset($data['has_config']) && $data['has_config'] == 1);//如果有配置文件
            $files[]    =   $addon_dir.'config.php';
        if(isset($data['has_outurl'])){
            $files[]    =   "{$addon_dir}controller/";
            $files[]    =   "{$addon_dir}controller/{$data['info']['name']}.php";
            $files[]    =   "{$addon_dir}model/";
            $files[]    =   "{$addon_dir}model/{$data['info']['name']}.php";

        }
        $custom_config  =   trim($data['custom_config']);
        if($custom_config)
            $data[]     =   "{$addon_dir}{$custom_config}";
       
        $custom_adminlist = trim($data['custom_adminlist']);
        if($custom_adminlist)
            $data[]     =   "{$addon_dir}{$custom_adminlist}";
        
        create_dir_or_files($files);

        //写文件
        file_put_contents("{$addon_dir}{$addon_name}", $addonFile);
        if(isset($data['has_outurl'])){
            $addonController = <<<str
<?php

namespace plugs\\{$data['info']['name']}\controller;
use app\system\controller\Addons;

class {$data['info']['name']} extends Addons{

}

str;
            file_put_contents("{$addon_dir}controller/{$data['info']['name']}.php", $addonController);
            $addonModel = <<<str
<?php

namespace plugs\\{$data['info']['name']}\model;
use think\Model;

/**
 * {$data['info']['name']}模型
 */
class {$data['info']['name']} extends Model{

}

str;
            file_put_contents("{$addon_dir}model/{$data['info']['name']}.php", $addonModel); //模型
        }
        if(!isset($data['has_config']) ){
           $data['config'] = <<<str
<?php 
    return [];
str;
        }

         file_put_contents("{$addon_dir}config.php", $data['config']);
        return $this->ajaxSuccess('插件创建成功',url('index'));
    }

    /**
     * 插件列表
     */
    public function index(){
       $list = model('Addons')->getList();
       
       $this->assign('list', $list['addons']);
       $this->assign('page', $list['page']);
       return $this->fetch();
    }

    /**
     * 插件后台显示页面
     * @param string $name 插件名
     */
    public function adminList($name){
        $class = get_plugs_class($name);

        if(!class_exists($class))
            $this->error('插件不存在');

        $addon  =   new $class();
        $this->assign('addon', $addon);
        $param  =   $addon->admin_list;
        if(!$param)
            $this->error('插件列表信息不正确');
        extract($param);

        $this->assign('title', $addon->custom_adminlist_title);
        $this->assign($param);
        if(!isset($fields)){
            $fields = '*';
        }
        if(!isset($map)){
            $map = [];
        }
        if(isset($search)){
            $map[$search] = ['like',"%".$this->request->param($search)."%"];
        }
        if(isset($model)){
            $list = Db::name($model)->where($map)->field($fields)->paginate(10);
            $page=$list->render(); //获取分页
            $this->assign('list',$list);
            $this->assign('page',$page);
            $this->assign('rooturl',$this->request->root(true));
        }
        $addons_tpl_path = PLUGS_PATH.'/'.$name.'/'.$addon->custom_adminlist.'.html';    
        if($addon->custom_adminlist){
            $this->assign('custom_adminlist',$this->fetch($addons_tpl_path));
        }
        return $this->fetch();
    }

   

    /**
     * 设置插件页面
     */
    public function config(){
        $id     =   (int)$this->request->param('id');
        $addon  =   Db::name('Addons')->find($id);
        if(!$addon)
            return $this->error('插件未安装');

        $addon_class = get_plugs_class($addon['name']);
        if(!class_exists($addon_class))
            return $this->error("插件{$addon['name']}无法实例化,",'ADDONS','ERR');
        $data  =   new $addon_class;
        $addon['addon_path'] = $data->addon_path;
        $addon['custom_config'] = $data->custom_config;

        $db_config = $addon['config'];

        $addon['config'] = include $data->config_file;
        if($db_config){
            $db_config = json_decode($db_config, true);
            if(isset($db_config['config'])){
                $db_config = $db_config['config'];
            }
            foreach ($addon['config'] as $key => $value) {
                if($value['type'] != 'group'){
                    $addon['config'][$key]['value'] = $db_config[$key];
                }else{
                    foreach ($value['options'] as $gourp => $options) {
                        foreach ($options['options'] as $gkey => $value) {
                            $addon['config'][$key]['options'][$gourp]['options'][$gkey]['value'] = $db_config[$gkey];
                        }
                    }
                }
            }
        }
        $this->assign('data',$addon);
        if(!empty($addon['custom_config']))
            $this->assign('custom_config', $this->fetch($addon['addon_path'].$addon['custom_config']));
        return $this->fetch();
    }

    /**
     * 保存插件设置
     */
    public function saveConfig(){
        $post = $this->request->param();
        $id= isset($post['id']) && $post['id'] > 0 ? $post['id'] : 0 ;
        $config = json_encode($post['config']);
        $flag = Db::name('Addons')->where("id={$id}")->update(['config'=>$config]);
        if($flag !== false){
            return $this->ajaxSuccess('保存成功', url('index'));
        }else{
            return $this->ajaxError('保存失败');
        }
    }

    /**
     * 安装插件
     */
    public function install(){
        $addon_name     =   trim($this->request->param('addon_name'));
        $class          =   get_plugs_class($addon_name);

        if(!class_exists($class))
            $this->error('插件不存在');
        
        $addons  =   new $class;
        $info = $addons->info;
        if(!$info || !$addons->checkInfo())//检测信息的正确性
            $this->error('插件信息缺失');
        session('addons_install_error',null);
        $install_flag   =   $addons->install();
        if(!$install_flag){
            $this->error('执行插件预安装操作失败'.session('addons_install_error'));
        }

        $addonsModel    =   Db::name('addons');
   
        $data           =   $addonsModel->createData(1,$info);
      
        if(is_array($addons->admin_list) && $addons->admin_list !== []){
            $data['has_adminlist'] = 1;
            $m_data = [
                'title' => $addons->admin_list['menu_title'],
                'pid'   =>  $addons->admin_list['pid'],
                'sort'  =>  99,
                'url'   =>  'Addons/adminList',
                'param' =>  'name=' . $info['name'],
                'font_class'=>  $addons->admin_list['font_class'],
            ];
            $data['mid'] = Db::name('menu')->insertGetId($m_data);
        }else{
            $data['has_adminlist'] = 0;
        }
        $data['create_time'] = time();

        $addonsModel    =   Db::name('addons');

        if(!$data)
            $this->error($addonsModel->getError());
        if($addonsModel->insert($data)){

            $config         =   [
                    'config'=>json_encode($addons->getConfig())
            ];
            $addonsModel->where("name='{$addon_name}'")->update($config);
            $hooks_update   =   model('Hooks')->updateHooks($addon_name);

            if($hooks_update){
                return $this->ajaxSuccess('安装成功',url('index'));
            }else{
                //$addonsModel->where("name='{$addon_name}'")->delete();
                return $this->ajaxError('更新钩子处插件失败,请卸载后尝试重新安装');
            }

        }else{
            return $this->ajaxError('写入插件数据失败');
        }
    }

    /**
     * 卸载插件
     */
    public function uninstall(){
        $id             =   trim($this->request->param('id'));
        $db_addons      =   Db::name('Addons')->find($id);
        $class          =   get_plugs_class($db_addons['name']);
        $this->assign('jumpUrl',url('index'));
        if(!$db_addons || !class_exists($class))
            return $this->ajaxError('插件不存在');
        session('addons_uninstall_error',null);
        $addons =   new $class;
        $uninstall_flag =   $addons->uninstall();
        if(!$uninstall_flag)
            return $this->ajaxError('执行插件预卸载操作失败'.session('addons_uninstall_error'));
        $hooks_update   =   model('Hooks')->removeHooks($db_addons['name']);
        if($hooks_update === false){
            return $this->ajaxError('卸载插件所挂载的钩子数据失败');

        }
        if($db_addons['mid'] > 0){
            Db::name('menu')->where("id='{$db_addons['mid']}'")->delete();
        }
        $delete = Db::name('Addons')->where("name='{$db_addons['name']}'")->delete();
        if($delete === false){
            return $this->ajaxError('卸载插件失败');
        }else{
            return $this->ajaxSuccess('卸载成功');
        }
    }

    /**
     * 钩子列表
     */
    public function hooks(){
        $map = [];
        $list = Db::name('Hooks')->where($map)->order('id DESC')->paginate(10); //分页查询
        $page=$list->render(); //获取分页
        $this->assign('list',$list);
        $this->assign('page',$page);
        //int_to_string($list, array('type'=>C('HOOKS_TYPE')));
        // 记录当前列表页的cookie
       // Cookie('__forward__',$_SERVER['REQUEST_URI']);
         return $this->fetch();
    }

    //新增钩子
    public function addhook(){
        $hooks_type = Config::get("HOOKS_TYPE");
        $this->assign('hooks_type',$hooks_type);
        return $this->fetch();
    }

    //钩子出编辑挂载插件页面
    public function edithook($id){
        $hook = Db::name('Hooks')->find($id);
        $hooks_type = Config::get("HOOKS_TYPE");
        $this->assign('hooks_type',$hooks_type);
        $this->assign('info',$hook);
        return $this->fetch();
    }

    //超级管理员删除钩子
    public function delhook($id){
        if(Db::name('Hooks')->delete($id) !== false){
            return $this->ajaxSuccess('删除成功');
        }else{
            return $this->ajaxError('删除失败');
        }
    }

    public function updateHook(){
        $hookModel  =   Db::name('Hooks');
        $data       =   $hookModel->createData(1);
        if($data){
            if(!empty($data['id'])){
                $flag = $hookModel->update($data);
                if($flag !== false)
                    return $this->ajaxSuccess('更新成功', url('hooks'));
                else
                    return $this->ajaxError('更新失败');
            }else{
                $flag = $hookModel->insert($data);
                if($flag)
                    return $this->ajaxSuccess('新增成功', url('hooks'));
                else
                    return $this->ajaxError('新增失败');
            }
        }else{
            return $this->ajaxError($hookModel->getError());
        }
    }

   

}
