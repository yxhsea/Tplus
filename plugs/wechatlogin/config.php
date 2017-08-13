<?php
return [
	'wechat_appid'=>[
		'title'=>'微信AppId:',
		'type'=>'text',
		'value'=>'',
	],
    'wechat_appsecret'=>[
        'title'=>'微信AppSecret:',
        'type'=>'text',
        'value'=>'',
    ],
    'wechat_scope'=>[
        'title'=>'微信scope:',
        'type'=>'select',
        'options'=>[
            'snsapi_base'=>'不弹授权页面，只拿openid',
            'snsapi_userinfo'=>'弹授权页面，拿openid头像昵称等',
        ],
        'value'=>'',
    ],
    'wechat_host'=>[
        'title'=>'网站域名:',
        'type'=>'text',
        'value'=>'',
    ],
    'wechat_redirects'=>[
        'title'=>'登录成功后回调地址:',
        'type'=>'text',
        'value'=>'',
    ],
    'redirectstisp'=>[
        'title'=>'回调地址填写格式:',
        'type'=>'tisp',
        'value'=>'以url()格式传递，如 home/index/index',
    ],
    'pulgtisp'=>[
        'title'=>'插件使用方式:',
        'type'=>'tisp',
        'value'=>'无需传参直接调用插件，登录成功后会将获取到的信息存在session中',
    ],
    'sessiontisp'=>[
        'title'=>'session中数据:',
        'type'=>'tisp',
        'value'=>'wechat_openid ,  [wechat_nickname] ,  [wechat_img_url]',
    ],

];