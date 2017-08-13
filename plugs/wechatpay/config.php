<?php
return [
    'wechat_mchid'=>[
        'title'=>'商户号:',
        'type'=>'text',
        'value'=>'',
    ],
    'wechat_secret'=>[
        'title'=>'支付秘钥:',
        'type'=>'text',
        'value'=>'',
    ],
    'wechat_redirects'=>[
        'title'=>'支付成功后回调类:',
        'type'=>'text',
        'value'=>'',
    ],
    'redirectstisp'=>[
        'title'=>'回调方法填写:',
        'type'=>'tisp',
        'value'=>'请填写类名，类里面自行复写notify方法，确保外部可访问，例如\app\user\controller\Wechatpay',
    ],
];
													