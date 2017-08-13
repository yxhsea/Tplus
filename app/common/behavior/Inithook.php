<?php
namespace app\common\behavior;
use think\Hook;
use think\Db;

// 初始化钩子信息
class Inithook {

    // 行为扩展的执行入口必须是run
    public function run(&$content){
        $data=[];
        if(!$data){
            $hooks = Db::name('hooks')->column('name,addons');
            foreach ($hooks as $key => $value) {
                if($value){
                    $map['status']  =   1;
                    $names          =   explode(',',$value);
                    $map['name']    =   ['IN',$names];
                    $data = Db::name('addons')->where($map)->column('id,name');
                    if($data){
                        $addons = array_intersect($names, $data);

                        Hook::add($key,$this->routes($addons));
                    }
                }
            }
            //S('hooks',Hook::get());
        }else{
            Hook::import($data,false);
        }
    }

    private function routes($addons){
        $data=[];
        foreach ($addons as $key => $value) {
            $data[$key] = 'plugs\\'.$value.'\\'.$value.'Plugs';
        }
        return $data;
    }
}