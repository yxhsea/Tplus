<?php
namespace app\install\controller;
/*
* 安装Tplus控制器
* Author: 初心 [jialin507@foxmail.com]
*/
use app\common\controller\Tplus;
use think\Db;
use think\Session;

class Index extends Tplus
{
    private $path = './app/install/data/';
    //安装开始
    public function index(){
        //检查是否已安装过
        $file = $this->path . 'install.lock';
        if(file_exists($file)){
            echo "您已经安装了Tpuls，如需重新安装请删除安装目录下的install.lock文件 :)";
            exit;
        }
        $info = 'install';
        if($this->request->isAjax()){
            $index = $this->request->param('index');
            switch ($index){
                case 1:
                    //环境监测
                    $info = $this->steps1();
                    break;
                case 3:
                    //数据库填写
                    $info = $this->steps2($this->request->param());
                    break;
                case 4:
                    //开始安装
                    $info = $this->steps3();
                    break;

            }
            return $info;
        }

        return $this->fetch();
    }

    public function steps1(){
        $info = [];
        $info['version'] = version_compare(PHP_VERSION,'5.5.0', '>');//版本 要求 ＞ 5.5
        //磁盘空间检测
        if(function_exists('disk_free_space')) {
            $info['disk'] = floor(disk_free_space(ROOT_PATH) / (1024*1024));
        }

        $items = [
            ['dir',  '可写', 'ok', './public/uploads'],
            ['dir',  '可写', 'ok', './runtime'],
            ['dir',  '可写', 'ok', './plugs'],
            ['file', '可写', 'ok', './app/database.php'],
            ['file', '可写', 'ok', './app/config.php'],
        ];


        foreach ($items as &$val) {
            if('dir' == $val[0]){
                if(!is_writable(ROOT_PATH . $val[3])) {
                    if(is_dir($val[3])) {
                        $val[1] = '可读';
                        $val[2] = 'error';
                    } else {
                        $val[1] = '不存在';
                        $val[2] = 'error';
                    }
                }
            } else {
                if(file_exists(ROOT_PATH . $val[3])) {
                    if(!is_writable(ROOT_PATH . $val[3])) {
                        $val[1] = '不可写';
                        $val[2] = 'error';
                    }
                } else {
                    if(!is_writable(dirname(ROOT_PATH . $val[3]))) {
                        $val[1] = '不存在';
                        $val[2] = 'error';
                    }
                }
            }
        }

        $info['items'] = $items;

        return $info;
    }

    //数据库填写
    public function steps2($data = []){
        $info = [];
        $info['error'] = true;
        //密码检测
        if($data['adminpwd'] != $data['adminrepwd']){
            $info['msg'] = '管理员密码不一致';
            $info['error'] = false;
        }
        //数据库连接监测
        try{
            Db::connect("mysql://{$data['username']}:{$data['password']}@{$data['ip']}:{$data['port']}/")->query("CREATE DATABASE IF NOT EXISTS {$data['mysqlname']} DEFAULT CHARACTER SET utf8");
        } catch (\Exception $e){
            $info['msg'] = '数据库连接失败。';
            $info['error'] = false;
        }
        $file = $this->path . 'database.tpl';
        if(!file_exists($file)){
            echo "安装配置文件不存在，请检查后重新安装";
            exit;
        }
        //写入数据库配置
        $dbconfig = file_get_contents($file);
        $dbconfig = str_replace('{dbhost}',$data['ip'],$dbconfig);
        $dbconfig = str_replace('{dbname}',$data['mysqlname'],$dbconfig);
        $dbconfig = str_replace('{dbadmin}',$data['username'],$dbconfig);
        $dbconfig = str_replace('{dbpwd}',$data['password'],$dbconfig);
        $dbconfig = str_replace('{dbport}',$data['port'],$dbconfig);
        $dbconfig = str_replace('{dbprefix}',$data['prefix'],$dbconfig);
        file_put_contents('./app/database.php',$dbconfig);

        Session::set('install_info',$data);
        return $info;
    }

    //写入数据库
    public function steps3(){
        $info = Session::get('install_info');

        //写入配置文件
        $file = $this->path . 'config.tpl';
        if(!file_exists($file)){
            echo "安装配置文件不存在，请检查后重新安装";
            exit;
        }
        $config = file_get_contents($file);
        $key = getRandChar(25);
        $config = str_replace('{auth_key}',$key,$config);
        file_put_contents('./app/config.php',$config);

        //写入tags
        $file = $this->path . 'tags.tpl';
        if(!file_exists($file)){
            echo "Tags不存在，请检查后重新安装";
            exit;
        }
        $tags = file_get_contents($file);
        file_put_contents('./app/tags.php',$tags);

        //导入Sql
        $file = $this->path . 'tplus.sql';
        if(!file_exists($file)){
            echo "安装SQL数据库不存在，请检查后重新安装";
            exit;
        }
        $sql = file_get_contents($file);
        $sql = str_replace('tplus_',$info['prefix'],$sql);
        $sql = str_replace("\r", "\n", $sql);
        $sql = explode(";\n", $sql);
//      $db = Db::connect("mysql://{$info['username']}:{$info['password']}@{$info['ip']}:{$info['port']}/{$info['mysqlname']}");
        foreach ($sql as $value) {
            $value = trim($value);
            if(empty($value)){continue;}
            Db::execute($value);
        }

        //写入创始人信息
        $username = $info['adminname'];
        $password = tplus_ucenter_md5($info['adminpwd'], $key);
        //print_r($password);
        $regtime = time();
        $sql = "INSERT INTO `tplus_user` VALUES ('1', '{$username}', '{$password}', '{$regtime}', '', '{$regtime}', '0', '{$regtime}', '1')";
        Db::execute($sql);

        file_put_contents($this->path . 'install.lock','Tplus-lock');
        return $info;
    }
}
