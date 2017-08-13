<?php
namespace plugs\chinacity;
use app\common\controller\Plugs;
use plugs\chinacity\controller\Chinacity;
use think\Db;
/**
 * 中国省市区三级联动插件
 * @author i友街
 */

    class chinacityPlugs extends Plugs{

        public $info = array(
            'name'=>'chinacity',
            'title'=>'中国省市区三级联动',
            'description'=>'每个系统都需要的一个中国省市区三级联动插件。',
            'status'=>1,
            'author'=>'i友街',
            'version'=>'2.0'
        );

        public function install(){

            /* 先判断插件需要的钩子是否存在 */
            $this->getisHook('cityselect', $this->info['name'], $this->info['description']);

            //读取插件sql文件
            $sqldata = file_get_contents(PLUGS_PATH.DS.$this->info['name'].'/install.sql');
            $sqlFormat = $this->sql_split($sqldata, config("database.prefix"));
            $counts = count($sqlFormat);
            
            for ($i = 0; $i < $counts; $i++) {
                $sql = trim($sqlFormat[$i]);
                Db::execute($sql);
            }
            return true;
        }

        public function uninstall(){
            //读取插件sql文件
            $sqldata = file_get_contents(PLUGS_PATH.DS.$this->info['name'].'/uninstall.sql');
            $sqlFormat = $this->sql_split($sqldata, config("database.prefix"));
            $counts = count($sqlFormat);
             
            for ($i = 0; $i < $counts; $i++) {
                $sql = trim($sqlFormat[$i]);
                Db::execute($sql);
            }

            //清除钩子
            $hook = Db::name('hooks')->where(array('name'=>'cityselect'))->find();
            if($hook){
                Db::name('hooks')->where(array('name'=>'cityselect'))->delete();
            }


            return true;
        }

        /**
         * @param $param  default  array('province'=>0,'city'=>0,'district'=>0,'community'=>0)
         */
        public function cityselect($param){

            empty($param['province']) ? $province =0 : $province = $param['province'];
            empty($param['city']) ? $city =0 : $city = $param['city'];
            empty($param['district']) ? $district =0 : $district = $param['district'];
            empty($param['community']) ? $community =0 : $community = $param['community'];

            $region = new Chinacity();

            $data = $region->loadArea($province, $city, $district, $community);
            if(!isset($data['city'])){
                $data['city'] = '';
            }
            if(!isset($data['district'])){
                $data['district'] = '';
            }
            if(!isset($data['community'])){
                $data['community'] = '';
            }
            $this->assign('region', $data);
            return $this->fetch('chinacity');
        }

        //获取插件所需的钩子是否存在
        public function getisHook($str, $addons, $msg=''){
            $hook_mod = Db::name('Hooks');
            $where['name'] = $str;
            $gethook = $hook_mod->where($where)->find();
            if(!$gethook || empty($gethook) || !is_array($gethook)){
                $data['name'] = $str;
                $data['description'] = $msg;
                $data['type'] = 1;
                $data['update_time'] = time();
                $data['addons'] = $addons;
                if($hook_mod->insert($data)){
                    //$hook_mod->insert($data);
                }
            }
        }

        /**
         * 解析数据库语句函数
         * @param string $sql  sql语句   带默认前缀的
         * @param string $tablepre  自己的前缀
         * @return multitype:string 返回最终需要的sql语句
         */
        public function sql_split($sql, $tablepre) {
          
            if ($tablepre != "onethink_")
                $sql = str_replace("onethink_", $tablepre, $sql);
                $sql = preg_replace("/TYPE=(InnoDB|MyISAM|MEMORY)( DEFAULT CHARSET=[^; ]+)?/", "ENGINE=\\1 DEFAULT CHARSET=utf8", $sql);

          /*  if ($r_tablepre != $s_tablepre)
                $sql = str_replace($s_tablepre, $r_tablepre, $sql);
                $sql = str_replace("\r", "\n", $sql);
                $ret = array();

                $num = 0;
                $queriesarray = explode(";\n", trim($sql));
                unset($sql);*/
                 $sql = str_replace("\r", "\n", $sql);
                $ret = array();
                $num = 0;
               $queriesarray = explode(";\n", trim($sql));
                unset($sql);   
            foreach ($queriesarray as $query) {
                $ret[$num] = '';
                $queries = explode("\n", trim($query));
                $queries = array_filter($queries);
                foreach ($queries as $query) {
                    $str1 = substr($query, 0, 1);
                    if ($str1 != '#' && $str1 != '-')
                        $ret[$num] .= $query;
                }
                $num++;
            }

            return $ret;
        }
    }