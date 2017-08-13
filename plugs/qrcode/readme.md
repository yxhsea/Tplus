#### php生成二维码的插件
> 这是一个使用phpqrcode类库生成二维码的插件，这个插件依赖于vendor文件夹下的Phpcode类库。   
> 插件必须已经在后台安装并启用后才能生效，调用示例：
```php
<?php
namespace app\test\controller;
use app\system\controller\Admin;
class Index extends Admin{
    public function index(){
        /**
         * qrcode 是插件名称
         * url 是扫描二维码之后的跳转地址，可携带get参数，例如：http://www.qq.com?id=1
         * path 参数可选 false是默认生成二维码到页面上 ,path是生成二维码保存为图片,是保存路径。
         *              例如：保存到public/code/目录下，即public/code/code.png 
         * size 参数是数字 二维码的大小   
         */
        plugs('qrcode',['url'=>'http://www.qq.com','path'=>'public/code/code.png','size'=>4]);
    }
}
```