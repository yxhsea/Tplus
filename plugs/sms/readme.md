#### 聚合数据短信接口插件
> 这是一个使用聚合数据平台短信接口封装的插件。  
> 插件使用，必须在后台安装并启用后才能生效，并且要求设置appkey。  
```
//调用示例
<?php
namespace app\test\controller;
use app\system\controller\Admin;
class Index extends Admin{
    public function index(){
        /**
        * 'mobile'    => '1891351****', //接受短信的用户手机号码
        * 'tpl_id'    => '111', //您申请的短信模板ID，根据实际情况修改
        * 'tpl_value' =>'#code#=1234&#company#=聚合数据' //您设置的模板变量，根据实际情况修改
        */
        $res = plugs('sms',['mobile'=>'18373288691','tpl_id'=>'39145','tpl_value'=>'#code#=1234']);
        print_r($res);//插件返回的结果是一个数组
    }
}
```