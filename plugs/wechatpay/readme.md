##前端页面调用方法
```javascript
<script>
js_pay({
                money:money,
                attach:attach,
                body:"在线充值",
                beforeSend:function () {
                    $('.sure').html('Loading...');
                    $('.sure').attr('disabled', '1');
                },
                complete:function () {
                    $('.sure').html('微信支付');
                    $('.sure').removeAttr('disabled');
                },
                succ:function () {
                    // 支付成功后的回调函数
                    mui.alert('充值成功', '提示', function () {
                        window.location.href = "{:url()}";
                    });
                },
                error:function () {
                    mui.alert('充值失败！');
                }
});
</script>
{:plugs('wechatpay')}
```

```php
<?php
namespace app\user\controller;
class Wechatpay
{
    public function notify($arr = []){
        if(!empty($arr)){
            //业务逻辑
        }
    }

}
```
