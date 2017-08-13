<?php

namespace plugs\sms;
use app\common\controller\Plugs;
use plugs\sms\controller\Sms;
use think\Db;
/**
 * 聚合数据短信接口插件插件
 * @author yxhsea
 */
class smsPlugs extends Plugs{

    Public $info =[
        'name'=>'sms',
        'title'=>'聚合数据短信接口插件',
        'description'=>'聚合数据平台短信接口封装的插件',
        'webvisit'=>0,
        'status'=>1,
        'author'=>'yxhsea',
        'version'=>'1.0'
    ];

    Public function install(){
        //写入钩子
        Db::execute("INSERT INTO `tplus_hooks` VALUES('','sms','短信插件','1',".time().",'')");
        return true;
    }

    Public function uninstall(){
        //移除插件
        Db::execute("DELETE FROM `tplus_hooks` WHERE `name` = 'sms'");
        return true;
    }

    //实现的sms钩子方法,调用示例：plugs('sms',['mobile'=>'','tpl_id'=>'','tp_value'=>'']);
    Public function sms($param){
        /**
         * 'mobile'    => '1891351****', //接受短信的用户手机号码
         * 'tpl_id'    => '111', //您申请的短信模板ID，根据实际情况修改
         * 'tpl_value' =>'#code#=1234&#company#=聚合数据' //您设置的模板变量，根据实际情况修改
         */
        $param['mobile'] = isset($param['mobile']) ? $param['mobile'] : '';
        $param['tpl_id'] = isset($param['tpl_id']) ? $param['tpl_id'] : '';
        $param['tpl_value'] =  isset($param['tpl_value']) ? $param['tpl_value'] : '';
        $sms = new Sms();
        $res = $sms->reqSend($param);
        if($res){
            $result = json_decode($res,true);
            $error_code = $result['error_code'];
            if($error_code == 0){
                //状态为0，说明短信发送成功
                //echo "短信发送成功,短信ID：".$result['result']['sid'];
                return ['status'=>1,'msg'=>"短信发送成功,短信ID：".$result['result']['sid']];
            }else{
                //状态非0，说明失败
                $msg = $result['reason'];
                //echo "短信发送失败(".$error_code.")：".$msg;
                return ['status'=>0,'msg'=>"短信发送失败(".$error_code.")：".$msg];
            }
        }else{
            //返回内容异常，以下可根据业务逻辑自行修改
            //echo "请求发送短信失败";
            return ['status'=>0,'msg'=>'请求发送短信失败'];
        }
    }
}