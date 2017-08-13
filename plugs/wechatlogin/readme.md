#### 微信登录插件
> 这是一个在控制器中调用微信登录的插件，调用示例。
```php
<?php 
namespace app\test\controller;

class Test{
    public function index(){
        plugs('wechatlogin');//调用插件
    }
}
?>
```